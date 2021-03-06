<?php declare(strict_types=1);
/**
 * Part of Windwalker project.
 *
 * @copyright  Copyright (C) 2019 LYRASOFT.
 * @license    LGPL-2.0-or-later
 */

namespace Windwalker\Legacy\Filesystem;

use Windwalker\Legacy\Filesystem\Comparator\FileComparatorInterface;
use Windwalker\Legacy\Filesystem\Iterator\RecursiveDirectoryIterator;

/**
 * Class Filesystem
 *
 * @since 2.0
 */
abstract class Filesystem
{
    /**
     * copy
     *
     * @param string $src
     * @param string $dest
     * @param bool   $force
     *
     * @return  bool
     */
    public static function copy($src, $dest, $force = false)
    {
        if (is_dir($src)) {
            Folder::copy($src, $dest, $force);
        } elseif (is_file($src)) {
            File::copy($src, $dest, $force);
        }

        return true;
    }

    /**
     * move
     *
     * @param string $src
     * @param string $dest
     * @param bool   $force
     *
     * @return  bool
     */
    public static function move($src, $dest, $force = false)
    {
        if (is_dir($src)) {
            Folder::move($src, $dest, $force);
        } elseif (is_file($src)) {
            File::move($src, $dest, $force);
        }

        return true;
    }

    /**
     * delete
     *
     * @param string $path
     *
     * @return  bool
     */
    public static function delete($path)
    {
        if (is_dir($path)) {
            Folder::delete($path);
        } elseif (is_file($path)) {
            File::delete($path);
        }

        return true;
    }

    /**
     * files
     *
     * @param   string $path
     * @param   bool   $recursive
     * @param   bool   $toArray
     *
     * @return  \CallbackFilterIterator
     */
    public static function files($path, $recursive = false, $toArray = false)
    {
        /**
         * Files callback
         *
         * @param \SplFileInfo                $current  Current item's value
         * @param string                      $key      Current item's key
         * @param \RecursiveDirectoryIterator $iterator Iterator being filtered
         *
         * @return boolean   TRUE to accept the current item, FALSE otherwise
         */
        $callback = function ($current, $key, $iterator) {
            return $current->isFile();
        };

        return static::findByCallback($path, $callback, $recursive, $toArray);
    }

    /**
     * folders
     *
     * @param   string  $path
     * @param   bool    $recursive
     * @param   boolean $toArray
     *
     * @return  \CallbackFilterIterator
     */
    public static function folders($path, $recursive = false, $toArray = false)
    {
        /**
         * Files callback
         *
         * @param \SplFileInfo                $current  Current item's value
         * @param string                      $key      Current item's key
         * @param \RecursiveDirectoryIterator $iterator Iterator being filtered
         *
         * @return boolean   TRUE to accept the current item, FALSE otherwise
         */
        $callback = function ($current, $key, $iterator) use ($path, $recursive) {
            if ($recursive) {
                // Ignore self
                if ($iterator->getRealPath() == Path::clean($path)) {
                    return false;
                }

                // If set to recursive, every returned folder name will include a dot (.),
                // so we can't using isDot() to detect folder.
                return $iterator->isDir() && ($iterator->getBasename() !== '..');
            } else {
                return $iterator->isDir() && !$iterator->isDot();
            }
        };

        return static::findByCallback($path, $callback, $recursive, $toArray);
    }

    /**
     * items
     *
     * @param   string  $path
     * @param   bool    $recursive
     * @param   boolean $toArray
     *
     * @return  \CallbackFilterIterator
     */
    public static function items($path, $recursive = false, $toArray = false)
    {
        /**
         * Files callback
         *
         * @param \SplFileInfo                $current  Current item's value
         * @param string                      $key      Current item's key
         * @param \RecursiveDirectoryIterator $iterator Iterator being filtered
         *
         * @return boolean   TRUE to accept the current item, FALSE otherwise
         */
        $callback = function ($current, $key, $iterator) use ($path, $recursive) {
            if ($recursive) {
                // Ignore self
                if ($iterator->getRealPath() == Path::clean($path)) {
                    return false;
                }

                // If set to recursive, every returned folder name will include a dot (.),
                // so we can't using isDot() to detect folder.
                return ($iterator->getBasename() !== '..');
            } else {
                return !$iterator->isDot();
            }
        };

        return static::findByCallback($path, $callback, $recursive, $toArray);
    }

