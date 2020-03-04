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

namespace Acme\App\Test\TestCase\Core\Component\Blog\Application\Notification\NewComment\Email;

use Acme\App\Core\Component\Blog\Application\Notification\NewComment\Email\NewCommentEmailGenerator;
use Acme\App\Core\Component\Blog\Application\Notification\NewComment\NewCommentNotification;
use Acme\App\Core\Component\Blog\Domain\Post\PostId;
use Acme\App\Core\Port\Notification\Client\Email\EmailAddress;
use Acme\App\Core\Port\Router\UrlGeneratorInterface;
use Acme\App\Core\Port\Router\UrlType;
use Acme\App\Core\Port\Translation\TranslatorInterface;
use Acme\App\Core\SharedKernel\Component\Blog\Domain\Post\Comment\CommentId;
use Acme\App\Core\SharedKernel\Component\User\Domain\User\UserId;
use Acme\App\Test\Framework\AbstractIntegrationTest;

/**
 * @internal
 *
 * @small
 */
final class NewCommentEmailGeneratorIntegrationTest extends AbstractIntegrationTest
{
    /**
     * @var UrlGeneratorInterface
     */
    private $urlGenerator;

    /**
     * @var TranslatorInterface
     */
    private $translator;

    /**
     * @var NewCommentEmailGenerator
     */
    private $generator;

    protected function setUp(): void
    {
        $this->urlGenerator = self::getService(UrlGeneratorInterface::class);
        $this->translator = self::getService(TranslatorInterface::class);
        $this->generator = self::getService(NewCommentEmailGenerator::class);
    }

    /**
     * @test
     */
    public function generate(): void
    {
        $toEmailAddress = new EmailAddress('email@email.com');
        $postTitle = 'some title';
        $postSlug = 'some slug';
        $commentId = new CommentId();
        $notification = new NewCommentNotification(
            new UserId(),
            '+31631769216',
            $commentId,
            $toEmailAddress,
            new PostId(),
            $postTitle,
            $postSlug
        );
        $linkToPost = $this->getPostUrl($postSlug, $commentId);

        $email = $this->generator->generate($notification);

        self::assertEquals(
            $this->translator->translate(NewCommentEmailGenerator::SUBJECT_TRANSLATION_KEY),
            $email->getSubject()
        );
        self::assertEquals([$toEmailAddress], $email->getTo());
        self::assertEquals(NewCommentEmailGenerator::CAMPAIGN_NAME, $email->getTrackingCampaign());

        self::assertValidHtml($email->getHtmlPart()->getContent());
        self::assertContains($postTitle, $email->getHtmlPart()->getContent());
        self::assertContains($linkToPost, $email->getHtmlPart()->getContent());

        self::assertContains($postTitle, $email->getPlainTextPart()->getContent());
        self::assertContains($linkToPost, $email->getPlainTextPart()->getContent());
    }

    private function getPostUrl(string $postSlug, CommentId $commentId): string
    {
        return $this->urlGenerator->generateUrl(
            'post',
            [
                'slug' => $postSlug,
                '_fragment' => 'comment_' . $commentId,
            ],
            UrlType::absoluteUrl()
        );
    }
}
