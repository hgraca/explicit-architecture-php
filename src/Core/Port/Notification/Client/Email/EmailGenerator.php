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

namespace Acme\App\Core\Port\Notification\Client\Email;

use Acme\App\Core\Port\TemplateEngine\EmailTemplateViewModelInterface;
use Acme\App\Core\Port\TemplateEngine\TemplateEngineInterface;

/**
 * @author Akis Krimpenis
 * @author Alexandre Eher
 * @author Henry Snoek
 * @author Henrique Moody
 * @author Herberto Graca <herberto.graca@gmail.com>
 * @author Jeroen Van Den Heuvel
 * @author Marijn Koesen
 * @author Nicolae Nichifor
 * @author Ruud Van Der Weiijde
 * @author Vinicius Andrade
 */
final class EmailGenerator
{
    /**
     * @var TemplateEngineInterface
     */
    private $templateEngine;

    /**
     * @var EmailAddress
     */
    private $defaultFromEmailAddress;

    public function __construct(
        TemplateEngineInterface $templateEngine,
        string $defaultFromEmailAddress,
        string $defaultFromEmailName
    ) {
        $this->templateEngine = $templateEngine;
        $this->defaultFromEmailAddress = new EmailAddress($defaultFromEmailAddress, $defaultFromEmailName);
    }

    /**
     * Create the Email Message that we can send.
     *
     * This is generated from two (twig) templates and an array with template data
     *
     * @param EmailAddress $fromEmailAddress The sender of the email
     * @param EmailAddress $recipient The recipient of the email
     * @param string $subject The subject of the email
     * @param string $txtTemplatePath The path to the txt template e.g.
     *     mails/toConsumer/notify-consumer-new-proposal.txt.twig
     * @param string $htmlTemplatePath The path to the txt template e.g.
     *     mails/toConsumer/notify-consumer-new-proposal.html.twig
     * @param EmailTemplateViewModelInterface $emailTemplateViewModel The data that will be used to render the template
     * @param EmailAddress[] $cc
     * @param EmailAddress[] $bcc
     * @param string[] $tagList Can be used for tracking email campaigns
     */
    public function generateEmailMessage(
        EmailAddress $recipient,
        string $subject,
        string $txtTemplatePath,
        string $htmlTemplatePath,
        EmailTemplateViewModelInterface $emailTemplateViewModel,
        EmailAddress $fromEmailAddress = null,
        array $cc = [],
        array $bcc = [],
        array $tagList = []
    ): Email {
        $email = new Email($subject, $fromEmailAddress ?? $this->defaultFromEmailAddress);
        $email->addTo($recipient);

        foreach ($cc as $address) {
            $email->addCc($address);
        }

        foreach ($bcc as $address) {
            $email->addBcc($address);
        }

        $email->setBodyText($this->templateEngine->render($txtTemplatePath, $emailTemplateViewModel));
        $email->setBodyHtml($this->templateEngine->render($htmlTemplatePath, $emailTemplateViewModel));
        $email->setTrackMessageOpening(true);
        $email->setTrackClicks(true);
        $email->setTags($tagList);

        return $email;
    }
}
