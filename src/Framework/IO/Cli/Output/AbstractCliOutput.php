<?php declare(strict_types=1);
/**
 * Part of Windwalker project.
 *
 * @copyright  Copyright (C) 2019 LYRASOFT.
 * @license    LGPL-2.0-or-later
 */

namespace Windwalker\Legacy\IO\Cli\Output;

/**
 * Class AbstractCliOutput
 *
 * @since 2.0
 */
abstract class AbstractCliOutput implements CliOutputInterface
{
    /**
     * Property outStream.
     *
     * @var  resource
     */
    protected $outputStream = STDOUT;

    /**
     * Property errorStream.
     *
     * @var  resource
     */
    protected $errorStream = STDERR;

    /**
     * getOutStream
     *
     * @return  resource
     */
    public function getOutputStream()
    {
        return $this->outputStream;
    }

    /**
     * setOutStream
     *
     * @param   resource $outStream
     *
     * @return  static  Return self to support chaining.
     */
    public function setOutputStream($outStream)
    {
        $this->outputStream = $outStream;

        return $this;
    }

    /**
     * Method to get property ErrorStream
     *
     * @return  resource
     */
    public function getErrorStream()
    {
        return $this->errorStream;
    }

    /**
     * Method to set property errorStream
     *
     * @param   resource $errorStream
     *
     * @return  static  Return self to support chaining.
     */
    public function setErrorStream($errorStream)
    {
        $this->errorStream = $errorStream;

        return $this;
    }
}
