<?php
/**
 * Created by PhpStorm.
 * User: holtz
 * Date: 10/26/2017
 * Time: 9:35 PM
 */

namespace Tests\AppBundle;


use AppBundle\Entity\AppLocation;
use AppBundle\Repository\LocationRepository;

class LocationRepositoryTest extends AppTestCase
{

    /** @var LocationRepository  */
    private $locationRepository;

    public function setUp()
    {
        parent::setUp();
        $this->locationRepository = new LocationRepository($this->em());
    }

    public function testCreateLocation()
    {
        $homeLocation = $this->getSavedLocation();

        self::assertGreaterThan(0, $homeLocation->getId());
    }

    public function testGetExistingLocationLatLong()
    {
        $savedLocation = $this->getSavedLocation();

        $lookUpLocation = new AppLocation($savedLocation->getLat(), $savedLocation->getLong());

        $retrievedLocation = $this->locationRepository->getExistingLocation($lookUpLocation);

        self::assertTrue($savedLocation->equals($retrievedLocation));
    }

    public function testCreateAndGetNewLocation()
    {
        $homeLocation = $this->getSavedLocation();

        $workLocation = new AppLocation(self::WORK_LOCATION_LAT, self::WORK_LOCATION_LONG);

        $retrievedLocation = $this->locationRepository->getExistingLocation($workLocation);

        self::assertTrue($retrievedLocation->equals($workLocation));
    }

    /**
     * @return AppLocation
     */
    public function getSavedLocation()
    {
        $homeLocation = new AppLocation(
            self::HOME_LOCATION_LAT,
            self::HOME_LOCATION_LONG
        );
        $this->locationRepository->save($homeLocation);
        return $homeLocation;
    }

    public function getSavedDestinationLocation()
    {
        $workLocation = new AppLocation(
            self::WORK_LOCATION_LAT,
            self::WORK_LOCATION_LONG
        );
        $this->locationRepository->save($workLocation);
        return $workLocation;
    }

}