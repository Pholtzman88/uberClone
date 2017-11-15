<?php
/**
 * Created by PhpStorm.
 * User: holtz
 * Date: 10/26/2017
 * Time: 9:59 PM
 */

namespace AppBundle\Repository;

use AppBundle\Entity\AppLocation;
use Doctrine\ORM\NoResultException;

class LocationRepository extends AppRepository
{

    public function getExistingLocation(AppLocation $location )
    {
        try
        {
            return $this
                ->em
                ->createQuery(
                    'select l from E:AppLocation l where l.lat = :lat and l.long = :long'
                )
                ->setParameter('lat', $location->getLat())
                ->setParameter('long', $location->getLong())
                ->getSingleResult();
        }
        catch (NoResultException $e)
        {
            $this->save($location);
            return $this->getExistingLocation($location);
        }
    }
}