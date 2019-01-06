<?php

/*
 * This file is part of the FerotresRedsysBundle package.
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Ferotres\RedsysBundle\Tests\Event;

use Ferotres\RedsysBundle\Event\RedsysResponseFailedEvent;
use PHPUnit\Framework\TestCase;

/**
 * Class RedsysResponseFailedEventTest.
 */
class RedsysResponseFailedEventTest extends TestCase
{
    /**
     * @test
     */
    public function whenPropertyNotExistReturnNull()
    {
        $event = new RedsysResponseFailedEvent(null, array(), false, null);

        $this->assertNull($event->getProperty());
    }

    /**
     * @test
     */
    public function whenPropertyExistReturnValue()
    {
        $event = new RedsysResponseFailedEvent(null, array('idOrder' => 100), false, null);
        $this->assertSame(100, $event->getIdOrder());
        $this->assertFalse($event->isValidated());
        $this->assertNull($event->exception());
        $this->assertNull($event->redsysResponse());
    }
}
