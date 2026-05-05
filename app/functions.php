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
    $base = app_base_url();

    return $base . ($path === '' ? '' : '/' . ltrim($path, '/'));
}

function public_url(string $path = ''): string
{
    // Vrati URL k public suborom.
    $base = app_base_url();
    $scriptFile = realpath($_SERVER['SCRIPT_FILENAME'] ?? '') ?: '';
    $publicIndex = realpath(base_path('public/index.php')) ?: '';
    $usesPublicIndex = $scriptFile === $publicIndex;
    $publicBase = $usesPublicIndex ? $base : $base . '/public';

    return rtrim($publicBase, '/') . ($path === '' ? '' : '/' . ltrim($path, '/'));
}

function current_route_path(): string
{
    // Vrati aktualnu routu.
    $path = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?? '/';
    $base = app_base_url();

    if ($base !== '' && str_starts_with($path, $base)) {
        $path = substr($path, strlen($base));
    }

    $path = '/' . trim($path, '/');

    return $path === '//' ? '/' : $path;
}

function app_base_url(): string
{
    // Vrati zaklad URL aplikacie.
    $scriptName = $_SERVER['SCRIPT_NAME'] ?? '';
    $base = rtrim(str_replace('\\', '/', dirname($scriptName)), '/');

    return $base === '/' ? '' : $base;
}

function asset(string $path): string
{
    return public_url('assets/' . ltrim($path, '/'));
}

function redirect(string $path): never
{
    // Presmeruje na inu stranku.
    header('Location: ' . app_url($path));
    exit;
}

function auth_user(): ?array
{
    return $_SESSION['user'] ?? null;
}

function auth_guard(): void
{
    if (auth_user() !== null) {
        return;
    }

    // Skus obnovit session z remember-me cookie.
    $token = $_COOKIE['remember_me'] ?? '';
    if ($token !== '') {
        $hash = hash('sha256', $token);
        $pdo  = \App\Core\Database::connection();
        $stmt = $pdo->prepare(
            'SELECT rt.user_id, rt.expires_at, u.workspace_id, u.name, u.email, u.role
             FROM remember_tokens rt
             JOIN users u ON u.id = rt.user_id
             WHERE rt.token_hash = :hash
             LIMIT 1'
        );
        $stmt->execute(['hash' => $hash]);
        $row = $stmt->fetch();

        if ($row && new \DateTime() < new \DateTime($row['expires_at'])) {
            $_SESSION['user'] = [
                'id'           => $row['user_id'],
                'workspace_id' => $row['workspace_id'],
                'name'         => $row['name'],
                'email'        => $row['email'],
                'role'         => $row['role'],
            ];
            return;
        }

        // Token expiroval alebo neexistuje — vymaž cookie.
        setcookie('remember_me', '', time() - 3600, '/', '', false, true);
    }

    redirect('/login');
}

function guest_guard(): void
{
    if (auth_user() === null) {
        return;
    }

    redirect('/dashboard');
}

function log_activity(string $entityType, ?int $entityId, string $action, array $meta = []): void
{
    $user = auth_user();
    if ($user === null) {
        return;
    }

    $pdo  = \App\Core\Database::connection();
    $stmt = $pdo->prepare(
        'INSERT INTO activity_log (workspace_id, user_id, entity_type, entity_id, action, meta)
         VALUES (:workspace_id, :user_id, :entity_type, :entity_id, :action, :meta)'
    );
    $stmt->execute([
        'workspace_id' => $user['workspace_id'],
        'user_id'      => $user['id'],
        'entity_type'  => $entityType,
        'entity_id'    => $entityId,
        'action'       => $action,
        'meta'         => $meta !== [] ? json_encode($meta, JSON_UNESCAPED_UNICODE) : null,
    ]);
}
