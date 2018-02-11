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

namespace Acme\App\Presentation\Web\Core\Component\Blog\User\Comment;

use Acme\App\Core\Component\Blog\Application\Service\CommentService;
use Acme\App\Core\Component\Blog\Domain\Entity\Comment;
use Acme\App\Core\Component\Blog\Domain\Entity\Post;
use Acme\App\Presentation\Web\Core\Port\Form\FormFactoryInterface;
use Acme\App\Presentation\Web\Core\Port\Response\ResponseFactoryInterface;
use Acme\App\Presentation\Web\Core\Port\TemplateEngine\TemplateEngineInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * Controller used to manage blog contents in the public part of the site.
 *
 * @author Ryan Weaver <weaverryan@gmail.com>
 * @author Javier Eguiluz <javier.eguiluz@gmail.com>
 * @author Herberto Graca <herberto.graca@gmail.com>
 */
class CommentController extends AbstractController
{
    /**
     * @var CommentService
     */
    private $commentService;

    /**
     * @var TemplateEngineInterface
     */
    private $templateEngine;

    /**
     * @var ResponseFactoryInterface
     */
    private $responseFactory;

    /**
     * @var FormFactoryInterface
     */
    private $formFactory;

    public function __construct(
        CommentService $commentService,
        TemplateEngineInterface $templateEngine,
        ResponseFactoryInterface $responseFactory,
        FormFactoryInterface $formFactory
    ) {
        $this->commentService = $commentService;
        $this->templateEngine = $templateEngine;
        $this->responseFactory = $responseFactory;
        $this->formFactory = $formFactory;
    }

    /**
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     * @ParamConverter("post", options={"mapping": {"postSlug": "slug"}})
     *
     * NOTE: The ParamConverter mapping is required because the route parameter
     * (postSlug) doesn't match any of the Doctrine entity properties (slug).
     * See https://symfony.com/doc/current/bundles/SensioFrameworkExtraBundle/annotations/converters.html#doctrine-converter
     */
    public function postAction(ServerRequestInterface $request, Post $post): ResponseInterface
    {
        $comment = new Comment();

        $form = $this->formFactory->createCommentForm($comment);
        $form->handleRequest($request);

        if (!$form->shouldBeProcessed()) {
            return $this->templateEngine->renderResponse(
                '@Blog/User/Comment/edit_error.html.twig',
                [
                    'post' => $post,
                    'form' => $form->createView(),
                ]
            );
        }

        $this->commentService->create($post, $comment, $this->getUser());

        return $this->responseFactory->redirectToRoute('post', ['slug' => $post->getSlug()]);
    }

    /**
     * This controller is called directly via the render() function in the
     * blog/post_show.html.twig template. That's why it's not needed to define
     * a route name for it.
     *
     * The "id" of the Post is passed in and then turned into a Post object
     * automatically by the ParamConverter.
     */
    public function editAction(Post $post): ResponseInterface
    {
        $form = $this->formFactory->createCommentForm();

        return $this->templateEngine->renderResponse(
            '@Blog/User/Comment/edit.html.twig',
            [
                'post' => $post,
                'form' => $form->createView(),
            ]
        );
    }
}
