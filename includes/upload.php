<?php
declare(strict_types=1);

const UPLOAD_MAX_BYTES = 5 * 1024 * 1024;

function project_root(): string
{
    return dirname(__DIR__);
}

function uploads_dir(): string
{
    return project_root() . '/uploads';
}

function allowed_image_mime(string $tmp): ?string
{
    if (!is_uploaded_file($tmp)) {
        return null;
    }
    $finfo = new finfo(FILEINFO_MIME_TYPE);
    $mime = $finfo->file($tmp);
    $map = [
        'image/jpeg' => 'jpg',
        'image/png' => 'png',
        'image/webp' => 'webp',
    ];
    return isset($map[$mime]) ? $mime : null;
}

function ext_for_mime(string $mime): string
{
    $map = [
        'image/jpeg' => 'jpg',
        'image/png' => 'png',
        'image/webp' => 'webp',
    ];
    return $map[$mime] ?? 'bin';
}

/**
 * @return string relative web path e.g. uploads/abc.jpg
 */
function store_uploaded_image(array $file): string
{
    if (($file['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK) {
        throw new RuntimeException('Dosya yüklenemedi.');
    }
    if (($file['size'] ?? 0) > UPLOAD_MAX_BYTES) {
        throw new RuntimeException('Dosya çok büyük (en fazla 5 MB).');
    }
    $tmp = $file['tmp_name'];
    $mime = allowed_image_mime($tmp);
    if ($mime === null) {
        throw new RuntimeException('Sadece JPEG, PNG veya WebP yükleyebilirsiniz.');
    }
    $ext = ext_for_mime($mime);
    $name = bin2hex(random_bytes(12)) . '.' . $ext;
    $dir = uploads_dir();
    if (!is_dir($dir)) {
        mkdir($dir, 0755, true);
    }
    $dest = $dir . '/' . $name;
    if (!move_uploaded_file($tmp, $dest)) {
        throw new RuntimeException('Dosya kaydedilemedi.');
    }
    return 'uploads/' . $name;
}
