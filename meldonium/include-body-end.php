<?php

$widgetsEnabled = false;
foreach ($config["widgets"] as $widget) {
    if ($widget["enabled"]) {
        $widgetsEnabled = true;
        break;
    }
}

if ($widgetsEnabled && $page == "index") {
    $json = json_encode($config["widgets"], JSON_UNESCAPED_UNICODE);
    $filepath = $page == 'index' ? "meldonium/assets/widgets.js" : "assets/widgets.js";
    $script = $filepath . "?" . filemtime($filepath);
    echo preg_replace('/\s+/S', " ", "
        <script>
          window.meldoniumWidgetsConfig = {$json};
        </script>
        <script defer src='{$script}'></script>
    ");
}

if ($page == "index") {
    echo preg_replace('/\s+/S', " ", "
        <script>
          (function(){
            for (var i = 0, forms = document.forms; i < forms.length; i++) {
              (function(form){
                form.addEventListener('submit', function() {
                  var tzoffset = (new Date()).getTimezoneOffset() * 60000;
                  var localISOTime = (new Date(Date.now() - tzoffset)).toISOString();
                  var splitted = localISOTime.split('T');
                  var localTime = splitted[0] + ' ' + splitted[1].substr(0, 8);
                  var fields = [
                    '<input type=\"hidden\" name=\"_time\" value=\"' + localTime + '\">',
                    '<input type=\"hidden\" name=\"_utm_medium\" value=\"{$_GET["utm_medium"]}\">',
                    '<input type=\"hidden\" name=\"_utm_source\" value=\"{$_GET["utm_source"]}\">',
                    '<input type=\"hidden\" name=\"_utm_campaign\" value=\"{$_GET["utm_campaign"]}\">',
                    '<input type=\"hidden\" name=\"_utm_term\" value=\"{$_GET["utm_term"]}\">',
                    '<input type=\"hidden\" name=\"_utm_content\" value=\"{$_GET["utm_content"]}\">',
                  ];
                  form.insertAdjacentHTML('beforeend', fields.join(''));
                  return true;
                })
              })(forms[i]);
            }
          })();
        </script>
    ");
}

if (!empty($config["code"])) {
    foreach ($config["code"] as $code) {
        if ($code["enabled"] && in_array($page, $code["pages"]) && $code["position"] == "body-end") {
            echo preg_replace('/\s+/S', " ", $code["code"]);
        }
    }
}