<?php
function responsive_image($photo, $alt = '') {
    if (!$photo) return '';

    $thumb = $photo->picture_path('thumbnail');
    $medium = $photo->picture_path('medium');
    $large = $photo->picture_path('large');
    $original = $photo->picture_path('original');

    return sprintf(
        '<picture>
            <source media="(min-width: 1200px)" srcset="%s">
            <source media="(min-width: 768px)" srcset="%s">
            <source media="(min-width: 480px)" srcset="%s">
            <img src="%s" alt="%s" class="img-fluid">
        </picture>',
        $large,
        $medium,
        $thumb,
        $original,
        htmlspecialchars($alt ?: $photo->alternate_text)
    );
}