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

namespace Acme\App\Presentation\Web\Core\Component\Blog\Anonymous\PostList;

use Acme\App\Core\Component\Blog\Application\Repository\Doctrine\PostRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Cache;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * @Route("/blog/posts")
 *
 * @author Ryan Weaver <weaverryan@gmail.com>
 * @author Javier Eguiluz <javier.eguiluz@gmail.com>
 * @author Herberto Graca <herberto.graca@gmail.com>
 */
class PostListController extends AbstractController
{
    /**
     * @Route("", defaults={"page": "1", "_format"="html"}, name="post_list")
     * @Route("/rss.xml", defaults={"page": "1", "_format"="xml"}, name="post_list_rss")
     * @Route("/page/{page}", defaults={"_format"="html"}, requirements={"page": "[1-9]\d*"}, name="post_list_paginated")
     * @Method("GET")
     * @Cache(smaxage="10")
     *
     * NOTE: For standard formats, Symfony will also automatically choose the best
     * Content-Type header for the response.
     *
     * @see https://symfony.com/doc/current/quick_tour/the_controller.html#using-formats
     */
    public function getAction(int $page, string $_format, PostRepository $postRepository): Response
    {
        $latestPosts = $postRepository->findLatest($page);

        // Every template name also has two extensions that specify the format and
        // engine for that template.
        // See https://symfony.com/doc/current/templating.html#template-suffix
        return $this->render('@Blog/Anonymous/PostList/get.' . $_format . '.twig', ['posts' => $latestPosts]);
    }

    /**
     * @Route("/search", name="post_list_search")
     * @Method("GET")
     */
    public function searchAction(Request $request, PostRepository $postRepository): Response
    {
        if (!$request->isXmlHttpRequest()) {
            return $this->render('@Blog/Anonymous/PostList/search.html.twig');
        }

        $query = $request->query->get('q', '');
        $limit = $request->query->get('l', 10);
        $foundPosts = $postRepository->findBySearchQuery($query, $limit);

        $results = [];
        foreach ($foundPosts as $post) {
            $results[] = [
                'title' => htmlspecialchars($post->getTitle()),
                'date' => $post->getPublishedAt()->format('M d, Y'),
                'author' => htmlspecialchars($post->getAuthor()->getFullName()),
                'summary' => htmlspecialchars($post->getSummary()),
                'url' => $this->generateUrl('post', ['slug' => $post->getSlug()]),
            ];
        }

        return $this->json($results);
    }
}
