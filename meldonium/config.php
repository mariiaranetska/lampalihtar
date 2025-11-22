<?php

function validateConfig($config)
{
    $valid = [
        "password" => validatePassword($config["password"]),
        "meta" => validateMeta($config["meta"]),
        "redirect" => validateRedirect($config["redirect"]),
        "analytics" => validateAnalytics($config["analytics"]),
        "code" => validateCode($config["code"]),
        "widgets" => validateWidgets($config["widgets"]),
        "upsells" => validateUpsells($config["upsells"]),
        "integrations" => validateIntegrations($config["integrations"])
    ];

    return $valid;
}

function validatePassword($password)
{
    return (!empty($password) && is_string($password))
        ? $password
        : "8c6976e5b5410415bde908bd4dee15dfb167a9c873fc4bb8a81f6f2ab448a918";
}

function validateMeta($config)
{
    $config = (array)$config;
    $valid = [];
    foreach (["title", "keywords", "description"] as $item) {
        $valid[$item] = (!empty($config[$item]) && is_string($config[$item]))
            ? $config[$item]
            : "";
    };

    return $valid;
}

function validateRedirect($config)
{
    $config = (array)$config;
    $valid = [
        "enabled" => $config["enabled"] === true ? true : false,
        "deviceType" => (!empty($config["deviceType"]) && in_array($config["deviceType"], ["mobile", "desktop"]))
            ? $config["deviceType"]
            : "mobile",
        "url" => (!empty($config["url"]) && is_string($config["url"]))
            ? $config["url"]
            : ""
    ];

    if (empty($valid["deviceType"]) || empty($valid["url"])) {
        $valid["enabled"] = false;
    }

    return $valid;
}

function validateAnalytics($config)
{
    $config = (array)$config;
    $valid = [];
    foreach (["gtm", "yandex", "google", "mail", "vk", "fb"] as $item) {
        $valid[$item] = (!empty($config[$item]) && is_array($config[$item]))
            ? array_reduce($config[$item], function ($result, $item) {
                if (!empty($item) && is_string($item) && !preg_match("/\s/", $item)) $result [] = $item;
                return $result;
            }, [])
            : [];
    };
    $valid["optimize"] = (!empty($config["optimize"]) && is_string($config["optimize"]))
        ? $config["optimize"]
        : "";

    return $valid;
}

function validateCode($config)
{
    return array_map(function ($snippet) {
        $validSnippet = [
            "enabled" => $snippet["enabled"] === true ? true : false,
            "pages" => (!empty($snippet["pages"]) && is_array($snippet["pages"]))
                ? array_reduce($snippet["pages"], function ($result, $item) {
                    if (!in_array($item, $result) && in_array($item, ["index", "thankyou"])) $result [] = $item;
                    return $result;
                }, [])
                : [],
            "position" => (!empty($snippet["position"]) && in_array($snippet["position"], ["head", "body-start", "body-end"]))
                ? $snippet["position"]
                : "",
            "code" => (!empty($snippet["code"]) && is_string($snippet["code"]))
                ? $snippet["code"]
                : ""
        ];

        foreach (array_keys($validSnippet) as $key) {
            if (empty($validSnippet[$key])) {
                $validSnippet["enabled"] = false;
            }
        }

        return $validSnippet;
    }, (!empty($config) && is_array($config)) ? $config : [[
        "pages" => ["index"],
        "position" => "body-end"
    ]]);
}

function validateWidgets($config)
{
    $config = (array)$config;
    return [
        "fakeorders" => validateWidgetFakeorders($config["fakeorders"]),
        "fastdelivery" => validateWidgetFastdelivery($config["fastdelivery"]),
        "panel" => validateWidgetPanel($config["panel"]),
        "geofeedback" => validateWidgetGeofeedback($config["geofeedback"]),
        "popup" => validateWidgetPopup($config["popup"])
    ];
}

