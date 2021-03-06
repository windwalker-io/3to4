<?php declare(strict_types=1);
/**
 * Part of Windwalker project.
 *
 * @copyright  Copyright (C) 2019 LYRASOFT.
 * @license    LGPL-2.0-or-later
 */

namespace Windwalker\Legacy\Filesystem;

use Windwalker\Legacy\Filesystem\Exception\FilesystemException;

/**
 * A File handling class
 *
 * @since  2.0
 * @deprecated Legacy code
 */
class File
{
    /**
     * Read file content.
     *
     * @param string   $filename
     * @param bool     $useIncludePath
     * @param resource $context
     * @param int      $offset
     * @param int|null $maxlen
     *
     * @return  string
     *
     * @since  3.5.6
     */
    public static function read(
        string $filename,
        bool $useIncludePath = false,
        $context = null,
        int $offset = 0,
        ?int $maxlen = null
    ): string {
        try {
            if ($maxlen) {
                $content = file_get_contents($filename, $useIncludePath, $context, $offset, $maxlen);
            } else {
                $content = file_get_contents($filename, $useIncludePath, $context, $offset);
            }

            if ($content === false) {
                $error = error_get_last();

                throw new FilesystemException(
                    $error['message'],
                    $error['type']
                );
            }
        } catch (\Throwable $e) {
            throw new FilesystemException(
                $e->getMessage(),
                $e->getCode(),
                $e
            );
        }

        return $content;
    }

    /**
     * Strips the last extension off of a file name
     *
     * @param string $file The file name
     *
     * @return  string  The file name without the extension
     *
     * @since   2.0
     */
    public static function stripExtension($file)
    {
        return preg_replace('#\.[^.]*$#', '', $file);
    }

    /**
     * getExtension
     *
     * @param string $file The file path to get extension.
     *
     * @return  string  The ext of file path.
     *
     * @since   2.0
     */
    public static function getExtension($file)
    {
        return pathinfo($file, PATHINFO_EXTENSION);
    }

    /**
     * Get file name from a path.
     *
     * @param string $path The file path to get basename.
     * @param bool   $safe Use safe splitter to get UTF-8 filename.
     *
     * @return  string  The file name.
     *
     * @since   2.0
     */
    public static function getFilename($path, bool $safe = true)
    {
        if ($safe) {
            $paths = explode(DIRECTORY_SEPARATOR, Path::clean($path));

            if ($paths === []) {
                return '';
            }

            return $paths[array_key_last($paths)];
        }

        $name = pathinfo($path, PATHINFO_FILENAME);

        $ext = pathinfo($path, PATHINFO_EXTENSION);

        if ($ext) {
            $name .= '.' . $ext;
        }

        return $name;
    }

    /**
     * Safe mb basename().
     *
     * @see https://www.php.net/manual/en/function.basename.php#121405
     *
     * @param string $path
     *
     * @return  string
     *
     * @since  3.5.17
     */
    public static function basename(string $path): string
    {
        if (preg_match('@^.*[\\\\/]([^\\\\/]+)$@s', $path, $matches)) {
            return $matches[1];
        }

        if (preg_match('@^([^\\\\/]+)$@s', $path, $matches)) {
            return $matches[1];
        }

        return '';
    }

    /**
     * Makes the file name safe to use
     *
     * @param string $file       The name of the file [not full path]
     * @param array  $stripChars Array of regex (by default will remove any leading periods)
     *
     * @return  string  The sanitised string
     *
     * @since   2.0
     */
    public static function makeSafe($file, array $stripChars = ['#^\.#'])
    {
        $regex = array_merge(['#(\.){2,}#', '#[^A-Za-z0-9\.\_\- ]#'], $stripChars);

        $file = preg_replace($regex, '', $file);

        // Remove any trailing dots, as those aren't ever valid file names.
        $file = rtrim($file, '.');

        return $file;
    }

    /**
     * Make file name safe with UTF8 name.
     *
     * @param string $file The file name.
     *
     * @return  false|string
     *
     * @since  3.4.5
     */
    public static function makeUtf8Safe($file)
    {
        $file = mb_ereg_replace("([^\w\s\d\-_~,;\[\]\(\).])", '', $file);

        return mb_ereg_replace("([\.]{2,})", '', $file);
    }

    /**
     * Copies a file
     *
     * @param string $src   The path to the source file
     * @param string $dest  The path to the destination file
     * @param bool   $force Force copy.
     *
     * @return  boolean  True on success
     *
     * @throws Exception\FilesystemException
     * @throws \UnexpectedValueException
     * @since   2.0
     */
    public static function copy($src, $dest, $force = false)
    {
        // Check src path
        if (!is_readable($src)) {
            throw new \UnexpectedValueException(__METHOD__ . ': Cannot find or read file: ' . $src);
        }

        // Check folder exists
        $dir = dirname($dest);

        if (!is_dir($dir)) {
            Folder::create($dir);
        }

        // Check is a folder or file
        if (file_exists($dest)) {
            if ($force) {
                Filesystem::delete($dest);
            } else {
                throw new FilesystemException($dest . ' has exists, copy failed.');
            }
        }

        if (!@ copy($src, $dest)) {
            throw new FilesystemException(__METHOD__ . ': Copy failed.');
        }

        return true;
    }

