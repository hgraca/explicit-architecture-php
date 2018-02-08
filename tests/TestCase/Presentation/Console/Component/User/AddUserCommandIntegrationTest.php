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

namespace Acme\App\Test\TestCase\Presentation\Console\Component\User;

use Acme\App\Core\Component\User\Domain\Entity\User;
use Acme\App\Presentation\Console\Component\User\AddUserCommand;
use Acme\App\Test\Framework\AbstractIntegrationTest;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;

class AddUserCommandIntegrationTest extends AbstractIntegrationTest
{
    /**
     * @var array
     */
    private $userData = [
        'username' => 'chuck_norris',
        'password' => 'foobar',
        'email' => 'chuck@norris.com',
        'full-name' => 'Chuck Norris',
    ];

    protected function setUp(): void
    {
        exec('stty 2>&1', $output, $exitcode);
        $isSttySupported = $exitcode === 0;

        $isWindows = '\\' === DIRECTORY_SEPARATOR;

        if ($isWindows || !$isSttySupported) {
            $this->markTestSkipped('`stty` is required to test this command.');
        }
    }

    /**
     * @dataProvider isAdminDataProvider
     *
     * This test provides all the arguments required by the command, so the
     * command runs non-interactively and it won't ask for any argument.
     */
    public function testCreateUserNonInteractive(bool $isAdmin): void
    {
        $input = $this->userData;
        if ($isAdmin) {
            $input['--admin'] = 1;
        }
        $this->executeCommand($input);

        $this->assertUserCreated($isAdmin);
    }

    /**
     * @dataProvider isAdminDataProvider
     *
     * This test doesn't provide all the arguments required by the command, so
     * the command runs interactively and it will ask for the value of the missing
     * arguments.
     * See https://symfony.com/doc/current/components/console/helpers/questionhelper.html#testing-a-command-that-expects-input
     */
    public function testCreateUserInteractive(bool $isAdmin): void
    {
        $this->executeCommand(
        // these are the arguments (only 1 is passed, the rest are missing)
            $isAdmin ? ['--admin' => 1] : [],
            // these are the responses given to the questions asked by the command
            // to get the value of the missing required arguments
            array_values($this->userData)
        );

        $this->assertUserCreated($isAdmin);
    }

    /**
     * This is used to execute the same test twice: first for normal userRepository
     * (isAdmin = false) and then for admin userRepository (isAdmin = true).
     */
    public function isAdminDataProvider()
    {
        yield [false];
        yield [true];
    }

    /**
     * This helper method checks that the user was correctly created and saved
     * in the database.
     */
    private function assertUserCreated(bool $isAdmin): void
    {
        $container = self::$kernel->getContainer();

        /** @var User $user */
        $user = $container->get('doctrine')->getRepository(User::class)->findOneByEmail($this->userData['email']);
        $this->assertNotNull($user);

        $this->assertSame($this->userData['full-name'], $user->getFullName());
        $this->assertSame($this->userData['username'], $user->getUsername());
        $this->assertTrue($container->get('security.password_encoder')->isPasswordValid($user, $this->userData['password']));
        $this->assertSame($isAdmin ? ['ROLE_ADMIN'] : ['ROLE_USER'], $user->getRoles());
    }

    /**
     * This helper method abstracts the boilerplate code needed to test the
     * execution of a command.
     *
     * @param array $arguments All the arguments passed when executing the command
     * @param array $inputs    The (optional) answers given to the command when it asks for the value of the missing arguments
     */
    private function executeCommand(array $arguments, array $inputs = []): void
    {
        $command = self::getService(AddUserCommand::class);
        $command->setApplication(new Application(self::$kernel));

        $commandTester = new CommandTester($command);
        $commandTester->setInputs($inputs);
        $commandTester->execute($arguments);
    }
}
