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
use Acme\App\Presentation\Web\Core\Port\Form\FormInterface;
use Acme\App\Presentation\Web\Core\Port\TemplateEngine\TemplateViewModelInterface;

final class EditViewModel implements TemplateViewModelInterface
{
    /**
     * @var FormInterface
     */
    private $form;

    /**
     * @var int
     */
    private $postId;

    /**
     * The view model constructor depends on the most raw elements possible.
     */
    public function __construct(int $postId, FormInterface $form)
    {
        $this->postId = $postId;
        $this->form = $form;
    }

    /**
     * We create named constructors for the cases where we need to extract the raw data from complex data structures.
     */
    public static function fromPostAndForm(Post $post, FormInterface $form): self
    {
        return new self($post->getId(), $form);
    }

    public function getForm(): FormInterface
    {
        return $this->form;
    }

    public function getPostId(): int
    {
        return $this->postId;
    }
}