    /**
     * Delete a file or array of files
     *
     * @param mixed $files The file name or an array of file names
     *
     * @return  boolean  True on success
     *
     * @throws  FilesystemException
     * @since   2.0
     */
    public static function delete($files)
    {
        $files = (array) $files;

        foreach ($files as $file) {
            $file = Path::clean($file);

            // Try making the file writable first. If it's read-only, it can't be deleted
            // on Windows, even if the parent folder is writable
            @chmod($file, 0777);

            // In case of restricted permissions we zap it one way or the other
            // as long as the owner is either the webserver or the ftp
            try {
                unlink($file);
            } catch (\Throwable $e) {
                throw new FilesystemException(
                    $e->getMessage(),
                    $e->getCode(),
                    $e
                );
            }
        }

        return true;
    }

    /**
     * Moves a file
     *
     * @param string $src   The path to the source file
     * @param string $dest  The path to the destination file
     * @param bool   $force Force move it.
     *
     * @return  boolean  True on success
     *
     * @throws Exception\FilesystemException
     * @since   2.0
     */
    public static function move($src, $dest, $force = false)
    {
        // Check src path
        if (!is_readable($src)) {
            return 'Cannot find source file.';
        }

        // Delete first if exists
        if (file_exists($dest)) {
            if ($force) {
                Filesystem::delete($dest);
            } else {
                throw new FilesystemException('File: ' . $dest . ' exists, move failed.');
            }
        }

        // Check folder exists
        $dir = dirname($dest);

        if (!is_dir($dir)) {
            Folder::create($dir);
        }

        try {
            rename($src, $dest);
        } catch (\Throwable $e) {
            throw new FilesystemException(
                $e->getMessage(),
                $e->getCode(),
                $e
            );
        }

        return true;
    }

    /**
     * Write contents to a file
     *
     * @param string $file   The full file path
     * @param string $buffer The buffer to write
     *
     * @return  boolean  True on success
     *
     * @throws  FilesystemException
     * @since   2.0
     */
    public static function write($file, $buffer)
    {
        @set_time_limit((int) ini_get('max_execution_time'));

        // If the destination directory doesn't exist we need to create it
        if (!is_dir(dirname($file))) {
            Folder::create(dirname($file));
        }

        $file = Path::clean($file);

        return is_int(file_put_contents($file, $buffer)) ? true : false;
    }

    /**
     * Moves an uploaded file to a destination folder
     *
     * @param string $src  The name of the php (temporary) uploaded file
     * @param string $dest The path (including filename) to move the uploaded file to
     *
     * @return  boolean  True on success
     *
     * @throws  FilesystemException
     * @since   2.0
     */
    public static function upload($src, $dest)
    {
        // Ensure that the path is valid and clean
        $dest = Path::clean($dest);

        // Create the destination directory if it does not exist
        $baseDir = dirname($dest);

        if (!file_exists($baseDir)) {
            Folder::create($baseDir);
        }

        if (is_writable($baseDir) && move_uploaded_file($src, $dest)) {
            // Short circuit to prevent file permission errors
            if (Path::setPermissions($dest)) {
                return true;
            }

            throw new FilesystemException(__METHOD__ . ': Failed to change file permissions.');
        }

        throw new FilesystemException(__METHOD__ . ': Failed to move file.');
    }

    /**
     * Check file exists.
     *
     * @param string $path        The file path to check.
     * @param bool   $inSensitive Insensitive file name case.
     *
     * @return  bool
     */
    public static function exists($path, $inSensitive = false)
    {
        $exists = is_file($path);

        if (!$inSensitive) {
            return $exists;
        }

        if (!$exists) {
            $lowerfile = strtolower($path);

            foreach (glob(dirname($path) . '/*') as $file) {
                if (strtolower($file) === $lowerfile) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * createTemp
     *
     * @param string|null $dir
     * @param string|null $prefix
     *
     * @return  string
     *
     * @since  3.5.12
     */
    public static function createTemp(?string $dir = null, ?string $prefix = null): string
    {
        $dir    = $dir ?? sys_get_temp_dir();
        $prefix = $prefix ?? 'Windwalker-Temp-';

        if (!is_dir($dir)) {
            Folder::create($dir);
        }

        $temp = tempnam($dir, $prefix);

        if (!$temp) {
            throw new FilesystemException(sprintf(
                'Create temp file on %s failure.',
                $dir
            ));
        }

        return (string) $temp;
    }
}
