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

namespace Acme\App\Test\Fixture\Doctrine;

use Acme\App\Core\Component\Blog\Domain\Entity\Comment;
use Acme\App\Core\Component\Blog\Domain\Entity\Post;
use Acme\App\Core\Component\User\Domain\Entity\User;
use Acme\App\Test\Fixture\FixturesTrait;
use Acme\PhpExtension\DateTime\DateTimeGenerator;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

/**
 * Defines the sample blog posts to load in the database before running the unit
 * and functional tests. Execute this command to load the data.
 *
 *   $ php bin/console doctrine:fixtures:load
 *
 * See https://symfony.com/doc/current/bundles/DoctrineFixturesBundle/index.html
 *
 * @author Ryan Weaver <weaverryan@gmail.com>
 * @author Javier Eguiluz <javier.eguiluz@gmail.com>
 * @author Yonel Ceruto <yonelceruto@gmail.com>
 */
class PostFixtures extends Fixture implements DependentFixtureInterface
{
    public const JANE_ADMIN_NUM_POSTS = 25;

    use FixturesTrait;

    /**
     * {@inheritdoc}
     */
    public function load(ObjectManager $manager): void
    {
        foreach ($this->getRandomPostTitles() as $i => $title) {
            $post = new Post();

            $post->setTitle($title);
            $post->setSummary($this->getRandomPostSummary());
            $post->setContent($this->getPostContent());
            $post->setPublishedAt(DateTimeGenerator::generate('now - ' . $i . 'days'));

            // Ensure that the first post is written by Jane Doe to simplify tests
            // "References" are the way to share objects between fixtures defined
            // in different files. This reference has been added in the UserFixtures
            // file and it contains an instance of the User entity.
            /** @var User $author */
            $author = $this->getReference($i < self::JANE_ADMIN_NUM_POSTS ? 'jane-admin' : 'tom-admin');
            $post->setAuthor($author);

            // for aesthetic reasons, the first blog post always has 2 tags
            foreach ($this->getRandomTags($i > 0 ? random_int(0, 3) : 2) as $tag) {
                $post->addTag($tag);
            }

            foreach (range(1, 5) as $j) {
                $comment = new Comment();

                /** @var User $commentAuthor */
                $commentAuthor = $this->getReference('john-user');
                $comment->setAuthor($commentAuthor);
                $comment->setPublishedAt(DateTimeGenerator::generate('now + ' . ($i + $j) . 'seconds'));
                $comment->setContent($this->getRandomCommentContent());

                $post->addComment($comment);

                $manager->persist($comment);
            }

            $manager->persist($post);
        }

        $manager->flush();
    }

    /**
     * Instead of defining the exact order in which the fixtures files must be loaded,
     * this method defines which other fixtures this file depends on. Then, Doctrine
     * will figure out the best order to fit all the dependencies.
     */
    public function getDependencies(): array
    {
        return [
            TagFixtures::class,
            UserFixtures::class,
        ];
    }

    private function getRandomTags(int $numTags = 0): array
    {
        $tags = [];

        if ($numTags === 0) {
            return $tags;
        }

        $indexes = (array) array_rand($this->getTagNames(), $numTags);
        foreach ($indexes as $index) {
            $tags[] = $this->getReference('tag-' . $index);
        }

        return $tags;
    }
}
