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

namespace Acme\App\Infrastructure\Translation\Symfony;

use Acme\App\Core\Port\Translation\TranslatorInterface;
use Acme\PhpExtension\Enum\AbstractEnum;
use Acme\PhpExtension\Helper\ClassHelper;
use Symfony\Component\Translation\TranslatorInterface as SymfonyTranslatorInterface;

final class Translator implements TranslatorInterface
{
    /**
     * @var SymfonyTranslatorInterface
     */
    private $translator;

    public function __construct(SymfonyTranslatorInterface $translator)
    {
        $this->translator = $translator;
    }

    public function translate(string $key, array $parameters = []): string
    {
        return $this->translator->trans($key, []);
    }

    public function translateEnum(AbstractEnum $enum, array $parameters = []): string
    {
        return $this->translator->trans($this->getTranslationKeyForEnum($enum), []);
    }

    private function getTranslationKeyForEnum(AbstractEnum $enum): string
    {
        return ClassHelper::extractCanonicalClassName(\get_class($enum)) . '::' . $enum;
    }
}
