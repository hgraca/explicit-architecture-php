<?php

declare(strict_types=1);

/*
 * This file is part of the Explicit Architecture POC,
 * which is created on top of the Symfony Demo application.
 *
 * (c) Herberto Graça <herberto.graca@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Acme\PhpExtension\Test\Helper;

use Acme\PhpExtension\Helper\ReflectionHelper;
use Acme\PhpExtension\Test\AbstractUnitTest;

/**
 * @small
 *
 * @internal
 */
final class ReflectionHelperUnitTest extends AbstractUnitTest
{
    /**
     * @test
     *
     * @throws \ReflectionException
     */
    public function get_protected_property_from_object_class(): void
    {
        $value = 7;
        $object = new DummyClass($value);

        self::assertSame($value, ReflectionHelper::getProtectedProperty($object, 'var'));
    }

    /**
     * @test
     *
     * @throws \ReflectionException
     */
    public function get_protected_property_from_object_parent_class(): void
    {
        $value = 7;
        $parentValue = 19;
        $object = new DummyClass($value, $parentValue);

        self::assertSame($parentValue, ReflectionHelper::getProtectedProperty($object, 'parentVar'));
    }

    /**
     * @test
     *
     * @throws \ReflectionException
     */
    public function get_protected_property_throws_exception_if_not_found(): void
    {
        $this->expectException(\ReflectionException::class);

        $object = new DummyClass();

        ReflectionHelper::getProtectedProperty($object, 'inexistentVar');
    }

    /**
     * @test
     *
     * @throws \ReflectionException
     */
    public function set_protected_property(): void
    {
        $newValue = 'something new';
        $object = new DummyClass();
        self::assertNotSame($newValue, $object->getTestProperty());

        ReflectionHelper::setProtectedProperty($object, 'testProperty', $newValue);
        self::assertSame($newValue, $object->getTestProperty());
    }

    /**
     * @test
     *
     * @throws \ReflectionException
     */
    public function set_protected_property_defined_in_parent_class(): void
    {
        $newValue = 'something new';
        $object = new DummyClass();
        self::assertNotSame($newValue, $object->getParentTestProperty());

        ReflectionHelper::setProtectedProperty($object, 'parentTestProperty', $newValue);
        self::assertSame($newValue, $object->getParentTestProperty());
    }

    /**
     * @test
     */
    public function set_protected_property_fails_when_cant_find_the_property(): void
    {
        $this->expectException(\ReflectionException::class);
        $this->expectExceptionMessage('Property i_dont_exist does not exist');

        $object = new DummyClass();
        ReflectionHelper::setProtectedProperty($object, 'i_dont_exist', 'non existent');
    }

    /**
     * @test
     *
     * @throws \ReflectionException
     */
    public function instantiate_without_constructor_does_not_use_the_constructor(): void
    {
        $object = ReflectionHelper::instantiateWithoutConstructor(DummyClass::class);
        self::assertNull($object->getAnotherVar());
    }

    /**
     * @test
     *
     * @throws \ReflectionException
     */
    public static function invokeProtectedMethod_works_with_protected_methods(): void
    {
        $var = 100;
        $dummyObject = new DummyClass($var);

        self::assertEquals($var, ReflectionHelper::invokeProtectedMethod($dummyObject, 'getVarProtected'));
    }

    /**
     * @test
     *
     * @throws \ReflectionException
     */
    public static function invokeProtectedMethod_works_with_private_methods(): void
    {
        $var = 120;
        $dummyObject = new DummyClass($var);

        self::assertEquals($var, ReflectionHelper::invokeProtectedMethod($dummyObject, 'getVarPrivate'));
    }
}
