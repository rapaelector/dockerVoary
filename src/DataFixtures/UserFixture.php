<?php

namespace App\DataFixtures;

use App\Entity\User;

use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class UserFixture extends Fixture
{
    public function __construct(UserPasswordEncoderInterface $encoder)
    {
        $this->encoder = $encoder;
    }

    public function load(ObjectManager $manager)
    {
        $roles = [
            'ROLE_USER_VIEW',
            'ROLE_USER_ADD',
            'ROLE_USER_EDIT',
            'ROLE_USER_DELETE',
        ];
        /**
         * - User for the test
         * - user mail test@gmail.com Test123
         * - password Test123
         */
        $user = new User();
        $user->setFirstName('test');
        $user->setLastName('test@gmail.com');
        $user->setEmail('test@gmail.com');
        $user->setPassword($this->encoder->encodePassword($user, 'Test123'));
        $user->setRoles($roles);
        $manager->persist($user);
        
        foreach ($roles as $key => $role) {
            $newUser = new User();
            $userName = 'user_'.strtolower($role);
            $userEmail = 'user_'.strtolower($role).'@app.locale';
            $passwordEncoded = $this->encoder->encodePassword($newUser, $userEmail);

            $newUser->setFirstName($userName);
            $newUser->setLastName($userName);
            $newUser->setEmail($userEmail);
            $newUser->setPassword($passwordEncoded);
            $newUser->setRoles([$role]);
            
            $mockUser = new User();
            $mockUserName = 'user_'.strtolower($role). '_1';
            $mockUserEmail = 'user_'.strtolower($role).'_1' . '_@app.locale';
            $mockPasswordEncoded = $this->encoder->encodePassword($mockUser, $userEmail);

            $mockUser->setFirstName($mockUserName);
            $mockUser->setLastName($mockUserEmail);
            $mockUser->setEmail($mockUserEmail);
            $mockUser->setPassword($mockPasswordEncoded);
            $mockUser->setRoles([$role]);

            $manager->persist($mockUser);
            $manager->persist($newUser);
        }

        $manager->flush();
    }
}
