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

use Acme\App\DataFixtures\AppFixtures;

class AdminVisitCest
{
    /**
     * @depends UserVisitCest:userCanLoginCommentLogout
     */
    public function adminCanLoginPostLogout(AcceptanceTester $I): void
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
        $I->fillField('#username', 'jane_admin');
        $I->fillField('#password', 'kitten');
        $I->click('Sign in');
        $I->seeInCurrentUrl('/en/admin/post');
        $I->see('Post List');
        $I->see('Create a new post');
        $I->seeNumberOfElements('//*[@id="main"]/table/tbody/tr', AppFixtures::JANE_ADMIN_NUM_POSTS);
        $I->seeLink('Logout');

        $I->comment('I can create a new post...');
        $I->click('Create a new post');
        $I->seeInCurrentUrl('/en/admin/post/new');
        $I->see('Post creation');
        $I->see('Create post');
        $I->see('Save and create new');
        $I->see('Back to the post list');
        $I->fillField('#post_title', $title = 'A hello world post');
        $I->fillField('#post_summary', $summary = 'A summary');
        $I->fillField('#post_content', $content = 'Some interesting content...');
        $I->click('Create post');
        $I->seeInCurrentUrl('/en/admin/post');
        $I->seeNumberOfElements('//*[@id="main"]/table/tbody/tr', AppFixtures::JANE_ADMIN_NUM_POSTS + 1);
        $I->see('Post created successfully!');
        $I->see($title);
        $I->click('//*[@id="main"]/table/tbody/tr[1]/td[3]/div/a[1]');
        $I->see($title);
        $I->see($summary);
        $I->see($content);
        $I->see('Edit contents');
        $I->see('Delete post');

        $I->comment('I can edit a post...');
        $I->click('Edit contents');
        $I->see('Edit post #');
        $I->see('Save changes');
        $I->fillField('#post_title', $title = 'An edited hello world post');
        $I->click('Save changes');
        $I->see('Post updated successfully!');
        $I->see('Show post');
        $I->see('Delete post');

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
