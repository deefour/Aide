<?php namespace Deefour\Aide\Authorization;

use TestArticle;
use TestDummy;
use TestUser;
use Deefour\Aide\TestCase;


class PolicyTest extends TestCase {

  /**
   * @expectedException \BadMethodCallException
   */
  public function testUndefinedPermittedAttributes() {
    $policy = (new Policy(new TestUser))->policy(new TestUser);

    $policy->permittedAttributes();
  }

  public function testPermittedAttributes() {
    $policy = (new Policy(new TestUser))->policy(new TestArticle);

    $this->assertContains('title', $policy->permittedAttributes());
  }

}