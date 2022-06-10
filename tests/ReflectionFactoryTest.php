<?php

namespace Jasny\ReflectionFactory\Tests;

use Jasny\ReflectionFactory\ReflectionFactory;
use Jasny\ReflectionFactory\ReflectionFactoryInterface;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Jasny\ReflectionFactory\ReflectionFactory
 */
class ReflectionFactoryTest extends TestCase
{
    /**
     * @var ReflectionFactory
     */
    protected $factory;

    public function setUp(): void
    {
        $this->factory = new ReflectionFactory();
    }

    public function testInterface()
    {
        $this->assertInstanceOf(ReflectionFactoryInterface::class, $this->factory);
    }
    

    public function testReflectClass()
    {
        $reflection = $this->factory->reflectClass('DateTime');

        $this->assertInstanceOf(\ReflectionClass::class, $reflection);
        $this->assertEquals('DateTime', $reflection->getName());
    }

    public function testReflectClassWithObject()
    {
        $datetime = new \DateTime();
        $reflection = $this->factory->reflectClass($datetime);

        $this->assertInstanceOf(\ReflectionClass::class, $reflection);
        $this->assertEquals('DateTime', $reflection->getName());
    }

    public function testReflectClassConstantWithClass()
    {
        $reflection = $this->factory->reflectClassConstant(\ReflectionClass::class, 'IS_FINAL');

        $this->assertInstanceOf(\ReflectionClassConstant::class, $reflection);
        $this->assertEquals('IS_FINAL', $reflection->getName());
        $this->assertEquals('ReflectionClass', $reflection->getDeclaringClass()->getName());
    }

    public function testReflectClassConstantWithObject()
    {
        $object = new \ReflectionClass(self::class);
        $reflection = $this->factory->reflectClassConstant($object, 'IS_FINAL');

        $this->assertInstanceOf(\ReflectionClassConstant::class, $reflection);
        $this->assertEquals('IS_FINAL', $reflection->getName());
        $this->assertEquals('ReflectionClass', $reflection->getDeclaringClass()->getName());
    }

    public function testReflectZendExtension()
    {
        $zendExtensions = get_loaded_extensions(true);

        if (empty($zendExtensions)) {
            return $this->markTestSkipped("No Zend extensions loaded");
        }

        $extension = $zendExtensions[0];
        $reflection = $this->factory->reflectZendExtension($extension);

        $this->assertInstanceOf(\ReflectionZendExtension::class, $reflection);
        $this->assertEquals($extension, $reflection->getName());
    }

    public function testReflectExtension()
    {
        $extensions = get_loaded_extensions();

        if (empty($extensions)) {
            $this->markTestSkipped("No extensions loaded");
        }

        $extension = $extensions[0];
        $reflection = $this->factory->reflectExtension($extension);

        $this->assertInstanceOf(\ReflectionExtension::class, $reflection);
        $this->assertEquals($extension, $reflection->getName());
    }

    public function testReflectFunction()
    {
        $reflection = $this->factory->reflectFunction('str_replace');

        $this->assertInstanceOf(\ReflectionFunction::class, $reflection);
        $this->assertEquals('str_replace', $reflection->getName());
    }

    public function testReflectFunctionWithClosure()
    {
        $closure = function($foo, $bar) {
            return $foo . $bar;
        };

        $reflection = $this->factory->reflectFunction($closure);

        $this->assertInstanceOf(\ReflectionFunction::class, $reflection);
        $this->assertTrue($reflection->isClosure());
    }

    public function testReflectMethod()
    {
        $reflection = $this->factory->reflectMethod('DateTime::add');

        $this->assertInstanceOf(\ReflectionMethod::class, $reflection);
        $this->assertEquals('add', $reflection->getName());
        $this->assertEquals('DateTime', $reflection->getDeclaringClass()->getName());
    }

    public function testReflectMethodWithClass()
    {
        $reflection = $this->factory->reflectMethod('DateTime', 'add');

        $this->assertInstanceOf(\ReflectionMethod::class, $reflection);
        $this->assertEquals('add', $reflection->getName());
        $this->assertEquals('DateTime', $reflection->getDeclaringClass()->getName());
    }

