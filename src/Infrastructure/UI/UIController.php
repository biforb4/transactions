<?php

declare(strict_types=1);

namespace App\Infrastructure\UI;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class UIController extends AbstractController
{
    #[Route(path: "/", methods: "GET")]
    public function index(): Response
    {
        return $this->render('list.html.twig');
    }

    #[Route(path: "/new", methods: "GET")]
    public function new(): Response
    {
        return $this->render('new.html.twig');
    }
    #[Route(path: "/edit/{id}", methods: "GET")]
    public function edit(string $id): Response
    {
        return $this->render('edit.html.twig', ['id' => $id]);
    }

}
