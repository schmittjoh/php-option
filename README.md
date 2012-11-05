PHP Option Type
===============
This adds an Option type for PHP.

The Option type is intended for cases where you sometimes might return a value (
typically an object), and sometimes you might return no value (typically null)
depending on arguments, or other runtime factors.

Often times, you forget to handle the case where no value is returned because it
is not covered by tests, or because you have added other runtime checks to your
code which might handle it in this specific situation.

Over time, code is refactored, some of these runtime checks might become invalid,
or incomplete, and then you end up with not handling the null case anymore. As a
result, you might sometimes get fatal PHP errors telling you that you called a
method on a non-object. Users might see blank pages, or worse.

With the Option type, a developer will consciously think about both cases (returning
an object, or returning null), and therefore contributes to more robust code. It
also empowers the developer using an API, and avoids extra work, or API bloat for
the API developers.

Installation
============
Installation is super-easy via composer

```
composer require phpoption/phpoption
```

or add it to your composer.json file.


Usage
=====

Using the Option Type in your API
---------------------------------
```php
class MyRepository
{
    public function findSomeEntity($criteria)
    {
        if (null !== $entity = $this->em->find(...)) {
            return new \PHPOption\Some($entity);
        }

        // We use a singleton, for the None case.
        return \PHPOption\None::create();
    }
}
```

Case 1: You always Require an Entity in Calling Code
----------------------------------------------------
```php
$entity = $repo->findSomeEntity(...)->get(); // returns entity, or throws exception
```

Case 2: Fallback to Default Value If Not Available
--------------------------------------------------
```php
$entity = $repo->findSomeEntity(...)->getOrElse(new Entity());

// Or, if you want to lazily create the entity.
$entity = $repo->findSomeEntity(...)->getOrCall(function() {
    return new Entity();
});
```

More Examples
=============

No More Boiler Plate Code
-------------------------
```php
// Before
if (null === $entity = $this->findSomeEntity()) {
    throw new NotFoundException();
}
echo $entity->name;

// After
echo $this->findSomeEntity()->get()->name;
```

No More Control Flow Exceptions
-------------------------------
```php
// Before
try {
    $entity = $this->findSomeEntity();
} catch (NotFoundException $ex) {
    $entity = new Entity();
}

// After
$entity = $this->findSomeEntity()->getOrElse(new Entity());
```

More Concise Null Handling
--------------------------
```php
// Before
$entity = $this->findSomeEntity();
if (null === $entity) {
    return new Entity();
}

return $entity;

// After
return $this->findSomeEntity()->getOrElse(new Entity());
```

Performance Considerations
==========================
Of course, performance is important, and that is why I have attached a
performance benchmark which you can reproduce on a machine of your choosing.

Since we use a new object to wrap the return value, the overhead that is introduced,
by using the Option type equals the creation of that object. In addition, we also
add an additional method call, to retrieve the actual value from the wrapper object
which adds one more method call.

* Overhead: Creation of 1 Object, and 1 Method Call
* Overhead per invocation (some case/value returned): 0.000000761s (that is 761 nano seconds)
* Overhead per invocation (none case/null returned): 0.000000368s (that is 368 nano seconds)

When I first saw these results, I could not really believe that the overhead is
that low, but after checking the benchmark again, everything looks accurate. I
have run these tests under Ubuntu precise, PHP 5.4.6.

So in conclusion, unless you plan to call a method thousands of times during a
request, there is no reason to stick to the ``object|null`` return value; better give
your code some options!