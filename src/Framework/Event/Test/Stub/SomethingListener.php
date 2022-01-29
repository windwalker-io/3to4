<?php declare(strict_types=1);
/**
 * @copyright  Copyright (C) 2019 LYRASOFT Source Matters.
 * @license    LGPL-2.0-or-later.txt
 */

namespace Windwalker\Legacy\Event\Test\Stub;

use Windwalker\Legacy\Event\Event;

/**
 * A listener listening to some events.
 *
 * @since  2.0
 * @deprecated Legacy code
 */
class SomethingListener
{
    /**
     * Listen to onBeforeSomething.
     *
     * @param   Event $event The event.
     *
     * @return  void
     *
     * @since   2.0
     */
    public function onBeforeSomething(Event $event)
    {
    }

    /**
     * Listen to onSomething.
     *
     * @param   Event $event The event.
     *
     * @return  void
     *
     * @since   2.0
     */
    public function onSomething(Event $event)
    {
    }

    /**
     * Listen to onAfterSomething.
     *
     * @param   Event $event The event.
     *
     * @return  void
     *
     * @since   2.0
     */
    public function onAfterSomething(Event $event)
    {
    }
}
