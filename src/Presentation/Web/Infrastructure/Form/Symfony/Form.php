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

use Acme\App\Presentation\Web\Core\Port\Form\FormInterface;
use Psr\Http\Message\ServerRequestInterface;
use Symfony\Bridge\PsrHttpMessage\HttpFoundationFactoryInterface;
use Symfony\Component\Form\Form as SymfonyForm;
use Symfony\Component\Form\FormView;

final class Form implements FormInterface
{
    /**
     * @var SymfonyForm
     */
    private $symfonyForm;

    /**
     * @var HttpFoundationFactoryInterface
     */
    private $symfonyResponseFactory;

    public function __construct(
        HttpFoundationFactoryInterface $symfonyResponseFactory,
        SymfonyForm $symfonyForm
    ) {
        $this->symfonyForm = $symfonyForm;
        $this->symfonyResponseFactory = $symfonyResponseFactory;
    }

    public function createView(): FormView
    {
        return $this->symfonyForm->createView();
    }

    public function getData()
    {
        return $this->symfonyForm->getData();
    }

    public function handleRequest(ServerRequestInterface $request): void
    {
        $this->symfonyForm->handleRequest($this->symfonyResponseFactory->createRequest($request));
    }

    public function shouldBeProcessed(): bool
    {
        return $this->symfonyForm->isSubmitted() && $this->symfonyForm->isValid();
    }

    public function clickedButton(string $buttonName): bool
    {
        return $this->symfonyForm->has($buttonName)
            ? $this->symfonyForm->get($buttonName)->isClicked()
            : false;
    }

    public function getFormName(): string
    {
        return $this->symfonyForm->getName();
    }
}
