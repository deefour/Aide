<?php namespace Deefour\Aide\Validation;

/**
 * Validation abstraction for the Illuminate/Validation library
 */
class IlluminateValidator extends AbstractValidator implements ValidatorInterface {

  /**
   * The Illuminate\Validation validator factory instance
   *
   * @var \Illuminate\Validation\Factory
   */
  protected $validator;



  public function __construct(\Illuminate\Validation\Factory $validator) {
    $this->validator = $validator;
  }

  /**
   * {@inheritdoc}
   */
  public function isValid() {
    $data      = $this->entity->getAttributes();
    $rules     = $this->entity->validations($this->getContext());
    $validator = $this->validator->make($data, $rules);
    $isValid   = $validator->passes();

    $this->errors = $validator->messages() ?: [];

    return $isValid;
  }

  /**
   * {@inheritdoc}
   */
  public function errors() {
    if (is_null($this->errors)) {
      $this->isValid();
    }

    $fields   = $this->errors->getMessages();
    $messages = [];
    $prefix   = strtolower(get_class($this->entity));

    foreach ($fields as $field => $errors) {
      foreach ($errors as $error) {
        $message = $this->getErrorMessage($field, $error);
        $messages[$field][] = $message;
      }
    }

    return empty($messages) ? false : $messages;
  }



  /**
   * {@inheritdoc}
   */
  protected function getErrorMessage($fieldName, $error) {
    $error = str_replace('validation.', '', $error);

    return parent::getErrorMessage($fieldName, $error);
  }

}
