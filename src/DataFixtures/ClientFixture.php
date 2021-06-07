<?php

namespace App\DataFixtures;

use App\Entity\Client;
use App\Entity\Constants\Activities;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class ClientFixture extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $this->generateFixture($manager);
    }

    public function generateFixture(ObjectManager $manager)
    {
        // $contacts = $manager->getRepository(User::class)->findAll();
        $paymentPeriods = Client::getPaymentPeriodChoices();
        $paymentType = Client::getPaymentTypeChoices();
        
        for ($i=0; $i < 5; $i++) { 
            $client = new Client();
            $client->setName('CLIENT N° : ' .$i);
            $client->setShortName('CLI00' .$i);
            $client->setClientNumber('CLI00' .$i);
            $client->setActivity(Activities::ALL_ACTIVITIES[$i]);
            $client->setPaymentMethod($paymentPeriods[$i]);
            // $client->setContacts($contacts[$i]);

            $manager->persist($client);
        }

        $manager->flush();
    }
}