    public function testReflectMethodWithObject()
    {
        $datetime = new \DateTime();
        $reflection = $this->factory->reflectMethod($datetime, 'add');

        $this->assertInstanceOf(\ReflectionMethod::class, $reflection);
        $this->assertEquals('add', $reflection->getName());
        $this->assertEquals('DateTime', $reflection->getDeclaringClass()->getName());
    }

    public function testReflectObject()
    {
        $object = $this->getMockBuilder(\stdClass::class)->setMockClassName('FooA')->getMock();
        $reflection = $this->factory->reflectObject($object);

        $this->assertInstanceOf(\ReflectionObject::class, $reflection);
        $this->assertEquals('FooA', $reflection->getName());
    }

    public function testReflectParameterOfFunction()
    {
        $reflection = $this->factory->reflectParameter('str_replace', 'subject');

        $this->assertInstanceOf(\ReflectionParameter::class, $reflection);
        $this->assertEquals('subject', $reflection->getName());
        $this->assertEquals('str_replace', $reflection->getDeclaringFunction()->getName());
        $this->assertEquals(2, $reflection->getPosition());
    }

    public function testReflectParameterOfMethod()
    {
        $reflection = $this->factory->reflectParameter(['DateTime', 'add'], 'interval');

        $this->assertInstanceOf(\ReflectionParameter::class, $reflection);
        $this->assertEquals('interval', $reflection->getName());
        $this->assertEquals('DateTime', $reflection->getDeclaringClass()->getName());
        $this->assertEquals('add', $reflection->getDeclaringFunction()->getName());
        $this->assertEquals(0, $reflection->getPosition());
    }

    public function testReflectParameterOfClosure()
    {
        $closure = function($foo, $bar) {
            return $foo . $bar;
        };

        $reflection = $this->factory->reflectParameter($closure, 'foo');

        $this->assertInstanceOf(\ReflectionParameter::class, $reflection);
        $this->assertEquals('foo', $reflection->getName());
        $this->assertTrue($reflection->getDeclaringFunction()->isClosure());
        $this->assertEquals(0, $reflection->getPosition());
    }

    public function testReflectPropertyWithClass()
    {
        $reflection = $this->factory->reflectProperty(\ReflectionClass::class, 'name');

        $this->assertInstanceOf(\ReflectionProperty::class, $reflection);
        $this->assertEquals('name', $reflection->getName());
        $this->assertEquals('ReflectionClass', $reflection->getDeclaringClass()->getName());
    }

    public function testReflectPropertyWithObject()
    {
        $datetime = new \DateInterval('P2Y4DT6H8M');
        $reflection = $this->factory->reflectProperty($datetime, 'days');

        $this->assertInstanceOf(\ReflectionProperty::class, $reflection);
        $this->assertEquals('days', $reflection->getName());
        $this->assertEquals('DateInterval', $reflection->getDeclaringClass()->getName());
    }

    public function testReflectPropertyWithDynamicObject()
    {
        $object = (object)['foo' => 10, 'bar' => 20];
        $reflection = $this->factory->reflectProperty($object, 'foo');

        $this->assertInstanceOf(\ReflectionProperty::class, $reflection);
        $this->assertEquals('foo', $reflection->getName());
        $this->assertEquals('stdClass', $reflection->getDeclaringClass()->getName());
        $this->assertEquals(10, $reflection->getValue($object));
    }

    public function testReflectGenerator()
    {
        $range = function ($from, $to) {
            for ($i = $from; $i <= $to; $i++) {
                yield $i;
            }
        };

        $generator = $range(13, 42);
        $reflection = $this->factory->reflectGenerator($generator);

        $this->assertInstanceOf(\ReflectionGenerator::class, $reflection);
        $this->assertTrue($reflection->getFunction()->isClosure());
    }


    public function testFunctionExists()
    {
        $this->assertTrue($this->factory->functionExists('str_replace'));
        $this->assertFalse($this->factory->functionExists('non_existing'));
    }

    public function testClassExists()
    {
        $this->assertTrue($this->factory->classExists("ReflectionClass"));
        $this->assertFalse($this->factory->classExists('NonExisting'));
    }

    public function testMethodExistsWithClass()
    {
        $this->assertTrue($this->factory->methodExists(\ReflectionClass::class, 'getName'));
        $this->assertFalse($this->factory->methodExists(\ReflectionClass::class, 'nonExisting'));
        $this->assertFalse($this->factory->methodExists("NonExisting", 'Foo'));
    }

