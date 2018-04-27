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

use Acme\App\Core\Component\Blog\Domain\Entity\TagId;

/**
 * The TagId is a binary UUID, which has the advantage of being smaller and faster to search in the DB, but it has the
 *  disadvantage of being "unreadable" if we need to take a look at the DB for debugging.
 * Most of the times we can make the entities IDs be binary UUIDs, but for the cases where we need to debug and query
 *  the DB directly we should make the IDs regular string UUIDs.
 * For the case of this demo application I made only this one binary, as an example, so that we can easily explore
 *  the DB.
 */
final class TagIdType extends AbstractBinaryUuidType
{
    protected function getMappedClass(): string
    {
        return TagId::class;
    }
}
