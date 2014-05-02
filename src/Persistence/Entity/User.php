<?php namespace Deefour\Aide\Persistence\Entity;

class User extends AbstractEntity {

  /**
   * The first name for the user
   *
   * @var string
   */
  public $first_name;

  /**
   * The last name for the user
   *
   * @var string
   */
  public $last_name;

  /**
   * The email address for the user
   *
   * @var string
   */
  public $email;



  /**
   * {@inheritdoc}
   */
  public function validations(array $context = []) {
    return [
      'first_name'  => [ 'required', 'between:3,30' ],
      'last_name'   => [ 'required', 'between:3,30' ],
      'email'       => [ 'required', 'email' ],
    ];
  }

}