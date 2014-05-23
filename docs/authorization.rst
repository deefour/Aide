Authorization
=============

Aide provides a lightweight, object-oriented authorization system based heavily on the popular Ruby gem, `Pundit <https://github.com/elabs/pundit>`_.

Policies
--------

At the core of Aide's authorization is the notion of policy classes. Policy classes must extend :class:`Deefour\\Aide\\Authorization\\AbstractPolicy`. Each method should return a boolean. For example

.. code-block:: php

    use Deefour\Aide\Authorization\AbstractPolicy;

    class ArticlePolicy extends AbstractPolicy {

      public function edit() {
        return $this->user->id === $this->record->author_id;
      }

    }

When a policy class is instantiated through Aide, a `$record` to authorize is passed along with a `$user` to authorize against. Using the helper methods provided in :class:`Deefour\\Aide\\Authorization\\PolicyTrait` is optional; you could instantiate a policy and check authorization for the `$user` yourself

.. code-block:: php

    $user    = User::find(1);              // find some User with id = 1
    $article = $user->articles()->first(); // get the first Article authored by the User

    $policy = new ArticlePolicy($user, $article);

    $policy->edit(); // true

Assumptions
^^^^^^^^^^^

When generating a policy class for an object via Aide's helpers, the following assumptions are made.

 1. The policy class has the same name as the object being authorized, suffixed with `"Policy"` *(though this can be overridden)*
 2. The first argument is the user to authorize for the action. When using Aide's helpers, this requires you create a `currentUser` method on the class using :class:`Deefour\\Aide\\Authorization\\PolicyTrait`.
 3. The second argument is the object you wish to check the authorization against.

Scopes
------

Aide also provides support for policy scopes. A policy scope will typically generate an iterable collection of objects the current user is able to access

For example, the scope against the `Article` model below will return a collection of only **published** articles unless the current user is an administrator, in which case a collection of **all** articles will be returned.

.. code-block:: php

    use Deefour\Aide\Authorization\AbstractScope;

    class ArticleScope extends AbstractScope {

      public function resolve() {
         if ($this->user->isAdmin()) {
           return $this->scope->all();
         } else {
           return $this->scope->where('published', true)->get();
         }
      }

    }

When a policy scope is instantiated through Aide, the current `$user` and a `$scope` object are passed into the derived policy scope. By default, the policy scope is determined based on the name of the `$scope` object.

.. code-block:: php

    $user        = User::find(1);
    $policyScope = new ArticleScope($user, Article::newQuery());

    $articles    = $policyScope->resolve(); // collection of Articles

Assumptions
^^^^^^^^^^^

When generating a policy scope via Aide's helpers, the following assumptions are made.

 1. The policy scope has the same name as the object being authorized, suffixed with `"Scope"` *(though this can be overridden)*
 2. The first argument is the user to filter the scope for. When using Aide's helpers, this requires you create a `currentUser` method on the class using :class:`Deefour\\Aide\\Authorization\\PolicyTrait`.
 3. The second argument is the scope object you wish to modify based on the state/details of the `$user`.

An Example in the Context of an Application
-------------------------------------------

Using Laravel, the following could be added to the `BaseController`.

.. code-block:: php

    class BaseController extends Controller {

      use Deefour\Aide\Authorization\PolicyTrait;

      protected function currentUser() {
        return Auth::user() ?: new User;
      }

    }

Now, for some `ArticleController`, to authorize the current user against the ability to edit a specific `Article`, the `edit()` method would like this

.. code-block:: php

    public function edit($id) {
      $article = Article::find($id);

      $this->authorize($article); // if NOT authorized, exception will be thrown

      return View::make('articles.edit'); // display the form
    }

The `$this->authorize($article);` line will generate a fresh `ArticlePolicy` instance through Aide, passing the current user and the fetched `$article` into it. The `ArticlePolicy::edit()` method will be called, and if the user is authorized to edit the article, the view for the action will render as expected.

Usage Within Laravel
--------------------

Aide provides a service provider and facade for the `Policy` class to make interacting with it very simple inside of a Laravel application.

Service Provider
^^^^^^^^^^^^^^^^

In Laravel's `app/config/app.php` file, add the class:`Deefour\\Aide\\Authorization\\PolicyServiceProvider` class to the list of providers

