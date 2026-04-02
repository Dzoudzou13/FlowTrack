<?php

declare(strict_types=1);

// Nacita triedy z app/.
spl_autoload_register(static function (string $class): void {
    $prefix = 'App\\';

    if (! str_starts_with($class, $prefix)) {
        return;
    }

    $relativeClass = substr($class, strlen($prefix));
    $relativePath = str_replace('\\', DIRECTORY_SEPARATOR, $relativeClass) . '.php';
    $baseDirectory = __DIR__ . DIRECTORY_SEPARATOR;

    $file = $baseDirectory . lcfirst($relativePath);

    if (is_file($file)) {
        require_once $file;
    }
});

function base_path(string $path = ''): string
{
    $basePath = dirname(__DIR__);

    return $path === '' ? $basePath : $basePath . DIRECTORY_SEPARATOR . ltrim($path, DIRECTORY_SEPARATOR);
}

function template_path(string $path = ''): string
{
    return base_path('templates' . ($path !== '' ? DIRECTORY_SEPARATOR . $path : ''));
}

function config_path(string $path = ''): string
{
    return base_path('config' . ($path !== '' ? DIRECTORY_SEPARATOR . $path : ''));
}

function config(string $key, mixed $default = null): mixed
{
    static $configs = [];

    [$file, $item] = array_pad(explode('.', $key, 2), 2, null);

    if ($file === null) {
        return $default;
    }

    if (! array_key_exists($file, $configs)) {
        $configFile = config_path($file . '.php');
        $configs[$file] = is_file($configFile) ? require $configFile : [];
    }

    if ($item === null) {
        return $configs[$file] ?? $default;
    }

    return $configs[$file][$item] ?? $default;
}

function app_url(string $path = ''): string
{
    // Vrati URL aplikacie.
    $scriptName = $_SERVER['SCRIPT_NAME'] ?? '';
    $base = rtrim(str_replace('\\', '/', dirname($scriptName)), '/');
    $base = $base === '/' ? '' : $base;

    return $base . ($path === '' ? '' : '/' . ltrim($path, '/'));
}

function asset(string $path): string
{
    return app_url('assets/' . ltrim($path, '/'));
}

function redirect(string $path): never
{
    // Presmeruje na inu stranku.
    header('Location: ' . app_url($path));
    exit;
}
