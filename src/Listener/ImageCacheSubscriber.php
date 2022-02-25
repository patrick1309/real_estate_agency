<?php

namespace App\Listener;

use App\Entity\PropertyImage;
use Doctrine\Common\EventSubscriber;
use Vich\UploaderBundle\Event\Event;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Liip\ImagineBundle\Imagine\Cache\CacheManager;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Vich\UploaderBundle\Handler\UploadHandler;
use Vich\UploaderBundle\Templating\Helper\UploaderHelper;

class ImageCacheSubscriber implements EventSubscriber
{

    private $cacheManager;
    private $uploaderHelper;
    private $uploadHandler;

    public function __construct(CacheManager $cacheManager, UploaderHelper $uploaderHelper, UploadHandler $uploadHandler)
    {
        $this->cacheManager = $cacheManager;
        $this->uploaderHelper = $uploaderHelper;
        $this->uploadHandler = $uploadHandler;
    }

    public function getSubscribedEvents()
    {
        return [
            'preRemove',
            'preUpdate',
            'onVichUploaderPreUpload'
        ];
    }

    public function preRemove(LifecycleEventArgs $args)
    {
        $entity = $args->getObject();

        if (!$entity instanceof PropertyImage) {
            return false;
        }

        $this->cacheManager->remove($this->uploaderHelper->asset($entity, 'imageFile'));
    }

    public function preUpdate(PreUpdateEventArgs $args)
    {
        $entity = $args->getEntity();

        if (!$entity instanceof PropertyImage) {
            return false;
        }

        if ($entity->getImageFile() instanceof UploadedFile) {
            $this->cacheManager->remove($this->uploaderHelper->asset($entity, 'imageFile'));
        }
    }

    public function onVichUploaderPreUpload(Event $event)
    {
        $object = $event->getObject();

        if ($object instanceof PropertyImage) {
            $this->uploadHandler->remove($object, 'imageFile');
        }
    }
}
