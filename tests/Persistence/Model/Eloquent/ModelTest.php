<?php namespace Deefour\Aide\Persistence\Model\Eloquent;

use Deefour\Aide\TestCase;
use Eloquent\TestDummy;

class ModelTest extends TestCase {

  public function testIsEloquentBacked() {
    $dummy = new TestDummy;

    $this->assertInstanceOf('\Illuminate\Database\Eloquent\Model', $dummy);
    $this->assertCount(3, $dummy->getDates());
  }

}