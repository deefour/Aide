<?php namespace Deefour\Aide\Validation;

use TestDummy;
use TestNewsArticle;
use TestCase;
use Deefour\Aide\Validation\IlluminateValidator as Validator;
use Symfony\Component\Translation\Translator;
use Symfony\Component\Translation\MessageSelector;
use Illuminate\Validation\Factory;


class AbstractValidatorTest extends TestCase {

  public function setUp() {
    $this->validator = $this->createValidator();
  }

  public function testSetGetEntity() {
    $testDummy = new TestDummy;

    $this->assertNull($this->validator->getEntity());

    $this->validator->setEntity($testDummy);

    $this->assertEquals($testDummy, $this->validator->getEntity());
  }

  public function testMake() {
    $testDummy = new TestDummy;

    $this->validator->make($testDummy, [ 'some' => 'context' ]);

    $this->assertEquals($testDummy, $this->validator->getEntity());
    $this->assertArrayHasKey('some', $this->validator->getContext());
  }

  public function testCallbackRule() {
    $goodNewsArticle = new TestNewsArticle(array(
      'title' => 'A news article',
      'slug'  => 'good-slug',
    ));

    $badNewsArticle = new TestNewsArticle(array(
      'title' => 'A news article',
      'slug'  => 'badslug',
    ));

    $this->validator->setEntity($goodNewsArticle);

    $this->assertTrue($this->validator->isValid());


    $this->validator->setEntity($badNewsArticle);

    $this->assertFalse($this->validator->isValid());
    $this->assertArrayHasKey('slug-has-hyphen', $this->validator->getErrors());
  }

  public function testContext() {
    $context = [ 'some' => 'context' ];
    $result  = $this->validator->setContext($context);

    $this->assertSame($this->validator, $result);
    $this->assertEquals($context, $this->validator->getContext());

    $this->validator->setContext([ 'new' => 'context' ]);

    $this->assertArrayHasKey('new', $this->validator->getContext());
    $this->assertCount(1, $this->validator->getContext());
  }

  /**
   * @expectedException \Exception
   * @expectedExceptionMessage There is nothing to validate
   */
  public function testIsValidExceptionWhenNoEntitySet() {
    $this->validator->isValid();
  }

  public function testContextAttributes() {
    $this->assertEmpty($this->validator->getContextAttributes());

    $context = [ 'some' => 'context' ];
    $result  = $this->validator->setContextAttributes($context);

    $this->assertArrayHasKey('some', $this->validator->getContextAttributes());
    $this->assertArrayHasKey('some', $this->validator->getContext()['attributes']);
  }



  protected function createValidator() {
    $translator          = new Translator('en', new MessageSelector);
    $illuminateValidator = new Factory($translator);

    return new Validator($illuminateValidator);
  }

}
