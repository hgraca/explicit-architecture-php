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

namespace Acme\App\Core\Component\Blog\Application\Query\DQL;

use Acme\App\Core\Component\Blog\Application\Query\FindHighestPostSlugSuffixQueryInterface;
use Acme\App\Core\Component\Blog\Application\Query\PostSlugExistsQueryInterface;
use Acme\App\Core\Port\Persistence\Exception\EmptyQueryResultException;

final class PostSlugExistsQuery implements PostSlugExistsQueryInterface
{
    /**
     * @var FindHighestPostSlugSuffixQueryInterface
     */
    private $findHighestPostSlugSuffixQuery;

    public function __construct(FindHighestPostSlugSuffixQueryInterface $findHighestPostSlugSuffixQuery)
    {
        $this->findHighestPostSlugSuffixQuery = $findHighestPostSlugSuffixQuery;
    }

    public function execute(string $slug): bool
    {
        try {
            $this->findHighestPostSlugSuffixQuery->execute($slug);

            return true;
        } catch (EmptyQueryResultException $e) {
            return false;
        }
    }
}
