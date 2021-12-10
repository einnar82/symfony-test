<?php

namespace App\Controller;

use App\Entity\Post;
use App\Form\PostType;
use App\Repository\PostRepository;
use App\Service\FileUploaderService;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\UploadedFile;
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
     * @throws ORMException
     */
    public function create(Request $request, PostRepository $postRepository, FileUploaderService $fileUploaderService): Response
    {
        $post = new Post;
        $form = $this->createForm(PostType::class, $post);
        $form->handleRequest($request);
        if ($form->isSubmitted()) {
            $entityManager = $postRepository->getEntityManager();
            /** @var UploadedFile $file */
            $file = $form->get('attachment')->getData();
            if ($file) {
                $filename = $fileUploaderService->upload($file);
                $post->setImage($filename);
                $entityManager->persist($post);
                $entityManager->flush();
            }
            return $this->redirectToRoute('post.index');
        }

        return $this->render('post/create.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/show/{id}", name="show")
     * @param $id
     * @param PostRepository $postRepository
     * @return Response
     */
    public function show($id, PostRepository $postRepository): Response
    {
        $post = $postRepository->find($id);
        return $this->render('post/show.html.twig', [
           'post' => $post
        ]);
    }

    /**
     * @Route("/delete/{id}", name="delete")
     * @param $id
     * @param PostRepository $postRepository
     * @return Response
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function delete($id, PostRepository $postRepository): Response
    {
        $post = $postRepository->find($id);
        $postRepository->getEntityManager()->remove($post);
        $postRepository->getEntityManager()->flush();

        $this->addFlash('success', 'Post was removed.');
        return $this->redirectToRoute('post.index');
    }

}
