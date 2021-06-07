<?php

namespace App\Tests\Controller;

use App\Repository\UserRepository;
use Symfony\Component\DomCrawler\Form;

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

    public function formatFormNames($blockName = '', array $values)
    {
        if (!$blockName) {
            return $values;
        }

        $res = [];
        foreach ($values as $fieldName => $fieldValue) {
            $res[sprintf('%s[%s]', $blockName, $fieldName)] = $fieldValue;
        }

        return $res;
    }

    /**
     * TODO: add type hint to client
     */
    public function submitOverride($client, Form $form, array $values = [], array $extra = [], array $serverParameters = [])
    {
        $form->setValues($values);
        print_r($form->getPhpValues());
        $formValues =  array_merge_recursive($form->getPhpValues(), $extra);
        print_r($formValues);

        return $client->request($form->getMethod(), $form->getUri(), $formValues, $form->getPhpFiles(), $serverParameters);
    }
}