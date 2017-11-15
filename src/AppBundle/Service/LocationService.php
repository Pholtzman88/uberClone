<?php
/**
 * Created by PhpStorm.
 * User: holtz
 * Date: 10/27/2017
 * Time: 1:20 AM
 */

namespace AppBundle\Service;

use AppBundle\Entity\AppLocation;
use AppBundle\Repository\LocationRepository;

class LocationService
{
    /**
     * @var LocationRepository
     */
    private $locationRepository;

    /**
     * LocationService constructor.
     * @param LocationRepository $locationRepository
     */
    public function __construct($locationRepository)
    {
        $this->locationRepository = $locationRepository;
    }

    /**
     * @param $lat
     * @param $long
     * @return AppLocation
     */
    public function getLocation($lat, $long)
    {
        return $this->locationRepository->getExistingLocation(
            new AppLocation(
                $lat,
                $long
            )
        );
    }
}
