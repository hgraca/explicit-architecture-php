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

class UserVisitCest
{
    /**
     * @depends AnonymousVisitCest:anyoneCanBrowseTheApplication
     */
    public function userCanLoginCommentLogout(AcceptanceTester $I): void
    {
        $I->comment('I start the journey...');
        $I->amOnPage('/');
        $I->see('Welcome to the Symfony Demo application');
        $I->seeLink('Browse application');
        $I->seeLink('Browse backend');

        $I->comment('I can login and see the blog posts list...');
        $I->click('Browse backend');
        $I->seeInCurrentUrl('/en/login');
        $I->seeLink('Symfony Demo');
        $I->seeLink('Homepage');
        $I->seeLink('Search');
        $I->see('Secure Sign in');
        $I->see('Try either of the following users');
        $I->fillField('#username', 'john_user');
        $I->fillField('#password', 'kitten');
        $I->click('Sign in');
        $I->amOnPage('/');
        $I->click('Browse application');
        $I->seeNumberOfElements('//*[@id="main"]/article/h2/a', 10);
        $I->seeLink('Logout');

        $I->comment('I can see the blog post and comments...');
        $I->click('//*[@id="main"]/article[1]/h2/a');
        $I->seeInCurrentUrl('/en/blog/posts');
        $I->seeElement('//*[@id="main"]/h1');
        $I->see('Add a comment');
        $I->see('Publish comment');
        $I->seeNumberOfElements('.post-comment', 5);

        $I->comment('I can add a comment...');
        $I->fillField('#comment_content', $comment = 'Hello world comment!');
        $I->click('Publish comment');
        $I->seeInCurrentUrl('/en/blog/posts');
        $I->see('Add a comment');
        $I->see('Publish comment');
        $I->see($comment);
        $I->seeNumberOfElements('.post-comment', 6);

        $I->comment('I go back to the beginning...');
        $I->click('Logout');
        $I->seeInCurrentUrl('/');
        $I->see('Welcome to the Symfony Demo application');
        $I->seeLink('Browse application');
        $I->seeLink('Browse backend');

        $I->comment('I am logged out...');
        $I->click('Browse application');
        $I->seeInCurrentUrl('/en/blog');
        $I->dontSeeLink('Logout');
    }
}
