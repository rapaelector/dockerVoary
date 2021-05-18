<?php

namespace App\Service\Client;

use App\Entity\Client;

interface ClientServiceInterface
{
    /**
     * Generate client number
     * Client number are unique
     * Create client number then find client by the new client number
     * If there is a client with the new client number then create another one else return the created client number
     */
    public function generateClientNumber();

    /**
     * Save client in argument
     * Loop each client contacts and assign mock password to avoid constraint violation: column 'password' cannot be null
     * 
     * @var Client $client
     */
    public function update(Client $client);
}