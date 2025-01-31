<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Annotation\Route;

class DefaultController extends AbstractController
{
    // Route pour la page d'accueil
    #[Route('/', name: 'home')]
    public function index(): RedirectResponse
    {
        // Redirection vers /admin/task/
        return $this->redirectToRoute('admin_task_index');
    }
}
