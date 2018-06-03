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

namespace Acme\App\Core\Component\Blog\Application\Notification\NewComment\Email;

use Acme\App\Core\Component\Blog\Application\Notification\NewComment\NewCommentNotification;
use Acme\App\Core\Port\Notification\Client\Email\Email;
use Acme\App\Core\Port\Notification\Client\Email\EmailGenerator;
use Acme\App\Core\Port\Router\UrlGeneratorInterface;
use Acme\App\Core\Port\Router\UrlType;
use Acme\App\Core\Port\Translation\TranslatorInterface;

class NewCommentEmailGenerator
{
    // Unfortunately we need to make these public, otherwise we can't test it without reflection
    public const CAMPAIGN_NAME = 'dummy_email_to_make_a_campaign_on_-_but_fine_as_an_example';
    public const SUBJECT_TRANSLATION_KEY = 'blog.email.new_comment.subject';
    public const TEMPLATE_TXT = '@Core/Component/Blog/Application/Notification/NewComment/Email/NewCommentEmail.txt.twig';
    public const TEMPLATE_HTML = '@Core/Component/Blog/Application/Notification/NewComment/Email/NewCommentEmail.html.twig';

    /**
     * @var EmailGenerator
     */
    private $emailGenerator;

    /**
     * @var UrlGeneratorInterface
     */
    private $urlGenerator;

    /**
     * @var TranslatorInterface
     */
    private $translator;

    public function __construct(
        EmailGenerator $emailGenerator,
        UrlGeneratorInterface $urlGenerator,
        TranslatorInterface $translator
    ) {
        $this->emailGenerator = $emailGenerator;
        $this->urlGenerator = $urlGenerator;
        $this->translator = $translator;
    }

    public function generate(NewCommentNotification $newCommentNotification): Email
    {
        $linkToPost = $this->urlGenerator->generateUrl(
            'post',
            [
                'slug' => $newCommentNotification->getPostSlug(),
                '_fragment' => 'comment_' . $newCommentNotification->getCommentId(),
            ],
            UrlType::absoluteUrl()
        );

        $subject = $this->translator->translate(self::SUBJECT_TRANSLATION_KEY);
        $email = $this->emailGenerator->generateEmailMessage(
            $newCommentNotification->getPostAuthorEmail(),
            $subject,
            self::TEMPLATE_TXT,
            self::TEMPLATE_HTML,
            new NewCommentEmailViewModel($subject, $newCommentNotification->getPostTitle(), $linkToPost)
        );

        $email->setTrackingCampaign(self::CAMPAIGN_NAME);

        return $email;
    }
}
