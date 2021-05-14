<?php

namespace App\Form\Model;

use Symfony\Component\Security\Core\Validator\Constraints as SecurityAssert;

class ChangePassword
{
    /**
     * @SecurityAssert\UserPassword(
     *     message = "Mauvaise valeur pour votre mot de passe actuel"
     * )
     */
    public $oldPassword;

    public $newPassword;
}