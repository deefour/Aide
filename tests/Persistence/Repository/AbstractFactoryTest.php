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
   * @expectedExceptionMessage does not match an existing entity class
   */
  public function testDeriveModelFromInvalidEntityString() {
    $factory = m::mock('Deefour\Aide\Persistence\Repository\AbstractFactory')->makePartial();

    $factory->create('NonExistentEntity');
  }

  /**
   * @expectedException \LogicException
   * @expectedExceptionMessage does not implement the
   */
  public function testDerivedModelFromNonEntityString() {
    $factory = m::mock('Deefour\Aide\Persistence\Repository\AbstractFactory')->makePartial();

    $factory->create('\\Eloquent\\TestDummy');
  }

  /**
   * @expectedException \LogicException
   * @expectedExceptionMessage could not derive the model class name
   */
  public function testDerivedModelFromEntityWithoutMatchingModel() {
    $factory = m::mock('Deefour\Aide\Persistence\Repository\AbstractFactory')->makePartial();

    $factory->create('OrphanTestDummy');
  }



  /**
   * @expectedException \LogicException
   * @expectedExceptionMessage could not derive the repository class name
   */
  public function testDerivedRepositoryFromEntityWithoutMatchingRepository() {
    $factory = m::mock('Deefour\Aide\Persistence\Repository\AbstractFactory')->makePartial();

    $factory->create('RepositorylessEntityModel');
  }


}