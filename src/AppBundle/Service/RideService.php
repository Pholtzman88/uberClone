<?php
/**
 * Created by PhpStorm.
 * User: holtz
 * Date: 10/30/2017
 * Time: 1:34 PM
 */

namespace AppBundle\Service;


use AppBundle\Entity\AppLocation;
use AppBundle\Entity\AppUser;
use AppBundle\Entity\Ride;
use AppBundle\Entity\RideEventType;
use AppBundle\Exception\RideLifeCycleException;
use AppBundle\Exception\UserNotDriverException;
use AppBundle\Exception\UserNotPassengerException;
use AppBundle\Repository\RideEventRepository;
use AppBundle\Repository\RideRepository;
use AppBundle\Entity\AppRole;

class RideService
{
    /**
     * @var RideRepository
     */
    private $rideRepository;
    /**
     * @var RideEventRepository
     */
    private $rideEventRepository;

    /**
     * RideService constructor.
     * @param RideRepository $rideRepository
     * @param RideEventRepository $rideEventRepository
     */
    public function __construct(RideRepository $rideRepository, RideEventRepository $rideEventRepository)
    {
        $this->rideRepository = $rideRepository;
        $this->rideEventRepository = $rideEventRepository;
    }

    public function newRide(AppUser $passenger, AppLocation $departure)
    {
        $this->validateUserIsPassenger($passenger);
        $newRide = new Ride($passenger, $departure);
        $this->rideRepository->save($newRide);

        $this->rideEventRepository->markRideStatusByActor(
            $newRide,
            $newRide->getPassenger(),
            RideEventType::requested()
        );
        return $newRide;
    }

    public function getRideStatus(Ride $ride)
    {
        $lastEvent = $this->rideEventRepository->getLastEventForRide($ride);
        return $lastEvent->getStatus();
    }

    public function acceptRide(Ride $ride, AppUser $driver)
    {
        $this->validateRideIsRequested($ride);

        $this->validateUserIsDriver($driver);

       $this->advanceLifeCycleOfRide(
           $ride,
           $driver,
           RideEventType::accepted()
       );

       return $ride;

    }

    public function rejectRide(Ride $ride, AppUser $driver)
    {
        $this->validateRideIsRequested($ride);

        $this->validateUserIsDriver($driver);

        $this->advanceLifeCycleOfRide(
            $ride,
            $driver,
            RideEventType::rejected()
        );

        return $ride;
    }

    public function markRideAsInProgress(Ride $ride, AppUser $driver)
    {
        $this->validateRideIsAccepted($ride);

        $this->validateUserIsDriver($driver);
        
        $this->advanceLifeCycleOfRide(
            $ride,
            $driver,
            RideEventType::inProgress()
        );

        return $ride;
    }

    public function cancelRide(Ride $ride, AppUser $user)
    {
        $this->validateUserIsPassenger($user);

        $this->advanceLifeCycleOfRide(
            $ride,
            $user,
            RideEventType::cancelled()
        );

//        $this->rideRepository->destroy($ride);
    }

    public function markRideAsCompleted(Ride $ride, AppUser $user)
    {
        $this->validateRideIsInProgress($ride);

        $this->validateUserIsDriver($user);

        $this->advanceLifeCycleOfRide(
            $ride,
            $user,
            RideEventType::completed()
        );
    }

    /**
     * @param Ride $ride
     * @param AppUser $user
     * @param RideEventType $eventType
     * @return Ride
     */
    public function advanceLifeCycleOfRide(Ride $ride,AppUser $user, RideEventType $eventType)
    {
        $this->rideEventRepository->markRideStatusByActor(
            $ride,
            $user,
            $eventType
        );

        $this->rideRepository->assignDriverToRide($ride, $user);

        return $ride;
    }

    /**
     * @param Ride $ride
     * @param AppUser $driver
     * @throws RideLifeCycleException
     * @throws UserNotDriverException
     */
    protected function validateRideIsRequested(Ride $ride)
    {
        $rideIsNotRequested = !RideEventType::requested()->equals(
            $this->getRideStatus($ride)
        );

        if ($rideIsNotRequested) {
            throw new RideLifeCycleException();
        }
    }

    protected function validateUserIsDriver(AppUser $user){
        if ($user->hasRole(AppRole::passenger())) {
            throw new UserNotDriverException();
        }
    }

    /**
     * @param Ride $ride
     * @throws RideLifeCycleException
     */
    protected function validateRideIsAccepted(Ride $ride)
    {
        $rideIsNotAccepted = !RideEventType::accepted()->equals(
            $this->getRideStatus($ride)
        );

        if ($rideIsNotAccepted) {
            throw new RideLifeCycleException();
        }
    }

    /**
     * @param AppUser $passenger
     * @throws UserNotPassengerException
     */
    protected function validateUserIsPassenger(AppUser $passenger)
    {
        if (!$passenger->hasRole(AppRole::passenger())) {
            throw new UserNotPassengerException();
        }
    }

    /**
     * @param Ride $ride
     * @throws RideLifeCycleException
     */
    protected function validateRideIsInProgress(Ride $ride)
    {
        $rideIsNotInProgress = !RideEventType::inProgress()->equals(
            $this->getRideStatus($ride)
        );

        if ($rideIsNotInProgress) {
            throw new RideLifeCycleException();
        }
    }

    public function destroyRide(Ride $ride, AppUser $user)
    {
        $this->validateUserIsDriver($user);

        $this->rideRepository->destroy($ride);
    }


}