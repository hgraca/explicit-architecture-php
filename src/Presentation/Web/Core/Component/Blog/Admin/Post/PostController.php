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

use Acme\App\Core\Component\Blog\Domain\Entity\Post;
use Acme\App\Presentation\Web\Core\Component\Blog\Admin\FormType\Entity\PostType;
use Acme\StdLib\String\Slugger;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
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
class PostController extends AbstractController
{
    /**
     * Finds and displays a Post entity.
     *
     * @Route("/{id}", requirements={"id": "\d+"}, name="admin_post_show")
     * @Method("GET")
     */
    public function getAction(Post $post): Response
    {
        // This security check can also be performed
        // using an annotation: @Security("is_granted('show', post)")
        $this->denyAccessUnlessGranted('show', $post, 'Posts can only be shown to their authors.');

        return $this->render(
            '@Blog/Admin/Post/get.html.twig',
            ['post' => $post]
        );
    }

    /**
     * Displays a form to edit an existing Post entity.
     *
     * @Route("/{id}/edit", requirements={"id": "\d+"}, name="admin_post_edit")
     * @Method({"GET"})
     */
    public function editAction(Post $post): Response
    {
        $this->denyAccessUnlessGranted('edit', $post, 'Posts can only be edited by their authors.');

        $form = $this->createForm(
            PostType::class,
            $post,
            ['action' => $this->generateUrl('admin_post_post', ['id' => $post->getId()])]
        );

        return $this->render(
            '@Blog/Admin/Post/edit.html.twig',
            [
                'post' => $post,
                'form' => $form->createView(),
            ]
        );
    }

    /**
     * Recives data from the form to edit an existing Post entity.
     *
     * @Route("/{id}", requirements={"id": "\d+"}, name="admin_post_post")
     * @Method({"POST"})
     */
    public function postAction(Request $request, Post $post): Response
    {
        $this->denyAccessUnlessGranted('edit', $post, 'Posts can only be edited by their authors.');

        $form = $this->createForm(PostType::class, $post);
        $form->handleRequest($request);

        if (!($form->isSubmitted() && $form->isValid())) {
            return $this->redirectToRoute('admin_post_edit', ['id' => $post->getId()]);
        }

        $post->setSlug(Slugger::slugify($post->getTitle()));
        $this->getDoctrine()->getManager()->flush();

        $this->addFlash('success', 'post.updated_successfully');

        return $this->redirectToRoute('admin_post_edit', ['id' => $post->getId()]);
    }

    /**
     * Deletes a Post entity.
     *
     * @Route("/{id}/delete", name="admin_post_delete")
     * @Method("POST")
     * @Security("is_granted('delete', post)")
     *
     * The Security annotation value is an expression (if it evaluates to false,
     * the authorization mechanism will prevent the user accessing this resource).
     */
    public function deleteAction(Request $request, Post $post): Response
    {
        if (!$this->isCsrfTokenValid('delete', $request->request->get('token'))) {
            return $this->redirectToRoute('admin_post_list');
        }

        // Delete the tags associated with this blog post. This is done automatically
        // by Doctrine, except for SQLite (the database used in this application)
        // because foreign key support is not enabled by default in SQLite
        $post->getTags()->clear();

        $em = $this->getDoctrine()->getManager();
        $em->remove($post);
        $em->flush();

        $this->addFlash('success', 'post.deleted_successfully');

        return $this->redirectToRoute('admin_post_list');
    }
}
