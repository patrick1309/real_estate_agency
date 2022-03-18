<?php

namespace App\Repository;

use App\Entity\Property;
use App\Entity\PropertyImage;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method PropertyImage|null find($id, $lockMode = null, $lockVersion = null)
 * @method PropertyImage|null findOneBy(array $criteria, array $orderBy = null)
 * @method PropertyImage[]    findAll()
 * @method PropertyImage[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PropertyImageRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PropertyImage::class);
    }

    /**
     * @param Property[] $properties
     * @return ArrayCollection
     */
    public function findForProperties(array $properties): ArrayCollection
    {
        $pictures = $this->createQueryBuilder('p')
            ->select('p')
            ->where('p.property IN (:properties)')
            ->groupBy('p.property')
            ->getQuery()
            ->setParameter('properties', $properties)
            ->getResult();

        $pictures = array_reduce($pictures, function (array $output, PropertyImage $picture) {
            $output[$picture->getProperty()->getId()] = $picture;
            return $output;
        }, []);

        return new ArrayCollection($pictures);
    }

    // /**
    //  * @return PropertyImage[] Returns an array of PropertyImage objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('p.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?PropertyImage
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
