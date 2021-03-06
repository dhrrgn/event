<?php

namespace spec\League\Event;

use League\Event\AbstractEvent;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class GeneratorSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('League\Event\Generator');
    }

    function it_should_accept_an_event_object(AbstractEvent $event)
    {
        $this->addEvent($event)->shouldReturn($this);
    }

    function it_should_release_events(AbstractEvent $event)
    {
        $this->releaseEvents()->shouldHaveCount(0);
        $this->addEvent($event);
        $this->releaseEvents()->shouldContain($event);
        $this->releaseEvents()->shouldHaveCount(0);
    }
}
