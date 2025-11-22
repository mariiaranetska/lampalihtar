<?php

// Google Tag Manager
if (!empty($config["analytics"]["gtm"])) {
    foreach ($config["analytics"]["gtm"] as $id) {
        echo preg_replace('/\s+/S', " ", "
            <script>
              (function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
                new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
                j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
                'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
              })(window,document,'script','dataLayer','{$id}');
            </script>
        ");
    }
}

// Google Analytics
if (!empty($config["analytics"]["google"])) {
    foreach ($config["analytics"]["google"] as $index => $id) {
        $gtagConfig = "gtag('config', '{$id}'";
        if ($index == 0 && !empty($config["analytics"]["optimize"])) {
            $gtagConfig .= ", {'optimize_id': '{$config["analytics"]["optimize"]}'}";
        }
        $gtagConfig .= ");";
        echo preg_replace('/\s+/S', " ", "
            <script async src='https://www.googletagmanager.com/gtag/js?id={$id}'></script>
            <script>
              window.dataLayer = window.dataLayer || [];
              function gtag(){dataLayer.push(arguments);}
              gtag('js', new Date());
              $gtagConfig
            </script>
        ");
    }
}

// Facebook
if (!empty($config["analytics"]["fb"])) {
    foreach ($config["analytics"]["fb"] as $id) {
        echo preg_replace('/\s+/S', " ", "
            <script>
              !function(f,b,e,v,n,t,s)
              {if(f.fbq)return;n=f.fbq=function(){n.callMethod?
              n.callMethod.apply(n,arguments):n.queue.push(arguments)};
              if(!f._fbq)f._fbq=n;n.push=n;n.loaded=!0;n.version='2.0';
              n.queue=[];t=b.createElement(e);t.async=!0;
              t.src=v;s=b.getElementsByTagName(e)[0];
              s.parentNode.insertBefore(t,s)}(window, document,'script',
              'https://connect.facebook.net/en_US/fbevents.js');
              fbq('init', '{$id}');
              fbq('track', 'PageView');
            </script>
            <noscript><img height='1' width='1' style='display:none'
              src='https://www.facebook.com/tr?id={$id}&ev=PageView&noscript=1'/></noscript>
        ");
    }
}

// ВКонтакте
if (!empty($config["analytics"]["vk"])) {
    foreach ($config["analytics"]["vk"] as $id) {
        echo preg_replace('/\s+/S', " ", "
            <script type='text/javascript'>
              (window.Image ? (new Image()) : document.createElement('img')).src = 'https://vk.com/rtrg?p={$id}';
            </script>
        ");
    }
}

// Мета теги
if (!empty($config["meta"]) && $page == "index") {

    $path = $_SERVER["REQUEST_URI"] . "/";
    if (substr($_SERVER["REQUEST_URI"], -3) == "php") {
        $path = dirname($_SERVER["REQUEST_URI"]);
    }
    $base = "//" . $_SERVER["HTTP_HOST"] . $path;

    // OpenGraph
    echo preg_replace('/\s+/S', " ", "
        <meta property='og:type' content='website'>
        <meta property='og:site_name' content='{$_SERVER['HTTP_HOST']}'>
        <meta property='og:image:type' content='image/png'>
        <meta property='og:image:width' content='968'>
        <meta property='og:image:height' content='504'>
        <meta property='og:image' content='$base/og.png'>
        <meta property='og:locale' content='ru_RU'>
        <meta property='og:url' content='//{$_SERVER['HTTP_HOST']}{$_SERVER['REQUEST_URI']}'>
    ");

    // Title (+OpenGraph Title)
    if (!empty($config["meta"]["title"])) {
        echo preg_replace('/\s+/S', " ", "
            <title>{$config["meta"]["title"]}</title>
            <meta property='og:title' content='{$config["meta"]["title"]}'>
        ");
    }

    // Description (+OpenGraph Description)
    if (!empty($config["meta"]["description"])) {
        echo preg_replace('/\s+/S', " ", "
            <meta name='description' content='{$config["meta"]["description"]}'>
            <meta property='og:description' content='{$config["meta"]["description"]}'>
        ");
    }

    // Keywords
    if (!empty($config["meta"]["keywords"])) {
        echo preg_replace('/\s+/S', " ", "
            <meta name='keywords' content='{$config["meta"]["keywords"]}'>
        ");
    }
}

// Дополнительный код
if (!empty($config["code"])) {
    foreach ($config["code"] as $code) {
        if ($code["enabled"] && in_array($page, $code["pages"]) && $code["position"] == "head") {
            echo preg_replace('/\s+/S', " ", $code["code"]);
        }
    }
}