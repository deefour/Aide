<?php namespace Deefour\Aide\Validation;

use TestDummy;
use Deefour\Aide\TestCase;
use Deefour\Aide\Validation\IlluminateValidator as Validator;
use Symfony\Component\Translation\Translator;
use Symfony\Component\Translation\MessageSelector;

class IlluminateValidatorTest extends TestCase {

  public function setUp() {
    $this->validator = $this->createValidator();
  }

  public function testConstruction() {
    $this->assertInstanceOf('\\Deefour\Aide\Validation\\IlluminateValidator', $this->validator);
  }

  public function testSetGetEntity() {
    $testDummy = new TestDummy;

    $this->assertNull($this->validator->getEntity());

    $this->validator->setEntity($testDummy);

    $this->assertEquals($testDummy, $this->validator->getEntity());
  }

  public function testValidation() {
    $invalidTestDummy = new TestDummy(array(
      'first_name' => 'Jason',
      'email'      => 'bad_email'
    ));

    $validTestDummy = new TestDummy(array(
      'first_name' => 'Jason',
      'last_name'  => 'Daly',
      'email'      => 'jason@deefour.me'
    ));


    $this->validator->setEntity($invalidTestDummy);

    $this->assertFalse($this->validator->isValid());
    $this->assertCount(2, $this->validator->errors()); // last name missing; bad email format


    $this->validator->setEntity($validTestDummy);

    $this->assertTrue($this->validator->isValid());
    $this->assertEmpty($this->validator->errors());
  }



  protected function createValidator() {
    $translator          = new Translator('em', new MessageSelector);
    $illuminateValidator = new \Illuminate\Validation\Factory($translator);

    return new Validator($illuminateValidator);
  }

}
