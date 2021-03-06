<?php namespace Deefour\Aide\Validation;

use \Deefour\Aide\Validation\ValidatableInterface;

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
   * The error messages, keyed by the attribute they belong to.
   *
   * @protected
   * @var array
   */
  protected $errors = [];

  /**
   * Whether or not the validation against the currently bound `$entity` has been
   * performed.
   *
   * @protected
   * @var boolean
   */
  protected $hasBeenValidated = false;



  /**
   * {@inheritdoc}
   */
  public function make(ValidatableInterface $entity, array $context = []) {
    return $this->reset($entity, $context);
  }

  /**
   * {@inheritdoc}
   */
  public function getEntity() {
    return $this->entity;
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
   * Pulls the key/value pairs bound to the `'attributes'` key in the `$context`.
   * This contains form fields/attributes that are _not_ attributes on the model
   * but which the validator relies on for validation.
   *
   * For example, the `IlluminateValidator` supports a `'confirmed'` rule. Given
   * a `'password'` attribute, the validator will look for a `'password_confirmed'`
   * attribute within the same list of data/attributes passed ot the valiator.
   *
   * When validation occurs, these context attributes are appended onto the entity's
   * list of attributes before sending the data into the validation class.
   *
   * @link http://laravel.com/docs/validation#rule-confirmed
   * @return array
   */
  public function getContextAttributes() {
    return array_key_exists('attributes', $this->context) ? $this->context['attributes'] : [];
  }

  /**
   * {@inheritdoc}
   */
  public function getErrors() {
    if ( ! $this->hasBeenValidated) {
      $this->isValid();
    }

    $messages = [];

    foreach ($this->errors as $field => $errors) {
      foreach ($errors as $error) {
        $message = $this->getErrorMessage($field, $error);
        $messages[$field][] = $message;
      }
    }

    return $messages;
  }

  /**
   * Accessor for the current raw validator instance
   *
   * @return mixed
   */
  public function getValidator() {
    return $this->validator;
  }

  /**
   * Boolean check whether the entity is valid or not
   */
  public function isValid() {
    if (is_null($this->getEntity())) {
      throw new \Exception('There is nothing to validate. No entity is currently bound to the validator.');
    }

    $this->validate();
    $this->hasBeenValidated = true;

    $errors = $this->getErrors();

    return empty($errors);
  }

  /**
   * {@inheritdoc}
   */
  public function setEntity(ValidatableInterface $entity) {
    $this->reset($entity, $this->getContext());

    return $this;
  }

  /**
   * Set context for the validator to be passed onto the entity
   *
   * @param  array  $context
   * @return \Deefour\Aide\Validation\ValidatorInterface
   */
  public function setContext(array $context) {
    $this->reset($this->getEntity(), $context);

    return $this;
  }

  /**
   * Sets the context attributes to be merged with entity attributes before validation
   * occurs
   *
   * @see  getContextAttributes
   * @param  $attributes  array
   */
  public function setContextAttributes(array $attributes) {
    $this->context['attributes'] = $attributes;
  }



  /**
   * Resets the current validation class instance. This includes
   *
   *  - flushing the bound `$entity`
   *  - flushing any previously stored errors
   *  - flushing any previously stored context
   *  - marking the class as having never had `isValid()` called
   */
  protected function reset(ValidatableInterface $entity = null, array $context) {
    $this->hasBeenValidated = false;
    $this->errors           = [];
    $this->entity           = $entity;
    $this->context          = $context;

    return $this;
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

}
