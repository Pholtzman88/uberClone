<?php
/**
 * Created by PhpStorm.
 * User: holtz
 * Date: 10/30/2017
 * Time: 3:03 PM
 */

namespace AppBundle\Repository;

use AppBundle\Entity\AppRole;
use AppBundle\Entity\AppUser;

interface UserRepositoryInterface
{
    /**
     * @param $userId
     * @return AppUser
     */
    public function getUserById($userId);

    public function assignRoleToUser(AppUser $user, AppRole $role);

    /**
     * @param AppRole $role
     * @return AppRole|object
     */
    public function getRoleReference(AppRole $role);

    public function save($newUser);
}