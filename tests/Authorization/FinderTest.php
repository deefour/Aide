<?php namespace Deefour\Aide\Authorization;

use TestArticle;
use TestNewsArticle;
use TestDummy;
use TestCase;



class FinderTest extends TestCase {

  public function testScope() {
    $finder = new Finder(new TestArticle);
    $this->assertEquals('TestArticleScope', $finder->scope());
  }

  public function testPolicy() {
    $finder = new Finder(new TestArticle);
    $this->assertEquals('TestArticlePolicy', $finder->policy());

    $finder = new Finder(new TestDummy);
    $this->assertEquals('DummyPolicy', $finder->policy());

    $finder = new Finder(new TestNewsArticle);
    $this->assertEquals('TestArticlePolicy', $finder->policy());

    $finder = new Finder(new \Csv\TestArticle);
    $this->assertEquals('Csv\\TestArticlePolicy', $finder->policy());
  }

  /**
   * @expectedException Deefour\Aide\Authorization\NotDefinedException
   */
  public function testScopeOrFail() {
    $finder = new Finder(new TestArticle);
    $this->assertEquals('TestArticlePolicy', $finder->policyOrFail());

    $finder = new Finder(new \Csv\TestArticle);
    $finder->scopeOrFail();
  }

  /**
   * @expectedException Deefour\Aide\Authorization\NotDefinedException
   */
  public function testPolicyOrFail() {
    $finder = new Finder(new TestArticle);
    $this->assertEquals('TestArticleScope', $finder->scopeOrFail());

    $finder = new Finder(new \Csv\TestArticle);
    $finder->policyOrFail();
  }

}
