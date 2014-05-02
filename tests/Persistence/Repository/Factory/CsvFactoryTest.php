<?php namespace Deefour\Aide\Persistence\Repository\Factory;

use Deefour\Aide\TestCase;

class CsvFactoryTest extends TestCase {

  public function testStaticCreation() {
    $repository = CsvFactory::create(new \TestDummy);

    $this->assertInstanceOf('\\Csv\\TestDummyRepository', $repository);
  }

  public function testInstanceCreation() {
    $factory    = new CsvFactory;
    $repository = $factory->create(new \TestDummy);

    $this->assertInstanceOf('\\Csv\\TestDummyRepository', $repository);
  }

}