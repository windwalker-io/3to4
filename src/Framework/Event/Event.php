<?php declare(strict_types=1);
/**
 * Part of Windwalker project.
 *
 * @copyright  Copyright (C) 2019 LYRASOFT.
 * @license    LGPL-2.0-or-later
 */

namespace Windwalker\Legacy\Event;

/**
 * Class Event
 *
 * @since 2.0
 * @deprecated Legacy code
 */
class Event implements EventInterface, \ArrayAccess, \Serializable, \Countable
{
    /**
     * The event name.
     *
     * @var    string
     *
     * @since  2.0
     */
    protected $name;

    /**
     * The event arguments.
     *
     * @var    array
     *
     * @since  2.0
     */
    protected $arguments = [];

    /**
     * A flag to see if the event propagation is stopped.
     *
     * @var    boolean
     *
     * @since  2.0
     */
    protected $stopped = false;

    /**
     * Constructor.
     *
     * @param   string $name      The event name.
     * @param   array  $arguments The event arguments.
     *
     * @since   2.0
     */
    public function __construct($name, array $arguments = [])
    {
        $this->name = $name;

        $this->mergeArguments($arguments);
    }

    /**
     * Get the event name.
     *
     * @return  string  The event name.
     *
     * @since   2.0
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Method to set property name
     *
     * @param   string $name
     *
     * @return  static  Return self to support chaining.
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get an event argument value.
     *
     * @param   string $name    The argument name.
     * @param   mixed  $default The default value if not found.
     *
     * @return  mixed  The argument value or the default value.
     *
     * @since   2.0
     */
    public function getArgument($name, $default = null)
    {
        if (isset($this->arguments[$name])) {
            return $this->arguments[$name];
        }

        return $default;
    }

    /**
     * Tell if the given event argument exists.
     *
     * @param   string $name The argument name.
     *
     * @return  boolean  True if it exists, false otherwise.
     *
     * @since   2.0
     */
    public function hasArgument($name)
    {
        return isset($this->arguments[$name]);
    }

    /**
     * Get all event arguments.
     *
     * @return  array  An associative array of argument names as keys
     *                 and their values as values.
     *
     * @since   2.0
     */
    public function getArguments()
    {
        return $this->arguments;
    }

    /**
     * Method to set property arguments
     *
     * @param   array $arguments   An associative array of argument names as keys
     *                             and their values as values.
     *
     * @return  static  Return self to support chaining.
     */
    public function setArguments(array $arguments)
    {
        $this->clearArguments();

        $this->mergeArguments($arguments);

        return $this;
    }

    /**
     * Add an event argument, only if it is not existing.
     *
     * @param   string $name  The argument name.
     * @param   mixed  $value The argument value.
     *
     * @return  Event  This method is chainable.
     *
     * @since   2.0
     */
    public function addArgument($name, $value)
    {
        if (!isset($this->arguments[$name])) {
            $this->arguments[$name] = $value;
        }

        return $this;
    }

    /**
     * Set the value of an event argument.
     * If the argument already exists, it will be overridden.
     *
     * @param   string $name  The argument name.
     * @param   mixed  $value The argument value.
     *
     * @return  Event  This method is chainable.
     *
     * @since   2.0
     */
    public function setArgument($name, $value)
    {
        $this->arguments[$name] = $value;

        return $this;
    }

    /**
     * mergeArguments
     *
     * @param   array $arguments
     *
     * @return  static
     */
    public function mergeArguments(array $arguments)
    {
        foreach ($arguments as $key => &$value) {
            $this->arguments[$key] = &$value;
        }

        return $this;
    }

    /**
     * Remove an event argument.
     *
     * @param   string $name The argument name.
     *
     * @return  mixed  The old argument value or null if it is not existing.
     *
     * @since   2.0
     */
    public function removeArgument($name)
    {
        $return = null;

        if (isset($this->arguments[$name])) {
            $return = $this->arguments[$name];

            unset($this->arguments[$name]);
        }

        return $return;
    }

    /**
     * Clear all event arguments.
     *
     * @return  static  Return self to support chaining.
     *
     * @since   2.0
     */
    public function clearArguments()
    {
        // Break the reference
        unset($this->arguments);

        $this->arguments = [];

        return $this;
    }

    /**
     * Stop the event propagation.
     *
     * @return  void
     *
     * @since   2.0
     */
    public function stop()
    {
        $this->stopped = true;
    }

    /**
     * Tell if the event propagation is stopped.
     *
     * @return  boolean  True if stopped, false otherwise.
     *
     * @since   2.0
     */
    public function isStopped()
    {
        return true === $this->stopped;
    }

    /**
     * Count the number of arguments.
     *
     * @return  integer  The number of arguments.
     *
     * @since   2.0
     */
    #[\ReturnTypeWillChange]
    public function count()
    {
        return count($this->arguments);
    }

    /**
     * Tell if the given event argument exists.
     *
     * @param   string $name The argument name.
     *
     * @return  boolean  True if it exists, false otherwise.
     *
     * @since   2.0
     */
    #[\ReturnTypeWillChange]
    public function offsetExists($name)
    {
        return $this->hasArgument($name);
    }

    /**
     * Get an event argument value.
     *
     * @param   string $name The argument name.
     *
     * @return  mixed  The argument value or null if not existing.
     *
     * @since   2.0
     */
    #[\ReturnTypeWillChange]
    public function offsetGet($name)
    {
        return $this->getArgument($name);
    }

    /**
     * Set the value of an event argument.
     *
     * @param   string $name  The argument name.
     * @param   mixed  $value The argument value.
     *
     * @return  void
     *
     * @throws  \InvalidArgumentException  If the argument name is null.
     *
     * @since   2.0
     */
    #[\ReturnTypeWillChange]
    public function offsetSet($name, $value)
    {
        if (is_null($name)) {
            throw new \InvalidArgumentException('The argument name cannot be null.');
        }

        $this->setArgument($name, $value);
    }

    /**
     * Remove an event argument.
     *
     * @param   string $name The argument name.
     *
     * @return  void
     *
     * @since   2.0
     */
    #[\ReturnTypeWillChange]
    public function offsetUnset($name)
    {
        $this->removeArgument($name);
    }

    public function __serialize(): array
    {
        return [$this->name, $this->arguments, $this->stopped];
    }

    public function __unserialize(array $data): void
    {
        [$this->name, $this->arguments, $this->stopped] = $data;
    }

    /**
     * Serialize the event.
     *
     * @return  string  The serialized event.
     *
     * @since   2.0
     */
    public function serialize()
    {
        return serialize([$this->name, $this->arguments, $this->stopped]);
    }

    /**
     * Unserialize the event.
     *
     * @param   string $serialized The serialized event.
     *
     * @return  void
     *
     * @since   2.0
     */
    public function unserialize($serialized)
    {
        [$this->name, $this->arguments, $this->stopped] = unserialize($serialized);
    }
}
