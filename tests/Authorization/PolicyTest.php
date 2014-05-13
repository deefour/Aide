<?php namespace Deefour\Aide\Authorization;

use TestArticle;
use TestDummy;
use TestUser;
use Deefour\Aide\TestCase;



class PolicyTest extends TestCase {

  /**
   * @expectedException \BadMethodCallException
   */
  public function testBadStaticMethod() {
    Policy::badMethod(new TestUser, new TestArticle);
  }

  public function testPolicyScope() {
    $this->assertEquals('scoped', Policy::policyScope(new TestUser, new TestArticle));
  }

  public function testPolicy() {
    $this->assertInstanceOf('TestArticlePolicy', Policy::policy(new TestUser, new TestArticle));
  }

  /**
   * @expectedException Deefour\Aide\Authorization\NotDefinedException
   */
  public function testPolicyScopeOrFail() {
    $this->assertEquals('scoped', Policy::policyScopeOrFail(new TestUser, new TestArticle));

    Policy::policyScopeOrFail(new TestUser, new TestDummy);
  }

  /**
   * @expectedException Deefour\Aide\Authorization\NotDefinedException
   */
  public function testPolicyOrFail() {
    $this->assertInstanceOf('TestArticlePolicy', Policy::policyOrFail(new TestUser, new TestArticle));

    Policy::policyOrFail(new TestUser, new TestDummy);
  }

  public function testInstance() {
    $policy = new Policy(new TestUser);

    $this->assertInstanceOf('TestArticlePolicy', $policy->policy(new TestArticle));
  }

  /**
   * @expectedException \BadMethodCallException
   */
  public function testInstanceNonApiException() {
    $policy = new Policy(new TestUser);

    $this->assertInstanceOf('TestArticlePolicy', $policy->badMethod(new TestArticle));
  }

}