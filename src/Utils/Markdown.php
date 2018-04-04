<?php

/*
 * This file is part of the Explicit Architecture POC,
 * which is created on top of the Symfony Demo application.
 *
 * (c) Herberto Graça <herberto.graca@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Acme\App\Utils;

use HTMLPurifier;
use HTMLPurifier_Config;
use Parsedown;

/**
 * This class is a light interface between an external Markdown parser library
 * and the application. It's generally recommended to create these light interfaces
 * to decouple your application from the implementation details of the third-party library.
 *
 * @author Ryan Weaver <weaverryan@gmail.com>
 * @author Javier Eguiluz <javier.eguiluz@gmail.com>
 */
class Markdown
{
    /**
     * @var Parsedown
     */
    private $parser;

    /**
     * @var HTMLPurifier
     */
    private $purifier;

    public function __construct()
    {
        $this->parser = new Parsedown();

        $purifierConfig = HTMLPurifier_Config::create([
            'Cache.DefinitionImpl' => null, // Disable caching
        ]);
        $this->purifier = new HTMLPurifier($purifierConfig);
    }

    public function toHtml(string $text): string
    {
        $html = $this->parser->text($text);
        $safeHtml = $this->purifier->purify($html);

        return $safeHtml;
    }
}