function validateWidgetFakeorders($config)
{
    $config = (array)$config;
    $valid = [
        "enabled" => $config["enabled"] === true ? true : false,
        "mobile" => $config["mobile"] === false ? false : true,
        "insertGeo" => $config["insertGeo"] === false ? false : true,
        "backgroundColor" => (!empty($config["backgroundColor"]) && is_string($config["backgroundColor"]))
            ? $config["backgroundColor"]
            : "#333",
        "textColor" => (!empty($config["textColor"]) && is_string($config["textColor"]))
            ? $config["textColor"]
            : "#fff",
        "borderRadius" => (!empty($config["borderRadius"]) && is_int($config["borderRadius"]))
            ? $config["borderRadius"]
            : 0,
        "maxKinds" => (!empty($config["maxKinds"]) && is_int($config["maxKinds"]))
            ? $config["maxKinds"]
            : 1,
        "maxItems" => (!empty($config["maxItems"]) && is_int($config["maxItems"]))
            ? $config["maxItems"]
            : 2,
        "measure" => (!empty($config["measure"]) && is_string($config["measure"]))
            ? $config["measure"]
            : "шт.",
        "goods" => (!empty($config["goods"]) && is_array($config["goods"]))
            ? array_filter($config["goods"], function ($good) {
                return (is_string($good) && !empty($good));
            })
            : [],
        "cities" => (!empty($config["cities"]) && is_array($config["cities"]))
            ? array_filter($config["cities"], function ($city) {
                return (is_string($city) && !empty($city));
            })
            : [
                "Абакан", "Азов", "Александров", "Алексин", "Альметьевск", "Анапа", "Ангарск", "Апатиты", "Арсеньев",
                "Артем", "Архангельск", "Асбест", "Астрахань", "Балаково", "Балашиха", "Балашов", "Батайск", "Белгород",
                "Белово", "Белогорск", "Белорецк", "Белореченск", "Бердск", "Березники", "Березовский", "Бийск",
                "Благовещенск", "Бор", "Борисоглебск", "Боровичи", "Братск", "Брянск", "Бугульма", "Буденновск",
                "Бузулук", "Буйнакск", "Великие Луки", "Видное", "Владимир", "Волгоград", "Волгодонск", "Волжск",
                "Волжский", "Вологда", "Вольск", "Воркута", "Воронеж", "Воскресенск", "Воткинск", "Всеволожск",
                "Выборг", "Выкса", "Вязьма", "Гатчина", "Георгиевск", "Глазов", "Губкин", "Гуково", "Донской",
                "Егорьевск", "Ейск", "Екатеринбург", "Елабуга", "Елец", "Железногорск", "Жуковский", "Заречный",
                "Зеленогорск", "Зеленодольск", "Златоуст", "Иваново", "Ивантеевка", "Ижевск", "Избербаш", "Иркутск",
                "Искитим", "Ишим", "Ишимбай", "Йошкар-Ола", "Казань", "Калининград", "Калуга", "Камышин", "Канск",
                "Кемерово", "Кинешма", "Кириши", "Киров", "Кирово-Чепецк", "Киселевск", "Кисловодск", "Клин", "Клинцы",
                "Ковров", "Когалым", "Коломна", "Копейск", "Королев", "Кострома", "Котлас", "Красногорск", "Краснодар",
                "Краснокаменск", "Краснокамск", "Краснотурьинск", "Красноярск", "Кропоткин", "Крымск", "Кстово", "Кузнецк",
                "Кунгур", "Курган", "Курск", "Лабинск", "Лениногорск", "Липецк", "Лиски", "Лобня", "Лысьва", "Лыткарино",
                "Люберцы", "Магадан", "Магнитогорск", "Минусинск", "Михайловка", "Михайловск", "Мичуринск", "Москва",
                "Мурманск", "Муром", "Мытищи", "Назарово", "Назрань", "Наро-Фоминск", "Находка", "Невинномысск", "Нерюнгри",
                "Нефтекамск", "Нефтеюганск", "Нижневартовск", "Нижнекамск", "Нижний Новгород", "Нижний Тагил", "Новоалтайск",
                "Новокузнецк", "Новокуйбышевск", "Новомосковск", "Новороссийск", "Новосибирск", "Новотроицк", "Новоуральск",
                "Новочебоксарск", "Новочеркасск", "Новошахтинск", "Ногинск", "Норильск", "Ноябрьск", "Нягань", "Обнинск",
                "Одинцово", "Озерск", "Октябрьский", "Омск", "Орел", "Оренбург", "Орехово-Зуево", "Орск", "Павлово",
                "Пенза", "Первоуральск", "Пермь", "Петрозаводск", "Подольск", "Полевской", "Прокопьевск", "Прохладный", "Псков",
                "Пушкино", "Раменское", "Ревда", "Реутов", "Ржев", "Россошь", "Рубцовск", "Рыбинск", "Рязань", "Сальск", "Самара",
                "Санкт-Петербург", "Саранск", "Сарапул", "Саратов", "Саров", "Свободный", "Севастополь", "Северодвинск", "Северск",
                "Сергиев Посад", "Серов", "Серпухов", "Сертолово", "Сибай", "Симферополь", "Смоленск", "Соликамск", "Солнечногорск",
                "Сосновый Бор", "Старый Оскол", "Стерлитамак", "Ступино", "Сургут", "Сызрань", "Сыктывкар", "Таганрог", "Тамбов",
                "Тверь", "Тимашевск", "Тихвин", "Тихорецк", "Тобольск", "Тольятти", "Томск", "Троицк", "Тула", "Тюмень", "Улан-Удэ",
                "Ульяновск", "Уссурийск", "Усть-Илимск", "Уфа", "Ухта", "Фрязино", "Хабаровск", "Химки", "Чайковский", "Чапаевск",
                "Чебоксары", "Челябинск", "Черемхово", "Череповец", "Черкесск", "Черногорск", "Чехов", "Чистополь", "Чита",
                "Шадринск", "Шали", "Шахты", "Щелково", "Электросталь", "Элиста", "Энгельс", "Юрга", "Якутск", "Ялта", "Ярославль"
            ],
        "men" => (!empty($config["men"]) && is_array($config["men"]))
            ? array_filter($config["men"], function ($name) {
                return (is_string($name) && !empty($name));
            })
            : (!isset($config["men"])
                ? [
                    "Александр", "Алексей", "Антон", "Андрей", "Анатолий", "Борис", "Богдан", "Владимир", "Виктор", "Виталий",
                    "Вячеслав", "Валерий", "Глеб", "Геннадий", "Григорий", "Герман", "Георгий", "Денис", "Давид", "Евгений", "Егор",
                    "Иван", "Игорь", "Игнат", "Илья", "Константин", "Кирилл", "Леонид", "Михаил", "Марат", "Николай", "Никита",
                    "Олег", "Петр", "Павел", "Роман", "Ренат", "Руслан", "Рустам", "Станислав", "Семен", "Федор", "Эдуард",
                    "Юрий", "Ярослав", "Яков"
                ]
                : []),
        "women" => (!empty($config["women"]) && is_array($config["women"]))
            ? array_filter($config["women"], function ($name) {
                return (is_string($name) && !empty($name));
            })
            : (!isset($config["women"])
                ? [
                    "Анна", "Анастасия", "Алена", "Алина", "Анжелла", "Агата", "Валентина", "Валерия", "Виктория", "Галина",
                    "Диана", "Дарья", "Екатерина", "Елена", "Евгения", "Жанна", "Зоя", "Ирина", "Инна", "Инесса", "Инга",
                    "Кристина", "Ксения", "Карина", "Любовь", "Людмила", "Марина", "Мария", "Марта", "Надежда", "Наталия",
                    "Ольга", "Римма", "Рая", "Светлана", "Софья", "Татьяна", "Тамара", "Ульяна", "Юлия", "Яна"
                ]
                : []),
    ];

    if (empty($valid["goods"]) || empty($valid["cities"]) || (empty($valid["men"]) && empty($valid["women"]))) {
        $valid["enabled"] = false;
    }

    return $valid;
}

