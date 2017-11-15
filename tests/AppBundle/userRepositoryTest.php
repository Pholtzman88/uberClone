<?php
/**
 * Created by PhpStorm.
 * User: holtz
 * Date: 10/25/2017
 * Time: 10:10 PM
 */

namespace Tests\AppBundle;

use AppBundle\Entity\AppRole;
use AppBundle\Entity\AppUser;
use AppBundle\Exception\DuplicateRoleAssignmentException;
use AppBundle\Repository\UserRepository;

class UserRepositoryTest extends AppTestCase
{

    public function setUp()
    {
        parent::setUp();

        $this->save(AppRole::driver());

    }

    public function testCreateAndSaveUser()
    {
        $user = $this->getSavedUser();
        self::assertGreaterThan(0, $user->getId());

    }

    public function testGetUserById()
    {
        $savedUser = $this->getSavedUser();

        $retrievedUser =  $this->userRepository->getUserById(1);

        self::assertSame($savedUser->getId(),$retrievedUser->getId());
    }

    public function testAssignDriverRoleToUser()
    {

        $this->assertUserHasExpectedRole( AppRole::driver());
    }

    public function testAssignPassengerRoleToUser()
    {
        $this->assertUserHasExpectedRole(AppRole::passenger());
    }

    public function testUserCanHaveBothRoles()
    {
        $savedUser = $this->getSavedUser();

        $this->userRepository->assignRoleToUser($savedUser,AppRole::driver());
        $this->userRepository->assignRoleToUser($savedUser,AppRole::passenger());

        $retrievedUser = $this->userRepository->getUserById($savedUser);

        self::assertTrue($retrievedUser->hasRole(AppRole::driver()));
        self::assertTrue($savedUser->hasRole(AppRole::passenger()));

    }

    public function testDuplicateRoleAssignmentThrows()
    {
        $savedUser = $this->getSavedUser();

        $this->userRepository->assignRoleToUser($savedUser,AppRole::driver());

        self::expectException(DuplicateRoleAssignmentException::class);

        $this->userRepository->assignRoleToUser($savedUser,AppRole::driver());
    }

    /**
     * @param $savedUser
     * @param $role
     */
    public function assertUserHasExpectedRole($role)
    {
        $savedUser = $this->getSavedUser();

        $this->userRepository->assignRoleToUser($savedUser, $role);

        $retrievedUser = $this->userRepository->getUserById($savedUser->getId());

        self::assertTrue($savedUser->hasRole($role));
    }
}