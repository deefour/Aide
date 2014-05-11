<?php namespace Deefour\Aide\Persistence\Repository\Factory;

use Deefour\Aide\TestCase;



class EloquentFactoryTest extends TestCase {

  public function testStaticCreation() {
    $repository = EloquentFactory::create(new \TestDummy);

    $this->assertInstanceOf('\\Eloquent\\TestDummyRepository', $repository);
  }

  public function testInstanceCreation() {
    $factory    = new EloquentFactory;
    $repository = $factory->create(new \TestDummy);

    $this->assertInstanceOf('\\Eloquent\\TestDummyRepository', $repository);
  }

  public function testCreationFromModel() {
    $factory    = new EloquentFactory;
    $repository = $factory->create(new \EntityModel);

    $this->assertInstanceOf('\\Eloquent\\EntityModelRepository', $repository);
  }

}