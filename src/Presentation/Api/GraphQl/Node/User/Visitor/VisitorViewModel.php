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

namespace Acme\App\Presentation\Api\GraphQl\Node\User\Visitor;

use Acme\App\Presentation\Api\GraphQl\Node\User\AbstractUserViewModel;

final class VisitorViewModel extends AbstractUserViewModel
{
    private const TYPE = 'Visitor';

    /**
     * @var string
     */
    private $visitorViewModelClass = __CLASS__;

    public static function construct(): self
    {
        return new static('', '', '', '', '', '', self::TYPE);
    }

    public function getVisitorViewModelClass(): string
    {
        return $this->visitorViewModelClass;
    }
}
