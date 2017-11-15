<?php
/**
 * Created by PhpStorm.
 * User: holtz
 * Date: 10/26/2017
 * Time: 8:34 PM
 */

namespace AppBundle\Service;


use AppBundle\Entity\AppRole;
use AppBundle\Entity\AppUser;
use AppBundle\Repository\UserRepository;
use AppBundle\Repository\UserRepositoryInterface;

class UserService
{
    /** @var  UserRepositoryInterface */
    private $userRepository;

    /**
     * UserService constructor.
     * @param $userRepository
     */
    public function __construct($userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function newUser($firstName, $lastName)
    {
        $newUser = new AppUser($firstName, $lastName);
        $this->userRepository->save($newUser);
    }

    /**
     * @param int $userId
     * @return AppUser
     */
    public function getUserById($userId)
    {
        return $this->userRepository->getUserById($userId);
    }

    public function makeDriver(AppUser $user)
    {
        $this->userRepository->assignRoleToUser($user, AppRole::driver());
    }

    public function isDriver(AppUser $user)
    {
        return $user->hasRole(AppRole::driver());
    }

    public function makePassenger(AppUser $user)
    {
        $this->userRepository->assignRoleToUser($user, AppRole::passenger());
    }

    public function isPassenger(AppUser $user)
    {
        return $user->hasRole(AppRole::passenger());
    }

}