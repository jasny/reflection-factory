Jasny Reflection factory
===

[![Build Status](https://travis-ci.org/jasny/reflection-factory.svg?branch=master)](https://travis-ci.org/jasny/reflection-factory)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/jasny/reflection-factory/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/jasny/reflection-factory/?branch=master)
[![Code Coverage](https://scrutinizer-ci.com/g/jasny/reflection-factory/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/jasny/reflection-factory/?branch=master)
[![Packagist Stable Version](https://img.shields.io/packagist/v/jasny/reflection-factory.svg)](https://packagist.org/packages/jasny/reflection-factory)
[![Packagist License](https://img.shields.io/packagist/l/jasny/reflection-factory.svg)](https://packagist.org/packages/jasny/reflection-factory)

Factory to use in dependency injection when using [PHP Reflection](https://php.net/reflection).

#### Why use a factory instead or just doing `new ReflectionClass()`?

_Dependency injection might seem like making things needlessly complex. However it's a key component in building
maintainable (and testable) code._

Using `new` within your classes creates a strong coupling between classes. This makes it more difficult when writing
unit tests because there is no opertunity to mock the reflection. In practice this means that a class using
`ReflectionClass` can only be tested with real, existing classes.

Using `ReflectionFactory` with dependency injection, allows you to inject a mock of the factory instead. This in
term allows you to create mock Reflection objects, for non-existing classes, functions, properties, etc.

Installation
---

    composer require jasny/reflection-factory

Usage
---

```php
use Jasny\ReflectionFactory\ReflectionFactory;

$factory = new ReflectionFactory();
$reflection = $factory->reflectClass(\DateTime::class);
```

Example use case
---

#### Without dependency injection

```php
use Jasny\ReflectionFactory\ReflectionFactory;

class SomeTool
{
    public function foo(string $class)
    {
        $reflection = new ReflectionClass($class);
        
        return $reflection->getConstant('FOO');
    }
}
```

But writing the test is hard, as it doesn't allow mocking. Instead we need to create a `SomeToolTestFooSupport` class
just to test this feature.

```php
class SomeToolTestFooSupport
{
    const FOO = 10;
}
```

In the unit test we do

```php
use PHPUnit\Framework\TestCase;

class SomeToolTest extends TestCase
{
    public function testFoo()
    {
        $tool = new SomeTool();
        
        $this->assertEquals(10, $tool->foo("SomeToolTestFooSupport"));
    }
}
```

Adding one test class isn't so bad. But consider we need to add one per test, it quickly becomes a mess.

### With dependency injection

Dependency injection adds a little overhead to the class as we need to pass the reflection factory to `SomeTool`.

```php
use Jasny\ReflectionFactory\ReflectionFactoryInterface;

class SomeTool
{
    protected $reflectionFactory;
    
    public function __construct(ReflectionFactoryInterface $reflectionFactory)
    {
        $this->reflectionFactory = $reflectionFactory;    
    }
    
    public function foo(string $class)
    {
        return $this->reflectionFactory->reflectClass($class)->getConstant('FOO');
    }
}
```

In the unit test, we mock the `ReflectionClass` and `ReflectionFactory`. The tests class `FakeClass` doesn't need
to exist.

```php
use PHPUnit\Framework\TestCase;
use Jasny\ReflectionFactory\ReflectionFactoryInterface;

class SomeToolTest extends TestCase
{
    public function testFoo()
    {
        $mockReflection = $this->createMock(\ReflectionClass::class);
        $mockReflection->expects($this->once())->method('getConstant')
            ->with('FOO')->willReturn(10);
            
        $mockFactory = $this->createMock(ReflectionFactoryInterface::class);
        $mockFactory->expects($this->once())->method('reflectClass')
            ->with('FakeClass')->willReturn($mockReflection);
            
        $tool = new SomeTool($mockFactory);
        
        $this->assertEquals(10, $tool->foo("FakeClass"));
    }
}
```

### Methods

| Method                       | Reflection class                         |
| ---------------------------- | ---------------------------------------- |
| `reflectClass`               | `ReflectionClass`                        |
| `reflectClassConstant`       | `ReflectionClassConstant`                |
| `reflectZendExtension`       | `ReflectionZendExtension`                |
| `reflectExtension`           | `ReflectionExtension`                    |
| `reflectFunction`            | `ReflectionFunction`                     |
| `reflectMethod`              | `ReflectionMethod`                       |
| `reflectObject`              | `ReflectionObject`                       |
| `reflectParameter`           | `ReflectionParameter`                    |
| `reflectProperty`            | `ReflectionProperty`                     |
| `reflectGenerator`           | `ReflectionGenerator`                    |

Some PHP functions have been wrapped, so they can be mocked

| Method                       | Function                                               |
| ---------------------------- | ------------------------------------------------------ |
| `functionExists`             | [`function_exists`](https://php.net/function_exists)   |
| `classExists`                | [`class_exists`](https://php.net/class_exists)         |
| `methodExists`               | [`method_exists`](https://php.net/method_exists)       |
| `propertyExists`             | [`property_exists`](https://php.net/property_exists)   |
| `extensionLoaded`            | [`extension_loaded`](https://php.net/extension_loaded) |
| `isA`                        | [`is_a`](https://php.net/is_a)                         |
| `isCallable`                 | [`is_callable`](https://php.net/is_callable)           |

