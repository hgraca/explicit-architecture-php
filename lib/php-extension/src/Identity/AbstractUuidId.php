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

namespace Acme\PhpExtension\Identity;

use Acme\PhpExtension\Uuid\Uuid;
use Acme\PhpExtension\Uuid\UuidGenerator;

/**
 * This class could be final instead of abstract, and we could have all entities use this same ID class.
 * However, that would have little more benefit than simply having the entities IDs as simple integers, we would still
 * have the ambiguity of "is this value supposed to be the ID of this entity, or is there a mistake and this is some
 * other value?".
 * What we really want to do is to have a specific ID class for each entity, so that we can type hint to specific
 * classes and remove all ambiguity.
 */
abstract class AbstractUuidId extends AbstractId
{
    /**
     * @var string
     */
    protected $id;

    public function __construct(string $uuidString = null)
    {
        parent::__construct($uuidString ?? UuidGenerator::generateAsString());
    }

    public function toScalar(): string
    {
        return $this->id;
    }

    protected function isValid($value): bool
    {
        return \is_string($value) && Uuid::isValid($value);
    }
}
