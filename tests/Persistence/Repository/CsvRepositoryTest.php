<?php namespace Deefour\Aide\Persistence\Repository;

use Deefour\Aide\TestCase;
use Csv\TestArticle;
use Csv\TestArticleRepository;



class CsvRepositoryTest extends TestCase {

  protected $repository;



  public function setUp() {
    $this->repository = new TestArticleRepository(new TestArticle, [ 'path' => sys_get_temp_dir() ]);
  }

  public function tearDown() {
    if ($this->repository and is_file($this->repository->getFilename())) {
      unlink($this->repository->getFilename());
    }
  }



  /**
   * @expectedException \Exception
   * @expectedExceptionMessage not writable
   */
  public function testBadConstruction() {
    $repository = new TestArticleRepository(new TestArticle, [ 'path' => '/some/random/path' ]);

    $this->assertEquals('/some/random/path/testarticle.csv', $repository->getFilename());
  }

  public function testConstruction() {
    $this->assertEquals(sys_get_temp_dir() . '/testarticle.csv', $this->repository->getFilename());
  }

  public function testCreate() {
    $entity = new \TestArticle([
      'title'  => 'Title One',
      'slug'   => 'title-one',
      'teaser' => 'a hook',
      'body'   => 'a longer message',
    ]);

    $this->assertEmpty(file($this->repository->getFilename()));

    $this->repository->save();

    // headers
    $this->assertCount(1, file($this->repository->getFilename()));

    $model = $this->repository->create($entity);

    $this->assertTrue($model->exists);
    $this->assertCount(2, file($this->repository->getFilename()));
  }

  public function testValidUpdate() {
    list($entity1, $entity2, $model1, $model2) = $this->addTwo();

    $entity1->slug = 'an-alternative-slug';

    $model1_updated = $this->repository->update($entity1);

    $this->assertEquals('an-alternative-slug', $entity1->slug);
    $this->assertEquals($entity1->slug, $model1_updated->slug);
    $this->assertEquals($model1_updated->slug, $model1->slug);

    $repository = new TestArticleRepository(new TestArticle, [ 'path' => sys_get_temp_dir() ]);
    $all        = $repository->all();

    $this->assertEquals('an-alternative-slug', reset($all)->slug);
  }

  /**
   * @expectedException \Exception
   * @expectedExceptionMessage does not exist
   */
  public function testFailingUpdateOnNonExistentRecord() {
    $entity = new \TestArticle([
      'title'  => 'Bad Article',
      'slug'   => 'non-existent-article',
      'teaser' => 'a hook',
      'body'   => 'a longer message',
    ]);

    $this->repository->update($entity);
  }

  public function testDelete() {
    list($entity1, $entity2, $model1, $model2) = $this->addTwo();

    $this->assertCount(2, $this->repository->all());

    $repository = new TestArticleRepository(new TestArticle, [ 'path' => sys_get_temp_dir() ]);

    $repository->delete($entity1);

    $this->assertCount(1, $repository->all());
    $this->assertCount(2, file($repository->getFilename()));
  }

  public function testDeleteNonExistentRecord() {
    $entity = new \TestArticle([
      'title'  => 'Bad Article',
      'slug'   => 'non-existent-article',
      'teaser' => 'a hook',
      'body'   => 'a longer message',
    ]);

    $this->assertNull($this->repository->delete($entity));
  }

  public function testFind() {
    list($entity1, $entity2, $model1, $model2) = $this->addTwo();

    $this->assertSame($this->repository->find($model1->id), $model1);
    $this->assertNull($this->repository->find('sssssssss'));
  }

  public function testAll() {
    list($entity1, $entity2, $model1, $model2) = $this->addTwo();

    $all = $this->repository->all();

    $this->assertSame($model1, reset($all));
    $this->assertSame($model2, end($all));
  }



  protected function addTwo() {
    $entity1 = new \TestArticle([
      'title'  => 'Title One',
      'slug'   => 'title-one',
      'teaser' => 'a hook',
      'body'   => 'a longer message',
    ]);

    $entity2 = new \TestArticle([
      'title'  => 'Title Two',
      'slug'   => 'title-two',
      'teaser' => 'a hook 2',
      'body'   => 'a longer message 2',
    ]);

    $model1 = $this->repository->create($entity1);
    $model2 = $this->repository->create($entity2);

    return [ $entity1, $entity2, $model1, $model2 ];
  }

}