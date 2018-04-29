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

namespace Acme\App\Core\Component\Blog\Application\Repository\DQL;

use Acme\App\Core\Component\Blog\Application\Repository\CommentRepositoryInterface;
use Acme\App\Core\Component\Blog\Domain\Entity\Comment;
use Acme\App\Core\Port\Persistence\PersistenceServiceInterface;

/**
 * @author Herberto Graca <herberto.graca@gmail.com>
 */
class CommentRepository implements CommentRepositoryInterface
{
    /**
     * @var PersistenceServiceInterface
     */
    private $persistenceService;

    public function __construct(PersistenceServiceInterface $persistenceService)
    {
        $this->persistenceService = $persistenceService;
    }

    public function upsert(Comment $entity): void
    {
        $this->persistenceService->upsert($entity);
    }
}
