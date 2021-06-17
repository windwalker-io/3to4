<?php

/**
 * Part of datavideo project.
 *
 * @copyright  Copyright (C) 2021 .
 * @license    __LICENSE__
 */

namespace W3to4\Converter;

use Windwalker\Filesystem\FileObject;
use Windwalker\Filesystem\Filesystem;
use Windwalker\Scalars\StringObject;

/**
 * The FileReplacer class.
 *
 * @since  __DEPLOY_VERSION__
 */
class FileReplacer
{
    public static function handle(string $path, callable $handler): iterable
    {
        if (!str_contains($path, '*')) {
            $path .= '/**/*';
        }

        /** @var FileObject $file */
        foreach (Filesystem::glob($path) as $file) {
            if ($file->isDir()) {
                continue;
            }

            $r = $handler($file);

            if (!$r) {
                continue;
            }

            if (is_array($r)) {
                [$path, $content] = $r;
            } else {
                $path = $file->getPathname();
                $content = $r;
            }

            yield Filesystem::write($path, (string) $content);
        }
    }

    public static function addUse(string|StringObject $content, string $use): StringObject
    {
        $content = \Windwalker\str($content);

        if ($content->contains("use $use;")) {
            return $content;
        }
        
        $lines = $content->explode("\n");
        
        $last = 0;

        foreach ($lines as $i => $line) {
            if (str_starts_with($line, 'use ')) {
                $last = $i;
            }
        }

        $lines->splice($last + 1, 0, "use $use;");

        return $lines->implode("\n");
    }
}