function validateWidgetFastdelivery($config)
{
    $config = (array)$config;
    $valid = [
        "enabled" => $config["enabled"] === true ? true : false,
        "mobile" => $config["mobile"] === false ? false : true,
        "backgroundColor" => (!empty($config["backgroundColor"]) && is_string($config["backgroundColor"]))
            ? $config["backgroundColor"]
            : "#333",
        "textColor" => (!empty($config["textColor"]) && is_string($config["textColor"]))
            ? $config["textColor"]
            : "#fff",
        "borderRadius" => (!empty($config["borderRadius"]) && is_int($config["borderRadius"]))
            ? $config["borderRadius"]
            : 0
    ];

    return $valid;
}

function validateWidgetPanel($config)
{
    $config = (array)$config;
    $valid = [
        "enabled" => $config["enabled"] === true ? true : false,
        "mobile" => $config["mobile"] === false ? false : true,
        "backgroundColor" => (!empty($config["backgroundColor"]) && is_string($config["backgroundColor"]))
            ? $config["backgroundColor"]
            : "#333",
        "textColor" => (!empty($config["textColor"]) && is_string($config["textColor"]))
            ? $config["textColor"]
            : "#fff",
    ];

    return $valid;
}

function validateWidgetGeofeedback($config)
{
    $config = (array)$config;
    $valid = [
        "enabled" => $config["enabled"] === true ? true : false
    ];

    return $valid;
}