    /**
     * Find one file and return.
     *
     * @param  string  $path          The directory path.
     * @param  mixed   $condition     Finding condition, that can be a string, a regex or a callback function.
     *                                Callback example:
     *                                <code>
     *                                function($current, $key, $iterator)
     *                                {
     *                                return @preg_match('^Foo', $current->getFilename())  && ! $iterator->isDot();
     *                                }
     *                                </code>
     * @param  boolean $recursive     True to resursive.
     *
     * @return  \SplFileInfo  Finded file info object.
     *
     * @since  2.0
     */
    public static function findOne($path, $condition, $recursive = false)
    {
        $iterator = new \LimitIterator(static::find($path, $condition, $recursive), 0, 1);

        $iterator->rewind();

        return $iterator->current();
    }

    /**
     * Support node style double star finder.
     *
     * @param string $pattern
     * @param int    $flags
     *
     * @return  array
     *
     * @since  3.5
     */
    public static function glob(string $pattern, int $flags = 0): array
    {
        $pattern = Path::clean($pattern);

        if (strpos($pattern, '**') === false) {
            $files = glob($pattern, $flags);
        } else {
            $position = strpos($pattern, '**');
            $rootPattern = substr($pattern, 0, $position - 1);
            $restPattern = substr($pattern, $position + 2);
            $patterns = [$rootPattern . $restPattern];
            $rootPattern .= DIRECTORY_SEPARATOR . '*';

            while ($dirs = glob($rootPattern, GLOB_ONLYDIR)) {
                $rootPattern .= DIRECTORY_SEPARATOR . '*';

                foreach ($dirs as $dir) {
                    $patterns[] = $dir . $restPattern;
                }
            }

            $files = [];

            foreach ($patterns as $pat) {
                $files[] = static::glob($pat, $flags);
            }

            $files = array_merge(...$files);
        }

        $files = array_unique($files);

        sort($files);

        return $files;
    }

    /**
     * globAll
     *
     * @param string $baseDir
     * @param array  $patterns
     * @param int    $flags
     *
     * @return  array
     *
     * @since  3.5
     */
    public static function globAll(string $baseDir, array $patterns, int $flags = 0): array
    {
        $files = [];
        $inverse = [];

        foreach ($patterns as $pattern) {
            if (strpos($pattern, '!') === 0) {
                $pattern = substr($pattern, 1);

                $inverse[] = static::glob(
                    rtrim($baseDir, '\\/') . '/' . ltrim($pattern, '\\/'),
                    $flags
                );
            } else {
                $files[] = static::glob(
                    rtrim($baseDir, '\\/') . '/' . ltrim($pattern, '\\/'),
                    $flags
                );
            }
        }

        if ($files !== []) {
            $files = array_unique(array_merge(...$files));
        }

        if ($inverse !== []) {
            $inverse = array_unique(array_merge(...$inverse));
        }

        return array_diff($files, $inverse);
    }

