<?php

namespace App\Repository;

use App\Entity\Property;
use App\Entity\PropertyImage;
use App\Entity\PropertySearch;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Knp\Component\Pager\PaginatorInterface;

/**
 * @method Property|null find($id, $lockMode = null, $lockVersion = null)
 * @method Property|null findOneBy(array $criteria, array $orderBy = null)
 * @method Property[]    findAll()
 * @method Property[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PropertyRepository extends ServiceEntityRepository
{
    private $paginator;

    public function __construct(ManagerRegistry $registry, PaginatorInterface $paginator)
    {
        parent::__construct($registry, Property::class);
        $this->paginator = $paginator;
    }


    /**
     * @return QueryBuilder
     */
    private function findVisibleQueryBuilder(): QueryBuilder
    {
        return $this->createQueryBuilder('p')
            // ->select('p', 'pics')
            // ->leftJoin('p.images', 'pics')
            ->where('p.sold = false');
    }

    public function paginateAllVisible(PropertySearch $search, int $page): PaginationInterface
    {
        $query = $this->findVisibleQueryBuilder();

        if ($search->getMaxPrice()) {
            $query = $query
                ->andWhere('p.price <= :maxprice')
                ->setParameter('maxprice', $search->getMaxPrice());
        }

        if ($search->getMinSurface()) {
            $query = $query
                ->andWhere('p.surface >= :minsurface')
                ->setParameter('minsurface', $search->getMinSurface());
        }

        if ($search->getOptions()->count() > 0) {
            $key = 0;
            foreach ($search->getOptions() as $option) {
                $key++;
                $query = $query
                    ->andWhere(":option$key MEMBER OF p.options")
                    ->setParameter("option$key", $option);
            }
        }

        $properties = $this->paginator->paginate(
            $query->getQuery(),
            $page, /*page number*/
            12 /*limit per page*/
        );

        $this->hydratePictures($properties);

        return $properties;
    }


    public function findAllVisible()
    {
        return $this->findVisibleQueryBuilder()
            ->getQuery()
            ->getResult();
    }

    /**
     * @return Property[]
     */
    public function findLatest()
    {
        $properties = $this->findVisibleQueryBuilder()
            ->orderBy('p.created_at', 'ASC')
            ->setMaxResults(3)
            ->getQuery()
            ->getResult();
        $this->hydratePictures($properties);
        return $properties;
    }

    private function hydratePictures($properties)
    {
        if (is_object($properties) && method_exists($properties, 'getItems')) {
            $properties = $properties->getItems();
        }
        $pictures = $this->getEntityManager()->getRepository(PropertyImage::class)->findForProperties($properties);
        foreach ($properties as $property) {
            /** @var Property $property  */
            if ($pictures->containsKey($property->getId())) {
                $property->setImage($pictures->get($property->getId()));
            }
        }
    }
}
