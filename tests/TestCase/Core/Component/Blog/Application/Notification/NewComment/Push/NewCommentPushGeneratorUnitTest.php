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

namespace Acme\App\Test\TestCase\Core\Component\Blog\Application\Notification\NewComment\Push;

use Acme\App\Core\Component\Blog\Application\Notification\NewComment\NewCommentNotification;
use Acme\App\Core\Component\Blog\Application\Notification\NewComment\Push\NewCommentPushGenerator;
use Acme\App\Core\Component\Blog\Domain\Post\Comment\CommentId;
use Acme\App\Core\Component\Blog\Domain\Post\PostId;
use Acme\App\Core\Port\Notification\Client\Email\EmailAddress;
use Acme\App\Core\Port\Translation\TranslatorInterface;
use Acme\App\Core\SharedKernel\Component\User\Domain\User\UserId;
use Acme\App\Test\Framework\AbstractUnitTest;
use Mockery;
use Mockery\MockInterface;

final class NewCommentPushGeneratorUnitTest extends AbstractUnitTest
{
    /**
     * @var MockInterface|TranslatorInterface
     */
    private $translator;

    /**
     * @var NewCommentPushGenerator
     */
    private $generator;

    protected function setUp(): void
    {
        $this->translator = Mockery::mock(TranslatorInterface::class);
        $this->generator = new NewCommentPushGenerator($this->translator);
    }

    /**
     * @test
     */
    public function generate(): void
    {
        $fakeMessageTranslation = 'some message';
        $fakeTitleTranslation = 'some title';
        $this->translator->shouldReceive('translate')
            ->with(NewCommentPushGenerator::MESSAGE_TRANSLATION_KEY)
            ->andReturn($fakeMessageTranslation);
        $this->translator->shouldReceive('translate')
            ->with(NewCommentPushGenerator::TITLE_TRANSLATION_KEY)
            ->andReturn($fakeTitleTranslation);

        $userId = new UserId();
        $userMobile = '+31631769216';
        $title = 'some title';
        $notification = new NewCommentNotification(
            $userId,
            $userMobile,
            new CommentId(),
            new EmailAddress('email@email.com'),
            new PostId(),
            $title,
            'some slug'
        );

        $pushNotification = $this->generator->generate($notification);

        self::assertEquals($fakeMessageTranslation, $pushNotification->getMessage());
        self::assertEquals(NewCommentPushGenerator::NOTIFICATION_NAME, $pushNotification->getShortName());
        self::assertEquals($userId, $pushNotification->getUserId());
        self::assertEquals(
            [
                NewCommentPushGenerator::DATA_KEY_POST_ID => (string) $notification->getPostId(),
            ],
            $pushNotification->getData()
        );
        self::assertEquals($title, $pushNotification->getTitle());
    }
}
