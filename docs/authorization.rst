Authorization
=============

Aide provides a lightweight, object-oriented authorization system based heavily on the popular Ruby gem, `Pundit <https://github.com/elabs/pundit>`_.

Policies
--------

At the core of Aide's authorization is the notion of policy classes. Policy classes must extend :class:`Deefour\Aide\Authorization\AbstractPolicy`. Each method should return a boolean. For example

.. code-block:: php

    use Deefour\Aide\Authorization\AbstractPolicy;

    class ArticlePolicy extends AbstractPolicy {

      public function edit() {
        return $this->user->id === $this->record->author_id;
      }

    }

When a policy class is instantiated through Aide, a `$record` to authorize is passed along with a `$user` to authorize against. Using the helper methods provided in :class:`Deefour\Aide\Authorization\PolicyTrait` is optional; you could instantiate a policy and check authorization for the `$user` yourself

.. code-block:: php

    $user    = User::find(1);              // find some User with id = 1
    $article = $user->articles()->first(); // get the first Article authored by the User

    $policy = new ArticlePolicy($user, $article);

    $policy->edit(); // true

Assumptions
^^^^^^^^^^^

When generating a policy class for an object via Aide's helpers, the following assumptions are made.

 1. The policy class has the same name as the object being authorized, suffixed with `"Policy"` *(though this can be overridden)*
 2. The first argument is the user to authorize for the action. When using Aide's helpers, this requires you create a `currentUser` method on the class using :class:`Deefour\Aide\Authorization\PolicyTrait`.
 3. The second argument is the object you wish to check the authorization against.

An Example
----------

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

The `$this->authorize($article);` line will generate a fresh `ArticlePolicy` instance through Aide, passing the current user and the fetched `$article` into it. The `ArticlePolicy::edit()` method will be called, and if `false` is returned, a :class:`Deefour\Aide\Authorization\NotAuthorizedException` will be thrown. This exception can be caught by Laravel with the following in `app/start/global.php`.

.. code-block:: php

    use Deefour\Aide\Authorization\NotAuthorizedException;

    App::error(function(NotAuthorizedException $exception) {
      // Handle the exception...
    });

.. note:: There is nothing Laravel-specific about Aide's authorization component. The :class:`Deefour\Aide\Authorization\PolicyTrait` trait can be used in any class.

Ensuring Policies Are Used
--------------------------

Again using Laravel as an example, an after filter can be configured to prevent accidentally unauthorized actions from being wide open by default. A filter in the constructor of the `ArticleController` could look like this

.. code-block:: php

    public function __construct() {
      $this->afterFilter(function() {
        $this->requireAuthorization();
      }, [ 'except' => 'index' ]);
    }

There is a similar method to ensure a scope is used, which is particularly useful for `index` actions where a collection of objects is rendered and is dependent on each user.

.. code-block:: php

    public function __construct() {
      $this->afterFilter(function() {
        $this->requirePolicyScoped();
      }, [ 'only' => 'index' ]);
    }

Instantiation Without Trait Methods
-----------------------------------



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

