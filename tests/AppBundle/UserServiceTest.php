<?php
/**
 * Created by PhpStorm.
 * User: holtz
 * Date: 10/26/2017
 * Time: 8:23 PM
 */

namespace Tests\AppBundle;


use AppBundle\Repository\UserRepository;
use AppBundle\Service\UserService;
use AppBundle\Entity\AppUser;
use AppBundle\Entity\AppRole;

class UserServiceTest extends AppTestCase
{

    public function setUp()
    {
        parent::setUp();
    }

    /**
     * register new user
     * get user by id
     * make user driver
     * make user passenger
     */

    public function testRegisterNewUser()
    {
        $user = $this->getSavedUser();

        self::assertSame("Patrick",$user->getFirstName());
        self::assertSame("Holtzman",$user->getLastName());
    }

    public function testMakeUserDriver()
    {
        $savedUser = $this->getSavedUser();

        $this->userService->makeDriver($savedUser);

        $retrievedUser = $this->userService->getUserById(1);

        self::assertTrue($this->userService->isDriver($retrievedUser));
    }

    public function testMakeUserPassenger()
    {
        $savedUser = $this->getSavedUser();

        $this->userService->makePassenger($savedUser);

        $retrievedUser = $this->userService->getUserById(1);

        self::assertTrue($this->userService->isPassenger($retrievedUser));
    }
    /**
     * @return AppUser
     */
    public function getSavedUser()
    {
        $this->userService->newUser("Patrick", "Holtzman");

        //** @var AppUser $user*/
        $user = $this->userService->getUserById(1);
        return $user;
    }
}