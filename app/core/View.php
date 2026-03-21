<?php

declare(strict_types=1);

namespace App\Core;

use RuntimeException;

final class View
{
    public static function render(string $template, array $data = []): void
    {
        $templateFile = template_path($template . '.php');

        if (! is_file($templateFile)) {
            throw new RuntimeException(sprintf('Template "%s" neexistuje.', $template));
        }

        extract($data, EXTR_SKIP);
        require $templateFile;
    }
}
