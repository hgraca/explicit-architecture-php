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

namespace Acme\App\Presentation\Web\Infrastructure\Auth\Symfony\Voter;

use Acme\App\Core\Component\Blog\Domain\Post\Post;
use Acme\App\Infrastructure\Security\SecurityUser;
use Acme\App\Presentation\Web\Core\Component\Blog\PostVoter;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

/**
 * It grants or denies permissions for actions related to blog posts (such as
 * showing, editing and deleting posts).
 *
 * See https://symfony.com/doc/current/security/voters.html
 *
 * @author Yonel Ceruto <yonelceruto@gmail.com>
 */
class PostVoterAdapter extends Voter
{
    /**
     * @var PostVoter
     */
    private $postVoter;

    public function __construct(PostVoter $postVoter)
    {
        $this->postVoter = $postVoter;
    }

    /**
     * @param string $attribute
     * @param Post $subject
     *
     * {@inheritdoc}
     */
    protected function supports($attribute, $subject): bool
    {
        return $this->postVoter->supports($attribute, $subject);
    }

    /**
     * {@inheritdoc}
     *
     * @param string $attribute
     * @param Post $post
     */
    protected function voteOnAttribute($attribute, $post, TokenInterface $token): bool
    {
        $securityUser = $token->getUser();

        // the user must be logged in; if not, deny permission
        if (!$securityUser instanceof SecurityUser) {
            return false;
        }

        return $this->postVoter->voteOnAttribute($attribute, $post, $securityUser->getUserId());
    }
}
