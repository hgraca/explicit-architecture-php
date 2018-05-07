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

use Acme\App\Core\Port\TemplateEngine\TemplateViewModelInterface;
use Acme\App\Presentation\Web\Core\Port\Form\FormInterface;

final class NewViewModel implements TemplateViewModelInterface
{
    /**
     * @var FormInterface
     */
    private $form;

    /**
     * The view model constructor depends on the most raw elements possible.
     */
    public function __construct(FormInterface $form)
    {
        $this->form = $form;
    }

    /**
     * We create named constructors for the cases where we need to extract the raw data from complex data structures.
     */
    public static function fromPostAndForm(FormInterface $form): self
    {
        return new self($form);
    }

    public function getForm(): FormInterface
    {
        return $this->form;
    }
}
