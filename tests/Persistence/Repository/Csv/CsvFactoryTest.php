<?php namespace Deefour\Aide\Persistence\Repository\Csv;

use TestCase;



class CsvFactoryTest extends TestCase {

  public function testStaticCreation() {
    $repository = Factory::make(new \TestDummy);

    $this->assertInstanceOf('\\Csv\\TestDummyRepository', $repository);
  }

  public function testInstanceCreation() {
    $factory    = new Factory;
    $repository = $factory->make(new \TestDummy);

    $this->assertInstanceOf('\\Csv\\TestDummyRepository', $repository);
  }

  /**
   * @expectedException \LogicException
   * @expectedExceptionMessage could not derive
   */
  public function testDerivedRepositoryException() {
    $factory = new Factory;

    $factory->make(new \OrphanTestDummy);
  }
}