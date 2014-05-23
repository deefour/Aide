<?php namespace Deefour\Aide\Persistence\Repository\Csv;

use Deefour\Aide\TestCase;



class CsvFactoryTest extends TestCase {

  public function testStaticCreation() {
    $repository = Factory::create(new \TestDummy);

    $this->assertInstanceOf('\\Csv\\TestDummyRepository', $repository);
  }

  public function testInstanceCreation() {
    $factory    = new Factory;
    $repository = $factory->create(new \TestDummy);

    $this->assertInstanceOf('\\Csv\\TestDummyRepository', $repository);
  }

  /**
   * @expectedException \LogicException
   * @expectedExceptionMessage could not derive
   */
  public function testDerivedRepositoryException() {
    $factory = new Factory;

    $factory->create(new \OrphanTestDummy);
  }
}