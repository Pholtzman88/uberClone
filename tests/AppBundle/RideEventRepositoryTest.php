<?php
/**
 * Created by PhpStorm.
 * User: holtz
 * Date: 10/29/2017
 * Time: 11:30 PM
 */

namespace Tests\AppBundle;


use AppBundle\Entity\Ride;
use AppBundle\Entity\RideEvent;
use AppBundle\Entity\RideEventType;
use AppBundle\Repository\RideEventRepository;

/**
 * Class RideEventRepositoryTest
 * @package Tests\AppBundle
 */
class RideEventRepositoryTest extends AppTestCase
{
    /** @var  Ride $savedRide */
    private $savedRide;
    /** @var  RideEventType $requestedType */
    private $requestedType;
    /** @var  RideEventType $acceptedType */
    private $acceptedType;
    /** @var  RideEventType $isRejectedType */
    private $isRejectedType;
    /** @var  RideEventType $inProgressType */
    private $inProgressType;
    /** @var  RideEventType $inCancelledType */
    private $isCancelledType;
    /** @var  RideEventType $isCompletedType */
    private $isCompletedType;

    public function setUp()
    {
        parent::setUp();
        $this->savedRide = $this->getSavedNewRide();
        $this->requestedType = RideEventType::requested();
        $this->acceptedType = RideEventType::accepted();
        $this->isRejectedType = RideEventType::rejected();
        $this->inProgressType = RideEventType::inProgress();
        $this->isCancelledType = RideEventType::cancelled();
        $this->isCompletedType = RideEventType::completed();
        $this->save($this->requestedType);
        $this->save($this->acceptedType);
        $this->save($this->isRejectedType);
        $this->save($this->inProgressType);
        $this->save($this->isCancelledType);
        $this->save($this->isCompletedType);
    }

    public function testSaveNewRideEvent()
    {
        $rideEvent = $this->getSavedRequestedRideEvent();

        self::assertGreaterThan(0, $rideEvent->getId());
    }

    public function testRideIsCurrentlyRequested()
    {
        $this->getSavedRequestedRideEvent();

        $lastEventForRide = $this->rideEventRepository->getLastEventForRide(
            $this->savedRide
        );

        self::assertTrue($lastEventForRide->is(RideEventType::Requested()));
    }

    public function testRideIsCurrentlyAccepted()
    {
        $this->assertLastEventIsOfType($this->acceptedType);
    }

    public function testRideHasBeenRejected()
    {
        $this->assertLastEventIsOfType($this->isRejectedType);
    }

    public function testRideIsCurrentlyInProgress()
    {
        $this->assertLastEventIsOfType($this->inProgressType);
    }

    public function testRideHasBeenCancelled()
    {
        $this->assertLastEventIsOfType($this->isCancelledType);
    }

    public function testRideHasBeenCompleted()
    {
        $this->assertLastEventIsOfType($this->isCompletedType);
    }

    public function testMarkRideAsStatus()
    {
        $status = RideEventType::requested();
        $actor = $this->savedRide->getPassenger();

        $this->rideEventRepository->markRideStatusByActor(
            $this->savedRide,
            $actor,
            $status
        );

        $lastEventForRide = $this->rideEventRepository->getLastEventForRide(
            $this->savedRide
        );

        self::assertTrue($lastEventForRide->is(RideEventType::requested()));
    }

    /**
     * @return RideEvent
     */
    public function getSavedRequestedRideEvent()
    {
        $actor = $this->savedRide->getPassenger();


        $rideEvent = $this->rideEventRepository->markRideStatusByActor(
            $this->savedRide,
            $actor,
            $this->requestedType
        );

        $this->rideEventRepository->save($rideEvent);
        return $rideEvent;
    }

    /**
     * @param $eventTypeToAssert
     * @return RideEvent
     */
    public function assertLastEventIsOfType(RideEventType $eventTypeToAssert)
    {
        $this->getSavedRequestedRideEvent();

        $acceptedEvent = new RideEvent(
            $this->savedRide,
            $this->getSavedUserWithName("Jamie", "Isaacs"),
            $eventTypeToAssert
        );

        $this->rideEventRepository->save($acceptedEvent);

        $lastEventForRide = $this->rideEventRepository->getLastEventForRide(
            $this->savedRide
        );
        self::assertTrue($lastEventForRide->is($eventTypeToAssert));

        return $lastEventForRide;
    }

}