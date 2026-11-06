<?php

/*
| Pintu masuk Laravel di Vercel.
| Vercel hanya mengizinkan menulis ke /tmp, jadi semua folder yang ditulis Laravel
| (cache, log, view, storage) dipindahkan ke sana sebelum aplikasi dijalankan.
*/

// Nilai bawaan untuk Vercel. Kalau sudah diisi di Environment Variables Vercel, yang itu yang dipakai.
$defaults = [
    'APP_STORAGE_PATH'   => '/tmp/storage',
    'APP_CONFIG_CACHE'   => '/tmp/config.php',
    'APP_EVENTS_CACHE'   => '/tmp/events.php',
    'APP_PACKAGES_CACHE' => '/tmp/packages.php',
    'APP_ROUTES_CACHE'   => '/tmp/routes.php',
    'APP_SERVICES_CACHE' => '/tmp/services.php',
    'VIEW_COMPILED_PATH' => '/tmp/storage/framework/views',
    'LOG_CHANNEL'        => 'stderr',
    'CACHE_DRIVER'       => 'array',
    'SESSION_DRIVER'     => 'array',
    'FORCE_HTTPS'        => 'true',
];

foreach ($defaults as $key => $value) {
    if (getenv($key) === false) {
        putenv("$key=$value");
        $_ENV[$key] = $value;
        $_SERVER[$key] = $value;
    }
}

// Buat folder yang dibutuhkan Laravel di /tmp
foreach (['app/public', 'framework/cache/data', 'framework/sessions', 'framework/views', 'logs'] as $dir) {
    if (!is_dir("/tmp/storage/$dir")) {
        mkdir("/tmp/storage/$dir", 0777, true);
    }
}

// Entry file ada di /api/index.php, padahal route Laravel sendiri sudah berawalan /api.
// Supaya Laravel tidak mengira "/api" adalah bagian dari nama skrip, skripnya dianggap /index.php.
$_SERVER['SCRIPT_NAME'] = '/index.php';
$_SERVER['PHP_SELF'] = '/index.php';
$_SERVER['SCRIPT_FILENAME'] = realpath(__DIR__ . '/../public/index.php');

require __DIR__ . '/../public/index.php';
