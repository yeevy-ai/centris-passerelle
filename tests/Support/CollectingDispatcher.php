<?php

declare(strict_types=1);

namespace Yeevy\CentrisPasserelle\Tests\Support;

use Psr\EventDispatcher\EventDispatcherInterface;

/**
 * Collects dispatched events for assertions.
 */
class CollectingDispatcher implements EventDispatcherInterface
{
    /** @var list<object> */
    public array $events = [];

    public function dispatch(object $event): object
    {
        $this->events[] = $event;

        return $event;
    }
}
