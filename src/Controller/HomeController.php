<?php

namespace App\Controller;

use App\Entity\Album;
use App\Entity\Media;
use App\Entity\User;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    public function __construct(private ManagerRegistry $registry) {}

    #[Route("/", name: "home")]
    public function home(): Response
    {
        return $this->render('front/home.html.twig');
    }

    #[Route("/guests", name: "guests")]
    public function guests(): Response
    {
        $guests = $this->registry->getRepository(User::class)->findBy(['admin' => false, 'enabled' => true]);
        return $this->render('front/guests.html.twig', [
            'guests' => $guests,
        ]);
    }

    #[Route("/guest/{id}", name: "guest")]
    public function guest(int $id): Response
    {
        $guest = $this->registry->getRepository(User::class)->findOneBy(['id' => $id, 'enabled' => true]);
        if ($guest === null) {
            return $this->redirectToRoute('home');
        }

        return $this->render('front/guest.html.twig', [
            'guest' => $guest,
        ]);
    }

    #[Route("/portfolio/{id}", name: "portfolio")]
    public function portfolio(?int $id = null): Response
    {
        $albums = $this->registry->getRepository(Album::class)->findAll();
        $album = $id ? $this->registry->getRepository(Album::class)->find($id) : null;
        $user = $this->registry->getRepository(User::class)->findOneByAdmin(true);

        $medias = $album
            ? $this->registry->getRepository(Media::class)->findByAlbum($album)
            : $this->registry->getRepository(Media::class)->findByUser($user);
        return $this->render('front/portfolio.html.twig', [
            'albums' => $albums,
            'album' => $album,
            'medias' => $medias,
        ]);
    }

    #[Route("/about", name: "about")]
    public function about(): Response
    {
        return $this->render('front/about.html.twig');
    }
}