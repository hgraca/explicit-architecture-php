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

namespace Acme\App\Core\Component\Blog\Application\Notification\NewComment\Push;

use Acme\App\Core\Component\Blog\Application\Notification\NewComment\NewCommentNotification;
use Acme\App\Core\Port\Notification\Client\Push\PushNotification;
use Acme\App\Core\Port\Translation\TranslatorInterface;

final class NewCommentPushGenerator
{
    // Unfortunately we need to make these public, otherwise we can't test it without reflection
    public const NOTIFICATION_NAME = 'NEW_COMMENT';
    public const TITLE_TRANSLATION_KEY = 'blog.push.new_comment.title';
    public const MESSAGE_TRANSLATION_KEY = 'blog.push.new_comment.message';
    public const DATA_KEY_POST_ID = 'post_id';

    /**
     * @var TranslatorInterface
     */
    private $translator;

    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }

    public function generate(NewCommentNotification $notification): PushNotification
    {
        return new PushNotification(
            self::NOTIFICATION_NAME,
            $this->translator->translate(self::TITLE_TRANSLATION_KEY),
            $this->translator->translate(self::MESSAGE_TRANSLATION_KEY),
            $notification->getDestinationUserId(),
            [
                self::DATA_KEY_POST_ID => (string) $notification->getPostId(),
            ]
        );
    }
}
