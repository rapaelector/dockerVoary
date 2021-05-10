<?php

namespace App\Message\User;

final class UserCreatedMessage
{
    /*
     * Add whatever properties & methods you need to hold the
     * data for this message class.
     */

    /**
     * @var int $id
     * We send the user id instead of the user object to avoid problems
     */
    private $userId;

    /**
     * @var string plainPassword
     * Get user plain password to notify the user about him created account
     */
    private $plainPassword;

    public function __construct(int $id, string $plainPassword)
    {
        $this->userId = $id;
        $this->plainPassword = $plainPassword;
    }

    /**
     * @return int $userId
     */
    public function getUserId()
    {
        return $this->userId;
    }

    /**
     * @return string $plainPassword
     */
    public function getPlainPassword()
    {
        return $this->plainPassword;
    }
}
