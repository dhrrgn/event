<?php

namespace League\Event;

use InvalidArgumentException;

class Emitter
{
    /**
     * Add a listener to for an event
     *
     * @param   string  $event  event name
     * @param   ListenerInterface|callable  $listener
     * @return  self
     */
    public function addListener($event, $listener)
    {
        $listener = $this->ensureListener($listener);

        if ( ! isset($this->listeners[$event]))
            $this->listeners[$event] = [];

        $this->listeners[$event][] = $listener;

        return $this;
    }

    /**
     * Add a listener to for an event
     *
     * @param   string  $event  event name
     * @param   ListenerInterface|callable  $listener
     * @return  self
     */
    public function addOneTimeListener($event, $listener)
    {
        $listener = $this->ensureListener($listener);
        $listener = new OneTimeListener($listener);

        return $this->addListener($event, $listener);
    }

    /**
     * Remove a specific listener for an event
     *
     * @param   string  $event  event name
     * @param   ListenerInterface|callable  $listener
     * @return  self
     */
    public function removeListener($event, $listener)
    {
        foreach($this->listeners[$event] as $index => $registered) {
            if ( ! $registered->isListener($listener)) continue;
            unset($this->listeners[$event][$index]);
            break;
        }

        return $this;
    }

    /**
     * Remove all listeners for an event
     *
     * @param   string  $event  event name
     * @param   ListenerInterface|callable  $listener
     * @return  self
     */
    public function removeAllListeners($event)
    {
        if ($this->hasListeners($event)) {
            unset($this->listeners[$event]);
        }

        return $this;
    }

    /**
     * Ensure the input is a listener
     *
     * @param   string  $event  event name
     * @param   ListenerInterface|CallbackListener  $listener
     * @throws  InvalidArgumentException
     * @return  $this
     */
    protected function ensureListener($listener)
    {
        if ($listener instanceof ListenerInterface) {
            return $listener;
        }

        if (is_callable($listener)) {
            return new CallbackListener($listener);
        }

        throw new InvalidArgumentException('Listeners should be be ListenerInterface, Closure or callable. Received type: ' . gettype($listener));
    }

    /**
     * Check weather an event has listeners
     *
     * @return  boolean
     */
    public function hasListeners($event)
    {
        if ( ! isset($this->listeners[$event])) {
            return false;
        }

        if (count($this->listeners[$event]) === 0) {
            return false;
        }

        return true;
    }

    /**
     * Get all the listeners for an event
     *
     * @return  array
     */
    public function getListeners($event)
    {
        if ( ! $this->hasListeners($event)) {
            return [];
        }

        return $this->listeners[$event];
    }

    /**
     * Emit an event
     *
     * @param   string|EventAbstract
     * @return  false|EventAbstract
     */
    public function emit($event)
    {
        // Prepare the event
        $event = $this->ensureEvent($event);
        $name = $event->getName();
        $event->setEmitter($this);

        // Get the listeners
        $listeners = $this->getListeners($name);

        if (empty($listeners)) {
            return false;
        }

        // Prepare the arguments
        $arguments = func_get_args();
        $arguments[0] = $event;

        foreach ($listeners as $listener) {
            call_user_func_array([$listener, 'handle'], $arguments);

            if ($event->isPropagationStopped()) {
                break;
            }
        }

        return $event;
    }

    /**
     * Ensure event input is of type EventAbstract or convert it
     *
     * @param   string|EventAbstract  $event
     * @throws  InvalidArgumentException
     * @return  EventAbstract
     */
    protected function ensureEvent($event)
    {
        if (is_string($event)) {
            return new Event($event);
        }

        if ( ! $event instanceof EventAbstract) {
            throw new InvalidArgumentException('Events should be provides as Event instances or string, received type: ' . gettype($event));
        }

        return $event;
    }
}