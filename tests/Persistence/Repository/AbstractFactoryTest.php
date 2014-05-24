<?php namespace Deefour\Aide\Persistence\Repository;

use Deefour\Aide\TestCase;
use Mockery as m;
use TestDummy;



class AbstractFactoryTest extends TestCase {

  /**
   * @expectedException \LogicException
   * @expectedExceptionMessage could not be derived
   */
  public function testDerivedModelException() {
    $entity  = m::mock('Deefour\Aide\Persistence\Entity\EntityInterface');
    $factory = m::mock('Deefour\Aide\Persistence\Repository\AbstractFactory')->makePartial();

    $factory->create($entity);
  }

}