    public function testMethodExistsWithObject()
    {
        $object = new \ReflectionClass(self::class);

        $this->assertTrue($this->factory->methodExists($object, 'getName'));
        $this->assertFalse($this->factory->methodExists($object, 'nonExisting'));
    }

    public function testPropertyExistsWithClass()
    {
        $this->assertTrue($this->factory->propertyExists(\ReflectionClass::class, 'name'));
        $this->assertFalse($this->factory->propertyExists(\ReflectionClass::class, 'non_existing'));
        $this->assertFalse($this->factory->propertyExists("NonExisting", 'foo'));
    }

    public function testPropertyExistsWithObject()
    {
        $object = new \ReflectionClass(self::class);

        $this->assertTrue($this->factory->propertyExists($object, 'name'));
        $this->assertFalse($this->factory->propertyExists($object, 'non_existing'));
    }

    public function testPropertyExistsWithDynamicObject()
    {
        $object = (object)['foo' => 10, 'bar' => 20];

        $this->assertTrue($this->factory->propertyExists($object, 'foo'));
        $this->assertFalse($this->factory->propertyExists($object, 'non_existing'));
    }

    public function testExtensionLoaded()
    {
        $extensions = get_loaded_extensions();

        if (empty($extensions)) {
            $this->markTestSkipped("No extensions loaded");
        }

        $extension = $extensions[0];

        $this->assertTrue($this->factory->extensionLoaded($extension));
        $this->assertFalse($this->factory->extensionLoaded('non_existing'));
    }

    public function testIsA()
    {
        $this->assertTrue($this->factory->isA(new \LogicException(''), \LogicException::class));
        $this->assertTrue($this->factory->isA(new \InvalidArgumentException(''), \LogicException::class));
        $this->assertFalse($this->factory->isA(new \UnexpectedValueException(''), \LogicException::class));

        $this->assertFalse($this->factory->isA(\InvalidArgumentException::class, \LogicException::class));
        $this->assertTrue($this->factory->isA(\InvalidArgumentException::class, \LogicException::class, true));
    }

    public function testIsCallable()
    {
        $this->assertFalse($this->factory->isCallable(22));
        $this->assertFalse($this->factory->isCallable([]));
        $this->assertFalse($this->factory->isCallable(new \LogicException('')));
        $this->assertFalse($this->factory->isCallable(true));

        $this->assertTrue($this->factory->isCallable('str_replace'));
        $this->assertFalse($this->factory->isCallable('non_existing'));

        $this->assertTrue($this->factory->isCallable(function () {}));

        $object = new class () {
            public function foo() {}
            protected function bar() {}
        };
        $this->assertFalse($this->factory->isCallable($object));
        $this->assertTrue($this->factory->isCallable([$object, 'foo']));
        $this->assertFalse($this->factory->isCallable([$object, 'bar']));
        $this->assertFalse($this->factory->isCallable([$object, 'qux']));

        $invokable = new class () {
            public function __invoke() {}
        };
        $this->assertTrue($this->factory->isCallable($invokable));
    }

    public function testIsCallableSyntaxOnly()
    {
        $this->assertFalse($this->factory->isCallable(22, true));
        $this->assertFalse($this->factory->isCallable([], true));
        $this->assertFalse($this->factory->isCallable(new \LogicException(''), true));
        $this->assertFalse($this->factory->isCallable(true, true));

        $this->assertTrue($this->factory->isCallable('str_replace', true));
        $this->assertTrue($this->factory->isCallable('non_existing', true));

        $this->assertTrue($this->factory->isCallable(function () {}, true));

        $object = new class () {
            public function foo() {}
            protected function bar() {}
        };
        $this->assertFalse($this->factory->isCallable($object, true));
        $this->assertTrue($this->factory->isCallable([$object, 'foo'], true));
        $this->assertTrue($this->factory->isCallable([$object, 'bar'], true));
        $this->assertTrue($this->factory->isCallable([$object, 'qux'], true));

        $invokable = new class () {
            public function __invoke() {}
        };
        $this->assertTrue($this->factory->isCallable($invokable));
    }
}

