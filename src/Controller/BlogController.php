<?php

namespace App\Controller;

use App\Repository\BlogPostRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/blog', name: 'app_blog')]
class BlogController extends AbstractController
{
    #[Route('', name: '_index')]
    public function index(BlogPostRepository $blogPostRepository): Response
    {
        $posts = $blogPostRepository->findPublished();

        return $this->render('pages/blog/index.html.twig', [
            'posts' => $posts,
        ]);
    }

    #[Route('/{slug}', name: '_show')]
    public function show(string $slug, BlogPostRepository $blogPostRepository): Response
    {
        $post = $blogPostRepository->findOnePublishedBySlug($slug);

        if (!$post) {
            throw $this->createNotFoundException('Article de blog introuvable.');
        }

        $recentPosts = $blogPostRepository->findRecentPublished(5);

        return $this->render('pages/blog/show.html.twig', [
            'post' => $post,
            'recentPosts' => $recentPosts,
        ]);
    }
}
