<?php declare(strict_types=1);
/**
 * @copyright  Copyright (C) 2019 LYRASOFT Source Matters.
 * @license    LGPL-2.0-or-later.txt
 */

namespace Windwalker\Legacy\Event\Test;

use Windwalker\Legacy\Event\ListenersQueue;
use Windwalker\Legacy\Event\Test\Stub\EmptyListener;

/**
 * Tests for the ListenersPriorityQueue class.
 *
 * @since  2.0
 * @deprecated Legacy code
 */
class ListenersQueueTest extends \PHPUnit\Framework\TestCase
{
    /**
     * Object under tests.
     *
     * @var    ListenersQueue
     *
     * @since  2.0
     */
    private $instance;

    /**
     * Test the add method.
     *
     * @return  void
     *
     * @since   2.0
     */
    public function testAdd()
    {
        $listener1 = new EmptyListener();
        $listener2 = new EmptyListener();
        $listener3 = function () {
        };
        $listener4 = new EmptyListener();

        $this->instance->add($listener1, 5);
        $this->instance->add($listener2, 5);
        $this->instance->add($listener3, 0);
        $this->instance->add($listener4, -100);

        $this->assertTrue($this->instance->has($listener1));
        $this->assertEquals(5, $this->instance->getPriority($listener1));

        $this->assertTrue($this->instance->has($listener2));
        $this->assertEquals(5, $this->instance->getPriority($listener2));

        $this->assertTrue($this->instance->has($listener3));
        $this->assertEquals(0, $this->instance->getPriority($listener3));

        $this->assertTrue($this->instance->has($listener4));
        $this->assertEquals(-100, $this->instance->getPriority($listener4));
    }

    /**
     * Test adding an existing listener will have no effect.
     *
     * @return  void
     *
     * @since   2.0
     */
    public function testAddExisting()
    {
        $listener = new EmptyListener();

        $this->instance->add($listener, 5);
        $this->instance->add($listener, 0);

        $this->assertTrue($this->instance->has($listener));
        $this->assertEquals(5, $this->instance->getPriority($listener));
    }

    /**
     * Test the getPriority method when the listener wasn't added.
     *
     * @return  void
     *
     * @since   2.0
     */
    public function testGetPriorityNonExisting()
    {
        $this->assertNull($this->instance->getPriority(new EmptyListener()));

        $this->assertFalse(
            $this->instance->getPriority(
                function () {
                },
                false
            )
        );
    }

    /**
     * Test the remove method.
     *
     * @return  void
     *
     * @since   2.0
     */
    public function testRemove()
    {
        $listener1 = new EmptyListener();
        $listener2 = new EmptyListener();
        $listener3 = function () {
        };
        $listener4 = new EmptyListener();

        $this->instance->add($listener1, 0);
        $this->instance->add($listener2, 0);
        $this->instance->add($listener3, 0);

        // Removing a non existing listener has no effect.
        $this->instance->remove($listener4);

        $this->assertTrue($this->instance->has($listener1));
        $this->assertTrue($this->instance->has($listener2));
        $this->assertTrue($this->instance->has($listener3));

        $this->instance->remove($listener1);

        $this->assertFalse($this->instance->has($listener1));
        $this->assertTrue($this->instance->has($listener2));
        $this->assertTrue($this->instance->has($listener3));

        $this->instance->remove($listener2);
        $this->instance->remove($listener3);

        $this->assertFalse($this->instance->has($listener1));
        $this->assertFalse($this->instance->has($listener2));
        $this->assertFalse($this->instance->has($listener3));
    }

