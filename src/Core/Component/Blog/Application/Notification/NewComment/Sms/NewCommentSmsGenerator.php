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

namespace Acme\App\Core\Component\Blog\Application\Notification\NewComment\Sms;

use Acme\App\Core\Component\Blog\Application\Notification\NewComment\NewCommentNotification;
use Acme\App\Core\Port\Notification\Client\Sms\Sms;
use Acme\App\Core\Port\Translation\TranslatorInterface;

final class NewCommentSmsGenerator
{
    // Unfortunately we need to make this public, otherwise we can't test it without reflection
    public const MESSAGE_TRANSLATION_KEY = 'blog.sms.new_comment.message';

    /**
     * @var TranslatorInterface
     */
    private $translator;

    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }

    public function generate(NewCommentNotification $notification): Sms
    {
        return new Sms(
            $this->translator->translate(self::MESSAGE_TRANSLATION_KEY),
            $notification->getPostAuthorMobile()
        );
    }
}
