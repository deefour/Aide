<?php namespace Deefour\Aide\Persistence\Repository\Eloquent;

use Deefour\Aide\TestCase;



class EloquentFactoryTest extends TestCase {

  public function testStaticCreation() {
    $repository = Factory::make(new \TestDummy);

    $this->assertInstanceOf('\\Eloquent\\TestDummyRepository', $repository);
  }

  public function testInstanceCreation() {
    $factory    = new Factory;
    $repository = $factory->make(new \TestDummy);

    $this->assertInstanceOf('\\Eloquent\\TestDummyRepository', $repository);
  }

  public function testCreationFromModel() {
    $factory    = new Factory;
    $repository = $factory->make(new \EntityModel);

    $this->assertInstanceOf('\\Eloquent\\EntityModelRepository', $repository);
  }

}