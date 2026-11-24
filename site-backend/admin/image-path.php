<?php
function isValidImagePath(string $image): bool
{
    if ($image === '') {
        return false;
    }

    $scheme = parse_url($image, PHP_URL_SCHEME);
    if ($scheme !== null) {
        return in_array(strtolower($scheme), ['http', 'https'], true)
            && filter_var($image, FILTER_VALIDATE_URL) !== false;
    }

    return !str_starts_with($image, '//')
        && !str_contains($image, '..')
        && !str_contains($image, '\\')
        && !str_contains($image, ':')
        && preg_match('/[\x00-\x20]/', $image) !== 1;
}
