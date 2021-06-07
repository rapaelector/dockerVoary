<?php

namespace App\Tests\Controller;

use App\Repository\UserRepository;

trait WebCaseTestTrait
{
    /**
     * TODO: add hint type for $client 
     */
    public function login($client, $email)
    {
        // $client = static::createClient();
        $userRepository = static::$container->get(UserRepository::class);

        // retrieve the test user
        $testUser = $userRepository->findOneByEmail($email);

        // simulate $testUser being logged in
        $client->loginUser($testUser);
    }
}