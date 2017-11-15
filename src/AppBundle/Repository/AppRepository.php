<?php
/**
 * Created by PhpStorm.
 * User: holtz
 * Date: 10/26/2017
 * Time: 10:47 PM
 */

namespace AppBundle\Repository;

use Doctrine\ORM\EntityManagerInterface;

abstract class AppRepository
{
    /**
     * @var EntityManagerInterface
     */
    protected $em;

    /**
     * UserRepository constructor.
     * @param $em
     */
    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function save($object){
        $this->em->persist($object);
        $this->em->flush();
    }

}