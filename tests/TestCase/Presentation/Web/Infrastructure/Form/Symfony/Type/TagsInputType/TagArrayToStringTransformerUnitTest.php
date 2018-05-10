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

namespace Acme\App\Test\TestCase\Presentation\Web\Infrastructure\Form\Symfony\Type\TagsInputType;

use Acme\App\Core\Component\Blog\Domain\Entity\Tag;
use Acme\App\Presentation\Web\Infrastructure\Form\Symfony\Type\TagsInputType\TagArrayToStringTransformer;
use Acme\App\Test\Framework\AbstractUnitTest;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\ORM\EntityRepository;

/**
 * Tests that tags are transformed correctly using the data transformer.
 *
 * See https://symfony.com/doc/current/testing/database.html
 */
class TagArrayToStringTransformerUnitTest extends AbstractUnitTest
{
    /**
     * Ensures that tags are created correctly.
     *
     * @throws \Doctrine\ORM\ORMException
     */
    public function testCreateTheRightAmountOfTags(): void
    {
        $tags = $this->getMockedTransformer()->reverseTransform('Hello, Demo, How');

        $this->assertCount(3, $tags);
        $this->assertSame('Hello', (string) $tags[0]);
    }

    /**
     * Ensures that empty tags and errors in the number of commas are
     * dealt correctly.
     *
     * @throws \Doctrine\ORM\ORMException
     */
    public function testCreateTheRightAmountOfTagsWithTooManyCommas(): void
    {
        $transformer = $this->getMockedTransformer();

        $this->assertCount(3, $transformer->reverseTransform('Hello, Demo,, How'));
        $this->assertCount(3, $transformer->reverseTransform('Hello, Demo, How,'));
    }

    /**
     * Ensures that leading/trailing spaces are ignored for tag names.
     *
     * @throws \Doctrine\ORM\ORMException
     */
    public function testTrimNames(): void
    {
        $tags = $this->getMockedTransformer()->reverseTransform('   Hello   ');

        $this->assertSame('Hello', (string) $tags[0]);
    }

    /**
     * Ensures that duplicated tag names are ignored.
     *
     * @throws \Doctrine\ORM\ORMException
     */
    public function testDuplicateNames(): void
    {
        $tags = $this->getMockedTransformer()->reverseTransform('Hello, Hello, Hello');

        $this->assertCount(1, $tags);
    }

    /**
     * Ensures that the transformer uses tags already persisted in the database.
     *
     * @throws \Doctrine\ORM\ORMException
     */
    public function testUsesAlreadyDefinedTags(): void
    {
        $persistedTags = [
            new Tag('Hello'),
            new Tag('World'),
        ];
        $tags = $this->getMockedTransformer($persistedTags)->reverseTransform('Hello, World, How, Are, You');

        $this->assertCount(5, $tags);
        $this->assertSame($persistedTags[0], $tags[0]);
        $this->assertSame($persistedTags[1], $tags[1]);
    }

    /**
     * Ensures that the transformation from Tag instances to a simple string
     * works as expected.
     *
     * @throws \Doctrine\ORM\ORMException
     */
    public function testTransform(): void
    {
        $persistedTags = [
            new Tag('Hello'),
            new Tag('World'),
        ];
        $transformed = $this->getMockedTransformer()->transform($persistedTags);

        $this->assertSame('Hello,World', $transformed);
    }

    /**
     * This helper method mocks the real TagArrayToStringTransformer class to
     * simplify the tests. See https://phpunit.de/manual/current/en/test-doubles.html.
     *
     * @param array $findByReturnValues The values returned when calling to the findBy() method
     *
     * @throws \Doctrine\ORM\ORMException
     *
     * @return TagArrayToStringTransformer
     */
    private function getMockedTransformer(array $findByReturnValues = []): TagArrayToStringTransformer
    {
        $tagRepository = $this->getMockBuilder(EntityRepository::class)
            ->disableOriginalConstructor()
            ->getMock();
        $tagRepository->expects($this->any())
            ->method('findBy')
            ->will($this->returnValue($findByReturnValues));

        $entityManager = $this
            ->getMockBuilder(ObjectManager::class)
            ->disableOriginalConstructor()
            ->getMock();
        $entityManager->expects($this->any())
            ->method('getRepository')
            ->will($this->returnValue($tagRepository));

        return new TagArrayToStringTransformer($entityManager);
    }
}
