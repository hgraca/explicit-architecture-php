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

namespace Acme\App\Infrastructure\Notification;

/**
 * @author Herberto Graca <herberto.graca@gmail.com>
 * @author Nicolae Nichifor
 */
final class StrategyDefinition
{
    private const VOTER_DEFAULT_METHOD = 'vote';

    /**
     * @var object
     */
    private $generator;

    /**
     * @var string
     */
    private $generatorMethod;

    /**
     * @var object|null
     */
    private $voter;

    /**
     * @var string|null
     */
    private $voterMethod;

    public function __construct(
        $generator,
        string $generatorMethod,
        $voter = null,
        ?string $voterMethod = self::VOTER_DEFAULT_METHOD
    ) {
        $this->generator = $generator;
        $this->generatorMethod = $generatorMethod;
        $this->voter = $voter;
        $this->voterMethod = $voterMethod ?? self::VOTER_DEFAULT_METHOD;
    }

    public function getGenerator()
    {
        return $this->generator;
    }

    public function getGeneratorMethod(): string
    {
        return $this->generatorMethod;
    }

    public function getVoter()
    {
        return $this->voter;
    }

    public function getVoterMethod(): string
    {
        return $this->voterMethod;
    }

    public function hasVoter(): bool
    {
        return $this->voter !== null;
    }
}
