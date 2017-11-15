<?php
/**
 * Created by PhpStorm.
 * User: holtz
 * Date: 10/25/2017
 * Time: 10:16 PM
 */
namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\Mapping as ORM;

/**
 * Class AppUser
 * @package AppBundle\Entity
 *
 * @ORM\Entity()
 * @ORM\Table(name="users")
 */
class AppUser
{
    /**
     * @var integer $id
     * @ORM\id()
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;
    /**
     * @var string $firstName
     * @ORM\Column(name="first_name", type="string", nullable=false)
     */
    private $firstName;
    /**
     * @var string $lastName
     * @ORM\Column(name="last_name", type="string", nullable=false)
     */
    private $lastName;

    /**
     * @var ArrayCollection | AppRole[] $roles
     * @ORM\ManyToMany(targetEntity="AppRole")
     * @ORM\JoinTable(
     *     name="users_roles",
     *     joinColumns={@ORM\JoinColumn(name="userId", referencedColumnName="id")},
     *     inverseJoinColumns={@ORM\JoinColumn(name="roleId", referencedColumnName="id")}
     * )
     */
    private $roles;

    /**
     * AppUser constructor.
     * @param string $firstName
     * @param string $lastName
     */
    public function __construct($firstName, $lastName)
    {
        $this->firstName = $firstName;
        $this->lastName = $lastName;
        $this->roles = new ArrayCollection();
    }

    public function getId()
    {
        return $this->id;
    }

    public function addRole($role)
    {
        $this->roles->add($role);
    }

    public function hasRole(AppRole $role)
    {
        $hasRoleCriteria =
        Criteria::create()->andWhere(
            Criteria::expr()->eq(
                'id', $role->getId()
            )
        );
        return $this->roles->matching($hasRoleCriteria)->count() > 0;
    }

    public function getFirstName()
    {
        return $this->firstName;
    }

    public function getLastName()
    {
        return $this->lastName;
    }
}