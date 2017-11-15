<?php
/**
 * Created by PhpStorm.
 * User: holtz
 * Date: 10/28/2017
 * Time: 4:48 PM
 */

namespace Tests\AppBundle;

//use AppBundle\Entity\AppLocation;
use AppBundle\Entity\AppUser;
use AppBundle\Entity\Ride;
use AppBundle\Repository\RideRepository;

class RideRepositoryTest extends AppTestCase
{
    private $workLocation;

    public function setUp()
    {
        parent::setUp();

        $this->workLocation = $this->locationService->getLocation(
            self::WORK_LOCATION_LAT,
            self::WORK_LOCATION_LONG
        );
    }

    public function testCreateRideWithDepartureAndPassenger()
    {
        $ride = $this->getSavedNewRide();

        self::assertGreaterThan(0, $ride->getId());

    }

    public function testAssignDestinationToRide()
    {
        $retrievedRide = $this->getRideWithDestination();

        self::assertTrue($retrievedRide->getDestination()->equals($this->workLocation));

    }

    public function testAssignDriverToRide()
    {
        /** @var AppUser $driver */
        $driver = $this->getSavedUserWithName("Jamie", "isaacs");

        $rideWithDestination = $this->getRideWithDestination();

        $this->rideRepository->assignDriverToRide($rideWithDestination, $driver);

        $retrievedRide = $this->rideRepository->getRideById($rideWithDestination->getId());

        self::assertSame($driver->getLastName(), $retrievedRide->getDriver()->getLastName());
    }

    /**
     * @return Ride
     */
    public function getRideWithDestination()
    {
        $ride = $this->getSavedNewRide();

        $this->rideRepository->assignDestinationToRide(
            $ride,
            $this->workLocation
        );

        $retrievedRide = $this->rideRepository->getRideById($ride->getId());
        return $retrievedRide;
    }

}