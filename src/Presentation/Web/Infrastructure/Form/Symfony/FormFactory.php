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

namespace Acme\App\Presentation\Web\Infrastructure\Form\Symfony;

use Acme\App\Presentation\Web\Core\Port\Form\FormFactoryInterface;
use Acme\App\Presentation\Web\Core\Port\Form\FormInterface;
use Acme\App\Presentation\Web\Infrastructure\Form\Symfony\Form\CommentForm;
use Acme\App\Presentation\Web\Infrastructure\Form\Symfony\Form\CreatePostForm;
use Acme\App\Presentation\Web\Infrastructure\Form\Symfony\Form\EditPostForm;
use Symfony\Bridge\PsrHttpMessage\HttpFoundationFactoryInterface;
use Symfony\Component\Form\Form as SymfonyForm;
use Symfony\Component\Form\FormFactoryInterface as SymfonyFormFactoryInterface;

final class FormFactory implements FormFactoryInterface
{
    /**
     * @var SymfonyFormFactoryInterface
     */
    private $symfonyFormFactory;

    /**
     * @var HttpFoundationFactoryInterface
     */
    private $symfonyResponseFactory;

    public function __construct(
        HttpFoundationFactoryInterface $symfonyResponseFactory,
        SymfonyFormFactoryInterface $symfonyFormFactory
    ) {
        $this->symfonyResponseFactory = $symfonyResponseFactory;
        $this->symfonyFormFactory = $symfonyFormFactory;
    }

    public function createEditPostForm($data = null, array $options = []): FormInterface
    {
        /** @var SymfonyForm $form */
        $form = $this->symfonyFormFactory->create(EditPostForm::class, $data, $options);

        return new Form($this->symfonyResponseFactory, $form);
    }

    public function createCreatePostForm($data = null, array $options = []): FormInterface
    {
        /** @var SymfonyForm $form */
        $form = $this->symfonyFormFactory->create(CreatePostForm::class, $data, $options);

        return new Form($this->symfonyResponseFactory, $form);
    }

    public function createCommentForm($data = null, array $options = []): FormInterface
    {
        /** @var SymfonyForm $form */
        $form = $this->symfonyFormFactory->create(CommentForm::class, $data, $options);

        return new Form($this->symfonyResponseFactory, $form);
    }
}
