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
 */
final class ReflectionHelperUnitTest extends AbstractUnitTest
{
    /**
     * @test
     *
     * @throws \ReflectionException
     */
    public function getProtectedProperty_from_object_class(): void
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
    public function getProtectedProperty_from_object_parent_class(): void
    {
        $value = 7;
        $parentValue = 19;
        $object = new DummyClass($value, $parentValue);

        self::assertSame($parentValue, ReflectionHelper::getProtectedProperty($object, 'parentVar'));
    }

    /**
     * @test
     *
     * @expectedException \ReflectionException
     *
     * @throws \ReflectionException
     */
    public function getProtectedProperty_throws_exception_if_not_found(): void
    {
        $object = new DummyClass();

        ReflectionHelper::getProtectedProperty($object, 'inexistentVar');
    }

    /**
     * @test
     *
     * @throws \ReflectionException
     */
    public function setProtectedProperty(): void
    {
        $newValue = 'something new';
        $object = new DummyClass();
        $this->assertNotSame($newValue, $object->getTestProperty());

        ReflectionHelper::setProtectedProperty($object, 'testProperty', $newValue);
        $this->assertSame($newValue, $object->getTestProperty());
    }

    /**
     * @test
     *
     * @throws \ReflectionException
     */
    public function setProtectedProperty_defined_in_parent_class(): void
    {
        $newValue = 'something new';
        $object = new DummyClass();
        $this->assertNotSame($newValue, $object->getParentTestProperty());

        ReflectionHelper::setProtectedProperty($object, 'parentTestProperty', $newValue);
        $this->assertSame($newValue, $object->getParentTestProperty());
    }

    /**
     * @test
     * @expectedException \ReflectionException
     * @expectedExceptionMessage Property i_dont_exist does not exist
     */
    public function setProtectedProperty_fails_when_cant_find_the_property(): void
    {
        $object = new DummyClass();
        ReflectionHelper::setProtectedProperty($object, 'i_dont_exist', 'non existent');
    }

    /**
     * @test
     *
     * @throws \ReflectionException
     */
    public function instantiateWithoutConstructor_does_not_use_the_constructor(): void
    {
        $object = ReflectionHelper::instantiateWithoutConstructor(DummyClass::class);
        $this->assertNull($object->getAnotherVar());
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
