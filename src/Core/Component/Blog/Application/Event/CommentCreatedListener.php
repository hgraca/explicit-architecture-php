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

namespace Acme\App\Core\Component\Blog\Application\Event;

use Acme\App\Core\Component\Blog\Application\Repository\CommentRepositoryInterface;
use Acme\App\Core\Port\Router\UrlGeneratorInterface;
use Acme\App\Core\Port\Router\UrlType;
use Acme\App\Core\Port\Translation\TranslatorInterface;
use Acme\App\Core\SharedKernel\Component\Blog\Application\Event\CommentCreatedEvent;
use Swift_Mailer;

/**
 * Listens to the CommentCreatedEvent and triggers all the logic associated with it, in this component.
 *
 * @author Oleg Voronkovich <oleg-voronkovich@yandex.ru>
 * @author Herberto Graca <herberto.graca@gmail.com>
 */
class CommentCreatedListener
{
    public const EMAIL_SUBJECT_KEY = 'notification.comment_created';

    /**
     * @var CommentRepositoryInterface
     */
    private $commentRepository;

    /**
     * @var Swift_Mailer
     */
    private $mailer;

    /**
     * @var TranslatorInterface
     */
    private $translator;

    /**
     * @var UrlGeneratorInterface
     */
    private $urlGenerator;

    /**
     * @var string
     */
    private $senderEmail;

    public function __construct(
        CommentRepositoryInterface $commentRepository,
        Swift_Mailer $mailer,
        UrlGeneratorInterface $urlGenerator,
        TranslatorInterface $translator,
        string $senderEmail
    ) {
        $this->commentRepository = $commentRepository;
        $this->mailer = $mailer;
        $this->urlGenerator = $urlGenerator;
        $this->translator = $translator;
        $this->senderEmail = $senderEmail;
    }

    public function notifyPostAuthorAboutNewComment(CommentCreatedEvent $event): void
    {
        $commentId = $event->getCommentId();
        $comment = $this->commentRepository->find($commentId);
        $post = $comment->getPost();

        $linkToPost = $this->urlGenerator->generateUrl(
            'post',
            [
                'slug' => $post->getSlug(),
                '_fragment' => 'comment_' . $comment->getId(),
            ],
            UrlType::absoluteUrl()
        );

        $subject = $this->translator->translate('notification.comment_created');
        $body = $this->translator->translate(
            'notification.comment_created.description',
            [
                '%title%' => $post->getTitle(),
                '%link%' => $linkToPost,
            ]
        );

        // Symfony uses a library called SwiftMailer to send emails. That's why
        // email messages are created instantiating a Swift_Message class.
        // See https://symfony.com/doc/current/email.html#sending-emails
        $message = (new \Swift_Message())
            ->setSubject($subject)
            ->setTo($post->getAuthor()->getEmail())
            ->setFrom($this->senderEmail)
            ->setBody($body, 'text/html');

        // In config/packages/dev/swiftmailer.yaml the 'disable_delivery' option is set to 'true'.
        // That's why in the development environment you won't actually receive any email.
        // However, you can inspect the contents of those unsent emails using the debug toolbar.
        // See https://symfony.com/doc/current/email/dev_environment.html#viewing-from-the-web-debug-toolbar
        $this->mailer->send($message);
    }
}
