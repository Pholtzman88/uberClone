<?php
/**
 * Created by PhpStorm.
 * User: holtz
 * Date: 10/27/2017
 * Time: 1:08 AM
 */

namespace Tests\AppBundle;


use AppBundle\Entity\AppLocation;
use AppBundle\Repository\LocationRepository;
use AppBundle\Service\LocationService;

class LocationServiceTest extends AppTestCase
{

    public function testGetLocation()
    {
        $referenceLocation = new AppLocation(
            self::HOME_LOCATION_LAT,
            self::HOME_LOCATION_LONG
        );

        $retrievedLocation = $this->locationService->getLocation(
            $referenceLocation->getLat(),
            $referenceLocation->getLong()
        );

        self::assertTrue($referenceLocation->equals($retrievedLocation));


    }

}