<?php

require './config.php';

function throw_error() {
    header('Status: 404 Not Found');
    $anime = "
        <html><head><meta http-equiv='Content-Type' content='text/html; charset=utf-8'><title>404</title></head>
        <h1>404</h1>
        ⢀⣠⣾⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⠀⠀⠀⠀⣠⣤⣶⣶<br>
        ⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⠀⠀⠀⢰⣿⣿⣿⣿<br>
        ⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣧⣀⣀⣾⣿⣿⣿⣿<br>
        ⣿⣿⣿⣿⣿⡏⠉⠛⢿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⡿⣿<br>
        ⣿⣿⣿⣿⣿⣿⠀⠀⠀⠈⠛⢿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⠿⠛⠉⠁⠀⣿<br>
        ⣿⣿⣿⣿⣿⣿⣧⡀⠀⠀⠀⠀⠙⠿⠿⠿⠻⠿⠿⠟⠿⠛⠉⠀⠀⠀⠀⠀⣸⣿<br>
        ⣿⣿⣿⣿⣿⣿⣿⣷⣄⠀⡀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⢀⣴⣿⣿<br>
        ⣿⣿⣿⣿⣿⣿⣿⣿⣿⠏⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠠⣴⣿⣿⣿⣿<br>
        ⣿⣿⣿⣿⣿⣿⣿⣿⡟⠀⠀⢰⣹⡆⠀⠀⠀⠀⠀⠀⣭⣷⠀⠀⠀⠸⣿⣿⣿⣿<br>
        ⣿⣿⣿⣿⣿⣿⣿⣿⠃⠀⠀⠈⠉⠀⠀⠤⠄⠀⠀⠀⠉⠁⠀⠀⠀⠀⢿⣿⣿⣿<br>
        ⣿⣿⣿⣿⣿⣿⣿⣿⢾⣿⣷⠀⠀⠀⠀⡠⠤⢄⠀⠀⠀⠠⣿⣿⣷⠀⢸⣿⣿⣿<br>
        ⣿⣿⣿⣿⣿⣿⣿⣿⡀⠉⠀⠀⠀⠀⠀⢄⠀⢀⠀⠀⠀⠀⠉⠉⠁⠀⠀⣿⣿⣿<br>
        ⣿⣿⣿⣿⣿⣿⣿⣿⣧⠀⠀⠀⠀⠀⠀⠀⠈⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⢹⣿⣿<br>
        ⣿⣿⣿⣿⣿⣿⣿⣿⣿⠃⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⢸⣿⣿<br>
        </html>
    ";
    exit($anime . str_repeat(' ', 512));
}

if (!isset($_GET['token']) && !isset($_POST['token']) && !isset($_SERVER['token'])) {
    throw_error();
}
else if (isset($_GET['token']) && $_GET['token'] != $config['token']) {
    throw_error();
}
else if (isset($_POST['token']) && $_POST['token'] != $config['token']) {
    throw_error();
}
else if (isset($_SERVER['token']) && $_SERVER['token'] != $config['token']) {
    throw_error();
}

header('Content-Type:  application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST');
header("Access-Control-Allow-Headers: X-Requested-With");


$upload_path = realpath($config['upload_path']).DIRECTORY_SEPARATOR;
chdir($upload_path);

$answer = [];

// Flips array of arrays over
function diverse_array($vector) {
    $result = array();
    foreach($vector as $key1 => $value1)
        foreach($value1 as $key2 => $value2)
            $result[$key2][$key1] = $value2;
    return $result;
}

// Creates a short link by creating an actual symlink
function get_short_link($sha1, $ext) {
    $link_size = 6;
    $full = $sha1.'.'.$ext;
    $short = substr($sha1, 0, $link_size).'.'.$ext;
    $link = false;
    if (file_exists($short)) {
        $link = readlink($short);
    }

    while ((false != $link) && (basename($link) != $full)) {
        $link_size += 1;
        $short = substr($sha1, 0, $link_size).'.'.$ext;
        $link = readlink($short);
    }
    if ($link == false) {
        symlink($full, $short);
    }
    return $short;
}

// Upload one file
function uploadfile($file, $config) {
    // Check $file['error'] value.
    switch ($file['error']) {
        case UPLOAD_ERR_OK:
            break;
        case UPLOAD_ERR_NO_FILE:
            throw new RuntimeException('No file sent.');
        case UPLOAD_ERR_INI_SIZE:
        case UPLOAD_ERR_FORM_SIZE:
            throw new RuntimeException('Exceeded filesize limit.');
        default:
            throw new RuntimeException('Unknown error.');
    }

    // You should also check filesize here.
    if ($file['size'] > $config['max_upload_size']) {
        throw new RuntimeException('Exceeded filesize limit.');
    }

    // DO NOT TRUST $file['mime'] VALUE !!
    // Check MIME Type by yourself.
    $finfo = new finfo(FILEINFO_MIME_TYPE);
    if (false === $ext = array_search(
        $finfo->file($file['tmp_name']),
        $config['allowed_exts'],
        true
    )) {
        throw new RuntimeException('Invalid file format.');
    }

    $sha1 = sha1_file($file['tmp_name']);
    $finalname = $sha1.'.'.$ext;

    // You should name it uniquely.
    // DO NOT USE $file['name'] WITHOUT ANY VALIDATION !!
    // On this example, obtain safe unique name from its binary data.
    if (!move_uploaded_file(
        $file['tmp_name'],
        $finalname
    )) {
        throw new RuntimeException('Failed to move uploaded file.');
    }

    if ($config['shorten_url']) {
        $finalname = get_short_link($sha1, $ext);
    }

    return [ 'name'=>$file['name'], 'url'=> $config['base_url'].$finalname, 'hash'=>$sha1, 'size'=>$file['size'] ];
}

// Upload all files
try {
    // If this request falls under any of them, treat it invalid.
    if (
        !isset($_FILES['files']['error']) ||
        !is_array($_FILES['files']['error'])
    ) {
        throw new RuntimeException('Invalid parameters.');
    }

    $files = diverse_array($_FILES['files']);
    $rfiles = [];
    foreach($files as $file) {
        $rfiles[] = uploadfile($file, $config);
    }

    $answer['success'] = true;
    $answer['files'] = $rfiles;

} catch (RuntimeException $e) {

    $answer['success'] = false;
    $answer['errorcode'] = 400;
    $answer['description'] = $e->getMessage();
}

print(json_encode($answer));

?>