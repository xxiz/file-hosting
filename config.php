<?php

$config = [
    'max_upload_size' => 5000*1024*1024,
	'upload_path' => './',
	'shorten_url' => true,
	'base_url' => 'YOUR_BASE_URL',
    'token' => 'YOUR_TOKEN_HERE',
];

$config['allowed_exts'] = [
    
    // image formats
    'jpg' => 'image/jpeg',
    'png' => 'image/png',
    'gif' => 'image/gif',
    'webp' => 'image/webp',
    'heic' => 'image/heic',
    'heif' => 'image/heif',

    // document formats
    'txt' => 'text/plain',
    'pdf' => 'application/pdf',
    'doc' => 'application/msword',
    
    // archive formats
    'gz' => 'application/x-gzip',
    'tar' => 'application/x-tar',
    'tgz' => 'application/x-compressed',
    'zip' => 'application/zip',
    'rar' => 'application/rar',
    '7z' => 'application/x-7z-compressed',
    'cbz' => 'application/x-cbz',
    
    // audo formats
    'mp3' => 'audio/mpeg',
    'wav' => 'audio/wav',
    'ogg' => 'audio/ogg',
    'flac' => 'audio/flac',
    'm4a' => 'audio/m4a',

    // video formats
    'mp4' => 'video/mp4',
    'avi' => 'video/x-msvideo',
    'mkv' => 'video/x-matroska',
    'webm' => 'video/webm',
    'hevc' => 'video/mp4; codecs="dvhe',
    'mov' => 'video/quicktime',
];

if (isset($_GET['t']) && $_GET['t'] == $config['token']) {
    header('Content-Type: application/json');
    $t_config = $config;

    $t_config['allowed_exts'] = array_keys($t_config['allowed_exts']);

    print(json_encode($t_config));
}

?>