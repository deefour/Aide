<?php namespace Deefour\Aide\Authorization;

use TestArticle;
use TestDummy;
use TestUser;
use Deefour\Aide\TestCase;



class PolicyClassTest extends TestCase {

  /**
   * @expectedException \BadMethodCallException
   */
  public function testBadStaticMethod() {
    Policy::badMethod(new TestUser, new TestArticle);
  }

  public function testPolicyScope() {
    $this->assertEquals('scoped', Policy::scope(new TestUser, new TestArticle));
  }

  public function testPolicy() {
    $this->assertInstanceOf('TestArticlePolicy', Policy::policy(new TestUser, new TestArticle));
  }

  /**
   * @expectedException Deefour\Aide\Authorization\NotDefinedException
   */
  public function testPolicyScopeOrFail() {
    $this->assertEquals('scoped', Policy::scopeOrFail(new TestUser, new TestArticle));

    Policy::scopeOrFail(new TestUser, new TestDummy);
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
   * @expectedException \InvalidArgumentException
   */
  public function testInstanceAuthorize() {
    $policy = new Policy(new TestUser);

    $this->assertTrue($policy->authorize(new TestArticle, 'edit'));

    $this->assertTrue($policy->authorize(new TestArticle));
  }

  /**
   * @expectedException \BadMethodCallException
   */
  public function testInstanceNonApiException() {
    $policy = new Policy(new TestUser);

    $this->assertInstanceOf('TestArticlePolicy', $policy->badMethod(new TestArticle));
  }

  public function testMake() {
    $policy = new Policy(new TestUser);

    $this->assertInstanceOf('TestArticlePolicy', $policy->make(new TestArticle));
  }

}