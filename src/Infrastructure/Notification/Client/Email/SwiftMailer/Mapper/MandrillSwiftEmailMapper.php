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

namespace Acme\App\Infrastructure\Notification\Client\Email\SwiftMailer\Mapper;

use Acme\App\Core\Port\Notification\Client\Email\Email;
use Swift_Mime_SimpleMessage;

/**
 * @author Marijn Koesen
 * @author Herberto Graca <herberto.graca@gmail.com>
 */
class MandrillSwiftEmailMapper extends SwiftEmailMapper
{
    /**
     * @var string
     */
    private $host;

    /**
     * @param string $host The domain/host used in google analytics
     */
    public function __construct($host)
    {
        $this->host = $host;
    }

    public function map(Email $message): Swift_Mime_SimpleMessage
    {
        $message = $this->addMtaHeadersToMessage($message);

        $swiftMessage = parent::map($message);

        return $swiftMessage;
    }

    /**
     * Adds Mandrill specific headers to the message
     *
     * @see http://help.mandrill.com/entries/21688056-Using-SMTP-Headers-to-customize-your-messages
     */
    protected function addMtaHeadersToMessage(Email $message)
    {
        $tracking = [];

        if ($message->shouldTrackClicks()) {
            $tracking[] = 'clicks_all';
        }

        if ($message->shouldTrackMessageOpening()) {
            $tracking[] = 'opens';
        }

        if (!empty($tracking)) {
            $message->addHeader('X-MC-Track', implode(',', $tracking));
        }

        $tags = $message->getTags();
        if (!empty($tags)) {
            $message->addHeader('X-MC-Tags', implode(',', $tags));
        }

        $googleCampaign = $message->getTrackingCampaign();
        if (!empty($googleCampaign)) {
            $message->addHeader('X-MC-GoogleAnalytics', $this->host);
            $message->addHeader('X-MC-GoogleAnalyticsCampaign', $googleCampaign);
        }

        return $message;
    }
}
