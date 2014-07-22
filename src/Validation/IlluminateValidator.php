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
  public function validate() {
    $data              = $this->entity->getAttributes();
    $contextAttributes = $this->getContextAttributes();

    $data = array_replace_recursive($data, $contextAttributes);

    list($rules, $callbacks) = $this->parseValidations();
    $validator               = $this->validator->make($data, $rules);
    $isValid                 = $validator->passes();

    foreach ($callbacks as $field => $callback) {
      $error = call_user_func($callback, $this->getContext());

      if (is_string($error)) {
        $isValid = false;
        $validator->messages()->add($field, $error);
      }
    }

    $this->errors = $validator->messages()->getMessages();
  }

}
