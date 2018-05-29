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

namespace Acme\App\Test\TestCase\Infrastructure\Notification\Client\Email\SwiftMailer\Mapper;

use Acme\App\Infrastructure\Notification\Client\Email\SwiftMailer\Mapper\MandrillSwiftEmailMapper;
use Acme\App\Infrastructure\Notification\Client\Email\SwiftMailer\Mapper\SwiftEmailMapper;

/**
 * @author Herberto Graca <herberto.graca@gmail.com>
 * @author Marijn Koesen
 */
class MandrillSwiftEmailMapperUnitTest extends SwiftEmailMapperUnitTest
{
    public function getMapData(): array
    {
        $data = parent::getMapData();

        $email = $this->getEmail();
        $email->setTrackMessageOpening(true);
        $swiftMessage = $this->getSwiftMessage();
        $swiftMessage->getHeaders()->addTextHeader('X-MC-Track', 'opens');
        $data[] = [$email, $swiftMessage];

        $email = $this->getEmail();
        $email->setTrackClicks(true);
        $swiftMessage = $this->getSwiftMessage();
        $swiftMessage->getHeaders()->addTextHeader('X-MC-Track', 'clicks_all');
        $data[] = [$email, $swiftMessage];

        $email = $this->getEmail();
        $email->setTrackMessageOpening(true);
        $email->setTrackClicks(true);
        $swiftMessage = $this->getSwiftMessage();
        $swiftMessage->getHeaders()->addTextHeader('X-MC-Track', 'clicks_all,opens');
        $data[] = [$email, $swiftMessage];

        $email = $this->getEmail();
        $email->setTags(['tag-1']);
        $swiftMessage = $this->getSwiftMessage();
        $swiftMessage->getHeaders()->addTextHeader('X-MC-Tags', 'tag-1');
        $data[] = [$email, $swiftMessage];

        $email = $this->getEmail();
        $email->setTags(['tag-1', 'tag-2']);
        $swiftMessage = $this->getSwiftMessage();
        $swiftMessage->getHeaders()->addTextHeader('X-MC-Tags', 'tag-1,tag-2');
        $data[] = [$email, $swiftMessage];

        $email = $this->getEmail();
        $email->setTrackingCampaign('some-tracking-campaign');
        $swiftMessage = $this->getSwiftMessage();
        $swiftMessage->getHeaders()->addTextHeader('X-MC-GoogleAnalytics', $this->getHost());
        $swiftMessage->getHeaders()->addTextHeader('X-MC-GoogleAnalyticsCampaign', 'some-tracking-campaign');
        $data[] = [$email, $swiftMessage];

        return $data;
    }

    protected function getMapper(): SwiftEmailMapper
    {
        return new MandrillSwiftEmailMapper($this->getHost());
    }

    protected function getHost(): string
    {
        return 'hostname';
    }
}
