<?php
declare(strict_types=1);

function h(?string $s): string
{
    return htmlspecialchars((string) $s, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

function csrf_token(): string
{
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function csrf_field(): string
{
    return '<input type="hidden" name="csrf" value="' . h(csrf_token()) . '">';
}

function csrf_verify(): bool
{
    $t = $_POST['csrf'] ?? '';
    return is_string($t) && isset($_SESSION['csrf_token'])
        && hash_equals($_SESSION['csrf_token'], $t);
}

function base_path(): string
{
    $script = $_SERVER['SCRIPT_NAME'] ?? '/index.php';
    $dir = dirname($script);
    if ($dir === '\\' || $dir === '.') {
        return '';
    }
    return rtrim(str_replace('\\', '/', $dir), '/');
}

function redirect(string $url): void
{
    header('Location: ' . $url);
    exit;
}

function flash_set(string $key, string $message): void
{
    $_SESSION['flash'][$key] = $message;
}

function flash_get(string $key): ?string
{
    if (!isset($_SESSION['flash'][$key])) {
        return null;
    }
    $m = $_SESSION['flash'][$key];
    unset($_SESSION['flash'][$key]);
    return $m;
}

/** Site kökü (admin alt klasöründen çağrıldığında bir üst dizin). */
function web_root(): string
{
    $script = str_replace('\\', '/', $_SERVER['SCRIPT_NAME'] ?? '/index.php');
    $dir = dirname($script);
    if (str_ends_with($dir, '/admin')) {
        $dir = dirname($dir);
    }
    $base = rtrim($dir, '/');
    return ($base === '' || $base === '/' || $base === '.') ? '' : $base;
}

/** Alt klasörde kurulumda görseller ve linkler için kök yol. */
function web_url(string $rel): string
{
    $rel = ltrim(str_replace('\\', '/', $rel), '/');
    $root = web_root();
    if ($root === '') {
        return '/' . $rel;
    }
    return $root . '/' . $rel;
}
