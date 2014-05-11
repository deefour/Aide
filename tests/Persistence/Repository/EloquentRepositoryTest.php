<?php namespace Deefour\Aide\Persistence\Repository;

use Deefour\Aide\TestCase;
use Eloquent\TestDummy;
use Eloquent\TestDummyRepository;
use Mockery as m;



class EloquentRepositoryTest extends TestCase {

  protected $repository;

  protected $testDummy;



  public function setUp() {
    $this->testDummy  = new TestDummy;
    $this->repository = new TestDummyRepository($this->testDummy);
  }

  public function testCreate() {
    $entity = new \TestDummy;

    $this->assertTrue($this->repository->create($entity,  [ '__save_result' => true ]));
    $this->assertFalse($this->repository->create($entity, [ '__save_result' => false ]));
  }

  public function testUpdate() {
    $entity = new \TestDummy;

    $entity->id     = 1;
    $entity->exists = true;

    $this->assertTrue($this->repository->update($entity,  [ '__save_result' => true ]));
    $this->assertFalse($this->repository->update($entity, [ '__save_result' => false ]));
  }

  public function testUsert() {
    $newEntity      = new \TestDummy;
    $existingEntity = new \TestDummy;

    $existingEntity->id     = 1;
    $existingEntity->exists = true;

    $this->assertTrue($this->repository->upsert($newEntity,      [ '__save_result' => true ]));
    $this->assertTrue($this->repository->upsert($existingEntity, [ '__save_result' => true ]));
  }

  public function testDelete() {
    $newEntity      = new \TestDummy;
    $existingEntity = new \TestDummy;

    $existingEntity->id     = 1;
    $existingEntity->exists = true;

    $this->assertTrue($this->repository->delete($newEntity));
    $this->assertTrue($this->repository->delete($existingEntity));
  }

  public function testFind() {
    $entity = new \TestDummy;

    $entity->id     = 1;
    $entity->exists = true;

    $this->assertEquals('foo', $this->repository->find($entity->id));
  }

  public function testAll() {
    $this->assertEquals('foo', $this->repository->all());
  }

}
