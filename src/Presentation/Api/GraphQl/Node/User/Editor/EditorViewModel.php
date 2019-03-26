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

namespace Acme\App\Presentation\Api\GraphQl\Node\User\Editor;

use Acme\App\Core\Component\User\Domain\User\User;
use Acme\App\Core\SharedKernel\Exception\AppRuntimeException;
use Acme\App\Presentation\Api\GraphQl\Node\User\AbstractUserViewModel;
use function json_encode;

final class EditorViewModel extends AbstractUserViewModel
{
    private const TYPE = 'Editor';

    /**
     * @var string
     */
    private $editorViewModelClass = __CLASS__;

    public static function constructFromEntity(User $user): self
    {
        if (!$user->isAdmin()) {
            throw new AppRuntimeException(
                'User must be ' . self::TYPE . ', but it is: ' . json_encode($user->getRoles())
            );
        }

        return new static(
            $user->getId()->toScalar(),
            $user->getFullName(),
            $user->getUsername(),
            $user->getEmail(),
            $user->getMobile(),
            $user->getPassword(),
            self::TYPE
        );
    }

    public function getEditorViewModelClass(): string
    {
        return $this->editorViewModelClass;
    }
}
