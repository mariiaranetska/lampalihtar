<?php

function processForm($config)
{
    $ok = false;
    $passed = true;
    $requests = [];
    $results = [];
    foreach ($config["webhooks"] as $webhook) {
        if (!$webhook["enabled"]) continue;
        if ($_POST["upsell"] && !in_array("upsell", $webhook["events"])) continue;
        if (!$_POST["upsell"] && !in_array("lead", $webhook["events"])) continue;
        $passed = false;
        $results[$webhook["name"]] = [
            "email" => $webhook["emailResponse"]
        ];
        $headers = array_reduce($webhook["headers"], function ($result, $header) {
            if (!empty($header["name"]) && !empty($header["value"])) {
                $result [] = implode(": ", $header);
            }
            return $result;
        }, []);
        $content = $webhook["contentType"] == "urlencoded"
            ? http_build_query(array_reduce($webhook["content"]["urlencoded"], function ($data, $item) {
                $data[$item["name"]] = expandMacro($item["value"], $_POST);
                return $data;
            }, []))
            : expandMacro($webhook["content"][$webhook["contentType"]], $_POST);
        $contentType = "application/x-www-form-urlencoded";
        switch ($webhook["contentType"]) {
            case "json":
                $contentType = "application/json";
                break;
            case "xml":
                $contentType = "text/xml";
                break;
        }
        $requests[$webhook["name"]] = [
            "url" => $webhook["url"],
            "method" => $webhook["method"],
            "headers" => $headers,
            "contentType" => $contentType,
            "content" => $content
        ];
    }
    $responses = sendRequests($requests);
    foreach ($responses as $webhook => $response) {
        $result = [];
        if ($response["responseCode"] > 199 && $response["responseCode"] < 300) {
            $result["status"] = "OK";
            $ok = true;
        } else {
            $result["status"] = "ERROR";
        }
        if ($response["responseCode"]) {
            $result["responseCode"] = $response["responseCode"];
            $result["responseBody"] = $response["responseBody"];
        } else {
            $result["responseCode"] = 0;
            $result["responseBody"] = $response["error"];
        }
        $result["email"] = $results[$webhook]["email"];
        $results[$webhook] = $result;
    }
    if ($config["email"]["enabled"]) {
        $passed = false;
        sendEmail(join(",", $config["email"]["emails"]), $results) && $ok = true;
    }
    if (!$ok && !$passed) {
        throw new Exception("Fail to send lead to any of destinations");
    }
}

function expandMacro($string, $replacements)
{
    $replacements['_domain'] = parse_url($_SERVER['HTTP_REFERER'])["host"];
    $replacements['_url'] = $_SERVER['HTTP_REFERER'];
    $replacements['_ip'] = $_SERVER["REMOTE_ADDR"];
    $replacements['_ua'] = $_SERVER["HTTP_USER_AGENT"];
    $splitted = preg_split("/(\{%[^(%\})]*%\})/", $string, -1, PREG_SPLIT_NO_EMPTY | PREG_SPLIT_DELIM_CAPTURE);
    $expanded = array_map(function ($entry) use ($replacements) {
        if (mb_substr($entry, 0, 2) == "{%") {
            $splittedEntry = explode("|", mb_substr($entry, 2, -2));
            foreach ($splittedEntry as $macro) {
                list($macro, $arguments) = explode(" ", trim($macro), 2);
                if (in_array(mb_substr($macro, 0, 1), ['"', "'"])) return mb_substr($macro, 1, -1);
                if ($macro == "_created") {
                    $date = new DateTime();
                    if (!empty($arguments)) {
                        try {
                            $date = new DateTime("now", new DateTimeZone(explode(" ", $arguments)[0]));
                        } catch (Exception $e) {
                        }
                    }
                    return $date->format('Y-m-d H:i:s');
                }
                if (isset($replacements[$macro]) && $replacements[$macro] != "") return $replacements[$macro];
            }
            return "";
        }
        return $entry;
    }, $splitted);
    return join("", $expanded);
}

