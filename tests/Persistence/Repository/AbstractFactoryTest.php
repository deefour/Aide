<?php namespace Deefour\Aide\Persistence\Repository;

use TestCase;
use Mockery as m;
use TestDummy;



class AbstractFactoryTest extends TestCase {

  public function testEntityDerivationFromString() {
    $factory = m::mock('Deefour\Aide\Persistence\Repository\AbstractFactory', [ 'EntityModel' ]);
  }

  /**
   * @expectedException \LogicException
   * @expectedExceptionMessage could not derive
   */
  public function testDerivedModelException() {
    $factory = m::mock('Deefour\Aide\Persistence\Repository\AbstractFactory')->makePartial();

    $factory->create('NonExistentEntity');
  }

}