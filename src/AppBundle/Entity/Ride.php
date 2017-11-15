<?php
/**
 * Created by PhpStorm.
 * User: holtz
 * Date: 10/28/2017
 * Time: 5:44 PM
 */

namespace AppBundle\Entity;


use AppBundle\Entity\AppLocation;
use AppBundle\Entity\AppUser;
use Doctrine\ORM\Mapping as ORM;

/**
 * Class Ride
 * @package AppBundle\Entity
 *
 * @ORM\Entity()
 * @ORM\Table(name="rides")
 */
class Ride
{
    /**
     * @var \AppBundle\Entity\AppUser
     * @ORM\ManyToOne(targetEntity="AppUser", fetch="EAGER")
     * @ORM\JoinColumn(name="passengerId", referencedColumnName="id")
     */
    private $passenger;
    /**
     * @var \AppBundle\Entity\AppUser
     * @ORM\ManyToOne(targetEntity="AppUser", fetch="EAGER")
     * @ORM\JoinColumn(name="driverId", referencedColumnName="id")
     */
    private $driver;
    /**
     * @var AppLocation
     *@ORM\ManyToOne(targetEntity="AppLocation", fetch="EAGER")
     * @ORM\JoinColumn(name="departureId", referencedColumnName="id")
     */

    private $departure;
    /**
     * @var AppLocation
     *@ORM\ManyToOne(targetEntity="AppLocation", fetch="EAGER")
     * @ORM\JoinColumn(name="destinationId", referencedColumnName="id")
     */
    private $destination;
    /**
     * @var int $id
     * @ORM\Id()
     * @ORM\GeneratedValue(strategy="IDENTITY")
     * @ORM\Column(name="id", type="integer", nullable=false)
     */
    private $id;

    /**
     * Ride constructor.
     * @param \AppBundle\Entity\AppUser $passenger
     * @param AppLocation $departure
     */
    public function __construct(AppUser $passenger, AppLocation $departure)
    {

        $this->passenger = $passenger;
        $this->departure = $departure;
    }

    public function getId()
    {
        return $this->id;
    }

    /**
     * @param \AppBundle\Entity\AppLocation $destination
     */
    public function setDestination(AppLocation $destination)
    {
        $this->destination = $destination;
    }

    /**
     * @return AppLocation
     */
    public function getDestination()
    {
        return $this->destination;
    }

    public function setDriver(AppUser $driver)
    {
        $this->driver = $driver;
    }

    /**
     * @return AppUser
     */
    public function getDriver()
    {
        return $this->driver;
    }

    public function getPassenger()
    {
        return $this->passenger;
    }
}