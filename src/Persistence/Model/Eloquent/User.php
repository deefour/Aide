<?php namespace Deefour\Aide\Persistence\Model\Eloquent;

class User extends Model {

  /**
   * The database table (via eloquent)
   *
   * @var string
   */
  protected $table = 'users';

  /**
   * List of black-listed attributs for mass-assignment (via eloquent)
   *
   * @var array
   */
  protected $guarded = [ 'id' ];

}