<?php declare(strict_types=1);
/**
 * Part of Windwalker project.
 *
 * @copyright  Copyright (C) 2019 LYRASOFT.
 * @license    LGPL-2.0-or-later
 */

namespace Windwalker\Legacy\IO\Cli\Output;

/**
 * Class CliOutputInterface
 *
 * @since 2.0
 */
interface CliOutputInterface
{
    /**
     * Write a string to standard output
     *
     * @param   string $text The text to display.
     *
     * @return  CliOutputInterface  Instance of $this to allow chaining.
     */
    public function out($text = '');

    /**
     * Write a string to standard error output.
     *
     * @param   string $text The text to display.
     *
     * @since   2.0
     * @return $this
     */
    public function err($text = '');
}
