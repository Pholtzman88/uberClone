<?php
/**
 * Created by PhpStorm.
 * User: holtz
 * Date: 10/30/2017
 * Time: 1:23 PM
 */

namespace Tests\AppBundle;


use AppBundle\Entity\AppRole;
use AppBundle\Entity\Ride;
use AppBundle\Entity\RideEventType;
use AppBundle\Exception\RideLifeCycleException;
use AppBundle\Exception\UserNotDriverException;
use AppBundle\Exception\UserNotPassengerException;
use AppBundle\Service\RideService;

class RideServiceTest extends AppTestCase
{
    /** @var  RideService */
    private $rideService;

    public function setUp()
    {
        parent::setUp();
        $this->rideService = new RideService(
            $this->rideRepository,
            $this->rideEventRepository
        );
    }

    public function testCreateRide()
    {
        $newRide = $this->getNewRideWithPassengerAndDestination();

        self::assertInstanceOf(Ride::class, $newRide);
        self::assertGreaterThan(0, $newRide->getId());
    }

    public function testRideUserNotPassengerThrowsRoleException()
    {
        $notPassenger = $this->getSavedUser();
        self::assertFalse($this->userService->isPassenger($notPassenger));

        $departure = $this->getSavedHomeLocation();

        self::expectException(UserNotPassengerException::class);

        $this->rideService->newRide($notPassenger, $departure);
    }

    public function testGetRideStatus()
    {
        $newRide = $this->getNewRideWithPassengerAndDestination();
        $rideStatus = $this->rideService->getRideStatus($newRide);

        self::assertTrue(RideEventType::requested()->equals($rideStatus));
    }

    public function testNewRideIsRequested()
    {
        $newRide = $this->getNewRideWithPassengerAndDestination();

        $rideStatus = $this->rideService->getRideStatus($newRide);

        self::assertTrue(RideEventType::requested()->equals($rideStatus));
    }

    public function testAcceptRideByProspectDriver()
    {
        $newRide = $this->getNewRideWithPassengerAndDestination();
        $driver = $this->getSavedUser();
        $this->userService->makeDriver($driver);

        $acceptedRide = $this->rideService->acceptRide($newRide, $driver);
        $rideStatus = $this->rideService->getRideStatus($acceptedRide);

        self::assertTrue(RideEventType::accepted()->equals($rideStatus));
        self::assertSame($driver->getLastName(),$acceptedRide->getDriver()->getLastName());
    }

    public function testAcceptingNonRequestedRideThrowsException()
    {
        $newRide = $this->getNewRideWithPassengerAndDestination();

        $winningDriver = $this->getSavedUser();
        $this->userService->makeDriver($winningDriver);

        $losingDriver = $this->getSavedUserWithName('loser','magoo');
        $this->userService->makeDriver($losingDriver);

        $this->rideService->acceptRide($newRide, $winningDriver);

        $this->expectException(RideLifeCycleException::class);

        $this->rideService->acceptRide($newRide, $losingDriver);

    }

    public function testPassengerAcceptingRideThrowsException()
    {
        $newRide = $this->getNewRideWithPassengerAndDestination();

        $passenger = $this->getSavedUser();
        $this->userService->makePassenger($passenger);

        $driver = $this->getSavedUser();
        $this->userService->makeDriver($driver);


        $this->expectException(UserNotDriverException::class);

        $this->rideService->acceptRide($newRide, $passenger);

//        $this->rideService->acceptRide($newRide, $driver);
    }


    public function testRejectRideByProspectDriver()
    {
        //verify that user is a driver
        //verify that event type is requested
        $newRide = $this->getNewRideWithPassengerAndDestination();
        $driver = $this->getSavedUser();
        $this->userService->makeDriver($driver);

        $rejectedRide = $this->rideService->rejectRide($newRide, $driver);
        $rideStatus = $this->rideService->getRideStatus($rejectedRide);

        self::assertTrue(RideEventType::rejected()->equals($rideStatus));
        self::assertSame($driver->getLastName(),$rejectedRide->getDriver()->getLastName());
    }

    public function testRejectingNonRequestedRideThrowException()
    {
        $newRide = $this->getNewRideWithPassengerAndDestination();

        $winningDriver = $this->getSavedUser();
        $this->userService->makeDriver($winningDriver);

        $losingDriver = $this->getSavedUserWithName('loser','magoo');
        $this->userService->makeDriver($losingDriver);

        $this->rideService->rejectRide($newRide, $winningDriver);

        $this->expectException(RideLifeCycleException::class);

        $this->rideService->rejectRide($newRide, $losingDriver);

    }

    public function testPassengerRejectingRideThrowsException()
    {
        $newRide = $this->getNewRideWithPassengerAndDestination();

        $passenger = $this->getSavedUser();
        $this->userService->makePassenger($passenger);

        $driver = $this->getSavedUser();
        $this->userService->makeDriver($driver);


        $this->expectException(UserNotDriverException::class);

        $this->rideService->rejectRide($newRide, $passenger);
        
    }

