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

namespace Acme\App\Presentation\Web\Core\Component\Login\Anonymous;

use Acme\App\Core\Port\Auth\AuthenticationException;
use Acme\App\Core\Port\TemplateEngine\TemplateViewModelInterface;

final class LoginViewModel implements TemplateViewModelInterface
{
    /**
     * @var string
     */
    private $lastUsername;

    /**
     * @var string
     */
    private $errorMessageKey;

    /**
     * @var array
     */
    private $errorMessageData;

    /**
     * The view model constructor depends on the most raw elements possible.
     */
    public function __construct(string $lastUsername, string $errorMessageKey, array $errorMessageData)
    {
        $this->lastUsername = $lastUsername;
        $this->errorMessageKey = $errorMessageKey;
        $this->errorMessageData = $errorMessageData;
    }

    /**
     * We create named constructors for the cases where we need to extract the raw data from complex data structures.
     */
    public static function fromLastUsernameAndError(string $lastUsername, ?AuthenticationException $error): self
    {
        return new self(
            $lastUsername,
            $error ? $error->getMessageKey() : '',
            $error ? $error->getMessageData() : []
        );
    }

    public function getLastUsername(): string
    {
        return $this->lastUsername;
    }

    public function getErrorMessageKey(): string
    {
        return $this->errorMessageKey;
    }

    public function getErrorMessageData(): array
    {
        return $this->errorMessageData;
    }
}