function sendRequests($requests)
{
    $results = [];
    $curls = [];
    foreach ($requests as $webhook => $request) {
        $curls[$webhook] = curl_init();
        curl_setopt($curls[$webhook], CURLOPT_CONNECTTIMEOUT, 0);
        curl_setopt($curls[$webhook], CURLOPT_TIMEOUT, 10);
        curl_setopt($curls[$webhook], CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curls[$webhook], CURLOPT_USERAGENT, "Meldonium");
        curl_setopt($curls[$webhook], CURLOPT_URL, $request["url"]);
        curl_setopt($curls[$webhook], CURLOPT_HEADER, false);
        curl_setopt($curls[$webhook], CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($curls[$webhook], CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($curls[$webhook], CURLOPT_CUSTOMREQUEST, $request["method"]);
        if ($request["method"] == "POST") {
            $request["headers"] [] = "Content-Type: {$request['contentType']}";
            curl_setopt($curls[$webhook], CURLOPT_POSTFIELDS, $request["content"]);
        }
        curl_setopt($curls[$webhook], CURLOPT_HTTPHEADER, $request["headers"]);
    }
    $multi = curl_multi_init();
    foreach ($curls as $webhook => $curl) {
        curl_multi_add_handle($multi, $curl);
    }
    $running = NULL;
    do {
        curl_multi_exec($multi, $running);
        curl_multi_select($multi);
    } while ($running > 0);
    foreach ($curls as $webhook => $curl) {
        $result = [
            "error" => curl_error($curl),
            "responseCode" => curl_getinfo($curl, CURLINFO_HTTP_CODE),
            "responseBody" => preg_replace_callback('/\\\\u([0-9a-fA-F]{4})/', function ($match) {
                return mb_convert_encoding(pack('H*', $match[1]), 'UTF-8', 'UTF-16BE');
            }, curl_multi_getcontent($curl))
        ];
        curl_multi_remove_handle($multi, $curl);
        $results[$webhook] = $result;
    }
    curl_multi_close($multi);
    return $results;
}

function sendEmail($email, $results)
{
    $ok = true;
    $siteName = parse_url($_SERVER["HTTP_REFERER"])["host"];
    $phone = $_POST["Телефон"] ?: $_POST["телефон"] ?: $_POST["phone"]
        ?: $_POST["Phone"] ?: $_POST["mobile"] ?: $_POST["Mobile"];
    $subject = "Заявка от $phone с сайта $siteName";
    $body = "<html><body>";
    if ($_POST["upsell"]) {
        $body .= "<p>Апселл к заказу от $phone</p><p>Товар: {$_POST['upsell']}</p>";
    } else {
        $body .= "<p>Новый заказ:</p><ul>";
        foreach ($_POST as $name => $value) {
            mb_substr($name, 0, 1) != "_" && $body .= "<li>$name: $value</li>";
        }
        $body .= "
          <li>IP: {$_SERVER["REMOTE_ADDR"]}</li>
          <li>URL: {$_SERVER["HTTP_REFERER"]}</li>
          </ul>
        ";
    }
    $add = "";
    foreach ($results as $name => $result) {
        $color = $result["status"] == "OK" ? "#2E7D32" : "#C62828";
        $add .= "$name: <b style=\"color:$color;\">{$result['status']}</b>";
        if ($result["responseCode"]) {
            $add .= " ({$result["responseCode"]})";
        }
        if ($result["email"] || $result["status"] == "ERROR") {
            if ($result["responseBody"]) {
                $add .= "<pre>" . print_r($result["responseBody"], true) . "</pre>";
            }
            $add .= "<br/><br/>";
        }
    }
    $add && $body .= "<p>Результаты вебхуков:</p>$add";
    $body .= "</body></html>";

    try {
        $sent = mail($email, $subject, $body, implode("\r\n", [
            "MIME-Version: 1.0",
            "Content-type: text/html; charset=utf-8",
            "From: no-reply@$siteName",
        ]));
        if (!$sent) {
            throw new Exception("Email not accepted by MTA");
        }
    } catch (Exception $e) {
        error_log("Fail to send notification email: $e", 0);
        $ok = false;
    }
    return $ok;
}