.. code-block:: php

    'providers' => array(

       // ...

       'Deefour\Aide\Support\Facades\PolicyServiceProvider',

    ),

    // ...


The IoC container is responsible for instantiating a single, shared instance of the class:`Deefour\\Aide\\Authorization\\Policy` class. This is done outside the scope of a controller method, meaning the IoC container has no access to or knowledge of the  `currentUser` method that may exist within a base controller. Further, the API provided by the `Policy` facade does not expect a user to be passed. This means instead of calling

.. code-block:: php

    $app['policy']->policy(Auth::user(), new Article);

the following is actually correct

.. code-block:: php

    $app['policy']->policy(new Article);

To accomplish this, the service provider looks for configuration in an `app/config/policy.php` file. At a minimum, the following is required when using the policy service provider.

.. code-block:: php

    <?php

    return array(

      'user' => function() {

        return Auth::user() ?: new User; // this logic can be replaced with anything you like.

      },

    );

To keep things DRY, the `currentUser` method in the base controller could be modified to take advantage of this same Closure.

.. code-block:: php

    public function currentUser() {
      return call_user_func(Config::get('policy.user'));
    }

Policy Facade
^^^^^^^^^^^^^

With the service provider in place, the instantiated policy class can be accessed via the main application container.

.. code-block:: php

    $articlePolicy = $app['policy']->policy(new Article);

Remember, you can also instantiate the policy provider yourself.

.. code-block:: php

    use Deefour\Aide\Authorization\Policy;

    $policyProvider = new Policy(Auth::user());
    $articlePolicy  = $policyProvider->policy(new Article);

This can be simplified through the use of a `Policy` facade. Add the following to `app/config/app.php`

.. code-block:: php

    'aliases' => array(

       // ...

       'Policy' => 'Deefour\Aide\Support\Facades\Policy',

    ),

    // ...

The same functionality above is now as simple as this

.. code-block:: php

    $articlePolicy = Policy::policy(new Article);

Policies in Views
^^^^^^^^^^^^^^^^^

The facade makes working with policies in views simple too. For example, to conditionally show an 'Edit' link for a specific `$article` based on the current user's ability to edit that article, the following could be used in a blade template

.. code-block:: php

    @if (Policy::policy($article)->edit())
      <a href="{{ URL::route('articles.edit', [ 'id' => $article->id ]) }}">Edit</a>
    @endif

Handling Unauthorized Exceptions
^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^

If `false` is returned by the `authorize()` call, a :class:`Deefour\\Aide\\Authorization\\NotAuthorizedException` will be thrown. This exception can be caught by Laravel with the following in `app/start/global.php`.

.. code-block:: php

    use Deefour\Aide\Authorization\NotAuthorizedException;

    App::error(function(NotAuthorizedException $exception) {
      // Handle the exception...
    });

.. note:: There is nothing Laravel-specific about Aide's authorization component. The :class:`Deefour\\Aide\\Authorization\\PolicyTrait` trait can be used in any class.

Ensuring Policies Are Used
--------------------------

Again using Laravel as an example, an after filter can be configured to prevent accidentally unauthorized actions from being wide open by default. A filter in the constructor of the `ArticleController` could look like this

.. code-block:: php

    public function __construct() {
      $this->afterFilter(function() {
        $this->verifyAuthorized();
      }, [ 'except' => 'index' ]);
    }

There is a similar method to ensure a scope is used, which is particularly useful for `index` actions where a collection of objects is rendered and is dependent on each user.

.. code-block:: php

    public function __construct() {
      $this->afterFilter(function() {
        $this->requirePolicyScoped();
      }, [ 'only' => 'index' ]);
    }

Policy/Scope Instantiation Without Trait Methods
------------------------------------------------

Policies and scopes can easily be retrieved using static or instance methods on the :class:`Deefour\\Aide\\Authorization\\Policy` class.

Static Instantiation
^^^^^^^^^^^^^^^^^^^^

The following methods are statically exposed:

 - `Policy::policy()`
 - `Policy::policyOrFail()`
 - `Policy::scope()`
 - `Policy::scopeOrFail()`

For example:

.. code-block:: php

  use Deefour\Aide\Authorization\Policy;

  $user    = User::find(1);
  $article = $user->articles()->first();

  Policy::policy($user, $article);
  Policy::policyOrFail($user, $article);

  Policy::scope($user, new Article);
  Policy::scopeOrFail($user, new Article);