function validateWidgetPopup($config)
{
    $config = (array)$config;
    $valid = [
        "enabled" => $config["enabled"] === true ? true : false,
        "title" => (!empty($config["title"]) && is_string($config["title"]))
            ? $config["title"]
            : "",
        "text" => (!empty($config["text"]) && is_string($config["text"]))
            ? $config["text"]
            : "",
        "borderRadius" => (!empty($config["borderRadius"]) && is_int($config["borderRadius"]))
            ? $config["borderRadius"]
            : 0,
        "buttonText" => (!empty($config["buttonText"]) && is_string($config["buttonText"]))
            ? $config["buttonText"]
            : "Отправить",
        "buttonBackgroundColor" => (!empty($config["buttonBackgroundColor"]) && is_string($config["buttonBackgroundColor"]))
            ? $config["buttonBackgroundColor"]
            : "#333",
        "buttonTextColor" => (!empty($config["buttonTextColor"]) && is_string($config["buttonTextColor"]))
            ? $config["buttonTextColor"]
            : "#fff",
        "textColor" => (!empty($config["textColor"]) && is_string($config["textColor"]))
            ? $config["textColor"]
            : "#fff",
        "backgroundColor" => (!empty($config["backgroundColor"]) && is_string($config["backgroundColor"]))
            ? $config["backgroundColor"]
            : "rgba(0,0,0,.7)",
        "image" => (!empty($config["image"]) && is_string($config["image"]))
            ? $config["image"]
            : "",
        "timer" => (!empty($config["timer"]) && is_int($config["timer"]))
            ? $config["timer"]
            : 0
    ];

    if (empty($valid["title"]) || empty($valid["text"])) {
        $valid["enabled"] = false;
    }

    return $valid;
}

function validateUpsells($config)
{
    return array_map(function ($upsell) {
        $validUpsell = [
            "enabled" => $upsell["enabled"] === true ? true : false,
            "title" => (!empty($upsell["title"]) && is_string($upsell["title"]))
                ? $upsell["title"]
                : "",
            "description" => (!empty($upsell["description"]) && is_string($upsell["description"]))
                ? $upsell["description"]
                : "",
            "crmid" => (!empty($upsell["crmid"]) && is_string($upsell["crmid"]))
                ? $upsell["crmid"]
                : "",
            "oldPrice" => (!empty($upsell["oldPrice"]) && is_int($upsell["oldPrice"]))
                ? $upsell["oldPrice"]
                : 0,
            "newPrice" => (!empty($upsell["newPrice"]) && is_int($upsell["newPrice"]))
                ? $upsell["newPrice"]
                : 0,
            "image" => (!empty($upsell["image"]) && is_string($upsell["image"]))
                ? $upsell["image"]
                : ""
        ];

        if (empty($validUpsell["title"]) || empty($validUpsell["description"]) || empty($validUpsell["crmid"]) || empty($validUpsell["image"])) {
            $validUpsell["enabled"] = false;
        }

        return $validUpsell;
    }, (!empty($config) && is_array($config)) ? $config : [[]]);
}

