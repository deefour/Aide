<?php namespace Deefour\Aide\Persistence\Entity;

use EntityModel;
use TestCase;

class AbstractModelTest extends TestCase {

  /**
   * @expectedException BadMethodCallException
   */
  public function testValidationEnforcement() {
    $user = new EntityModel;

    $user->validations();
  }

  public function testGetMessageTemplates() {
    $user = new EntityModel;

    $this->assertEmpty($user->getMessageTemplates());
  }

}
