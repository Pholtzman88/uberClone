<?php
/**
 * Created by PhpStorm.
 * User: holtz
 * Date: 10/26/2017
 * Time: 3:40 PM
 */

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Class AppRole
 * @package AppBundle\Entity
 *
 * @ORM\Entity()
 * @ORM\Table(name="roles")
 */
class AppRole
{
    /**
     * @var integer $id
     * @ORM\id()
     * @ORM\Column(name="id", type="integer", nullable=false)
     */
    private $id;
    /**
     * @var string $name
     * @ORM\Column(name="name", type="string", nullable=false)
     */
    private $name;

    private function __construct($id, $name)
    {
        $this->id = $id;
        $this->name = $name;
    }

    public static function driver()
    {
        return new self(1,"Driver");
    }

    public static function passenger()
    {
        return new self(2,"Passenger");
    }

    public function getId()
    {
        return $this->id;
    }
}