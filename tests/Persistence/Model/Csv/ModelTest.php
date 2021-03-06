<?php namespace Deefour\Aide\Persistence\Model\Csv;

use TestCase;
use Csv\TestArticle;

class ModelTest extends TestCase {

  public function setUp() {
    $this->dummy = new TestArticle;
  }

  public function testConstruction() {
    $this->dummy = new TestArticle([ 'title' => 'A Test Dummy!' ]);

    $this->assertEquals('A Test Dummy!', $this->dummy->getAttribute('title'));
    $this->assertNotNull($this->dummy->getAttribute('id'));
    $this->assertFalse($this->dummy->exists);

    $this->dummy = new TestArticle([ 'title' => 'A Test Dummy!', 'id' => '234a' ]);

    $this->assertEquals('A Test Dummy!', $this->dummy->getAttribute('title'));
    $this->assertEquals('234a', $this->dummy->getAttribute('id'));
    $this->assertTrue($this->dummy->exists);
  }

  public function testMagicSetter() {
    $this->dummy->title = 'A new title';

    $this->assertEquals('A new title', $this->dummy->getAttribute('title'));

    $this->dummy->non_column_attribute = 'Non-Column Attribute';

    $this->assertNull($this->dummy->getAttribute('non_column_attribute'));
    $this->assertEquals('Non-Column Attribute', $this->dummy->non_column_attribute);

    $this->assertArrayNotHasKey('non_column_attribute', $this->dummy->toArray());
  }

  public function testGetters() {
    $this->dummy->title = 'A new title';
    $this->dummy->non_column_attribute = 'Non-Column Attribute';

    $this->assertEquals('A new title', $this->dummy->title);
    $this->assertEquals('A new title', $this->dummy->getAttribute('title'));
    $this->assertEquals('Non-Column Attribute', $this->dummy->non_column_attribute);
    $this->assertNull($this->dummy->getAttribute('non_column_attribute'));
  }

  public function testToFromArray() {
    $this->dummy->fromArray([ 'title' => 'A new title' ]);

    $this->assertEquals('A new title', $this->dummy->getAttribute('title'));

    $originalID = $this->dummy->id;

    $this->dummy->fromArray([ 'id' => '123a', 'title' => 'Another title', 'non_column_attribute' => 'Mmm' ]);

    $this->assertEquals('123a', $this->dummy->getId('id')); // allow id to be changed like any other attribute
    $this->assertEquals('Another title', $this->dummy->getAttribute('title'));
  }

  public function testToArray() {
    $this->assertArrayHasKey('id', array_filter($this->dummy->toArray()));

    $this->dummy->fromArray([ 'title' => 'A new title' ]);

    $this->assertEquals('A new title', $this->dummy->toArray()['title']);

    $originalID = $this->dummy->id;

    $this->dummy->fromArray([ 'id' => '123a', 'title' => 'Another title', 'non_column_attribute' => 'Mmm' ]);

    $this->assertEquals('123a', $this->dummy->toArray()['id']);
    $this->assertEquals('Another title', $this->dummy->toArray()['title']);
    $this->assertArrayNotHasKey('non_column_attribute', $this->dummy->toArray());
    $this->assertNull($this->dummy->non_column_attribute);
  }

  public function testSetAttributes() {
    $this->dummy->setAttributes([ 'title' => 'Another title', 'teaser' => 'Attention grabber here', ]);

    $this->assertEquals('Another title', $this->dummy->title);
  }

  public function testSetAttribute() {
    $this->assertFalse($this->dummy->setAttribute('invalid', 1));
    $this->assertNull($this->dummy->invalid);
    $this->assertTrue($this->dummy->setAttribute('title', 'NEW TITLE'));
    $this->assertEquals('NEW TITLE', $this->dummy->title);
    $this->assertTrue($this->dummy->setAttribute('id', '456789'));
    $this->assertEquals('456789', $this->dummy->getId());
  }

  public function testArrayAccess() {
    $this->dummy = new TestArticle([ 'title' => 'A new title' ]);

    $this->assertTrue(isset($this->dummy['id']));
    $this->assertFalse(isset($this->dummy['bad_attribute']));

    $this->assertEquals('A new title', $this->dummy['title']);

    $this->dummy['title'] = 'New title';

    $this->assertEquals('New title', $this->dummy->title);

    unset($this->dummy['title']);

    $this->assertNull($this->dummy->title);
    $this->assertArrayHasKey('title', $this->dummy->toArray());
  }

  /**
   * @expectedException InvalidArgumentException
   * @expectedExceptionMessage not a valid attribute
   */
  public function testInvalidOffsetGet() {
    $this->dummy['bad-attribute'];
  }

  /**
   * @expectedException InvalidArgumentException
   * @expectedExceptionMessage not a valid attribute
   */
  public function testInvalidOffsetSet() {
    $this->dummy['bad-attribute'] = 'mmm';
  }

  public function testIsColumn() {
    $this->assertTrue($this->dummy->isColumn('id'));
    $this->assertTrue($this->dummy->isColumn('title'));
    $this->assertFalse($this->dummy->isColumn('bad_column'));

    $this->dummy->non_column_attribute = 'Mmm';

    $this->assertFalse($this->dummy->isColumn('non_column_attribute'));
  }

}
