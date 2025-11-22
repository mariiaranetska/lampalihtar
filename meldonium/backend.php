<?php ignore_user_abort(true); error_reporting(0);

include 'config.php';
$config = getConfig();

if (hash("sha256", urldecode($_SERVER["HTTP_AUTH"])) != $config["password"]) {
    header("HTTP/1.1 401 Unauthorized");
    exit(0);
}

if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    header('Content-type: application/json');
    $config["password"] = "";
    echo json_encode($config, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    return;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (!empty($_FILES["file"])) {
        $uploadDir = 'uploads';
        is_dir($uploadDir) || mkdir($uploadDir);
        move_uploaded_file($_FILES["file"]["tmp_name"], $uploadDir . "/" . $_FILES["file"]["name"]);
    } else {
        try {
            setConfig(json_decode(file_get_contents('php://input'), true));
        } catch (Exception $e) {
            header('HTTP/1.1 500 Internal Server Error');
        }
    }
    return;
}