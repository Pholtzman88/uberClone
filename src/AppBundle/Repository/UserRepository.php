<?php
/**
 * Created by PhpStorm.
 * User: holtz
 * Date: 10/25/2017
 * Time: 11:23 PM
 */

namespace AppBundle\Repository;

use AppBundle\Entity\AppRole;
use AppBundle\Entity\AppUser;
use AppBundle\Exception\DuplicateRoleAssignmentException;

class UserRepository extends AppRepository implements UserRepositoryInterface
{
    /**
     * @param $userId
     * @return AppUser
     */
    public function getUserById($userId)
    {
        return $this->em->createQuery(
            'select u from E:AppUser u where u.id = :userId'

        )
            ->setParameter('userId',$userId)
            ->getSingleResult();

    }

    public function assignRoleToUser(AppUser $user, AppRole $role)
    {
        if ($user->hasRole($role)){
            throw new DuplicateRoleAssignmentException();
        }
        $role = $this->getRoleReference($role);
        $user->addRole($role);
        $this->save($user);
    }

    /**
     * @param AppRole $role
     * @return AppRole|object
     */
    public function getRoleReference(AppRole $role)
    {
        /** @var AppRole $role */
        $role = $this->em->getReference(AppRole::class, $role->getId());
        return $role;
    }
}