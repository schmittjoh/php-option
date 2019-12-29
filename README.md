# PHP Option Type

This package implements the Option type for PHP!

![Banner](https://user-images.githubusercontent.com/2829600/71564011-3077bf00-2a91-11ea-9083-905702cc262b.png)

<p align="center">
<a href="https://travis-ci.org/schmittjoh/php-option"><img src="https://img.shields.io/travis/schmittjoh/php-option/master.svg?style=flat-square" alt="Build Status"></img></a>
<a href="https://scrutinizer-ci.com/g/schmittjoh/php-option"><img src="https://img.shields.io/scrutinizer/g/schmittjoh/php-option.svg?style=flat-square" alt="Quality Score"></img></a>
<a href="LICENSE"><img src="https://img.shields.io/badge/license-Apache%202.0-brightgreen.svg?style=flat-square" alt="Software License"></img></a>
<a href="https://packagist.org/packages/phpoption/phpoption"><img src="https://img.shields.io/packagist/dt/phpoption/phpoption.svg?style=flat-square" alt="Total Downloads"></img></a>
<a href="https://github.com/schmittjoh/php-option/releases"><img src="https://img.shields.io/github/release/schmittjoh/php-option.svg?style=flat-square" alt="Latest Version"></img></a>
</p>


## Motivation

The Option type is intended for cases where you sometimes might return a value
(typically an object), and sometimes you might return a base value (typically
null) depending on arguments, or other runtime factors.

Often times, you forget to handle the case where a base value should be
returned. Not intentionally of course, but maybe you did not account for all
possible states of the system; or maybe you indeed covered all cases, then time
goes on, code is refactored, some of these your checks might become invalid, or
incomplete. Suddenly, without noticing, the base value case is not handled
anymore. As a result, you might sometimes get fatal PHP errors telling you that
you called a method on a non-object; users might see blank pages, or worse.

On one hand, the Option type forces a developer to consciously think about both
cases (returning a value, or returning a base value). That in itself will
already make your code more robust. On the other hand, the Option type also
allows the API developer to provide more concise API methods, and empowers the
API user in how he consumes these methods.


## Installation

Installation is super-easy via [Composer](https://getcomposer.org/):

```bash
$ composer require phpoption/phpoption
```

or add it by hand to your `composer.json` file.


## Usage

### Using the Option Type in your API

```php
class MyRepository
{
    public function findSomeEntity($criteria)
    {
        if (null !== $entity = $this->em->find(...)) {
            return new \PhpOption\Some($entity);
        }

        // We use a singleton, for the None case.
        return \PhpOption\None::create();
    }
}
```

If you are consuming an existing library, you can also use a shorter version
which by default treats ``null`` as ``None``, and everything else as ``Some`` case:

```php
class MyRepository
{
    public function findSomeEntity($criteria)
    {
        return \PhpOption\Option::fromValue($this->em->find(...));

        // or, if you want to change the none value to false for example:
        return \PhpOption\Option::fromValue($this->em->find(...), false);
    }
}
```

### Case 1: You always Require an Entity in Calling Code

```php
$entity = $repo->findSomeEntity(...)->get(); // returns entity, or throws exception
```

### Case 2: Fallback to Default Value If Not Available

```php
$entity = $repo->findSomeEntity(...)->getOrElse(new Entity());

// Or, if you want to lazily create the entity.
$entity = $repo->findSomeEntity(...)->getOrCall(function() {
    return new Entity();
});
```

## More Examples

### No More Boiler Plate Code

```php
// Before
if (null === $entity = $this->findSomeEntity()) {
    throw new NotFoundException();
}
echo $entity->name;

// After
echo $this->findSomeEntity()->get()->name;
```

### No More Control Flow Exceptions

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

### More Concise Null Handling

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

### Trying Multiple Alternative Options

If you'd like to try multiple alternatives, the ``orElse`` method allows you to
do this very elegantly:

```php
return $this->findSomeEntity()
            ->orElse($this->findSomeOtherEntity())
            ->orElse($this->createEntity());
```
The first option which is non-empty will be returned. This is especially useful 
with lazy-evaluated options, see below.

### Lazy-Evaluated Options

The above example has the flaw that we would need to evaluate all options when
the method is called which creates unnecessary overhead if the first option is 
already non-empty.

Fortunately, we can easily solve this by using the ``LazyOption`` class:

```php
return $this->findSomeEntity()
            ->orElse(new LazyOption(array($this, 'findSomeOtherEntity')))
            ->orElse(new LazyOption(array($this, 'createEntity')));
```

This way, only the options that are necessary will actually be evaluated.

## Performance Considerations

Of course, performance is important. Attached is a performance benchmark which
you can run on a machine of your choosing.

The overhead incurred by the Option type comes down to the time that it takes to
create one object, our wrapper. Also, we need to perform one additional method call
to retrieve the value from the wrapper.

* Overhead: Creation of 1 Object, and 1 Method Call
* Average Overhead per Invocation (some case/value returned): 0.000000761s (that is 761 nano seconds)
* Average Overhead per Invocation (none case/null returned): 0.000000368s (that is 368 nano seconds)

The benchmark was run under Ubuntu precise with PHP 5.4.6. As you can see the
overhead is surprisingly low, almost negligible.

So in conclusion, unless you plan to call a method thousands of times during a
request, there is no reason to stick to the ``object|null`` return value; better give
your code some options!

## Security

If you discover a security vulnerability within this package, please send an email to one of the security contacts. All security vulnerabilities will be promptly addressed. You may view our full security policy [here](https://github.com/schmittjoh/php-option/security/policy).

## License

PHP Option Type is licensed under [Apache License 2.0](LICENSE).
