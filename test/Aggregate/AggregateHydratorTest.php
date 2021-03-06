<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace ZendTest\Hydrator\Aggregate;

use PHPUnit_Framework_TestCase;
use stdClass;
use Zend\Hydrator\Aggregate\AggregateHydrator;

/**
 * Unit tests for {@see AggregateHydrator}
 */
class AggregateHydratorTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var AggregateHydrator
     */
    protected $hydrator;

    /**
     * @var \Zend\EventManager\EventManagerInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $eventManager;

    /**
     * {@inheritDoc}
     */
    public function setUp()
    {
        $this->eventManager = $this->getMock('Zend\EventManager\EventManagerInterface');
        $this->hydrator     = new AggregateHydrator();

        $this->hydrator->setEventManager($this->eventManager);
    }

    /**
     * @covers \Zend\Hydrator\Aggregate\AggregateHydrator::add
     */
    public function testAdd()
    {
        $attached = $this->getMock('Zend\Hydrator\HydratorInterface');

        $this
            ->eventManager
            ->expects($this->once())
            ->method('attachAggregate')
            ->with($this->isInstanceOf('Zend\Hydrator\Aggregate\HydratorListener'), 123);

        $this->hydrator->add($attached, 123);
    }

    /**
     * @covers \Zend\Hydrator\Aggregate\AggregateHydrator::hydrate
     */
    public function testHydrate()
    {
        $object = new stdClass();

        $this
            ->eventManager
            ->expects($this->once())
            ->method('trigger')
            ->with($this->isInstanceOf('Zend\Hydrator\Aggregate\HydrateEvent'));

        $this->assertSame($object, $this->hydrator->hydrate(['foo' => 'bar'], $object));
    }

    /**
     * @covers \Zend\Hydrator\Aggregate\AggregateHydrator::extract
     */
    public function testExtract()
    {
        $object = new stdClass();

        $this
            ->eventManager
            ->expects($this->once())
            ->method('trigger')
            ->with($this->isInstanceOf('Zend\Hydrator\Aggregate\ExtractEvent'));

        $this->assertSame([], $this->hydrator->extract($object));
    }

    /**
     * @covers \Zend\Hydrator\Aggregate\AggregateHydrator::getEventManager
     * @covers \Zend\Hydrator\Aggregate\AggregateHydrator::setEventManager
     */
    public function testGetSetManager()
    {
        $hydrator     = new AggregateHydrator();
        $eventManager = $this->getMock('Zend\EventManager\EventManagerInterface');

        $this->assertInstanceOf('Zend\EventManager\EventManagerInterface', $hydrator->getEventManager());

        $eventManager
            ->expects($this->once())
            ->method('setIdentifiers')
            ->with(
                [
                     'Zend\Hydrator\Aggregate\AggregateHydrator',
                     'Zend\Hydrator\Aggregate\AggregateHydrator',
                ]
            );

        $hydrator->setEventManager($eventManager);

        $this->assertSame($eventManager, $hydrator->getEventManager());
    }
}
