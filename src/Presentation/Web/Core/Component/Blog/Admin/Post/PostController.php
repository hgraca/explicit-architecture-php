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

use Acme\App\Core\Component\Blog\Application\Repository\PostRepositoryInterface;
use Acme\App\Core\Component\Blog\Application\Service\PostService;
use Acme\App\Presentation\Web\Core\Port\FlashMessage\FlashMessageServiceInterface;
use Acme\App\Presentation\Web\Core\Port\Form\FormFactoryInterface;
use Acme\App\Presentation\Web\Core\Port\Response\ResponseFactoryInterface;
use Acme\App\Presentation\Web\Core\Port\Router\UrlGeneratorInterface;
use Acme\App\Presentation\Web\Core\Port\TemplateEngine\TemplateEngineInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

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
class PostController extends AbstractController
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

    public function __construct(
        PostService $postService,
        PostRepositoryInterface $postRepository,
        FlashMessageServiceInterface $flashMessageService,
        UrlGeneratorInterface $urlGenerator,
        TemplateEngineInterface $templateEngine,
        ResponseFactoryInterface $responseFactory,
        FormFactoryInterface $formFactory
    ) {
        $this->postService = $postService;
        $this->flashMessageService = $flashMessageService;
        $this->urlGenerator = $urlGenerator;
        $this->templateEngine = $templateEngine;
        $this->responseFactory = $responseFactory;
        $this->formFactory = $formFactory;
        $this->postRepository = $postRepository;
    }

    /**
     * Finds and displays a Post entity.
     */
    public function getAction(ServerRequestInterface $request): ResponseInterface
    {
        $post = $this->postRepository->find((int) $request->getAttribute('id'));
        $this->denyAccessUnlessGranted('show', $post, 'Posts can only be shown to their authors.');

        return $this->templateEngine->renderResponse(
            '@Blog/Admin/Post/get.html.twig',
            ['post' => $post]
        );
    }

    /**
     * Displays a form to edit an existing Post entity.
     */
    public function editAction(ServerRequestInterface $request): ResponseInterface
    {
        $post = $this->postRepository->find((int) $request->getAttribute('id'));

        $this->denyAccessUnlessGranted('edit', $post, 'Posts can only be edited by their authors.');

        $form = $this->formFactory->createEditPostForm(
            $post,
            ['action' => $this->urlGenerator->generateUrl('admin_post_post', ['id' => $post->getId()])]
        );

        return $this->templateEngine->renderResponse(
            '@Blog/Admin/Post/edit.html.twig',
            [
                'post' => $post,
                'form' => $form->createView(),
            ]
        );
    }

    /**
     * Receives data from the form to edit an existing Post entity.
     */
    public function postAction(ServerRequestInterface $request): ResponseInterface
    {
        $post = $this->postRepository->find((int) $request->getAttribute('id'));
        $this->denyAccessUnlessGranted('edit', $post, 'Posts can only be edited by their authors.');

        $form = $this->formFactory->createEditPostForm($post);
        $form->handleRequest($request);

        if (!($form->shouldBeProcessed())) {
            return $this->responseFactory->redirectToRoute('admin_post_edit', ['id' => $post->getId()]);
        }

        $this->postService->update($post);

        $this->flashMessageService->success('post.updated_successfully');

        return $this->responseFactory->redirectToRoute('admin_post_edit', ['id' => $post->getId()]);
    }

    /**
     * Deletes a Post entity.
     *
     * The Security annotation value is an expression (if it evaluates to false,
     * the authorization mechanism will prevent the user accessing this resource).
     */
    public function deleteAction(ServerRequestInterface $request): ResponseInterface
    {
        $post = $this->postRepository->find((int) $request->getAttribute('id'));
        $this->denyAccessUnlessGranted('delete', $post, 'Posts can only be deleted by an admin or the author.');

        if (!$this->isCsrfTokenValid('delete', $request->getParsedBody()['token'] ?? '')) {
            return $this->responseFactory->redirectToRoute('admin_post_list');
        }

        $this->postService->delete($post);

        $this->flashMessageService->success('post.deleted_successfully');

        return $this->responseFactory->redirectToRoute('admin_post_list');
    }
}
