<?php

namespace App\Controller;

use App\Entity\Post;
use App\Repository\PostRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/post", name="post.")
 */
class PostController extends AbstractController
{
    /**
     * @Route("/", name="index")
     * @param PostRepository $postRepository
     * @return Response
     */
    public function index(PostRepository $postRepository): Response
    {
        $posts = $postRepository->findAll();
        return $this->render('post/index.html.twig', [
            'posts' => $posts
        ]);
    }

    /**
     * @Route("/create", name="create")
     * @param Request $request
     * @param PostRepository $postRepository
     * @return Response
     */
    public function create(Request $request, PostRepository $postRepository): Response
    {
        $post = (new Post())->setTitle('sample title');
        $entityManager = $postRepository->getEntityManager();
        $entityManager->persist($post);
        $entityManager->flush();

        return new Response("Post was created.");
    }
}
