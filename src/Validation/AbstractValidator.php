<?php namespace Deefour\Aide\Validation;

abstract class AbstractValidator {

  /**
   * Token -> text translations for validation error messages.
   *
   * @protected
   * @var array
   */
  protected $messageTemplates = array(
    'required'       => '%s is required',
    'email'          => '%s must be a valid email address',
    'date'           => '%s is not a valid date',
    'digits_between' => '%s is out of bounds',
  );

  /**
   * A default message to be used if no validation message template is specified
   * for the passed rule
   *
   * @var string
   */
  protected $defaultMessageTemplate = '%s is not valid';

  /**
   * The entity containing the validation rules and which the validation
   * will be performed against
   *
   * @protected
   * @var \Deefour\Aide\Persistence\Entity\EntityInterface
   */
  protected $entity;

  /**
   * Context for the validation. This is passed into the request for validation
   * rules on the entity on the chance the entity's validation is dynamic
   *
   * @protected
   * @var array
   */
  protected $context = [];



  /**
   * {@inheritdoc}
   */
  public function setEntity(\Deefour\Aide\Validation\ValidatableInterface $entity) {
    $this->flushErrors();
    $this->flushContext();

    $this->entity = $entity;

    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getEntity() {
    return $this->entity;
  }

  /**
   * Set context for the validator to be passed onto the entity
   *
   * @param  array  $context
   * @return \Deefour\Aide\Validation\ValidatorInterface
   */
  public function setContext(array $context) {
    $this->context = $context;

    return $this;
  }

  /**
   * Accessor for the current context.
   *
   * @return array
   */
  public function getContext() {
    return $this->context;
  }



  /**
   * Accessor for the `$messageTemplates` variable.
   *
   * @return array
   */
  protected function getMessageTemplates() {
    return array_replace($this->messageTemplates, $this->getEntity()->getMessageTemplates());
  }

  /**
   * Builds a user-friendly error message for a specific field based on an error
   * that occurred
   *
   * @param  string  $fieldName
   * @param  string  $error
   * @return string
   */
  protected function getErrorMessage($fieldName, $error) {
    $templates       = $this->getMessageTemplates();
    $prettyFieldName = preg_replace('/_/', ' ', $fieldName);
    $messageTemplate = array_key_exists($error, $templates) ? $templates[$error] : $this->defaultMessageTemplate;

    return sprintf($messageTemplate, $prettyFieldName);
  }

  /**
   * Clears any previously set errors from an earlier validation
   *
   * @protected
   */
  protected function flushErrors() {
    $this->errors = null;
  }

  /**
   * Clears the current context
   *
   * @protected
   */
  protected function flushContext() {
    $this->context = [];
  }

  /**
   * Parses the validation rules for the current entity set on the validation
   * instance.
   *
   * The return value is an array containing all rule strings for the validator
   * in the first position, and any callbacks in the second position.
   *
   * @protected
   * @return array  [ 'rules', 'callbacks' ]
   */
  protected function parseValidations() {
    $rawValidations = $this->getEntity()->validations($this->getContext());
    $rules          = [];
    $callbacks      = [];

    foreach ($rawValidations as $key => $rawValidation) {
      if ($rawValidation instanceof \Closure) {
        $callbacks[$key] = $rawValidation;

        continue;
      }

      $rules[$key] = $rawValidation;
    }

    return [ $rules, $callbacks ];
  }



  /**
   * Accessor for the current raw validator instance
   *
   * @return mixed
   */
  abstract public function getValidator();

}