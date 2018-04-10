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

use Acme\App\Core\Component\Blog\Application\Query\FindPostsBySearchRequestQueryInterface;
use Acme\App\Core\Component\Blog\Application\Repository\PostRepositoryInterface;
use Acme\App\Presentation\Web\Core\Port\Paginator\PaginatorFactoryInterface;
use Acme\App\Presentation\Web\Core\Port\Response\ResponseFactoryInterface;
use Acme\App\Presentation\Web\Core\Port\Router\UrlGeneratorInterface;
use Acme\App\Presentation\Web\Core\Port\TemplateEngine\TemplateEngineInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * @author Ryan Weaver <weaverryan@gmail.com>
 * @author Javier Eguiluz <javier.eguiluz@gmail.com>
 * @author Herberto Graca <herberto.graca@gmail.com>
 */
class PostListController
{
    /**
     * @var PaginatorFactoryInterface
     */
    private $paginatorFactory;

    /**
     * @var UrlGeneratorInterface
     */
    private $urlGenerator;

    /**
     * @var TemplateEngineInterface
     */
    private $templateEngine;

    /**
     * @var ResponseFactoryInterface
     */
    private $responseFactory;

    /**
     * @var PostRepositoryInterface
     */
    private $postRepository;

    /**
     * @var FindPostsBySearchRequestQueryInterface
     */
    private $findPostsBySearchRequestQuery;

    public function __construct(
        PaginatorFactoryInterface $paginatorFactory,
        UrlGeneratorInterface $urlGenerator,
        TemplateEngineInterface $templateEngine,
        ResponseFactoryInterface $responseFactory,
        PostRepositoryInterface $postRepository,
        FindPostsBySearchRequestQueryInterface $findPostsBySearchRequestQuery
    ) {
        $this->paginatorFactory = $paginatorFactory;
        $this->urlGenerator = $urlGenerator;
        $this->templateEngine = $templateEngine;
        $this->responseFactory = $responseFactory;
        $this->postRepository = $postRepository;
        $this->findPostsBySearchRequestQuery = $findPostsBySearchRequestQuery;
    }

    /**
     * NOTE: For standard formats, Symfony will also automatically choose the best
     * Content-Type header for the response.
     *
     * @see https://symfony.com/doc/current/quick_tour/the_controller.html#using-formats
     */
    public function getAction(int $page, string $_format): ResponseInterface
    {
        $latestPosts = $this->postRepository->findLatest();

        // Every template name also has two extensions that specify the format and
        // engine for that template.
        // See https://symfony.com/doc/current/templating.html#template-suffix

        $response = $this->templateEngine->renderResponse(
            '@Blog/Anonymous/PostList/get.' . $_format . '.twig',
            $_format === 'xml'
                ? GetXmlViewModel::fromPostList($this->paginatorFactory, $page, ...$latestPosts)
                : GetHtmlViewModel::fromPostList($this->paginatorFactory, $page, ...$latestPosts)
        );

        return $response->withAddedHeader('Cache-Control', 's-maxage=10');
    }

    public function searchAction(ServerRequestInterface $request): ResponseInterface
    {
        if (!$this->isXmlHttpRequest($request)) {
            return $this->templateEngine->renderResponse('@Blog/Anonymous/PostList/search.html.twig');
        }

        $query = $request->getQueryParams()['q'] ?? '';
        $limit = $request->getQueryParams()['l'] ?? 10;
        $foundPosts = $this->findPostsBySearchRequestQuery->execute($query, (int) $limit);

        $results = [];
        foreach ($foundPosts as $post) {
            $results[] = [
                'title' => htmlspecialchars($post->getTitle()),
                'date' => $post->getPublishedAt()->format('M d, Y'),
                'author' => htmlspecialchars($post->getFullName()),
                'summary' => htmlspecialchars($post->getSummary()),
                'url' => $this->urlGenerator->generateUrl('post', ['slug' => $post->getSlug()]),
            ];
        }

        return $this->responseFactory->respondJson($results);
    }

    private function isXmlHttpRequest(ServerRequestInterface $request): bool
    {
        return ($request->getHeader('X-Requested-With')[0] ?? '') === 'XMLHttpRequest';
    }
}
