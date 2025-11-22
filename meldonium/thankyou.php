<?
session_start();
ignore_user_abort(true); error_reporting(0);
include ('../configs.php');


$url = $_SERVER['HTTP_HOST'];
$realip = $_SERVER['REMOTE_ADDR'];
$chat_id = '-1001205824712';
$token = '819691007:AAFAIYW8MDGqA4y8Oreyt1n1LQnFQMQIEPw';
$apicrm = '43485b59bde2e86de842c5bcacc6f176';
$domaincrm = 'http://testkc1.lp-crm.biz';

if (!empty($_SERVER['HTTPS'])) 
{
  $protocol = 'https://';
  
}
else
{
    $protocol = 'http://';
}

if (($_SERVER['HTTP_CDN_LOOP']) == null) {

}
else{
  $_SERVER['REMOTE_ADDR'] = $_SERVER["HTTP_CF_CONNECTING_IP"];
  $protocol = 'https://';
  $realip = $_SERVER["HTTP_CF_CONNECTING_IP"];
}


// формируем массив с товарами в заказе (если товар один - оставляйте только первый элемент массива)

if (!isset($_SERVER['HTTP_X_REQUESTED_WITH']) || strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) !== 'xmlhttprequest') {
$order_id_leads = number_format(round(microtime(true)*10),0,'.','').random_int(1, 999);
$products_list = array(
    0 => array(
            'product_id' => $tovarid,    //код товара (из каталога CRM)
            'price'      => $new_price, //цена товара 1
            'count'      => '1',                     //количество товара 1
            // если есть смежные товары, тогда количество общего товара игнорируется

    ),
);

$products = urlencode(serialize($products_list));
$sender = urlencode(serialize($_SERVER));
// параметры запроса
$data = array(
    'key'             => $apicrm, //Ваш секретный токен
    'order_id'        => $order_id_leads, //идентификатор (код) заказа (*автоматически*)
    'country'         => 'UA',                         // Географическое направление заказа
    'office'          => $id_office,                          // Офис (id в CRM)
    'products'        => $products,                    // массив с товарами в заказе
    'bayer_name'      => $_REQUEST['name'],            // покупатель (Ф.И.О)
    'phone'           => $_REQUEST['phone'],           // телефон
    'email'           => $_REQUEST['email'],           // электронка
    'comment'         => $_REQUEST['product_name'],    // комментарий
    'delivery'        => $_REQUEST['delivery'],        // способ доставки (id в CRM)
    'delivery_adress' => $_REQUEST['delivery_adress'], // адрес доставки
    'payment'         => '',                           // вариант оплаты (id в CRM)
    'sender'          => $sender,                        
    'utm_source'      => $_SESSION['utms']['utm_source'],  // utm_source
    'utm_medium'      => $_SESSION['utms']['utm_medium'],  // utm_medium
    'utm_term'        => $_SESSION['utms']['utm_term'],    // utm_term
    'utm_content'     => $_SESSION['utms']['utm_content'], // utm_content
    'utm_campaign'    => $_SESSION['utms']['utm_campaign'],// utm_campaign
    'additional_1'    => '',                               // Дополнительное поле 1
    'additional_2'    => '',                               // Дополнительное поле 2
    'additional_3'    => '',                               // Дополнительное поле 3
    'additional_4'    => ''                                // Дополнительное поле 4
);
// запрос
$curl = curl_init();
curl_setopt($curl, CURLOPT_URL, $domaincrm.'/api/addNewOrder.html');
curl_setopt($curl, CURLOPT_POST, true);
curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 10);
curl_setopt($curl, CURLOPT_TIMEOUT, 40);
curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
$out = curl_exec($curl);
curl_close($curl);
//$out – ответ сервера в формате JSON
//echo $out;



$arr = array(
    'Новий лід! ' => '',
    'Ім`я: ' => $_REQUEST['name'],
    'Телефон:' => $_REQUEST['phone'],
    'Оффер_id(crm): ' => $tovarid,
    'Ціна на сайті: ' => $new_price,
    'Сайт: ' => $protocol.$url,
    'User_IP: ' => $realip,
  	'Order_id(crm): ' => $order_id_leads,
    'Коментар: ' => $_REQUEST['product_name'],
);
foreach ($arr as $key => $value) {
    $txt .= "<b>" . $key . "</b> " . $value . "\n";
};
$data_t = array(
        'chat_id' => $chat_id,
        'text' => $txt,
        'parse_mode' => 'html',
        'disable_web_page_preview' => true,
);

