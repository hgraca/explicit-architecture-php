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

namespace Acme\App\Core\Port\TemplateEngine;

use Psr\Http\Message\ResponseInterface;

interface TemplateEngineInterface
{
    public const PARSED_METHOD_PREFIXES = 'get|has|is|should|can';

    public function render(string $template, TemplateViewModelInterface $viewModel = null): string;

    public function renderResponse(
        string $template,
        TemplateViewModelInterface $viewModel = null,
        ResponseInterface $response = null
    ): ResponseInterface;

    public function exists(string $template): bool;
}
