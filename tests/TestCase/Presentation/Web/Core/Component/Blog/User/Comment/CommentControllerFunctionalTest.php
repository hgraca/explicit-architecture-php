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

namespace Acme\App\Test\TestCase\Presentation\Web\Core\Component\Blog\User\Comment;

use Acme\App\Core\Component\Blog\Application\Event\CommentCreatedListener;
use Acme\App\Core\Port\Router\UrlGeneratorInterface;
use Acme\App\Core\Port\Router\UrlType;
use Acme\App\Core\Port\Translation\TranslatorInterface;
use Acme\App\Test\Fixture\Doctrine\UserFixtures;
use Acme\App\Test\Framework\AbstractFunctionalTest;
use Acme\PhpExtension\DateTime\DateTimeGenerator;
use DateTimeImmutable;
use InvalidArgumentException;
use Symfony\Bundle\SwiftmailerBundle\DataCollector\MessageDataCollector;

/**
 * Functional test for the controllers defined inside CommentController.
 *
 * See https://symfony.com/doc/current/book/testing.html#functional-tests
 *
 * Execute the application tests using this command (requires PHPUnit to be installed):
 *
 *     $ cd your-symfony-project/
 *     $ ./vendor/bin/phpunit
 */
class CommentControllerFunctionalTest extends AbstractFunctionalTest
{
    /**
     * @test
     *
     * This test changes the database contents by creating a new comment. However,
     * thanks to the DAMADoctrineTestBundle and its PHPUnit listener, all changes
     * to the database are rolled back when this test completes. This means that
     * all the application tests begin with the same database contents.
     */
    public function new_comment(): void
    {
        $this->overrideDateTimeGenerator();

        $client = $this->getClient([], [
            'PHP_AUTH_USER' => 'john_user',
            'PHP_AUTH_PW' => 'kitten',
        ]);

        // Find first blog post
        $crawler = $client->request('GET', '/en/blog/posts');
        $postLink = $crawler->filter('article.post > h2 a')->link();

        $crawler = $client->click($postLink);
        $contentBefore = $client->getResponse()->getContent();

        $form = $crawler->selectButton('Publish comment')->form([
            'comment_form[content]' => 'Hi, Symfony!',
        ]);

        // enables the profiler for the next request (it does nothing if the profiler is not available)
        $client->enableProfiler();

        $client->submit($form);

        /** @var MessageDataCollector $mailCollector */
        $mailCollector = $client->getProfile()->getCollector('swiftmailer');
        self::assertEmailWasSent(
            $mailCollector,
            'anonymous@example.com',
            UserFixtures::JANE_EMAIL,
            $this->getTranslator()->translate(CommentCreatedListener::EMAIL_SUBJECT_KEY)
        );

        $crawler = $client->followRedirect();
        $contentAfter = $client->getResponse()->getContent();

        try {
            $newComment = $crawler->filter('.post-comment')->first()->filter('div > p')->text();
        } catch (InvalidArgumentException $e) {
            self::fail('Something went wrong: ' . $e->getMessage() . "\n\n\n"
                . 'Content before: ' . $contentBefore . "\n\n\n"
                . 'Content after: ' . $contentAfter . "\n\n\n");

            return;
        }

        $this->assertSame(
            'Hi, Symfony!',
            $newComment,
            'Content before: ' . $contentBefore . "\n\n\n"
            . 'Content after: ' . $contentAfter . "\n\n\n"
        );
    }

    /**
     * @test
     *
     * This test changes the database contents by creating a new comment. However,
     * thanks to the DAMADoctrineTestBundle and its PHPUnit listener, all changes
     * to the database are rolled back when this test completes. This means that
     * all the application tests begin with the same database contents.
     */
    public function new_comment_without_being_logged_in_redirects_to_login_page(): void
    {
        /** @var UrlGeneratorInterface $urlGenerator */
        $urlGenerator = $this->getService(UrlGeneratorInterface::class);

        $client = static::createClient();
        $client->followRedirects();

        // Find first blog post
        $crawler = $client->request(
            'POST',
            '/en/blog/posts/some-post-slug-that-will-not-matter/comments',
            ['comment_form[content]' => 'Hi, Symfony!']
        );

        $this->assertSame(
            $urlGenerator->generateUrl('security_login', [], UrlType::absoluteUrl()),
            $crawler->getUri()
        );
    }

    private function overrideDateTimeGenerator(): void
    {
        DateTimeGenerator::overrideDefaultGenerator(
            function () {
                return new DateTimeImmutable('now + 1 hour');
            }
        );
    }

    private function getTranslator(): TranslatorInterface
    {
        return self::getService(TranslatorInterface::class);
    }
}
