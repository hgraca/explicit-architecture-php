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

namespace Acme\App\Core\Component\Blog\Domain\Post;

use Acme\App\Core\SharedKernel\Exception\AppRuntimeException;

final class SlugIsImmutableException extends AppRuntimeException
{
    public function __construct()
    {
        parent::__construct(
            'A post slug can only be changed during the post creation,'
            . ' after that it is immutable so the post retains the SEO value.'
        );
    }
}
