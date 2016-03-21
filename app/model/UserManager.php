<?php

namespace App\Model;

use App\Model\Repository\UserRepository;
use Nette;
use Nette\Security\Passwords;

class UserManager extends Nette\Object implements Nette\Security\IAuthenticator
{
    /** @var UserRepository */
    private $userRepository;

    /**
     * UserManager constructor.
     * @param UserRepository $userRepository
     */
    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    /**
     * Performs an authentication.
     * @param array $credentials
     * @return Nette\Security\Identity
     * @throws Nette\Security\AuthenticationException
     */
    public function authenticate(array $credentials)
    {
        list($username, $password) = $credentials;

        $user = $this->userRepository->getOneByParameters(array('email' => $username));

        if (is_null($user)) {
            throw new Nette\Security\AuthenticationException('The username is incorrect.');
        } elseif (!Passwords::verify($password, $user->getPassword())) {
            throw new Nette\Security\AuthenticationException('The password is incorrect.');
        } elseif (Passwords::needsRehash($user->getPassword())) {
            $this->userRepository->updateWhere(array('password' => $password), array('email' => $username));
        }

        $arr = $user->getAsArray();
        return new Nette\Security\Identity($user->getId(), 'admin', $arr);
    }

}