The `...OrFail` version of each method will throw a :class:`Deefour\\Aide\\Authorization\\NotDefinedException` exception if the policy class Aide tries to instantiate doesn't exist.

Instance Instantiation
^^^^^^^^^^^^^^^^^^^^^^

A limited version of the above API is available when creating an instance of the `Policy` class.

 - `Policy::policy()`
 - `Policy::scope()`
 - `Policy::authorize()`

.. code-block:: php

  use Deefour\Aide\Authorization\Policy;

  $user    = User::find(1);
  $article = $user->articles()->first();
  $policy  = new Policy($user);

  $policy->policy($article);

  $policy->scope($article);

  $policy->authorize($article, 'edit');

.. note:: The authorize method in this case **requires** an action/method be passed as the second argument.

The `policy()` and `scope()` methods are pass-through's to the `...OrFail()` methods on the `PolicyTrait`; exceptions will be thrown if a policy or scope cannot be found.

Manually Specifying Policy Classes
----------------------------------

The policy class Aide tries to instantiate for an object can be overridden. Given the following scenario

.. code-block:: php

    use Deefour\Aide\Authorization\Policy;

    class ArticlePolicy {}

    class Article { }
    class NewsArticle extends Article { }

    Policy::policyOrFail(new Article);     // returns fresh ArticlePolicy instance
    Policy::policyOrFail(new NewsArticle); // throws Deefour\Aide\Authorization\NotDefinedException

Aide can be instructed to instantiate an :class:`ArticlePolicy` class for the :class:`NewsArticle` through a `policyClass()` method on :class:`Article` *(since :class:`NewsArticle` extends it)*.

.. code-block:: php

    use Deefour\Aide\Authorization\Policy;

    class ArticlePolicy {}

    class Article {

      public function policyClass() {
        return 'ArticlePolicy';
      }

    }
    class NewsArticle extends Article { }

    Policy::policyOrFail(new Article);     // returns fresh ArticlePolicy instance
    Policy::policyOrFail(new NewsArticle); // returns fresh ArticlePolicy instance

Similarly, if a `name()` method is provided on the object, the string returned will be used as the class prefix for the policy class Aide tries to instantiate.

.. code-block:: php

    use Deefour\Aide\Authorization\Policy;

    class PostPolicy {}

    class Article {

      public function name() {
        return 'Post';
      }

    }

    Policy::policyOrFail(new Article); // returns fresh PostPolicy instance

Closed System
-------------

Many apps only allow authenticated users to perform most actions. Instead of verifying on every policy action that the current user is not `null`, unpersisted in the database, or similarly not a legitimately authenticated user, this can be done through a special :class:`ApplicationPolicy` that your other policy classes extend.

.. code-block:: php

    use Deefour\Aide\Authorization\AbstractPolicy;
    use Deefour\Aide\Authorization\NotAuthorizedException;

    class ApplicationPolicy extends AbstractPolicy {

      public function __construct($user, $record) {
        if (is_null($user) or ! $user->exists) {
          throw new NotAuthorizedException;
        }

        parent::__construct($user, $record);
      }

    }

    class ArticlePolicy extends ApplicationPolicy { }

Mass Assignment Protection
--------------------------

A special `permittedAttributes` method can be created on a policy to conditionally provide a whitelist of attributes for a given request by a user to create or modify a record.

.. code-block:: php

    use Deefour\Aide\Authorization\AbstractPolicy;

    class ArticlePolicy extends AbstractPolicy {

      public function permittedAttributes() {
        $attributes = [ 'title', 'body', ];

        // prevent the author and slug from being modified after the article
        // has been persisted to the database.
        if ( ! $this->record->exists) {
          return array_merge($attributes, [ 'user_id', 'slug', ]);
        }

        return $attributes;
      }

    }

This policy method can be used within a controller to filter unauthorized attributes from being set on a model via mass assignment. Again, in a Laravel controller action *(`Repository` below comes from a facade provided by Aide for Laravel)*

.. code-block:: php

    $article    = Article::find(1);
    $repository = Repository::make($article);
    $policy     = Policy::make($article);

    $repository->update(
      $policy->permittedAttributes(Input::get('article'))
    );