    /**
     * Find all files which matches condition.
     *
     * @param  string  $path        The directory path.
     * @param  mixed   $condition   Finding condition, that can be a string, a regex or a callback function.
     *                              Callback example:
     *                              <code>
     *                              function($current, $key, $iterator)
     *                              {
     *                              return @preg_match('^Foo', $current->getFilename())  && ! $iterator->isDot();
     *                              }
     *                              </code>
     * @param  boolean $recursive   True to resursive.
     * @param  boolean $toArray     True to convert iterator to array.
     *
     * @return  \CallbackFilterIterator  Found files or paths iterator.
     *
     * @since  2.0
     */
    public static function find($path, $condition, $recursive = false, $toArray = false)
    {
        // If conditions is string or array, we make it to regex.
        if (!($condition instanceof \Closure) && !($condition instanceof FileComparatorInterface)) {
            if (is_array($condition)) {
                $condition = '/(' . implode('|', $condition) . ')/';
            } else {
                $condition = '/' . (string) $condition . '/';
            }

            /**
             * Files callback
             *
             * @param \SplFileInfo                $current  Current item's value
             * @param string                      $key      Current item's key
             * @param \RecursiveDirectoryIterator $iterator Iterator being filtered
             *
             * @return boolean   TRUE to accept the current item, FALSE otherwise
             */
            $condition = function ($current, $key, $iterator) use ($condition) {
                return @preg_match($condition, $iterator->getFilename()) && !$iterator->isDot();
            };
        } elseif ($condition instanceof FileComparatorInterface) {
            // If condition is compare object, wrap it with callback.
            /**
             * Files callback
             *
             * @param \SplFileInfo                $current  Current item's value
             * @param string                      $key      Current item's key
             * @param \RecursiveDirectoryIterator $iterator Iterator being filtered
             *
             * @return boolean   TRUE to accept the current item, FALSE otherwise
             */
            $condition = function ($current, $key, $iterator) use ($condition) {
                return $condition->compare($current, $key, $iterator);
            };
        }

        return static::findByCallback($path, $condition, $recursive, $toArray);
    }

    /**
     * Using a closure function to filter file.
     *
     * Reference: http://www.php.net/manual/en/class.callbackfilteriterator.php
     *
     * @param  string   $path      The directory path.
     * @param  \Closure $callback  A callback function to filter file.
     * @param  boolean  $recursive True to recursive.
     * @param  boolean  $toArray   True to convert iterator to array.
     *
     * @return  \CallbackFilterIterator  Filtered file or path iteator.
     *
     * @since  2.0
     */
    public static function findByCallback($path, \Closure $callback, $recursive = false, $toArray = false)
    {
        $itarator = new \CallbackFilterIterator(static::createIterator($path, $recursive), $callback);

        if ($toArray) {
            return static::iteratorToArray($itarator);
        }

        return $itarator;
    }

    /**
     * Create file iterator of current dir.
     *
     * @param  string  $path      The directory path.
     * @param  boolean $recursive True to recursive.
     * @param  integer $options   FilesystemIterator Flags provides which will affect the behavior of some methods.
     *
     * @throws \InvalidArgumentException
     * @return  \FilesystemIterator|\RecursiveIteratorIterator  File & dir iterator.
     */
    public static function createIterator($path, $recursive = false, $options = null)
    {
        $path = Path::clean($path);

        if ($recursive) {
            $options = $options ?: (\FilesystemIterator::KEY_AS_PATHNAME | \FilesystemIterator::CURRENT_AS_FILEINFO);
        } else {
            $options = $options ?: (\FilesystemIterator::KEY_AS_PATHNAME | \FilesystemIterator::CURRENT_AS_FILEINFO
                | \FilesystemIterator::SKIP_DOTS);
        }

        try {
            $iterator = new RecursiveDirectoryIterator($path, $options);
        } catch (\UnexpectedValueException $exception) {
            throw new \InvalidArgumentException(sprintf('Dir: %s not found.', (string) $path), null, $exception);
        }

        // If rescurive set to true, use RecursiveIteratorIterator
        return $recursive ? new \RecursiveIteratorIterator($iterator) : $iterator;
    }

    /**
     * iteratorToArray
     *
     * @param \Traversable $iterator
     *
     * @return  array
     */
    public static function iteratorToArray(\Traversable $iterator)
    {
        $array = [];

        foreach ($iterator as $key => $file) {
            $array[] = (string) $file;
        }

        return $array;
    }
}