    /**
     * @return Ride
     */
    protected function getNewRideWithPassengerAndDestination()
    {
        $passenger = $this->getSavedUser();
        $this->userService->makePassenger($passenger);

        $departure = $this->getSavedHomeLocation();

        $newRide = $this->rideService->newRide(
            $passenger,
            $departure
        );
        return $newRide;
    }


    public function testMarkRideAsInProgress()
    {

        $ride = $this->getNewRideWithPassengerAndDestination();

        $driver = $this->getSavedUser();
        $this->userService->makeDriver($driver);

        $this->rideRepository->assignDriverToRide($ride, $driver);

        $this->rideEventRepository->markRideStatusByActor(
            $ride,
            $driver,
            RideEventType::accepted()
        );

        $this->rideService->markRideAsInProgress(
            $ride,
            $driver
        );

        $rideStatus = $this->rideService->getRideStatus($ride);
        self::assertTrue(RideEventType::inProgress()->equals($rideStatus));
    }

    public function testMarkingNonAcceptedRideAsInProgressThrowsException()
    {
        $ride = $this->getNewRideWithPassengerAndDestination();

        $driver = $this->getSavedUser();
        $this->userService->makeDriver($driver);

        $this->rideRepository->assignDriverToRide($ride, $driver);

        $this->rideEventRepository->markRideStatusByActor(
            $ride,
            $driver,
            RideEventType::requested()
        );

        $this->expectException(RideLifeCycleException::class);

        $this->rideService->markRideAsInProgress(
            $ride,
            $driver
        );
    }

    public function testPassengerMarksRideAsInProgressThrowsException()
    {
        $ride = $this->getNewRideWithPassengerAndDestination();

        $passenger = $ride->getPassenger();

        $driver = $this->getSavedUser();
        $this->userService->makeDriver($driver);

        $this->rideRepository->assignDriverToRide($ride, $driver);

        $this->rideEventRepository->markRideStatusByActor(
            $ride,
            $driver,
            RideEventType::accepted()
        );

        $this->expectException(UserNotDriverException::class);

        $this->rideService->markRideAsInProgress(
            $ride,
            $passenger
        );
    }



    public function advanceLifeCycleByDriver($eventType)
    {
        $ride = $this->getNewRideWithPassengerAndDestination();
        $driver = $this->getSavedUser();
        $this->userService->makeDriver($driver);

        $acceptedRide = $this->rideService->advanceLifeCycleOfRide(
            $ride,
            $driver,
            $eventType
        );

        self::assertSame($driver->getLastName(),$acceptedRide->getDriver()->getLastName());

        return $ride;
    }

    public function testCancelRide()
    {
        $ride = $this->getNewRideWithPassengerAndDestination();

        $otherRide = $this->getNewRideWithPassengerAndDestination();

        $passenger = $ride->getPassenger();

        $driver = $this->getSavedUser();
        $this->userService->makeDriver($driver);

        $this->rideService->cancelRide($ride, $passenger);

        self::assertTrue($passenger->hasRole(AppRole::passenger()));
        self::assertTrue(RideEventType::cancelled()->equals($this->rideService->getRideStatus($ride)));
//        self::assertNull($ride->getId());
//        self::assertNotNull($otherRide->getId());
    }

    public function testDriverCancelsRideThrowsException()
    {
        $ride = $this->getNewRideWithPassengerAndDestination();

        $driver = $this->getSavedUser();
        $this->userService->makeDriver($driver);

        $this->expectException(UserNotPassengerException::class);

        $this->rideService->cancelRide($ride, $driver);
    }

    public function testMarkRideAsCompleted()
    {

        $ride = $this->getNewRideWithPassengerAndDestination();

        $user = $this->getSavedUser();
        $this->userService->makeDriver($user);

        $this->rideRepository->assignDriverToRide($ride, $user);

        $this->rideEventRepository->markRideStatusByActor(
            $ride,
            $user,
            RideEventType::inProgress()
        );

        $this->rideService->markRideAsCompleted($ride, $user);

        $rideStatus = $this->rideService->getRideStatus($ride);

        self::assertTrue($user->hasRole(AppRole::driver()));
        self::assertTrue($ride->getDriver() === $user);
        self::assertTrue(RideEventType::completed()->equals($rideStatus));
    }

    public function testDestroyRide()
    {
        $ride = $this->getNewRideWithPassengerAndDestination();
        $user = $this->getSavedUser();
        $this->userService->makeDriver($user);
        $this->rideService->destroyRide($ride, $user);

        self::assertNull($ride->getId());
    }

    /**
     * 1) the user of the Ride must be a passenger
     * 2) the Ride must not have already been requested
     */
}