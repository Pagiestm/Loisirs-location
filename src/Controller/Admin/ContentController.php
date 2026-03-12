<?php

namespace App\Controller\Admin;

use EasyCorp\Bundle\EasyAdminBundle\Attribute\AdminRoute;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

class ContentController extends AbstractController
{
    #[AdminRoute('/edit-content', name: 'edit_content')]
    public function index(): Response
    {
        return $this->render('admin/edit_content.html.twig');
    }
}
