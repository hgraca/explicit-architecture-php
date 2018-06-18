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

namespace Acme\App\Test\TestCase\Core\Component\Blog\Application\Notification\NewComment\Sms;

use Acme\App\Core\Component\Blog\Application\Notification\NewComment\NewCommentNotification;
use Acme\App\Core\Component\Blog\Application\Notification\NewComment\Sms\NewCommentSmsGenerator;
use Acme\App\Core\Component\Blog\Domain\Post\PostId;
use Acme\App\Core\Port\Notification\Client\Email\EmailAddress;
use Acme\App\Core\Port\Translation\TranslatorInterface;
use Acme\App\Core\SharedKernel\Component\Blog\Domain\Post\Comment\CommentId;
use Acme\App\Core\SharedKernel\Component\User\Domain\User\UserId;
use Acme\App\Test\Framework\AbstractUnitTest;
use Mockery;
use Mockery\MockInterface;

final class NewCommentSmsGeneratorUnitTest extends AbstractUnitTest
{
    /**
     * @var MockInterface|TranslatorInterface
     */
    private $translator;

    /**
     * @var NewCommentSmsGenerator
     */
    private $generator;

    protected function setUp(): void
    {
        $this->translator = Mockery::mock(TranslatorInterface::class);
        $this->generator = new NewCommentSmsGenerator($this->translator);
    }

    /**
     * @test
     */
    public function generate(): void
    {
        $fakeTranslation = 'some message';
        $this->translator->shouldReceive('translate')
            ->with(NewCommentSmsGenerator::MESSAGE_TRANSLATION_KEY)
            ->andReturn($fakeTranslation);

        $userMobile = '+31631769216';
        $notification = new NewCommentNotification(
            new UserId(),
            $userMobile,
            new CommentId(),
            new EmailAddress('email@email.com'),
            new PostId(),
            'some title',
            'some slug'
        );

        $sms = $this->generator->generate($notification);

        self::assertEquals($fakeTranslation, $sms->getContent());
        self::assertEquals($userMobile, $sms->getPhoneNumber());
    }
}
