<?php

// Редирект
if ($config["redirect"]["enabled"] && $page == "index") {
    $condition = $config["redirect"]["deviceType"] == "mobile"
        ? "max"
        : "min";
    echo preg_replace('/\s+/S', " ", "
        <script>
          if (window.matchMedia('only screen and ({$condition}-width: 600px)').matches) {
            window.location = '{$config["redirect"]["url"]}' + window.location.search;
          }
        </script>
    ");
}

// Google Tag Manager (noscript)
if (!empty($config["analytics"]["gtm"])) {
    foreach ($config["analytics"]["gtm"] as $id) {
        echo preg_replace('/\s+/S', " ", "
            <noscript>
              <iframe src='https://www.googletagmanager.com/ns.html?id={$id}' height='0' width='0' style='display:none;visibility:hidden'></iframe>
            </noscript>
        ");
    }
}

// Яндекс.Метрика
if (!empty($config["analytics"]["yandex"])) {
    foreach ($config["analytics"]["yandex"] as $id) {
        echo preg_replace('/\s+/S', " ", "
            <script type='text/javascript'>
              (function (d, w, c) {
                (w[c] = w[c] || []).push(function() {
                  try {
                    w.yaCounter{$id} = new Ya.Metrika2({
                      id:{$id},
                      clickmap:true,
                      trackLinks:true,
                      accurateTrackBounce:true,
                      webvisor:true
                    });
                  } catch(e) { }
                });
                var n = d.getElementsByTagName('script')[0],
                  s = d.createElement('script'),
                  f = function () { n.parentNode.insertBefore(s, n); };
                  s.type = 'text/javascript';
                  s.async = true;
                  s.src = 'https://mc.yandex.ru/metrika/tag.js';
                if (w.opera == '[object Opera]') {
                  d.addEventListener('DOMContentLoaded', f, false);
                } else { f(); }
              })(document, window, 'yandex_metrika_callbacks2');
            </script>
            <noscript><div><img src='https://mc.yandex.ru/watch/{$id}' style='position:absolute; left:-9999px;' alt=''/></div></noscript>
        ");
    }
}

// Mail.Ru
if (!empty($config["analytics"]["mail"])) {
    foreach ($config["analytics"]["mail"] as $id) {
        echo preg_replace('/\s+/S', " ", "
            <script type='text/javascript'>
              var _tmr = window._tmr || (window._tmr = []);
              _tmr.push({id: '{$id}', type: 'pageView', start: (new Date()).getTime()});
              (function (d, w, id) {
                if (d.getElementById(id)) return;
                var ts = d.createElement('script'); ts.type = 'text/javascript'; ts.async = true; ts.id = id;
                ts.src = (d.location.protocol == 'https:' ? 'https:' : 'http:') + '//top-fwz1.mail.ru/js/code.js';
                var f = function () {var s = d.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ts, s);};
                if (w.opera == '[object Opera]') { d.addEventListener('DOMContentLoaded', f, false); } else { f(); }
              })(document, window, 'topmailru-code');
            </script>
            <noscript><div><img src='//top-fwz1.mail.ru/counter?id={$id};js=na' style='border:0;position:absolute;left:-9999px;' alt=''/>
            </div></noscript>
        ");
    }
}

// Дополнительный код
if (!empty($config["code"])) {
    foreach ($config["code"] as $code) {
        if ($code["enabled"] && in_array($page, $code["pages"]) && $code["position"] == "body-start") {
            echo preg_replace('/\s+/S', " ", $code["code"]);
        }
    }
}