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

use Acme\App\Core\Component\Blog\Application\Repository\PostRepositoryInterface;
use Acme\App\Core\Component\Blog\Application\Service\PostService;
use Acme\App\Core\Component\Blog\Domain\Entity\Post;
use Acme\App\Presentation\Web\Core\Component\Blog\Admin\FormType\Entity\PostType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Controller used to manage blog contents in the backend.
 *
 * Please note that the application backend is developed manually for learning
 * purposes. However, in your real Symfony application you should use any of the
 * existing bundles that let you generate ready-to-use backends without effort.
 *
 * See http://knpbundles.com/keyword/admin
 *
 * @Route("/admin/posts")
 *
 * @author Ryan Weaver <weaverryan@gmail.com>
 * @author Javier Eguiluz <javier.eguiluz@gmail.com>
 * @author Herberto Graca <herberto.graca@gmail.com>
 */
class PostListController extends AbstractController
{
    /**
     * @var PostService
     */
    private $postService;

    public function __construct(PostService $postService)
    {
        $this->postService = $postService;
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
     *
     * @Route("/", name="admin_index")
     * @Route("/", name="admin_post_list")
     * @Method("GET")
     */
    public function getAction(PostRepositoryInterface $postRepository): Response
    {
        $authorPosts = $postRepository->findByAuthorOrderedByPublishDate($this->getUser());

        return $this->render('@Blog/Admin/PostList/get.html.twig', ['posts' => $authorPosts]);
    }

    /**
     * Shows the form to create a new Post entity.
     *
     * @Route("/new", name="admin_post_new")
     * @Method({"GET"})
     *
     * NOTE: the Method annotation is optional, but it's a recommended practice
     * to constraint the HTTP methods each controller responds to (by default
     * it responds to all methods).
     */
    public function newAction(): Response
    {
        $post = new Post();

        // See https://symfony.com/doc/current/book/forms.html#submitting-forms-with-multiple-buttons
        $form = $this->createForm(PostType::class, $post, ['action' => $this->generateUrl('admin_post_new_post')])
            ->add('saveAndCreateNew', SubmitType::class);

        return $this->render('@Blog/Admin/PostList/new.html.twig', [
            'post' => $post,
            'form' => $form->createView(),
        ]);
    }

    /**
     * Creates a new Post entity.
     *
     * @Route("", name="admin_post_new_post")
     * @Method({"POST"})
     *
     * NOTE: the Method annotation is optional, but it's a recommended practice
     * to constraint the HTTP methods each controller responds to (by default
     * it responds to all methods).
     */
    public function postAction(Request $request): Response
    {
        $post = new Post();

        // See https://symfony.com/doc/current/book/forms.html#submitting-forms-with-multiple-buttons
        $form = $this->createForm(PostType::class, $post)
            ->add('saveAndCreateNew', SubmitType::class);

        $form->handleRequest($request);

        // the isSubmitted() method is completely optional because the other
        // isValid() method already checks whether the form is submitted.
        // However, we explicitly add it to improve code readability.
        // See https://symfony.com/doc/current/best_practices/forms.html#handling-form-submits
        if (!($form->isSubmitted() && $form->isValid())) {
            return $this->redirectToRoute('admin_post_list');
        }

        $this->postService->create($post, $this->getUser());

        // Flash messages are used to notify the user about the result of the
        // actions. They are deleted automatically from the session as soon
        // as they are accessed.
        // See https://symfony.com/doc/current/book/controller.html#flash-messages
        $this->addFlash('success', 'post.created_successfully');

        if ($form->get('saveAndCreateNew')->isClicked()) {
            return $this->redirectToRoute('admin_post_new');
        }

        return $this->redirectToRoute('admin_post_list');
    }
}
