<?php

namespace App\Tests\Command\User;

use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Console\Tester\CommandTester;

class UserCreateCommandTest extends KernelTestCase
{
    public function testSomething(): void
    {
        $kernel = self::bootKernel();
        $application = new Application($kernel);

        $email = 'test'. rand(1000, 10000) .'@gmail.com';
        $password = 'Test123';

        $command = $application->find('app:user:create');
        $commandTester = new CommandTester($command);
        $commandTester->execute([
            // pass arguments to the helper
            'email' => $email,
            'password' => $password,

            // prefix the key with two dashes when passing options,
            // e.g: '--some-option' => 'option_value',
        ]);

        // the output of the command in the console
        $output = $commandTester->getDisplay();
        $this->assertStringContainsString(sprintf('User "%s" created', $email), $output);
    }
}