function validateIntegrations($config)
{
    $config = (array)$config;
    return [
        "email" => validateIntegrationEmail($config["email"]),
        "webhooks" => validateIntegrationWebhooks($config["webhooks"])
    ];
}

function validateIntegrationEmail($config)
{
    $config = (array)$config;
    $valid = [
        "enabled" => $config["enabled"] === true ? true : false,
        "emails" => (!empty($config["emails"]) && is_array($config["emails"]))
            ? array_filter($config["emails"], function ($email) {
                return (is_string($email) && !empty($email));
            })
            : []
    ];

    if (empty($valid["emails"])) {
        $valid["enabled"] = false;
    }

    return $valid;
}

function validateIntegrationWebhooks($config)
{
    return array_map(function ($webhook) {
        $validWebhook = [
            "enabled" => $webhook["enabled"] === true ? true : false,
            "name" => (!empty($webhook["name"]) && is_string($webhook["name"]))
                ? $webhook["name"]
                : "",
            "url" => (!empty($webhook["url"]) && is_string($webhook["url"]))
                ? $webhook["url"]
                : "",
            "method" => (!empty($webhook["method"]) && in_array($webhook["method"], ["POST", "GET"]))
                ? $webhook["method"]
                : "POST",
            "events" => (!empty($webhook["events"]) && is_array($webhook["events"]))
                ? array_reduce($webhook["events"], function ($result, $item) {
                    if (!in_array($item, $result) && in_array($item, ["lead", "upsell"])) $result [] = $item;
                    return $result;
                }, [])
                : [],
            "emailResponse" => $webhook["emailResponse"] === false ? false : true,
            "contentType" => (!empty($webhook["contentType"]) && in_array($webhook["contentType"], ["urlencoded", "json", "xml"]))
                ? $webhook["contentType"]
                : "urlencoded",
            "content" => [
                "urlencoded" => (!empty($webhook["content"]["urlencoded"]) && is_array($webhook["content"]["urlencoded"]))
                    ? array_filter($webhook["content"]["urlencoded"], function ($data) {
                        return (is_array($data) && !empty($data["name"]) && is_string($data["name"]) && !empty($data["value"] && is_string($data["value"])));
                    })
                    : [],
                "json" => (!empty($webhook["content"]["json"]) && is_string($webhook["content"]["json"]))
                    ? $webhook["content"]["json"]
                    : "",
                "xml" => (!empty($webhook["content"]["xml"]) && is_string($webhook["content"]["xml"]))
                    ? $webhook["content"]["xml"]
                    : ""
            ],
            "headers" => (!empty($webhook["headers"]) && is_array($webhook["headers"]))
                ? array_filter($webhook["headers"], function ($header) {
                    return (is_array($header) && !empty($header["name"]) && is_string($header["name"]) && !empty($header["value"] && is_string($header["value"])));
                })
                : []
        ];

        foreach (["name", "url", "method", "events"] as $prop) {
            if (empty($validWebhook[$prop])) {
                $validWebhook["enabled"] = false;
            }
        }
        if (empty($validWebhook["headers"])) {
            $validWebhook["headers"] = [[
                "name" => "",
                "value" => ""
            ]];
        }
        if (empty($validWebhook["content"]["urlencoded"])) {
            $validWebhook["content"]["urlencoded"] = [[
                "name" => "",
                "value" => ""
            ]];
        }

        return $validWebhook;
    }, (!empty($config) && is_array($config)) ? $config : [[]]);
}


function getConfig()
{
    $config = [];
    if (file_exists(dirname(__FILE__) . '/config.json')) {
        try {
            $config = json_decode(file_get_contents(dirname(__FILE__) . '/config.json'), true);
        } catch (Exception $e) {
            error_log("Fail to load config file: $e", 0);
        }
    }
    return validateConfig($config);
}

function setConfig($data)
{
    try {
        if (!empty($data["password"])) {
            $data["password"] = hash("sha256", $data["password"]);
        }
        file_put_contents('./config.json', json_encode(($data), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
    } catch (Exception $e) {
        error_log("Fail to save config file: $e", 0);
        throw $e;
    }
}