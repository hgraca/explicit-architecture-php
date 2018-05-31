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

namespace Acme\App\Infrastructure\Notification\Strategy;

use Acme\App\Core\Port\Notification\NotificationInterface;
use Acme\App\Infrastructure\Notification\StrategyDefinition;
use Acme\PhpExtension\Helper\TypeHelper;

/**
 * @author Herberto Graca <herberto.graca@gmail.com>
 * @author Nicolae Nichifor
 */
abstract class AbstractNotificationStrategy implements NotificationStrategyInterface
{
    /**
     * @var [
     *      '$notificationName' => StrategyDefinition,
     *      ...
     *  ]
     */
    private $notificationGeneratorList = [];

    public function addNotificationMessageGenerator(
        $generator,
        string $notificationName,
        string $generatorMethodName,
        $voter = null,
        ?string $voterMethodName = null
    ): void {
        if (!method_exists($generator, $generatorMethodName)) {
            throw new InvalidGeneratorException(\get_class($generator), $generatorMethodName);
        }

        $this->notificationGeneratorList[$notificationName] = new StrategyDefinition(
            $generator,
            $generatorMethodName,
            $voter,
            $voterMethodName
        );
    }

    public function canHandleNotification(NotificationInterface $notification): bool
    {
        return $this->hasGeneratorForNotification($notification)
            && $this->isAllowedForNotification($notification);
    }

    /**
     * @return mixed
     */
    protected function generateNotificationMessage(NotificationInterface $notification)
    {
        if (!$this->canHandleNotification($notification)) {
            throw new UnprocessableNotificationException($this->getNotificationName($notification), self::class);
        }

        $generator = $this->getStrategyDefinitionForNotification($notification)->getGenerator();
        $generatorMethodName = $this->getStrategyDefinitionForNotification($notification)->getGeneratorMethod();

        return $generator->$generatorMethodName($notification);
    }

    private function getNotificationName(NotificationInterface $notification): string
    {
        return \get_class($notification);
    }

    private function hasGeneratorForNotification(NotificationInterface $notification): bool
    {
        return array_key_exists($this->getNotificationName($notification), $this->notificationGeneratorList);
    }

    private function isAllowedForNotification(NotificationInterface $notification): bool
    {
        $strategyDefinition = $this->getStrategyDefinitionForNotification($notification);
        if (!$strategyDefinition->hasVoter()) {
            return true;
        }

        $voter = $strategyDefinition->getVoter();
        $voterMethod = $strategyDefinition->getVoterMethod();

        if (!method_exists($voter, $voterMethod)) {
            throw new InvalidVoterException(TypeHelper::getType($voter), $voterMethod);
        }

        return $voter->$voterMethod($notification);
    }

    private function getStrategyDefinitionForNotification(NotificationInterface $notification): StrategyDefinition
    {
        return $this->notificationGeneratorList[$this->getNotificationName($notification)];
    }
}
