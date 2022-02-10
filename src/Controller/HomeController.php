<?php

namespace App\Controller;

use App\Repository\PropertyRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class HomeController extends AbstractController
{


    public function index(PropertyRepository $propertyRepository): Response
    {
        return $this->render('pages/home.html.twig', [
            'properties' => $propertyRepository->findLatest()
        ]);
    }
}
