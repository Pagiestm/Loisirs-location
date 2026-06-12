<?php

namespace App\Controller\Admin;

use App\Controller\Admin\Quote\QuoteCrudController;
use App\Controller\Admin\Quote\QuoteResponseCrudController;
use App\Controller\Admin\Van\EquipmentCrudController;
use App\Controller\Admin\Van\VanCrudController;
use App\Entity\BlogPost;
use App\Service\Admin\DashboardStatsService;
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
        private DashboardStatsService $statsService,
    ) {}

    public function configureAssets(): Assets
    {
        return Assets::new()
            ->addWebpackEncoreEntry('admin')
            ->addHtmlContentToHead('<meta name="turbo-visit-control" content="reload">');
    }

    public function index(): Response
    {
        $stats = $this->statsService->getStats();

        $evolutionChart = $this->chartBuilder->createChart(Chart::TYPE_BAR);

        $evolutionChart->setData([
            'labels' => array_column($stats['responses_evolution'], 'day'),
            'datasets' => [[
                'label' => 'Demandes de devis',
                'backgroundColor' => [
                    'rgba(99, 102, 241, 0.8)',
                    'rgba(168, 85, 247, 0.8)',
                    'rgba(236, 72, 153, 0.8)',
                    'rgba(59, 130, 246, 0.8)',
                    'rgba(16, 185, 129, 0.8)',
                ],
                'data' => array_column($stats['responses_evolution'], 'total'),
                'borderRadius' => 4,
            ]],
        ]);

        $evolutionChart->setOptions([
            'responsive' => true,
            'maintainAspectRatio' => false,
            'plugins' => [
                'legend' => ['display' => false],
            ],
            'scales' => [
                'y' => [
                    'beginAtZero' => true,
                    'ticks' => ['stepSize' => 1],
                ],
            ],
        ]);

        $vanChart = $this->chartBuilder->createChart(Chart::TYPE_BAR);

        $vanChart->setData([
            'labels' => array_column($stats['van_usage'], 'name'),
            'datasets' => [[
                'label' => 'Demandes',
                'data' => array_column($stats['van_usage'], 'total'),
                'backgroundColor' => [
                    'rgba(99, 102, 241, 0.8)',
                    'rgba(168, 85, 247, 0.8)',
                    'rgba(236, 72, 153, 0.8)',
                    'rgba(59, 130, 246, 0.8)',
                    'rgba(16, 185, 129, 0.8)',
                ],
                'borderRadius' => 6,
            ]],
        ]);

        $vanChart->setOptions([
            'responsive' => true,
            'maintainAspectRatio' => false,
            'plugins' => [
                'legend' => ['display' => false],
            ],
            'scales' => [
                'y' => [
                    'beginAtZero' => true,
                    'ticks' => ['stepSize' => 1],
                ],
            ],
        ]);

        $typeChart = $this->chartBuilder->createChart(Chart::TYPE_DOUGHNUT);

        $typeChart->setData([
            'labels' => array_column($stats['quotes_by_type'], 'name'),
            'datasets' => [[
                'data' => array_column($stats['quotes_by_type'], 'total'),
                'backgroundColor' => [
                    'rgba(99, 102, 241, 0.8)',
                    'rgba(168, 85, 247, 0.8)',
                    'rgba(236, 72, 153, 0.8)',
                    'rgba(59, 130, 246, 0.8)',
                ],
            ]],
        ]);

        $typeChart->setOptions([
            'responsive' => true,
            'maintainAspectRatio' => false,
            'plugins' => [
                'legend' => ['position' => 'bottom'],
            ],
        ]);

        return $this->render('admin/dashboard.html.twig', [
            'stats' => $stats,
            'evolutionChart' => $evolutionChart,
            'vanChart' => $vanChart,
            'typeChart' => $typeChart,
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
        yield MenuItem::linkTo(NewsletterCrudController::class, 'Newsletter', 'fa fa-envelope');
    }
}