    /**
     * Test the getAll method.
     * All listeners with the same priority must be sorted in the order
     * they were added.
     *
     * @return  void
     *
     * @since   2.0
     */
    public function testGetAll()
    {
        $this->assertEmpty($this->instance->getAll());

        $listener0 = new EmptyListener();
        $listener1 = new EmptyListener();
        $listener2 = new EmptyListener();

        $listener3 = function () {
        };

        $listener4 = new EmptyListener();
        $listener5 = new EmptyListener();

        $listener6 = function () {
        };

        $listener7 = new EmptyListener();

        $listener8 = function () {
        };

        $listener9 = new EmptyListener();

        $this->instance->add($listener0, 10);
        $this->instance->add($listener1, 3);
        $this->instance->add($listener2, 3);
        $this->instance->add($listener3, 3);
        $this->instance->add($listener4, 3);
        $this->instance->add($listener5, 2);
        $this->instance->add($listener6, 2);
        $this->instance->add($listener7, 2);
        $this->instance->add($listener8, 0);
        $this->instance->add($listener9, -10);

        $listeners = $this->instance->getAll();

        $this->assertSame($listeners[0], $listener0);
        $this->assertSame($listeners[1], $listener1);
        $this->assertSame($listeners[2], $listener2);
        $this->assertSame($listeners[3], $listener3);
        $this->assertSame($listeners[4], $listener4);
        $this->assertSame($listeners[5], $listener5);
        $this->assertSame($listeners[6], $listener6);
        $this->assertSame($listeners[7], $listener7);
        $this->assertSame($listeners[8], $listener8);
        $this->assertSame($listeners[9], $listener9);
    }

    /**
     * Test the getIterator method.
     *
     * @return  void
     *
     * @since   2.0
     */
    public function testGetIterator()
    {
        $listener0 = new EmptyListener();
        $listener1 = new EmptyListener();
        $listener2 = new EmptyListener();

        $listener3 = function () {
        };

        $listener4 = new EmptyListener();
        $listener5 = new EmptyListener();

        $listener6 = function () {
        };

        $listener7 = new EmptyListener();

        $listener8 = function () {
        };

        $listener9 = new EmptyListener();

        $this->instance->add($listener0, 10);
        $this->instance->add($listener1, 3);
        $this->instance->add($listener2, 3);
        $this->instance->add($listener3, 3);
        $this->instance->add($listener4, 3);
        $this->instance->add($listener5, 2);
        $this->instance->add($listener6, 2);
        $this->instance->add($listener7, 2);
        $this->instance->add($listener8, 0);
        $this->instance->add($listener9, -10);

        $listeners = [];

        foreach ($this->instance as $listener) {
            $listeners[] = $listener;
        }

        $this->assertSame($listeners[0], $listener0);
        $this->assertSame($listeners[1], $listener1);
        $this->assertSame($listeners[2], $listener2);
        $this->assertSame($listeners[3], $listener3);
        $this->assertSame($listeners[4], $listener4);
        $this->assertSame($listeners[5], $listener5);
        $this->assertSame($listeners[6], $listener6);
        $this->assertSame($listeners[7], $listener7);
        $this->assertSame($listeners[8], $listener8);
        $this->assertSame($listeners[9], $listener9);
    }

    /**
     * Test that ListenersPriorityQueue is not a heap.
     *
     * @return  void
     *
     * @since   2.0
     */
    public function testGetIteratorMultipleIterations()
    {
        $listener0 = new EmptyListener();
        $listener1 = new EmptyListener();
        $listener2 = new EmptyListener();

        $this->instance->add($listener0, 0);
        $this->instance->add($listener1, 1);
        $this->instance->add($listener2, 2);

        $firstListeners = [];

        foreach ($this->instance as $listener) {
            $firstListeners[] = $listener;
        }

        $secondListeners = [];

        foreach ($this->instance as $listener) {
            $secondListeners[] = $listener;
        }

        $this->assertSame($firstListeners, $secondListeners);
    }

    /**
     * Test the count method.
     *
     * @return  void
     *
     * @since   2.0
     */
    public function testCount()
    {
        $this->assertCount(0, $this->instance);

        $listener1 = new EmptyListener();
        $listener2 = new EmptyListener();

        $this->instance->add($listener1, 0);
        $this->instance->add($listener2, 0);

        $this->assertCount(2, $this->instance);
    }

    /**
     * Sets up the fixture.
     *
     * This method is called before a test is executed.
     *
     * @return  void
     *
     * @since   2.0
     */
    protected function setUp(): void
    {
        $this->instance = new ListenersQueue();
    }
}
