<?php

require_once realpath(dirname(__FILE__) . '/../../../') . '/inc/init.php';

if (!isset($_GET['id'])) {
    die('no id given');
}

$id = cleanID($_GET['id']);


if (auth_quickaclcheck($id) < AUTH_READ) {
    header('HTTP/1.1 403 Forbidden');
    die('access denied');
}

$path = preg_replace('/.txt$/i', '.xml', wikiFN($id));

if (!file_exists($path)) {
    header('HTTP/1.0 404 Not Found');
    die('dosn\'t exists');
}

header('Content-Type: text/xml');
echo file_get_contents($path);