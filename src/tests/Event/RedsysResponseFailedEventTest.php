<?php

namespace Ferotres\RedsysBundle\Tests\Event;

use Ferotres\RedsysBundle\Event\RedsysResponseFailedEvent;
use PHPUnit\Framework\TestCase;

/**
 * Class RedsysResponseFailedEventTest
 * @package Ferotres\RedsysBundle\Tests\Event
 */
class RedsysResponseFailedEventTest extends TestCase
{

    /**
     * @test
     */
    public function whenPropertyNotExistReturnNull()
    {
        $event = new RedsysResponseFailedEvent(null, [], false, null);

        $this->assertNull($event->getProperty());
    }

    /**
     * @test
     */
    public function whenPropertyExistReturnValue()
    {
        $event = new RedsysResponseFailedEvent(null, ['idOrder' => 100], false, null);
        $this->assertEquals(100, $event->getIdOrder());
        $this->assertFalse($event->isValidated());
        $this->assertNull($event->exception());
        $this->assertNull($event->redsysResponse());
    }
}