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

namespace Acme\App\Presentation\Web\Core\Port\Form;

use Psr\Http\Message\ServerRequestInterface;

interface FormInterface
{
    public const BUTTON_NAME_SAVE_AND_CREATE_NEW = 'save_and_create_new';

    public function createView();

    public function getData();

    public function handleRequest(ServerRequestInterface $request): void;

    public function shouldBeProcessed(): bool;

    public function clickedButton(string $buttonName): bool;

    public function getFormName(): string;
}
