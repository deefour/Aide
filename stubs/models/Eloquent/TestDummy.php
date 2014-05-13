<?php namespace Eloquent;

use Deefour\Aide\Persistence\Model\Eloquent\Model;
use Mockery as m;



class TestDummy extends Model {

  public function save(array $options = array()) {
    return $options['__save_result'];
  }

  public function newQuery($excludeDeleted = true) {
    $mock = m::mock('Illuminate\Database\Eloquent\Builder');

    // find
    $mock->shouldReceive('find')->once()->with(1)->andReturn('foo');

    // all
    $mock->shouldReceive('get')->once()->andReturn('foo');

    // delete
    $mock->shouldReceive('where')->once()->with('id', 1)->andReturn($mock);
    $mock->shouldReceive('delete')->once()->andReturn(true);

    return $mock;
  }

}