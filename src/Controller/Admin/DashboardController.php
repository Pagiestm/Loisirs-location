<?php

namespace App\Controller\Admin;

use App\Controller\Admin\Quote\QuoteCrudController;
use App\Controller\Admin\Quote\QuoteResponseCrudController;
use App\Controller\Admin\Van\EquipmentCrudController;
use App\Controller\Admin\Van\VanCrudController;
use App\Entity\BlogPost;
use EasyCorp\Bundle\EasyAdminBundle\Attribute\AdminDashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\Assets;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\UX\Chartjs\Builder\ChartBuilderInterface;
use Symfony\UX\Chartjs\Model\Chart;

#[AdminDashboard(routePath: '/admin', routeName: 'admin')]
class DashboardController extends AbstractDashboardController
{
    public function __construct(
        private ChartBuilderInterface $chartBuilder,
    ) {}

    public function configureAssets(): Assets
    {
        return Assets::new()
            ->addWebpackEncoreEntry('admin')
            ->addHtmlContentToHead('<meta name="turbo-visit-control" content="reload">');
    }

    public function index(): Response
    {
        // Exemple de création d'un graphique avec Symfony UX Chart.js
        // À adapter plus tard pour afficher des données réelles
        $chart = $this->chartBuilder->createChart(Chart::TYPE_LINE);

        $chart->setData([
            'labels' => ['January', 'February', 'March', 'April', 'May', 'June', 'July'],
            'datasets' => [
                [
                    'label' => 'My First dataset',
                    'backgroundColor' => 'rgb(255, 99, 132)',
                    'borderColor' => 'rgb(255, 99, 132)',
                    'data' => [0, 10, 5, 2, 20, 30, 45],
                ],
            ],
        ]);

        $chart->setOptions([
            'scales' => [
                'y' => [
                    'suggestedMin' => 0,
                    'suggestedMax' => 100,
                ],
            ],
        ]);

        return $this->render('admin/dashboard.html.twig', [
            'chart' => $chart,
        ]);
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('Loisirs Location');
    }

    public function configureMenuItems(): iterable
    {
        $appHome = $this->generateUrl('app_home');
        yield MenuItem::linkToUrl('Retour au site', 'fa fa-arrow-left', $appHome);
        yield MenuItem::linkToDashboard('Dashboard', 'fa fa-home');
        yield MenuItem::linkToRoute('Gestion du contenu', 'fa fa-pencil-alt', 'admin_edit_content');
        yield MenuItem::linkToCrud('Blog', 'fa fa-newspaper', BlogPost::class);
        yield MenuItem::subMenu('Gestion des vans', 'fa fa-van-shuttle')->setSubItems([
            MenuItem::linkTo(VanCrudController::class, 'Vans', 'fa fa-list'),
            MenuItem::linkTo(EquipmentCrudController::class, 'Équipements', 'fa fa-gears'),
            MenuItem::linkToRoute('Galerie', 'fa fa-image', 'admin_vans_gallery'),
        ]);
        yield MenuItem::subMenu('Gestion des devis', 'fa fa-file-invoice')->setSubItems([
            MenuItem::linkTo(QuoteCrudController::class, 'Devis', 'fa fa-list'),
            MenuItem::linkTo(QuoteResponseCrudController::class, 'Réponses', 'fa fa-reply'),
        ]);
    }
}
