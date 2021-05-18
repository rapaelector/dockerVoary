<?php

namespace App\Services;

use App\Entity\Client;
use App\Repository\ClientRepository;

use Doctrine\ORM\EntityManagerInterface;

class ClientService
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
     * Generate client number
     * Client number are unique
     * Create client number then find client by the new client number
     * If there is a client with the new client number then create another one else return the created client number
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
}