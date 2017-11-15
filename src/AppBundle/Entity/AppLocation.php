<?php
/**
 * Created by PhpStorm.
 * User: holtz
 * Date: 10/26/2017
 * Time: 10:58 PM
 */

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
/**
 * Class AppLocation
 * @package AppBundle\Entity
 *
 * @ORM\Entity()
 * @ORM\Table(name="locations")
 */

class AppLocation
{
    /**
     * @var float
     * @ORM\Column(name="lat", type="float", nullable=false)
     */
    private $lat;
    /**
     * @var float
     * @ORM\Column(name="long", type="float", nullable=false)
     */
    private $long;
    /**
     * @var int id
     * @ORM\id()
     * @ORM\GeneratedValue(strategy="IDENTITY")
     * @ORM\Column(name="id", type="integer", nullable=false)
     */
    private $id;

    /**
     * AppLocation constructor.
     * @param float $lat
     * @param float $long
     */
    public function __construct($lat, $long)
    {
        $this->lat = $lat;
        $this->long = $long;
    }

    public function getId()
    {
        return $this->id;
    }

    public function getLat()
    {
        return $this->lat;
    }

    public function getLong()
    {
        return $this->long;
    }

    public function equals(AppLocation $comparedLocation)
    {
        return (
        ($comparedLocation->getLat() === $this->getLat())
        &&
        ($comparedLocation->getLong() === $this->getLong())
        );
    }

}