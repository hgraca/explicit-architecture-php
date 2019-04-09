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

namespace Acme\App\Presentation\Web\Core\Component\Blog\Admin\PostList;

use Acme\App\Core\Component\Blog\Application\Query\PostDto;
use Acme\App\Core\Component\Blog\Application\Query\PostListQueryInterface;
use Acme\App\Core\Component\Blog\Application\Service\PostService;
use Acme\App\Core\Component\Blog\Domain\Post\Post;
use Acme\App\Core\Port\Auth\Authentication\AuthenticationServiceInterface;
use Acme\App\Core\Port\Router\UrlGeneratorInterface;
use Acme\App\Core\Port\TemplateEngine\TemplateEngineInterface;
use Acme\App\Presentation\Web\Core\Port\FlashMessage\FlashMessageServiceInterface;
use Acme\App\Presentation\Web\Core\Port\Form\FormFactoryInterface;
use Acme\App\Presentation\Web\Core\Port\Form\FormInterface;
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
class PostListController
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
     * @var AuthenticationServiceInterface
     */
    private $authenticationService;

    public function __construct(
        PostService $postService,
        FlashMessageServiceInterface $flashMessageService,
        UrlGeneratorInterface $urlGenerator,
        TemplateEngineInterface $templateEngine,
        ResponseFactoryInterface $responseFactory,
        FormFactoryInterface $formFactory,
        AuthenticationServiceInterface $authenticationService
    ) {
        $this->postService = $postService;
        $this->flashMessageService = $flashMessageService;
        $this->urlGenerator = $urlGenerator;
        $this->templateEngine = $templateEngine;
        $this->responseFactory = $responseFactory;
        $this->formFactory = $formFactory;
        $this->authenticationService = $authenticationService;
    }

    /**
     * Lists all Post entities.
     *
     * This controller responds to two different routes with the same URL:
     *     'admin_post_list' is the route with a name that follows the same
     *     structure as the rest of the controllers of this class.
     *     'admin_index' is a nice shortcut to the backend homepage. This allows
     *     to create simpler links in the templates. Moreover, in the future we
     *     could move this annotation to any other controller while maintaining
     *     the route name and therefore, without breaking any existing link.
     */
    public function get(PostListQueryInterface $postListQuery): ResponseInterface
    {
        $postDtoList = $postListQuery->execute($this->authenticationService->getLoggedInUserId())
            ->hydrateResultItemsAs(PostDto::class);

        return $this->templateEngine->renderResponse(
            '@Blog/Admin/PostList/get.html.twig',
            GetViewModel::fromPostDtoList(...$postDtoList)
        );
    }

    /**
     * Shows the form to create a new Post entity.
     *
     * NOTE: the Method annotation is optional, but it's a recommended practice
     * to constraint the HTTP methods each controller responds to (by default
     * it responds to all methods).
     */
    public function new(): ResponseInterface
    {
        $form = $this->createCreatePostForm(new Post());

        return $this->renderCreatePost($form);
    }

    /**
     * Creates a new Post entity.
     *
     * NOTE: the Method annotation is optional, but it's a recommended practice
     * to constraint the HTTP methods each controller responds to (by default
     * it responds to all methods).
     */
    public function post(ServerRequestInterface $request): ResponseInterface
    {
        $post = new Post();

        $form = $this->createCreatePostForm($post);

        $form->handleRequest($request);

        // the isSubmitted() method is completely optional because the other
        // isValid() method already checks whether the form is submitted.
        // However, we explicitly add it to improve code readability.
        // See https://symfony.com/doc/current/best_practices/forms.html#handling-form-submits
        if (!$form->shouldBeProcessed()) {
            return $this->renderCreatePost($form);
        }

        $this->postService->create($post, $this->authenticationService->getLoggedInUserId());

        // Flash messages are used to notify the user about the result of the
        // actions. They are deleted automatically from the session as soon
        // as they are accessed.
        // See https://symfony.com/doc/current/book/controller.html#flash-messages
        $this->flashMessageService->success('post.created_successfully');

        // See https://symfony.com/doc/current/book/forms.html#submitting-forms-with-multiple-buttons
        if ($form->clickedButton(FormInterface::BUTTON_NAME_SAVE_AND_CREATE_NEW)) {
            return $this->responseFactory->redirectToRoute('admin_post_new');
        }

        return $this->responseFactory->redirectToRoute('admin_post_list');
    }

    protected function createCreatePostForm(Post $post): FormInterface
    {
        return $this->formFactory->createCreatePostForm(
            $post,
            ['action' => $this->urlGenerator->generateUrl('admin_post_new_post')]
        );
    }

    protected function renderCreatePost(FormInterface $form): ResponseInterface
    {
        return $this->templateEngine->renderResponse(
            '@Blog/Admin/PostList/new.html.twig',
            NewViewModel::fromPostAndForm($form)
        );
    }
}
