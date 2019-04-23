<?php

declare(strict_types=1);

namespace Jasny\ReflectionFactory;

/**
 * Interface for Reflection factory.
 */
interface ReflectionFactoryInterface
{
    /**
     * Create a ReflectionClass.
     * @see \ReflectionClass
     *
     * @param string|object $class  Either a string containing the name of the class to reflect, or an object.
     * @return \ReflectionClass
     * @throws \ReflectionException
     */
    public function reflectClass($class): \ReflectionClass;

    /**
     * Create a ReflectionClassConstant.
     * @see \ReflectionClassConstant
     *
     * @param string|object $class  Either a string containing the name of the class to reflect, or an object.
     * @param string        $name   The name of the class constant.
     * @return \ReflectionClassConstant
     * @throws \ReflectionException
     */
    public function reflectClassConstant($class, string $name): \ReflectionClassConstant;

    /**
     * Create a ReflectionZendExtension.
     * @see \ReflectionZendExtension
     *
     * @param string $name  Name of the extension.
     * @return \ReflectionZendExtension
     * @throws \ReflectionException
     */
    public function reflectZendExtension(string $name): \ReflectionZendExtension;

    /**
     * Create a ReflectionExtension.
     * @see \ReflectionExtension
     *
     * @param string $name  Name of the extension.
     * @return \ReflectionExtension
     * @throws \ReflectionException
     */
    public function reflectExtension(string $name): \ReflectionExtension;

    /**
     * Create a ReflectionFunction.
     * @see \ReflectionFunction
     *
     * @param string|\Closure $name  The name of the function to reflect or a closure.
     * @return \ReflectionFunction
     * @throws \ReflectionException
     */
    public function reflectFunction($name): \ReflectionFunction;

    /**
     * Create a ReflectionMethod.
     * @see \ReflectionMethod
     *
     * @param string|object $class  Classname, class method or object (instance of the class) that contains the method.
     * @param string        $name   Name of the method.
     * @return \ReflectionMethod
     * @throws \ReflectionException
     */
    public function reflectMethod($class, string $name = null): \ReflectionMethod;

    /**
     * Create a ReflectionObject.
     * @see \ReflectionObject
     *
     * @param object $object  An object instance.
     * @return \ReflectionObject
     * @throws \ReflectionException
     */
    public function reflectObject($object): \ReflectionObject;

    /**
     * Create a ReflectionParameter.
     * @see \ReflectionParameter
     *
     * @param string|array $function   The function (string) or method (array) to reflect parameters from.
     * @param string       $parameter  The parameter name.
     * @return \ReflectionParameter
     * @throws \ReflectionException
     */
    public function reflectParameter($function, string $parameter): \ReflectionParameter;

    /**
     * Create a ReflectionProperty.
     * @see \ReflectionProperty
     *
     * @param string|object $class  The class name, that contains the property.
     * @param string        $name   The name of the property being reflected.
     * @return \ReflectionProperty
     * @throws \ReflectionException
     */
    public function reflectProperty($class, string $name): \ReflectionProperty;

    /**
     * Create a ReflectionGenerator.
     * @see \ReflectionGenerator
     *
     * @param \Generator $generator  A generator object.
     * @return \ReflectionGenerator
     * @throws \ReflectionException
     */
    public function reflectGenerator(\Generator $generator): \ReflectionGenerator;


    /**
     * Return true if the given function has been defined.
     * @see function_exists()
     *
     * @param string $name  The function name, as a string.
     * @return bool
     */
    public function functionExists(string $name): bool;

    /**
     * Checks if the class has been defined.
     * @see class_exists()
     *
     * @param string $class     The class name. The name is matched in a case-insensitive manner.
     * @param bool   $autoload  Whether or not to call autoload by default.
     * @return bool
     */
    public function classExists(string $class, bool $autoload = true): bool;

    /**
     * Checks if the class method exists.
     * @see method_exists()
     *
     * @param string|object $class   The class name or an object of the class to test for.
     * @param string        $method  The method name.
     * @return bool
     */
    public function methodExists($class, string $method): bool;

    /**
     * Checks if the object or class has a property.
     * @see property_exists()
     *
     * @param string|object $class     The class name or an object of the class to test for.
     * @param string        $property  The name of the property
     * @return bool|null
     */
    public function propertyExists($class, string $property): ?bool;

    /**
     * Find out whether an extension is loaded.
     * @see extension_loaded()
     *
     * @param string $name  The extension name.
     * @return bool
     */
    public function extensionLoaded(string $name): bool;

    /**
     * Checks if the object is of this class or has this class as one of its parents.
     * @see is_a()
     *
     * @param object|string $object        Object instance or class name.
     * @param string        $class         The class or interface name.
     * @param bool          $allow_string  Allow class name as `object`
     * @return bool
     */
    public function isA($object, string $class, bool $allow_string = false): bool;
}
