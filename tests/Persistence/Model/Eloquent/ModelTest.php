<?php namespace Deefour\Aide\Persistence\Model\Eloquent;

use TestCase;
use Eloquent\TestDummy;
use Eloquent\TestDummyWithoutEntity;

class ModelTest extends TestCase {

  public function testIsEloquentBacked() {
    $dummy = new TestDummy;

    $this->assertInstanceOf('\Illuminate\Database\Eloquent\Model', $dummy);
    $this->assertCount(2, $dummy->getDates());
  }

  public function testFromArray() {
    $model = new TestDummy;

    $model->fromArray([ 'first_name' => 'Jason', 'last_name' => 'Daly' ]);

    $this->assertEquals('Jason', $model->first_name);

    $model->fromArray([ 'first_name' => 'Jase' ], true);

    $this->assertNull($model->last_name);
    $this->assertEquals('Jase', $model->first_name);
  }

  public function testFlush() {
    $model = new TestDummy;

    $model->first_name = 'Jason';

    $model->flush();

    $this->assertNull($model->first_name);
  }

  public function testSetAttributes() {
    $model = new TestDummy;

    $model->setAttributes([ 'first_name' => 'Jason' ]);

    $this->assertEquals('Jason', $model->first_name);
  }

  public function testNewInstance() {
    $model = new TestDummy;

    $model->first_name = 'Jason';

    $newModel = $model->newInstance([ 'last_name' => 'Daly' ], true);

    $this->assertInstanceOf('\Eloquent\TestDummy', $newModel);
    $this->assertNotSame($newModel, $model);

    $this->assertEquals('Jason', $model->first_name);
    $this->assertNull($newModel->first_name);

    $this->assertEquals('Daly', $newModel->last_name);
    $this->assertNull($model->last_name);

    $this->assertFalse($model->exists);
    $this->assertTrue($newModel->exists);
  }

  public function testToEntity() {
    $model = new TestDummy;

    $model->first_name = 'Jason';
    $model->exists     = true;

    $entity = $model->toEntity();

    $this->assertInstanceOf('\\TestDummy', $entity);
    $this->assertEquals('Jason', $entity->first_name);
    $this->assertTrue($entity->exists);
  }

  public function testToEntityOnEntityModel() {
    $model = new \EntityModel;

    $model->first_name = 'Jason';

    $entity = $model->toEntity();

    $this->assertEquals($entity, $model);
    $this->assertEquals('Jason', $entity->first_name);
    $this->assertEquals($model->first_name, $entity->first_name);
  }

  /**
   * @expectedException \LogicException
   * @expectedExceptionMessage Could not derive
   */
  public function testToEntityOnModelWithoutEntity() {
    $model = new TestDummyWithoutEntity;

    $model->toEntity();
  }

}
