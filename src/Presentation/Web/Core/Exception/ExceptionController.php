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

namespace Acme\App\Presentation\Web\Core\Exception;

use Symfony\Bundle\TwigBundle\Controller\ExceptionController as TwigExceptionController;
use Symfony\Component\HttpFoundation\Request;

final class ExceptionController extends TwigExceptionController
{
    private const TEMPLATE_NAMESPACE_TWIG = 'Twig';
    private const TEMPLATE_NAMESPACE_WEB = 'Web';

    /**
     * @param Request $request
     * @param string  $format
     * @param int     $code          An HTTP response status code
     * @param bool    $showException
     */
    protected function findTemplate(Request $request, $format, $code, $showException): string
    {
        $name = $showException ? 'exception' : 'error';
        $namespace = self::TEMPLATE_NAMESPACE_WEB;
        if ($showException && $format === 'html') {
            $name = 'exception_full';
            $namespace = self::TEMPLATE_NAMESPACE_TWIG;
        }

        // For error pages, try to find a template for the specific HTTP status code and format
        if (!$showException) {
            $template = sprintf('@%s/Exception/%s%s.%s.twig', $namespace, $name, $code, $format);
            if ($this->templateExists($template)) {
                return $template;
            }
        }

        // try to find a template for the given format
        $template = sprintf('@%s/Exception/%s.%s.twig', $namespace, $name, $format);
        if ($this->templateExists($template)) {
            return $template;
        }

        // default to a generic HTML exception
        $request->setRequestFormat('html');

        return sprintf(
            '@%s/Exception/%s.html.twig',
            $showException ? self::TEMPLATE_NAMESPACE_TWIG : self::TEMPLATE_NAMESPACE_WEB,
            $showException ? 'exception_full' : $name
        );
    }
}
