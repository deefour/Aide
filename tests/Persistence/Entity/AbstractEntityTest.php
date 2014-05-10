<?php namespace Deefour\Aide\Persistence\Entity;

use TestDummy;
use Deefour\Aide\TestCase;

class AbstractEntityTest extends TestCase {

  public function setUp() {
    $this->data = $data = array(
      'first_name' => 'Jason',
      'last_name'  => 'Daly',
      'email'      => 'jason@deefour.me',

      'rejected'   => true,
    );
  }


  public function testConstruction() {
    $user = new TestDummy($this->data);

    $this->assertEquals('Jason', $user->first_name);
    $this->assertFalse(property_exists($user, 'rejected'));

    $user = new TestDummy;

    $this->assertNull($user->first_name);
  }

  public function testSetAttributes() {
    $user = new TestDummy;

    $user->setAttributes($this->data);

    $this->assertEquals('Jason', $user->first_name);
    $this->assertFalse(property_exists($user, 'rejected'));

    $user->setAttributes(array('first_name' => 'Jase'));

    $this->assertEquals('Jase', $user->first_name);
  }

  public function testFlush() {
    $user = new TestDummy($this->data);

    $user->flush();

    $this->assertNull($user->first_name);
  }

  public function testFromArray() {
    $user = new TestDummy($this->data);

    $user->fromArray([ 'first_name' => 'Jase', 'rejected' => 22 ]);

    $this->assertFalse(property_exists($user, 'rejected'));
    $this->assertEquals('Jase', $user->first_name);
    $this->assertEquals('Daly', $user->last_name);
  }

  public function testFromArrayWithFlush() {
    $user = new TestDummy($this->data);

    $user->fromArray([ 'first_name' => 'Jase' ], true);

    $this->assertEquals('Jase', $user->first_name);
    $this->assertNull($user->last_name);
  }

  public function testToArray() {
    $user = new TestDummy;

    $this->assertEmpty(array_filter($user->toArray()));

    $user->fromArray($this->data);

    $this->assertArrayHasKey('id', $user->toArray());
    $this->assertArrayNotHasKey('exists', $user->toArray());
    $this->assertEquals($this->data['first_name'], $user->toArray()['first_name']);
  }

  /**
   * @expectedException BadMethodCallException
   */
  public function testValidationEnforcement() {
    $user = new TestDummyWithoutValidator;

    $user->validations();
  }

  public function testValidationPresence() {
    $user = new TestDummy;

    $this->assertTrue(is_array($user->validations()));
  }

}






// Stubs
// -----------------------------------------------------------------------------

class TestDummyWithoutValidator extends AbstractEntity {

  public $first_name;
  public $last_name;
  public $email;

}