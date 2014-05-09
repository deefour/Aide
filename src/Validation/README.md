## Validation

Aide provides an abstraction for validating entities using 3rd party validation libraries. Out of the box Aide supports Laravel's validator from the [`illuminate/validation` package](https://packagist.org/packages/illuminate/validation). The example below will use the `\Deefour\Aide\Validation\IlluminateValidator`.

### Instantiation

To validate an entity, a new instance of the validator must be created. This typically will be done within some sort of service provider.

```php
use Symfony\Component\Translation\Loader\ArrayLoader;
use Illuminate\Validation\Factory;
use Symfony\Component\Translation\Translator;
use Deefour\Aide\Validation\IlluminateValidator as Validator;

$translator = new Translator(new ArrayLoader, 'en');
$factory    = new Factory($translator);

$validator  = new Validator($factory);
```

### Usage

With this new `$validator` instance, entities can be validated easily.

```php
$entity = new User([ 'first_name' => 'Jason', 'last_name' => 'Daly' ]);

$validator->setEntity($entity);

$validator->isValid(); // boolean whether the entity passes validation rules or not

$validator->errors(); // array, keyed by attribute names, with array values containing list of errors for each attribute
```

The raw validation instance behind the abstraction Aide provides can also be accessed.

```php
$validator->getValidator(); // returns the \Illuminate\Validation\Factory instance
```

### Validation Rules

Part of the `Deefour\Aide\Persistence\Entity\EntityInterface` contract is the following

```php
/**
 * List of rules to use in the validation abstraction layer to ensure all required
 * information has been provided in the expected format.
 *
 * @param  array  $context  [optional]
 * @return array
 */
public function validations(array $context = []);
```

__This requires every entity to define a list of rules to be validated against.__

_**Note:** This is a strict requirement. The `AbstractEntity` all entity classes are to extend defines an implementation of this `validations()` method that will throw a `\BadMethodCallException` in an attempt to prevent the developer from forgetting to set up proper validation rules._

The `User` entity Aide provides contains a simple set of default rules.

```php
public function validations(array $context = []) {
  return [
    'first_name' => [ 'required', 'between:3,30' ],
    'last_name'  => [ 'required', 'between:3,30' ],
    'email'      => [ 'required', 'email' ],
  ];
}
```

The keys match attribute names. The values are arrays of strings matching the format Laravel's validator expects. See the [basic usage](http://laravel.com/docs/validation#basic-usage) for Laravel's Validator to learn more about the above syntax.

#### Context

When there is a need to validate against external data, configuration, etc..., a special context can be built up on the validator. The context is passed into every `validations()` method, and as the 2nd argument to all Closure validation rules.

With the context being passed into the `validations()` method, rules can be conditionally set.

First, set the entity and context on the validator

```php
$user = new User([ 'first_name' => 'Jason', 'email' => 'jason@deefour.me' ]);

$validator->setEntity($user)
          ->setContext([ 'last_name_max' => 20 ]);
```

Then refer to the context and make the validation rule dependent on it's value.

```php
public function validations(array $context = []) {
  $lastNameMax = array_key_exists('last_name_max', $context) ? $context['last_name_max'] : 30;

  return [
    'first_name' => [ 'required', 'between:3,30' ],
    'last_name'  => [ 'required', 'between:3,' . $lastNameMax ],
    'email'      => [ 'required', 'email' ],
  ];
}

```

#### Rule Callbacks

There are times where more complex validation is required for a rule. PHP Closures can be appended to the rules. The current entity bound to the validator will be passed to the Closure.

For example, to do a dns lookup against the domain used for the email address on the `User` entity above, the example could be expanded as follows

```php
public function validations(array $context = []) {
  $rules = [
    'first_name'  => [ 'required', 'between:3,30' ],
    'last_name'   => [ 'required', 'between:3,30' ],
    'email'       => [ 'required', 'email' ],
  ];

  $rules['dns-lookup'] = function($entity) {
    $email  = $entity->email;
    $domain = substr($email, mb_strpos($email, '@'));

    if (dns_get_record($domain) === false) {
      return 'invalid-hostname';
    }
  };

  return $rules;
}
```

The validation Closure will be considered failing if a string is returned. The returned string should match a key for a message template. The Closure rules are not keyed in the validation rules do not have ot be keyed by a specific attribute on the entity. It is important the developer be aware of this, Because the string `'dns-lookup'` does not match any attributes on the entity


### Message Templates

The base `Deefour\Aide\Validation\AbstractValidator` instance has a currently-very-limited-but-growing set of error message templates.

```php
protected $messageTemplates = array(
  'required'       => '%s is required',
  'email'          => '%s must be a valid email address',
  'date'           => '%s is not a valid date',
  'digits_between' => '%s is out of bounds',
);
```

The collection of error messages returned when calling `$validator->errors()` is composed of message templates like those above after having their `sprintf` tokens replaced by data from the validator. This token replacement currently does not leverage translation/localization or other sophisticated message replacement strategies. The single `%s` is replaced with the attribute name related to each error message. An attributes name like `first_name` will be transformed into `first name` by removing the snake case.

#### Entity Message Templates

Any entity can define it's own additional message templates. Since there is no default `'invalid-hostname'` message template defined, it can be defined directly on the `User` entity.

```php
protected $messageTemplates = array(
  'invalid-hostname' => '%s contains an invalid/unknown domain',
);
```

### An Example

Let's look at a full example within the context of a Laravel controller action.

```php
public function update($id) {
  $user      = User::find($id)->toEntity(); // toEntity() is an Aide method
  $input     = Input::get('user');
  $validator = $this->validator;

  $errors = $validator->setEntity($user)->errors();

  if ( ! empty($errors)) {
    // error: invalid data
    return View::make('user.edit', compact('user', 'input', 'errors'));
  }

  // success
  return Redirect::to('home');
}
```