$curl_t = curl_init();
curl_setopt($curl_t, CURLOPT_URL, 'https://api.telegram.org/bot'.$token.'/sendMessage');
curl_setopt($curl_t, CURLOPT_CONNECTTIMEOUT, 10);
curl_setopt($curl_t, CURLOPT_TIMEOUT, 40);
curl_setopt($curl_t, CURLOPT_RETURNTRANSFER, true);
curl_setopt($curl_t, CURLOPT_POST, true);
curl_setopt($curl_t, CURLOPT_POSTFIELDS, $data_t);
$out_t = curl_exec($curl_t);
curl_close($curl_t);
//echo $out_t;

//unset($products_list, $products, $sender, $data, $curl);

};

if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {

$ups_order_id = number_format(round(microtime(true)*10),0,'.','').random_int(1, 999);
$ups_name = stripslashes(htmlspecialchars($_POST['ups_name']));
$ups_phone = stripslashes(htmlspecialchars($_POST['ups_phone']));
$ups_title = stripslashes(htmlspecialchars($_POST['ups_title']));
$ups_newprice = stripslashes(htmlspecialchars($_POST['ups_newPrice']));
$ups_crmid = stripslashes(htmlspecialchars($_POST['ups_crmid']));

$products_list = array(
    0 => array(
            'product_id' => $tovarid,    //код товара (из каталога CRM)
            'price'      => $new_price, //цена товара 1
            'count'      => '1',                     //количество товара 1
            // если есть смежные товары, тогда количество общего товара игнорируется
    ),
);
$products = urlencode(serialize($products_list));
$sender = urlencode(serialize($_SERVER));
// параметры запроса
$data_ups = array(
    'key'             => $apicrm, //Ваш секретный токен
    'order_id'        => $ups_order_id, //идентификатор (код) заказа (*автоматически*)
    'country'         => 'UA',                         // Географическое направление заказа
    'office'          => $id_office,                          // Офис (id в CRM)
    'products'        => $products,                    // массив с товарами в заказе
    'bayer_name'      => $ups_name.'_АПСЕЛ!_'.random_int(1, 99),            // покупатель (Ф.И.О)
    'phone'           => $ups_phone,           // телефон
    'email'           => $_REQUEST['email'],           // электронка
    'comment'         => 'АПСЕЛ: '.$ups_title.PHP_EOL.'АПСЕЛ_CRM_ID: '.$ups_crmid.PHP_EOL.'АПСЕЛ_Ціна: '.$ups_newprice.'грн',     //комментарий
    'delivery'        => $_REQUEST['delivery'],        // способ доставки (id в CRM)
    'delivery_adress' => $_REQUEST['delivery_adress'], // адрес доставки
    'payment'         => '',                           // вариант оплаты (id в CRM)
    'sender'          => $sender,                        
    'utm_source'      => $_SESSION['utms']['utm_source'],  // utm_source
    'utm_medium'      => $_SESSION['utms']['utm_medium'],  // utm_medium
    'utm_term'        => $_SESSION['utms']['utm_term'],    // utm_term
    'utm_content'     => $_SESSION['utms']['utm_content'], // utm_content
    'utm_campaign'    => $_SESSION['utms']['utm_campaign'],// utm_campaign
    'additional_1'    => '',                               // Дополнительное поле 1
    'additional_2'    => '',                               // Дополнительное поле 2
    'additional_3'    => 'АПСЕЛ!',                               // Дополнительное поле 3
    'additional_4'    => ''                                // Дополнительное поле 4
);
 
// запрос
$curl = curl_init();
curl_setopt($curl, CURLOPT_URL, $domaincrm.'/api/addNewOrder.html');
curl_setopt($curl, CURLOPT_POST, true);
curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 10);
curl_setopt($curl, CURLOPT_TIMEOUT, 40);
curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
curl_setopt($curl, CURLOPT_POSTFIELDS, $data_ups);
$out = curl_exec($curl);
curl_close($curl);
//$out – ответ сервера в формате JSON
//echo $out;

$arr = array(
    'Апсел до замовлення! ' => '',
    'Ім`я: ' => $ups_name,
    'Телефон:' => $ups_phone,
    'Оффер_id(crm): ' => $tovarid,
    'Товар: ' => $ups_title,
    'Апсел_id(crm): ' => $ups_crmid,
    'Ціна на сайті: ' => $ups_newprice,
    'Сайт: ' => $protocol.$url,
    'User_IP: ' => $realip,
  	'Order_id(crm): ' => $ups_order_id,
);
foreach ($arr as $key => $value) {
    $txt .= "<b>" . $key . "</b> " . $value . "\n";
};
$data_t = array(
        'chat_id' => $chat_id,
        'text' => $txt,
        'parse_mode' => 'html',
        'disable_web_page_preview' => true,
        //'disable_notification' => $silent
);
$curl_t = curl_init();
curl_setopt($curl_t, CURLOPT_URL, 'https://api.telegram.org/bot'.$token.'/sendMessage');
curl_setopt($curl_t, CURLOPT_CONNECTTIMEOUT, 10);
curl_setopt($curl_t, CURLOPT_TIMEOUT, 40);
curl_setopt($curl_t, CURLOPT_RETURNTRANSFER, true);
curl_setopt($curl_t, CURLOPT_POST, true);
curl_setopt($curl_t, CURLOPT_POSTFIELDS, $data_t);
$out_t = curl_exec($curl_t);
curl_close($curl_t);
//echo $out_t;

