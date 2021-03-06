<?php namespace Deefour\Aide\Validation;

use TestDummy;
use TestNewsArticle;
use TestCase;
use Deefour\Aide\Validation\IlluminateValidator as Validator;
use Symfony\Component\Translation\Translator;
use Symfony\Component\Translation\MessageSelector;
use Illuminate\Validation\Factory;



class IlluminateValidatorTest extends TestCase {

  public function setUp() {
    $this->validator = $this->createValidator();
  }

  public function testConstruction() {
    $this->assertInstanceOf('\\Deefour\Aide\Validation\\IlluminateValidator', $this->validator);
  }

  public function testValidation() {
    list($invalidTestDummy, $validTestDummy) = $this->getStubs();

    $this->validator->setEntity($invalidTestDummy);

    $this->assertFalse($this->validator->isValid());
    $this->assertCount(2, $this->validator->getErrors()); // last name missing; bad email format

    $this->validator->setEntity($validTestDummy);

    $this->assertTrue($this->validator->isValid());
    $this->assertEmpty($this->validator->getErrors());
  }

  public function testGetValidator() {
    $this->assertInstanceOf('Illuminate\Validation\Factory', $this->validator->getValidator());
  }

  public function testCallToErrorsTriggersValidationIfNotYetValidated() {
    list($invalidTestDummy, $validTestDummy) = $this->getStubs();

    $this->validator->setEntity($invalidTestDummy);

    $this->assertNotEmpty($this->validator->getErrors());

    $this->validator->setEntity($validTestDummy);

    $this->assertEmpty($this->validator->getErrors());
  }



  protected function createValidator() {
    $translator          = new Translator('en', new MessageSelector);
    $illuminateValidator = new Factory($translator);

    return new Validator($illuminateValidator);
  }

  protected function getStubs() {
    $invalidTestDummy = new TestDummy(array(
      'first_name' => 'Jason',
      'email'      => 'bad_email'
    ));

    $validTestDummy = new TestDummy(array(
      'first_name' => 'Jason',
      'last_name'  => 'Daly',
      'email'      => 'jason@deefour.me'
    ));

    return [ $invalidTestDummy, $validTestDummy ];
  }

}
