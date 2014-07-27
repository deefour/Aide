<?php namespace Deefour\Aide\Persistence\Repository;

use TestCase;
use Mockery as m;
use TestDummy;



class AbstractFactoryTest extends TestCase {

  /**
   * @expectedException \LogicException
   * @expectedExceptionMessage could not derive
   */
  public function testDerivedModelException() {
    $entity  = m::mock('Deefour\Aide\Persistence\Entity\EntityInterface');
    $factory = m::mock('Deefour\Aide\Persistence\Repository\AbstractFactory')->makePartial();

    $factory->create($entity);
  }

}