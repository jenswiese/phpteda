<?php

namespace Phpteda\Test\Reflection;

/**
 * This is a test class for class reflection,
 * nothing more, nothing less
 *
 * @author Jens Wiese
 * @since 1.0.0
 *
 * @fakerLocale de_DE
 * @fakerProvider \path\to\customProviderOne
 * @fakerProvider \path\to\customProviderTwo
 *
 * @method string getName() Returns a name
 * @method string parseName(string $name) Parses name
 */
class TestClassForReflection
{
    function __construct()
    {
        // TODO: Implement __construct() method.
    }

    function __call($name, $arguments)
    {
        // TODO: Implement __call() method.
    }

    function __get($name)
    {
        // TODO: Implement __get() method.
    }
}