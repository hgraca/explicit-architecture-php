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

namespace Acme\App\Presentation\Web\Core\Component\Blog\Admin\Post;

use Acme\App\Core\Component\Blog\Application\Query\PostQueryInterface;
use Acme\App\Core\Component\Blog\Application\Repository\PostRepositoryInterface;
use Acme\App\Core\Component\Blog\Application\Service\PostService;
use Acme\App\Core\Component\Blog\Domain\Post\PostId;
use Acme\App\Core\Port\Auth\AuthenticationServiceInterface;
use Acme\App\Core\Port\Auth\AuthorizationServiceInterface;
use Acme\App\Core\Port\Auth\ResourceActionVoterInterface;
use Acme\App\Core\Port\Router\UrlGeneratorInterface;
use Acme\App\Core\Port\TemplateEngine\TemplateEngineInterface;
use Acme\App\Presentation\Web\Core\Port\FlashMessage\FlashMessageServiceInterface;
use Acme\App\Presentation\Web\Core\Port\Form\FormFactoryInterface;
use Acme\App\Presentation\Web\Core\Port\Response\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Controller used to manage blog contents in the backend.
 *
 * Please note that the application backend is developed manually for learning
 * purposes. However, in your real Symfony application you should use any of the
 * existing bundles that let you generate ready-to-use backends without effort.
 *
 * See http://knpbundles.com/keyword/admin
 *
 * @author Ryan Weaver <weaverryan@gmail.com>
 * @author Javier Eguiluz <javier.eguiluz@gmail.com>
 * @author Herberto Graca <herberto.graca@gmail.com>
 */
class PostController
{
    /**
     * @var PostService
     */
    private $postService;

    /**
     * @var FlashMessageServiceInterface
     */
    private $flashMessageService;

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
     * @var FormFactoryInterface
     */
    private $formFactory;

    /**
     * @var PostRepositoryInterface
     */
    private $postRepository;

    /**
     * @var AuthorizationServiceInterface
     */
    private $authorizationService;

    /**
     * @var AuthenticationServiceInterface
     */
    private $authenticationService;

    /**
     * @var PostQueryInterface
     */
    private $postQuery;

    public function __construct(
        PostService $postService,
        PostRepositoryInterface $postRepository,
        FlashMessageServiceInterface $flashMessageService,
        UrlGeneratorInterface $urlGenerator,
        TemplateEngineInterface $templateEngine,
        ResponseFactoryInterface $responseFactory,
        FormFactoryInterface $formFactory,
        AuthorizationServiceInterface $authorizationService,
        AuthenticationServiceInterface $authenticationService,
        PostQueryInterface $postQuery
    ) {
        $this->postService = $postService;
        $this->flashMessageService = $flashMessageService;
        $this->urlGenerator = $urlGenerator;
        $this->templateEngine = $templateEngine;
        $this->responseFactory = $responseFactory;
        $this->formFactory = $formFactory;
        $this->postRepository = $postRepository;
        $this->authorizationService = $authorizationService;
        $this->authenticationService = $authenticationService;
        $this->postQuery = $postQuery;
    }

    /**
     * Finds and displays a Post entity.
     */
    public function get(ServerRequestInterface $request): ResponseInterface
    {
        $this->authorizationService->denyAccessUnlessGranted(
            [],
            ResourceActionVoterInterface::SHOW,
            'When the user is authenticated, posts can only be shown to their authors.',
            $this->postRepository->find(new PostId($request->getAttribute('id')))
        );

        return $this->templateEngine->renderResponse(
            '@Blog/Admin/Post/get.html.twig',
            $this->postQuery
                ->includeAuthor()
                ->includeTags()
                ->execute(new PostId($request->getAttribute('id')))
                ->hydrateSingleResultAs(GetViewModel::class)
        );
    }

    /**
     * Displays a form to edit an existing Post entity.
     */
    public function edit(ServerRequestInterface $request): ResponseInterface
    {
        $post = $this->postRepository->find(new PostId($request->getAttribute('id')));

        $this->authorizationService->denyAccessUnlessGranted(
            [],
            ResourceActionVoterInterface::EDIT,
            'Posts can only be edited by their authors.',
            $post
        );

        $form = $this->formFactory->createEditPostForm(
            $post,
            ['action' => $this->urlGenerator->generateUrl('admin_post_post', ['id' => (string) $post->getId()])]
        );

        return $this->templateEngine->renderResponse(
            '@Blog/Admin/Post/edit.html.twig',
            EditViewModel::fromPostAndForm($post, $form)
        );
    }

    /**
     * Receives data from the form to edit an existing Post entity.
     */
    public function post(ServerRequestInterface $request): ResponseInterface
    {
        $post = $this->postRepository->find(new PostId($request->getAttribute('id')));

        $this->authorizationService->denyAccessUnlessGranted(
            [],
            ResourceActionVoterInterface::EDIT,
            'Posts can only be edited by their authors.',
            $post
        );

        $form = $this->formFactory->createEditPostForm($post);
        $form->handleRequest($request);

        if (!($form->shouldBeProcessed())) {
            return $this->responseFactory->redirectToRoute('admin_post_edit', ['id' => (string) $post->getId()]);
        }

        $this->flashMessageService->success('post.updated_successfully');

        return $this->responseFactory->redirectToRoute('admin_post_edit', ['id' => (string) $post->getId()]);
    }

    /**
     * Deletes a Post entity.
     *
     * The Security annotation value is an expression (if it evaluates to false,
     * the authorization mechanism will prevent the user accessing this resource).
     */
    public function delete(ServerRequestInterface $request): ResponseInterface
    {
        $post = $this->postRepository->find(new PostId($request->getAttribute('id')));

        $this->authorizationService->denyAccessUnlessGranted(
            [],
            ResourceActionVoterInterface::DELETE,
            'Posts can only be deleted by an admin or the author.',
            $post
        );

        if (!$this->authenticationService->isCsrfTokenValid('delete', $request->getParsedBody()['token'] ?? '')) {
            return $this->responseFactory->redirectToRoute('admin_post_list');
        }

        $this->postService->delete($post);

        $this->flashMessageService->success('post.deleted_successfully');

        return $this->responseFactory->redirectToRoute('admin_post_list');
    }
}
