<?php

namespace App\Service\Client;

use App\Entity\User;
use App\Entity\Client;
use App\Repository\ClientRepository;
use App\Service\Client\ClientServiceInterface;

use Doctrine\ORM\EntityManagerInterface;

class ClientService implements ClientServiceInterface
{
    /** @var EntityManagerInterface */
    private $em;

    /** @var ClientRepository */
    private $repository;

    public function __construct(EntityManagerInterface $em, ClientRepository $repository)
    {
        $this->em = $em;
        $this->repository = $repository;
    }

    /**
     * {@inheritdoc}
     */
    public function generateClientNumber()
    {
        $cliStart = 967;
        $prStart = 633;
        $repo = $this->em->getRepository(Client::class);
        $allCN = $this->repository->getAllClientNumbers();
        $maxCLI = 0;
        $maxPR = 0;

        foreach ($allCN as $elem) {
            $clientNumber = $elem['clientNumber'];
            if (preg_match('#CLI(\d*)$#', $clientNumber, $matches)) {
                if ($matches[1] && $matches[1] < 9000) {
                    $maxCLI = max([$maxCLI, $matches[1]]);
                }
            } else if (preg_match('#PR(\d*)#', $clientNumber, $matches)) {
                if ($matches[1] && $matches[1] < 9000) {
                    $maxPR = max([$maxPR, $matches[1]]);
                }
            }
        }

        $maxCLI = max([$cliStart, $maxCLI]);
        $maxPR = max([$prStart, $maxPR]);

        while ($repo->findByClientNumber('CLI' . ((strlen(''.$maxCLI) < 4) ? '0' : '') . $maxCLI)) {
            $maxCLI++;
        }

        while ($repo->findByClientNumber('PR' . ((strlen(''.$maxPR) < 4) ? '0' : '') . $maxPR)) {
            $maxPR++;
        }

        return [
            Client::TYPE_CLIENT => 'CLI' . ((strlen(''.$maxCLI) < 4) ? '0' : '') . $maxCLI,
            Client::TYPE_PROSPECT => 'PR' . ((strlen(''.$maxPR) < 4) ? '0' : '') . $maxPR
        ];
    }

    /**
     * {@inheritdoc}
     * User created from the client are external type and cant access the CRM
     * By default in the user constructor:
     *      - user type is internal
     *      - can login set to true
     * Here we denied the access in the CRM for the Client user (contact) 
     */
    public function update(Client $client)
    {
        if ($contacts = $client->getContacts()) {
            foreach ($contacts as $contact) {
                $mockPassword = md5($contact->getEmail());
                $contact->setPassword($mockPassword);

                /**
                 * Type of user = external dont shown in the user list
                 * canLogin = false avoid the access of the crm
                 */
                $contact->setType(User::TYPE_EXTERNAL);
                $contact->setCanLogin(false);
                $this->em->persist($contact);
            }
        }

        if ($client->getId()) {
            $this->em->flush();
        } else {
            $this->em->persist($client);
            $this->em->flush();
        }
    }
}