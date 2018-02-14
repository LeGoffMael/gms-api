<?php
namespace AppBundle\Security;

use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Core\User\User;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Doctrine\ORM\EntityRepository;

class AuthTokenUserProvider implements UserProviderInterface {
    protected $authTokenRepository;
    protected $userRepository;

    public function __construct(EntityRepository $authTokenRepository, EntityRepository $userRepository) {
        $this->authTokenRepository = $authTokenRepository;
        $this->userRepository = $userRepository;
    }

    public function getAuthToken($authTokenHeader) {
        return $this->authTokenRepository->findOneByValue($authTokenHeader);
    }

    public function loadUserByUsername($name) {
        return $this->userRepository->findByUsername($name);
    }
    
    public function loadUserByEmail($email) {
        return $this->userRepository->findByEmail($email);
    }

    public function refreshUser(UserInterface $user) {
        // The authentication system is stateless, so you should never call the refreshUser method
        throw new UnsupportedUserException();
    }

    public function supportsClass($class) {
        return 'AppBundle\Entity\User' === $class;
    }
}