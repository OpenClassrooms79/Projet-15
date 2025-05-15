<?php

namespace App\Controller\Admin;

use App\Constant\Pagination;
use App\Entity\Media;
use App\Form\MediaType;
use App\Repository\MediaRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

use function unlink;

class MediaController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private MediaRepository $mediaRepository,
    ) {}

    #[Route("/admin/media", name: "admin_media_index")]
    public function index(Request $request): Response
    {
        $page = $request->query->getInt('page', 1);

        $criteria = [];

        if (!$this->isGranted('ROLE_ADMIN')) {
            $criteria['user'] = $this->getUser();
        }

        $medias = $this->mediaRepository->findBy(
            $criteria,
            ['id' => 'ASC'],
            Pagination::IMAGES_PER_PAGE,
            Pagination::IMAGES_PER_PAGE * ($page - 1),
        );
        $total = $this->mediaRepository->count($criteria);

        return $this->render('admin/media/index.html.twig', [
            'medias' => $medias,
            'total' => $total,
            'page' => $page,
            'images_per_page' => Pagination::IMAGES_PER_PAGE,
        ]);
    }

    #[Route("/admin/media/add", name: "admin_media_add")]
    public function add(Request $request): Response
    {
        $media = new Media();
        $form = $this->createForm(MediaType::class, $media, ['is_admin' => $this->isGranted('ROLE_ADMIN')]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if (!$this->isGranted('ROLE_ADMIN')) {
                $media->setUser($this->getUser());
            }
            $uploadDir = $this->getParameter('kernel.project_dir') . '/public/uploads';
            $media->setPath('uploads/' . md5(uniqid('', true)) . '.' . $media->getFile()->guessExtension());
            $media->getFile()->move($uploadDir, $media->getPath());
            $this->entityManager->persist($media);
            $this->entityManager->flush();

            return $this->redirectToRoute('admin_media_index');
        }

        return $this->render('admin/media/add.html.twig', ['form' => $form->createView()]);
    }

    #[Route('/admin/media/delete/{id}', name: 'admin_media_delete')]
    public function delete(int $id, EntityManagerInterface $entityManager, Filesystem $filesystem): Response
    {
        $criteria = ['id' => $id];

        if (!$this->isGranted('ROLE_ADMIN')) {
            $criteria['user'] = $this->getUser();
        }

        $media = $this->mediaRepository->findOneBy($criteria);
        if ($media !== null) {
            $entityManager->remove($media);
            $entityManager->flush();
            if ($filesystem->exists($media->getPath())) {
                $filesystem->remove($media->getPath());
            }
        }

        return $this->redirectToRoute('admin_media_index');
    }
}