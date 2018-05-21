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

use Acme\App\Core\Component\Blog\Application\Query\PostQueryInterface;
use Acme\App\Core\Port\TemplateEngine\TemplateEngineInterface;
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
     * @var PostQueryInterface
     */
    private $postQuery;

    public function __construct(
        TemplateEngineInterface $templateEngine,
        PostQueryInterface $postQuery
    ) {
        $this->templateEngine = $templateEngine;
        $this->postQuery = $postQuery;
    }

    /**
     * NOTE: The $post controller argument is automatically injected by Symfony
     * after performing a database query looking for a Post with the 'slug'
     * value given in the route.
     * See https://symfony.com/doc/current/bundles/SensioFrameworkExtraBundle/annotations/converters.html.
     */
    public function get(ServerRequestInterface $request): ResponseInterface
    {
        // Symfony's 'dump()' function is an improved version of PHP's 'var_dump()' but
        // it's not available in the 'prod' environment to prevent leaking sensitive information.
        // It can be used both in PHP files and Twig templates, but it requires to
        // have enabled the DebugBundle. Uncomment the following line to see it in action:
        //
        // dump($post, $this->getUser(), new \DateTimeImmutable());

        return $this->templateEngine->renderResponse(
            '@Blog/Anonymous/Post/get.html.twig',
            $this->postQuery
                ->includeAuthor()
                ->includeTags()
                ->includeComments()
                ->includeCommentsAuthor()
                ->execute($request->getAttribute('slug'))
                ->hydrateSingleResultAs(GetViewModel::class)
        );
    }
}
