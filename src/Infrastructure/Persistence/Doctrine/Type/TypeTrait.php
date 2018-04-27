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

namespace Acme\App\Infrastructure\Persistence\Doctrine\Type;

use Acme\PhpExtension\Helper\ClassHelper;
use Acme\PhpExtension\ScalarObjectInterface;
use Doctrine\DBAL\Platforms\AbstractPlatform;

trait TypeTrait
{
    abstract protected function getMappedClass(): string;

    public function convertToPHPValue($value, AbstractPlatform $platform)
    {
        $value = parent::convertToPHPValue($value, $platform);

        if ($value === null) {
            return null;
        }

        return $this->createSpecificObject($value);
    }

    /**
     * @param AbstractPlatform $platform This needs to be here in order to comply to the Type class method signature
     */
    public function convertToDatabaseValue($value, AbstractPlatform $platform)
    {
        if ($value === null && $this->allowsNullValues()) {
            return null;
        }

        if ($value instanceof ScalarObjectInterface) {
            return $value->toScalar();
        }

        return $value;
    }

    /**
     * This is used to generate a DC2Type:<whatever_type> comment for the field
     * and allow doctrine diff to match the types instead of assuming
     * it's an integer type (id).
     *
     * The value outputted here is what is used as key in config/packages/doctrine.yaml:doctrine|dbal|types
     *
     * By convention, we use the canonical class name in snake case. If at some point we get a collision,
     * we can override this method in the collision type mapper.
     *
     * @return string
     */
    public function getName()
    {
        return ClassHelper::toSnakeCase(
            ClassHelper::extractCanonicalClassName($this->getMappedClass())
        );
    }

    /**
     * @param AbstractPlatform $platform This needs to be here in order to comply to the Type class method signature
     */
    public function requiresSQLCommentHint(AbstractPlatform $platform): bool
    {
        return true;
    }

    protected function createSpecificObject($value)
    {
        $class = $this->getMappedClass();

        return new $class($value);
    }

    /**
     * In general an Enum is required and should have a value, but in some cases, because of Doctrine's implementation,
     * we cannot enforce this:.
     *
     * When we have a case of table inheritance, if we query an entity by the top table then Doctrine joins all the
     * inheritance tables and tries to hydrate all of their fields, including the ones from types
     * that don't belong to the entity we fetch for the database.
     *
     * This means that when we fetch a entity, doctrine will try to hydrate the sister entities fields as well,
     * and as we have enums in the sister entities, doctrine will try to create an enum for them. Of course
     * for the entity we care about this value will be NULL, so we need to do a null check in all Doctrine Enums
     * that we use in combination with a superclass.
     *
     * When we don't have a superclass (most cases) we should not allow null values.
     */
    protected function allowsNullValues(): bool
    {
        return false;
    }
}
