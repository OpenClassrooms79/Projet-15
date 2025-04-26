<?php

namespace App\Controller\Admin;

use App\Entity\Album;
use App\Form\AlbumType;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AlbumController extends AbstractController
{
    public function __construct(private ManagerRegistry $registry) {}

    #[Route("/admin/album", name: "admin_album_index")]
    public function index(): Response
    {
        $albums = $this->registry->getRepository(Album::class)->findAll();

        return $this->render('admin/album/index.html.twig', ['albums' => $albums]);
    }

    #[Route("/admin/album/add", name: "admin_album_add")]
    public function add(Request $request): Response
    {
        $album = new Album();
        $form = $this->createForm(AlbumType::class, $album);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->registry->getManager()->persist($album);
            $this->registry->getManager()->flush();

            return $this->redirectToRoute('admin_album_index');
        }

        return $this->render('admin/album/add.html.twig', ['form' => $form->createView()]);
    }

    #[Route("/admin/album/update/{id}", name: "admin_album_update")]
    public function update(Request $request, int $id): Response
    {
        $album = $this->registry->getRepository(Album::class)->find($id);
        $form = $this->createForm(AlbumType::class, $album);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->registry->getManager()->flush();

            return $this->redirectToRoute('admin_album_index');
        }

        return $this->render('admin/album/update.html.twig', ['form' => $form->createView()]);
    }

    #[Route("/admin/album/delete/{id}", name: "admin_album_delete")]
    public function delete(int $id): Response
    {
        $media = $this->registry->getRepository(Album::class)->find($id);
        $this->registry->getManager()->remove($media);
        $this->registry->getManager()->flush();

        return $this->redirectToRoute('admin_album_index');
    }
}