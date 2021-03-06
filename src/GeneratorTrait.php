<?php

namespace League\Event;


trait GeneratorTrait
{
    /**
     * @var AbstractEvent[]  $events
     */
    protected $events = [];

    /**
     * Add an event
     *
     * @param   AbstractEvent $event
     * @return  $this
     */
    public function addEvent(AbstractEvent $event)
    {
        $this->events[] = $event;

        return $this;
    }

    /**
     * Release all the added events.
     *
     * @return AbstractEvent[]
     */
    public function releaseEvents()
    {
        $events = $this->events;
        $this->events = [];

        return $events;
    }
} 