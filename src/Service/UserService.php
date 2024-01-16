<?php
/**
 * This file is part of meritesportif application
 * @author jfsenechal <jfsenechal@gmail.com>
 * @date 3/10/19
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 */

namespace AcMarche\MeriteSportif\Service;


use AcMarche\MeriteSportif\Entity\Club;
use AcMarche\MeriteSportif\Entity\User;
use AcMarche\MeriteSportif\Repository\UserRepository;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserService
{
    public function __construct(
        private UserPasswordHasherInterface $userPasswordEncoder,
        private UserRepository $userRepository
    ) {
    }

    public function createUser(Club $club): User
    {
        if (!$user = $this->userRepository->findOneByEmail($club->getEmail())) {
            $user = new User();
            $user->setUsername($club->getEmail());
        }

        $password = random_int(9999, 999999);
        $user->setNom($club->getNom());
        $user->setPassword($this->userPasswordEncoder->hashPassword($user, $password));
        $user->addRole('ROLE_MERITE');
        $user->addRole('ROLE_MERITE_CLUB');

        $this->userRepository->persist($user);

        $club->setUser($user);

        return $user;
    }

}