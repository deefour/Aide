<?php namespace Deefour\Aide\Authorization;

use TestUser;
use TestArticle;
use TestDummy;
use AuthorizationController;
use Deefour\Aide\TestCase;



class PolicyTraitTest extends TestCase {

  public function setUp() {
    $this->user       = new TestUser;
    $this->controller = new AuthorizationController;
  }

  public function testGetPolicyScope() {
    $this->assertEquals('scoped', $this->controller->getScope($this->user, new TestArticle));
  }

  public function testGetPolicy() {
    $this->assertInstanceOf('TestArticlePolicy', $this->controller->getPolicy($this->user, new TestArticle));
  }

  /**
   * @expectedException Deefour\Aide\Authorization\NotDefinedException
   */
  public function testGetPolicyScopeOrFail() {
    $this->assertEquals('scoped', $this->controller->getScopeOrFail($this->user, new TestArticle));

    $this->controller->getScopeOrFail($this->user, new TestDummy);
  }

  /**
   * @expectedException Deefour\Aide\Authorization\NotDefinedException
   */
  public function testGetPolicyOrFail() {
    $this->assertInstanceOf('TestArticlePolicy', $this->controller->getPolicyOrFail($this->user, new TestArticle));

    $this->controller->getPolicyOrFail($this->user, new TestDummy);
  }

  /**
   * @expectedException Deefour\Aide\Authorization\AuthorizationNotPerformedException
   */
  public function testVerifyAuthorizedException() {
    $this->controller->verifyAuthorized();
  }

  /**
   * @expectedException Deefour\Aide\Authorization\ScopingNotPerformedException
   */
  public function testVerifyPolicyScopedException() {
    $this->controller->verifyPolicyScoped();
  }

  public function testVerifyAuthorized() {
    $this->controller->edit();

    $this->assertNull($this->controller->verifyAuthorized());
  }

  public function testVerifyPolicyScoped() {
    $this->controller->edit();

    $this->assertNull($this->controller->verifyPolicyScoped());
  }

  /**
   * @expectedException Deefour\Aide\Authorization\NotAuthorizedException
   */
  public function testAuthorize() {
    $this->assertNull($this->controller->edit());
    $this->assertTrue($this->controller->authorize(new TestArticle, 'edit'));

    // throw exception
    $this->controller->authorize(new TestArticle, 'destroy');
  }

  public function testPolicyScope() {
    $this->assertEquals('scoped', $this->controller->scope(new TestArticle));
  }

  public function testPolicy() {
    $this->assertInstanceOf('TestArticlePolicy', $this->controller->policy(new TestArticle));
  }

}