<?php declare(strict_types=1);
/**
 * Part of Windwalker project.
 *
 * @copyright  Copyright (C) 2019 LYRASOFT.
 * @license    LGPL-2.0-or-later
 */

namespace Windwalker\Legacy\IO\Cli;

use Windwalker\Legacy\IO\Cli\Input\CliInput;
use Windwalker\Legacy\IO\Cli\Input\CliInputInterface;
use Windwalker\Legacy\IO\Cli\Output\CliOutput;
use Windwalker\Legacy\IO\Cli\Output\CliOutputInterface;
use Windwalker\Legacy\IO\Cli\Output\ColorfulOutputInterface;

/**
 * The IO class.
 *
 * @since  2.0
 * @deprecated Legacy code
 */
class IO implements IOInterface, \IteratorAggregate, \ArrayAccess, \Serializable, \Countable, \JsonSerializable
{
    /**
     * Property input.
     *
     * @var  CliInputInterface
     */
    protected $input = null;

    /**
     * Property output.
     *
     * @var  CliOutputInterface|ColorfulOutputInterface
     */
    protected $output = null;

    /**
     * Class init.
     *
     * @param CliInputInterface  $input
     * @param CliOutputInterface $output
     */
    public function __construct(CliInputInterface $input = null, CliOutputInterface $output = null)
    {
        $this->input = $input ?: new CliInput();
        $this->output = $output ?: new CliOutput();
    }

    /**
     * Write a string to standard output
     *
     * @param   string  $text The text to display.
     * @param   boolean $nl   True (default) to append a new line at the end of the output string.
     *
     * @return  IO  Instance of $this to allow chaining.
     */
    public function out($text = '', $nl = true)
    {
        $this->output->out($text, $nl);

        return $this;
    }

    /**
     * Get a value from standard input.
     *
     * @return  string  The input string from standard input.
     */
    public function in()
    {
        return $this->input->in();
    }

    /**
     * Write a string to standard error output.
     *
     * @param   string  $text The text to display.
     * @param   boolean $nl   True (default) to append a new line at the end of the output string.
     *
     * @since   2.0
     * @return $this
     */
    public function err($text = '', $nl = true)
    {
        $this->output->err($text, $nl);

        return $this;
    }

    /**
     * Gets a value from the input data.
     *
     * @param   string $name    Name of the value to get.
     * @param   mixed  $default Default value to return if variable does not exist.
     *
     * @return  mixed  The filtered input value.
     *
     * @since   2.0
     */
    public function getOption($name, $default = null)
    {
        return $this->input->get($name, $default);
    }

    /**
     * Sets a value
     *
     * @param   string $name  Name of the value to set.
     * @param   mixed  $value Value to assign to the input.
     *
     * @return  IO
     *
     * @since   2.0
     */
    public function setOption($name, $value)
    {
        $this->input->set($name, $value);

        return $this;
    }

    /**
     * getArgument
     *
     * @param integer $offset
     * @param mixed   $default
     *
     * @return  mixed
     */
    public function getArgument($offset, $default = null)
    {
        return $this->input->getArgument($offset, $default);
    }

    /**
     * setArgument
     *
     * @param integer $offset
     * @param mixed   $value
     *
     * @return  IO
     */
    public function setArgument($offset, $value)
    {
        $this->input->setArgument($offset, $value);

        return $this;
    }

    /**
     * getInput
     *
     * @return  CliInput|CliInputInterface
     */
    public function getInput()
    {
        return $this->input;
    }

    /**
     * setInput
     *
     * @param   CliInputInterface $input
     *
     * @return  IO  Return self to support chaining.
     */
    public function setInput(CliInputInterface $input)
    {
        $this->input = $input;

        return $this;
    }

    /**
     * getOutput
     *
     * @return  CliOutputInterface|ColorfulOutputInterface
     */
    public function getOutput()
    {
        return $this->output;
    }

    /**
     * setOutput
     *
     * @param   CliOutputInterface $output
     *
     * @return  IO  Return self to support chaining.
     */
    public function setOutput(CliOutputInterface $output)
    {
        $this->output = $output;

        return $this;
    }

    /**
     * getExecuted
     *
     * @return  mixed
     */
    public function getCalledScript()
    {
        return $this->input->getCalledScript();
    }

    /**
     * getOptions
     *
     * @return  string[]
     */
    public function getOptions()
    {
        return $this->input->all();
    }

    /**
     * getArguments
     *
     * @return  string[]
     */
    public function getArguments()
    {
        return $this->input->args;
    }

    /**
     * Set value to property
     *
     * @param mixed $offset Property key.
     * @param mixed $value  Property value to set.
     *
     * @return  void
     */
    #[\ReturnTypeWillChange]
    public function offsetSet($offset, $value)
    {
        $this->input->getArgument($offset, $value);
    }

    /**
     * Unset a property.
     *
     * @param mixed $offset Key to unset.
     *
     * @return  void
     */
    #[\ReturnTypeWillChange]
    public function offsetUnset($offset)
    {
        unset($this->input->args[$offset]);
    }

    /**
     * Property is exist or not.
     *
     * @param mixed $offset Property key.
     *
     * @return  boolean
     */
    #[\ReturnTypeWillChange]
    public function offsetExists($offset)
    {
        return isset($this->input->args[$offset]);
    }

    /**
     * Get a value of property.
     *
     * @param mixed $offset Property key.
     *
     * @return  mixed The value of this property.
     */
    #[\ReturnTypeWillChange]
    public function offsetGet($offset)
    {
        return $this->getArgument($offset);
    }

    /**
     * Get the data store for iterate.
     *
     * @return  \Traversable The data to be iterator.
     */
    #[\ReturnTypeWillChange]
    public function getIterator()
    {
        return new \ArrayIterator($this->input->args);
    }

    /**
     * Serialize data.
     *
     * @return  string Serialized data string.
     */
    public function serialize()
    {
        return serialize($this->input);
    }

    /**
     * Unserialize the data.
     *
     * @param string $serialized THe serialized data string.
     *
     * @return  IO Support chaining.
     */
    public function unserialize($serialized)
    {
        $this->input = unserialize($serialized);

        return $this;
    }

    public function __serialize(): array
    {
        return (array) $this->input;
    }

    /**
     * Unserialize the data.
     *
     * @param string $serialized THe serialized data string.
     *
     * @return  IO Support chaining.
     */
    public function __unserialize($serialized): void
    {
        $this->input = $serialized;
    }

    /**
     * Count data.
     *
     * @return  int
     */
    #[\ReturnTypeWillChange]
    public function count()
    {
        return count($this->input->args);
    }

    /**
     * Serialize to json format.
     *
     * @return  string Encoded json string.
     */
    #[\ReturnTypeWillChange]
    public function jsonSerialize()
    {
        return [
            'arguments' => $this->input->args,
            'options' => $this->input->all(),
        ];
    }
}
