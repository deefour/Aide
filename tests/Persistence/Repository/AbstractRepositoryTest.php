<?php namespace Deefour\Aide\Persistence\Repository;

use TestCase;
use TestArticle;
use Mockery as m;
use Eloquent\TestDummyRepository;


class AbstractRepositoryTest extends TestCase {

  protected $repository;



  public function setUp() {
    $this->repository = m::mock('Deefour\Aide\Persistence\Repository\AbstractRepository[create,update]', [ new \Csv\TestArticle ]);
  }

  public function tearDown() {
    m::close();
  }



  public function testUpsert() {
    $newArticle      = new TestArticle;
    $existingArticle = new TestArticle;

    $existingArticle->exists = true;

    $this->repository->shouldReceive('create')->andReturn('created');
    $this->repository->shouldReceive('update')->andReturn('updated');

    $this->assertEquals('created', $this->repository->upsert($newArticle));
    $this->assertEquals('updated', $this->repository->upsert($existingArticle));
  }

  public function testNewInstance() {
    $this->assertInstanceOf('\\Csv\\TestArticle', $this->repository->newInstance());
  }

  public function testModelDerivationFromCaller() {
    $repository = new TestDummyRepository;

    $this->assertInstanceOf('\\Eloquent\\TestDummy', $repository->newInstance());
  }

}
