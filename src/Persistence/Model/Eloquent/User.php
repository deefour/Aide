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



  /**
   * {@inheritdoc}
   */
  public function validations(array $context = []) {
    return [
      'first_name' => [ 'required', 'between:3,30' ],
      'last_name'  => [ 'required', 'between:3,30' ],
      'email'      => [ 'required', 'email', 'unique:users' ],
    ];
  }

}