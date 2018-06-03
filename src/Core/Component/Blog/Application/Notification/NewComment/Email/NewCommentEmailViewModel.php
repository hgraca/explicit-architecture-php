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

namespace Acme\App\Core\Component\Blog\Application\Notification\NewComment\Email;

use Acme\App\Core\Port\TemplateEngine\EmailTemplateViewModelInterface;

final class NewCommentEmailViewModel implements EmailTemplateViewModelInterface
{
    /**
     * @var string
     */
    private $subject;

    /**
     * @var string
     */
    private $title;

    /**
     * @var string
     */
    private $link;

    public function __construct(string $subject, string $title, string $link)
    {
        $this->subject = $subject;
        $this->title = $title;
        $this->link = $link;
    }

    public function getSubject(): string
    {
        return $this->subject;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getLink(): string
    {
        return $this->link;
    }
}
