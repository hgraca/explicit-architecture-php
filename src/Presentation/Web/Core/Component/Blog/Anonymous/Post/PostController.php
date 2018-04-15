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

namespace Acme\App\Presentation\Web\Core\Component\Blog\Anonymous\Post;

use Acme\App\Core\Component\Blog\Application\Repository\PostRepositoryInterface;
use Acme\App\Core\Component\Blog\Domain\Entity\Comment;
use Acme\App\Presentation\Web\Core\Port\TemplateEngine\TemplateEngineInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Controller used to manage blog contents in the public part of the site.
 *
 * @author Ryan Weaver <weaverryan@gmail.com>
 * @author Javier Eguiluz <javier.eguiluz@gmail.com>
 * @author Herberto Graca <herberto.graca@gmail.com>
 */
class PostController
{
    /**
     * @var TemplateEngineInterface
     */
    private $templateEngine;

    /**
     * @var PostRepositoryInterface
     */
    private $postRepository;

    public function __construct(
        TemplateEngineInterface $templateEngine,
        PostRepositoryInterface $postRepository
    ) {
        $this->templateEngine = $templateEngine;
        $this->postRepository = $postRepository;
    }

    /**
     * NOTE: The $post controller argument is automatically injected by Symfony
     * after performing a database query looking for a Post with the 'slug'
     * value given in the route.
     * See https://symfony.com/doc/current/bundles/SensioFrameworkExtraBundle/annotations/converters.html.
     */
    public function getAction(ServerRequestInterface $request): ResponseInterface
    {
        $post = $this->postRepository->findBySlug($request->getAttribute('slug'));

        /*
         * For some reason when running the tests we get the comment dates, and order, all weird.
         * For now we will order them by their ID, we will fix this when we have a way to control the DateTime
         * objects during the tests, to make sure the dates and times are as they would in production.
         */
        $commentList = $post->getComments()->toArray();
        usort(
            $commentList,
            function (Comment $commentA, Comment $commentB) {
                return ($commentA->getId() > $commentB->getId()) ? -1 : 1;
            }
        );

        // Symfony's 'dump()' function is an improved version of PHP's 'var_dump()' but
        // it's not available in the 'prod' environment to prevent leaking sensitive information.
        // It can be used both in PHP files and Twig templates, but it requires to
        // have enabled the DebugBundle. Uncomment the following line to see it in action:
        //
        // dump($post, $this->getUser(), new \DateTime());

        return $this->templateEngine->renderResponse(
            '@Blog/Anonymous/Post/get.html.twig',
            GetViewModel::fromPostAndCommentList($post, ...$commentList)
        );
    }
}
