<?php namespace Deefour\Aide\Persistence\Entity;

use TestCase;

class UserTest extends TestCase {

  public function testConstruction() {
    $user = new User(array( 'first_name' => 'Jason' ));

    $this->assertEquals('Jason', $user->first_name);
    $this->assertNull($user->last_name);
    $this->assertObjectHasAttribute('last_name', $user);
  }

}