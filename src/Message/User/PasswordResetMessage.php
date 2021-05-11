<?php

namespace App\Message\User;

use SymfonyCasts\Bundle\ResetPassword\Model\ResetPasswordToken;

final class PasswordResetMessage
{
    
    /**
     *  email address of user will receive the message
     */
    private $email;
    /**
     * token of the message
     */
    private $resetToken;

    public function __construct(string $email, ResetPasswordToken $resetToken)
    {
        $this->email = $email;
        $this->resetToken = $resetToken;
    }
    /**
     * 
     */
   public function getEmail(): string
   {
       return $this->email;
   }

   /**
    * 
    */
   public function getResetToken(): ResetPasswordToken
   {
       return $this->resetToken;
   }
}
