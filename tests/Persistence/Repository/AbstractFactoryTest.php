<?php namespace Deefour\Aide\Persistence\Repository;

use TestCase;
use Mockery as m;
use TestDummy;



class AbstractFactoryTest extends TestCase {

  protected $factory;



  public function setUp() {
    $this->factory = m::mock('Deefour\Aide\Persistence\Repository\AbstractFactory')->makePartial();
  }



  /**
   * @expectedException \LogicException
   * @expectedExceptionMessage does not match an existing entity class
   */
  public function testDeriveModelFromInvalidEntityString() {
    $this->factory->create('NonExistentEntity');
  }

  /**
   * @expectedException \LogicException
   * @expectedExceptionMessage does not implement the
   */
  public function testDerivedModelFromNonEntityString() {
    $this->factory->create('\\Eloquent\\TestDummy');
  }

  /**
   * @expectedException \LogicException
   * @expectedExceptionMessage could not derive the model class name
   */
  public function testDerivedModelFromEntityWithoutMatchingModel() {
    $this->factory->create('OrphanTestDummy');
  }

  /**
   * @expectedException \LogicException
   * @expectedExceptionMessage could not derive the repository class name
   */
  public function testDerivedRepositoryFromEntityWithoutMatchingRepository() {
    $this->factory->create('RepositorylessEntityModel');
  }

  public function testEntityDerivationFromString() {
    $factory = m::mock('Deefour\Aide\Persistence\Repository\AbstractFactory', [ 'EntityModel' ]);
  }


}
