<?php
/**
 * Created by PhpStorm.
 * User: holtz
 * Date: 10/28/2017
 * Time: 5:55 PM
 */

namespace AppBundle\Repository;

use AppBundle\Entity\AppLocation;
use AppBundle\Entity\AppUser;
use AppBundle\Entity\Ride;

/**
 * Class RideRepository
 * @package AppBundle\Repository
 */
class RideRepository extends AppRepository
{

    /**
     * @param Ride $ride
     * @param AppLocation $destination
     */
    public function assignDestinationToRide(Ride $ride, AppLocation $destination)
    {
        $ride->setDestination($destination);
        $this->save($ride);
    }
    /**
     * @param $id
     * @return Ride
     */
    public function getRideById($id)
    {
        return $this->em->createQuery(
            'select r from E:Ride r where r.id = :id'
        )
            ->setParameter('id',$id)
            ->getSingleResult();
    }

    public function assignDriverToRide(Ride $ride, AppUser $driver)
    {
        $ride->setDriver($driver);
        $this->save($ride);
    }

    public function destroy(Ride $ride)
    {
        $this->em->remove($ride);
        $this->em->flush();
    }

}