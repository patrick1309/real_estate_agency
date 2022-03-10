<?php

namespace App\Listener;

use App\Entity\PropertyImage;
use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Event\OnFlushEventArgs;


class PropertyImageSubscriber implements EventSubscriber
{
    private $manager;

    public function __construct(EntityManagerInterface $manager)
    {
        $this->manager = $manager;
    }

    public function getSubscribedEvents()
    {
        return [
            'onFlush',
        ];
    }


    public function onFlush(OnFlushEventArgs $args)
    {
        $uow = $this->manager->getUnitOfWork();
        foreach ($uow->getScheduledEntityUpdates() as $keyEntity => $entity) {
            if ($entity instanceof PropertyImage) {
                $property = $entity->getProperty();
                $property->setUpdatedAt(new \DateTime);
                $this->manager->persist($property);
                $classMetadata = $this->manager->getClassMetadata('App\Entity\Property');
                $uow->computeChangeSet($classMetadata, $property);
            }
        }
    }
}