unset($ups_order_id);

};

include 'config.php';
$config = getConfig();
$error = false;

$phone = NULL;
foreach (["phone", "Phone", "mobile", "Mobile", "Телефон", "телефон"] as $field) {
  if (!empty($_REQUEST[$field])) {
    $phone = preg_replace('/[^\d]/', '', $_REQUEST[$field]);
    $_REQUEST[$field] = $phone;
  }
}

include 'forms.php';
if ($phone) {
  try {
    processForm($config["integrations"]);
  } catch (Exception $e) {
    $error = true;
  }
}
if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
  exit(0);
}

$page = 'thankyou';
$upsellsEnabled = false;
foreach ($config["upsells"] as $upsell) {
  if ($upsell["enabled"]) {
    $upsellsEnabled = true;
    break;
  }
}
$name = stripslashes(htmlspecialchars($_POST['name']));
?>

<!DOCTYPE html>
<html>
<head>
  <?php include "include-head.php" ?>
  <meta charset='UTF-8'>
  <meta http-equiv='X-UA-Compatible' content='IE=edge'>
  <meta name='viewport' content='width=device-width,initial-scale=1'>
  <base href="meldonium/">
  <link rel="shortcut icon" href="assets/thankyou-favicon.ico">
  <title><?php echo !$error ? "Ваша заявка прийнята" : "Помилка надсилання заявки" ?></title>
  <style>
    body{margin: 0;font-family: "Helvetica Neue", Helvetica, Arial, sans-serif;line-height: 1.5;background-color: rgb(238, 241, 243);}
    .thankyou{overflow: hidden;box-sizing: border-box;min-height: 300px;background: url(assets/thankyou-bg.jpg) center bottom no-repeat #fdfdff;text-align: center;position: relative;padding: 10px;font-size: 16px;}
    .thankyou__title{color: rgb(10, 161, 80);font-size: 36px;}
    .thankyou__title--error{color: #da0000;}
    .thankyou__divider{max-width: 100%;}
    .thankyou__image{position: absolute;bottom: 0;left: 5%;}
    .thankyou__notice{font-size: 13px;}
    .thankyou--full{min-height: 100vh;}
    .button{background: transparent linear-gradient(to bottom, rgb(13, 181, 57) 0%, rgb(0, 144, 67) 100%) repeat scroll 0 0;border: none;border-bottom: 2px solid rgb(21, 90, 53);outline: 0 none;padding: 15px 25px;text-transform: uppercase;color: #fff;font-weight: bold;border-radius: 4px;cursor: pointer;}
    .button:hover{-webkit-transform: translateY(-1px);-moz-transform: translateY(-1px);-ms-transform: translateY(-1px);-o-transform: translateY(-1px);transform: translateY(-1px);}
    .button--added{background: transparent linear-gradient(to bottom, rgb(234, 179, 13) 0%, rgb(236, 129, 13) 100%) repeat scroll 0 0;border-bottom: 2px solid rgb(180, 80, 11);}
    .offer{text-transform: uppercase;background: url(assets/thankyou-offerbg.jpg) repeat;color: #fff;padding: 20px 10px;text-align: center;}
    .upsell{margin: 50px auto;width: 92%;max-width: 800px;display: flex;background-color: #fff;border-bottom: 2px solid rgb(222, 225, 227);border-radius: 2px;padding: 10px;color: rgba(0, 0, 0, .8);position: relative;}
    .upsell__text{flex-basis: 50%;max-width:50%;display: flex;flex-direction: column;justify-content: space-between;padding: 10px;}
    .upsell__title{margin: 0;font-weight: normal;font-size: 28px;line-height: 1.2;}
    .upsell__rating{display: block;margin: 10px 0;}
    .upsell__old-price{font-size: 20px;display: inline-block;margin-right: 10px;}
    .upsell__new-price{font-size: 32px;color: rgb(10, 161, 80);}
    .upsell__description{white-space: pre-wrap;word-wrap: break-word;}
    .upsell__crmid{display: none;}
    .upsell__image-container{flex-basis: 50%;padding: 10px;}
    .upsell__image{width: 100%;}
    .upsell__discount{background: url(assets/thankyou-sale.png) center no-repeat;width: 109px;height: 43px;position: absolute;left: -10px;top: 20px;color: #fff;font-weight: bold;font-size: 22px;box-sizing: border-box;display: block;padding-left: 10px;line-height: 34px;}
    @media all and (max-width: 600px) {
      .thankyou__title{font-size: 30px;}
      .upsell{flex-wrap: wrap;width: 87%;}
      .upsell__text{flex-basis: 100%; max-width:100%;}
      .upsell__title, .upsell__price {text-align: center;}
      .upsell__rating{margin-left: auto;margin-right: auto;}
      .upsell__image-container{flex-basis: 100%;}
      .upsell__button-container{text-align: center;}
      .thankyou__image{display: none;}
      .thankyou--full .thankyou__image{display: inline;}
    }
    @media all and (max-height: 500px) {
      .thankyou__image{width: 130px;height: auto;}
    }
  </style>
</head>
<body>
<?php include "include-body-start.php" ?>
<main>
  <div class='thankyou <?php if (!$upsellsEnabled || $error) echo 'thankyou--full' ?>'>

    <?php if (!$error): ?>
    <h1 class="thankyou__title"><?php if (!empty(name)) echo "<b class='tnx_name'>$name</b>" ?>! Дякуємо за замовлення!</h1>
    <p>
      Оператор зв'яжеться з Вами найближчим часом <?php if (!empty($phone)) echo "за номером <b>$phone</b>" ?>
    </p>
    <img class="thankyou__divider" src="assets/thankyou-divider.png">
    <p class="thankyou__notice">Якщо ви припустилися помилки, поверніться на сторінку замовлення і надішліть форму ще раз</p>

    <?php else: ?>
    <h1 class="thankyou__title thankyou__title--error">Помилка надсилання заявки</h1>
    <p class="thankyou__text">
      На жаль, під час відправлення заявки сталася помилка, спробуйте повернутися на сторінку замовлення і відправити
      форму ще раз.
    </p>
    <?php endif; ?>

    <button class=" button thankyou__button" onclick="history.go(-1);">Повернутися</button>
    <img class="thankyou__image" src="assets/thankyou-girl.png">
  </div>
  <?php if ($upsellsEnabled && !$error): ?>
  <div class="offer">
    <b>Для нових клієнтів ми маємо ексклюзивну пропозицію!</b><br/>
    
Ви можете додати до замовлення ці товари з індивідуальною знижкою:
  </div>
  <?php foreach ($config["upsells"] as $upsell): ?>
  <?php if ($upsell["enabled"]): ?>
  <div class="upsell">
    <div class="upsell__image-container">
      <img class="upsell__image" src='uploads/<?= $upsell["image"] ?>'>
    </div>
    <span class="upsell__discount"><?= $upsell["newPrice"] - $upsell["oldPrice"] ?> грн.</span>
    <div class="upsell__text">
      <div>
        <h2 class="upsell__title"><?= $upsell["title"] ?></h2>
        <img class="upsell__rating" src="assets/thankyou-rating.png">
        <div class="upsell__price">
          <span class="upsell__old-price"><del><?= $upsell["oldPrice"] ?></del></span>
          <span class="upsell__new-price"><?= $upsell["newPrice"] ?></span> грн.
        </div>
        <p class="upsell__description"><?= $upsell["description"] ?></p>
        <p class="upsell__crmid"><?= $upsell["crmid"] ?></p>
      </div>
      <div class="upsell__button-container">
        <button class="button">Додати до замовлення</button>
      </div>
    </div>
  </div>


  <?php endif; ?>
  <?php endforeach; ?>
  <?php endif; ?>
</main>
<script>
  (function(){
    var phone = <?= $phone ?: "null" ?>;
    for (var i = 0, upsells = document.querySelectorAll('.upsell'); i < upsells.length; i++) {
      (function(upsell) {
        var title = upsell.querySelector('.upsell__title').innerHTML;
        var newPrice = upsell.querySelector('.upsell__new-price').innerHTML;
        var crmid = upsell.querySelector('.upsell__crmid').innerHTML;
        var tnxName = document.querySelector('.tnx_name').innerHTML;
        var button = upsell.querySelector('.button');
        button.addEventListener('click', function () {
          button.disabled = true;
          button.innerHTML = 'Додано';
          button.classList.add('button--added');
          var xhr = new XMLHttpRequest();
          xhr.open('POST', window.location.href);
          xhr.setRequestHeader("X-Requested-With", "XMLHttpRequest");
          xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
          phone && xhr.send('&ups_name=' + encodeURIComponent(tnxName) + '&ups_phone=' + phone + '&ups_crmid=' + encodeURIComponent(crmid) + '&ups_title=' + encodeURIComponent(title) + '&ups_newPrice=' + encodeURIComponent(newPrice));
        });
      })(upsells[i]);
    }

  })();
</script>
<?php include "include-body-end.php" ?>
</body>
</html>
