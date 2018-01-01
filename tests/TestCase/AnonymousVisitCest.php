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

use Acme\App\Presentation\Web\Core\Port\Paginator\PaginatorInterface;

/**
 * @group acceptance
 */
class AnonymousVisitCest
{
    public function anyoneCanBrowseTheApplication(AcceptanceTester $I): void
    {
        $I->comment('I start the journey...');
        $I->amOnPage('/');
        $I->see('Welcome to the Symfony Demo application');
        $I->seeLink('Browse application');
        $I->seeLink('Browse backend');

        $I->comment('I can see the blog posts list...');
        $I->click('Browse application');
        $I->seeInCurrentUrl('/en/blog');
        $I->seeLink('Symfony Demo');
        $I->seeLink('Homepage');
        $I->seeLink('Search');
        $I->seeNumberOfElements('//*[@id="main"]/article/h2/a', PaginatorInterface::DEFAULT_MAX_ITEMS_PER_PAGE);

        $I->comment('I can see the blog post and comments...');
        $I->click('//*[@id="main"]/article[1]/h2/a');
        $I->seeInCurrentUrl('/en/blog/posts');
        $I->seeElement('//*[@id="main"]/h1');
        $I->see('Sign in to publish a comment');
        $I->seeNumberOfElements('.post-comment', 5);

        $I->comment('I go back to the beginning...');
        $I->click('Symfony Demo');
        $I->seeInCurrentUrl('/');
        $I->see('Welcome to the Symfony Demo application');
        $I->seeLink('Browse application');
        $I->seeLink('Browse backend');
    }
}
