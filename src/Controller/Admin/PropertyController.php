<?php

namespace App\Controller\Admin;

use App\Entity\Property;
use App\Form\PropertyType;
use App\Repository\PropertyRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Liip\ImagineBundle\Imagine\Cache\CacheManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class PropertyController extends AbstractController
{
    private $repository;
    private $em;

    public function __construct(PropertyRepository $propertyRepository, EntityManagerInterface $em)
    {
        $this->repository = $propertyRepository;
        $this->em = $em;
    }

    public function index()
    {
        $properties = $this->repository->findAll();
        return $this->render('admin/property/index.html.twig', ['properties' => $properties]);
    }

    public function edit(Property $property, Request $request)
    {
        $form = $this->createForm(PropertyType::class, $property);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $propertyImages = $property->getImages();
            foreach ($propertyImages as $key => $propertyImage) {
                if ($propertyImage->getProperty() && !$propertyImage->getImageName()) {
                    $property->removeImage($propertyImage);
                } else {
                    $propertyImage->setProperty($property);
                    $propertyImages->set($key, $propertyImage);
                }
            }

            $this->em->flush();
            $this->addFlash('success', 'Le bien a bien été modifié');
            return $this->redirectToRoute('admin.property.edit', ['id' => $property->getId()]);
        }

        return $this->render('admin/property/edit.html.twig', [
            'property' => $property,
            'form' => $form->createView()
        ]);
    }


    public function new(Request $request)
    {
        $property = new Property();
        $form = $this->createForm(PropertyType::class, $property);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $propertyImages = $property->getImages();
            foreach ($propertyImages as $key => $propertyImage) {
                $propertyImage->setProperty($property);
                $propertyImages->set($key, $propertyImage);
            }

            $this->em->persist($property);
            $this->em->flush();
            $this->addFlash('success', 'Le bien a bien été créé');
            return $this->redirectToRoute('admin.property.edit', ['id' => $property->getId()]);
        }

        return $this->render('admin/property/new.html.twig', [
            'property' => $property,
            'form' => $form->createView()
        ]);
    }


    public function delete(Property $property, Request $request)
    {
        if ($this->isCsrfTokenValid('delete' . $property->getId(), $request->get('_token'))) {
            $this->em->remove($property);
            $this->em->flush();
            $this->addFlash('success', 'Le bien a bien été supprimé');
        }
        return $this->redirectToRoute('admin.property.index');
    }
}
