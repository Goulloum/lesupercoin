<?php

namespace App\Controller;

use App\Entity\Announcement;
use App\Form\CreateAnnouncementType;
use App\Repository\AnnouncementRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AnnouncementController extends AbstractController
{
    #[Route('/', name: 'app_announcement')]
    public function index(AnnouncementRepository $announcementRepository): Response
    {

        $announcements = $announcementRepository->findAll();
        return $this->render('announcement/index.html.twig', [
            'controller_name' => 'AnnouncementController',
            'announcements' => $announcements
        ]);
    }

    #[Route('/announcement/{id}', name: 'app_announcement_show', requirements: ['id' => '\d+'])]
    public function show($id, AnnouncementRepository $announcementRepository): Response
    {
        $announcement = $announcementRepository->find($id);
        return $this->render('announcement/show.html.twig', [
            'controller_name' => 'AnnouncementController',
            'announcement' => $announcement
        ]);
    }

    #[Route('/announcement/create', name: 'app_announcement_create')]
    public function create(Request $request, EntityManagerInterface $entityManager): Response
    {
        $announcement = new Announcement();
        $form = $this->createForm(CreateAnnouncementType::class, $announcement);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $announcement->setCreatedAt(new \DateTimeImmutable());
            $announcement->setUpdatedAt(new \DateTimeImmutable());
            $announcement->setUser($this->getUser());

            $entityManager->persist($announcement);

            // actually executes the queries (i.e. the INSERT query)
            $entityManager->flush();

            return $this->redirectToRoute('app_announcement');
        }

        return $this->render('announcement/create.html.twig', [
            'controller_name' => 'AnnouncementController',
            'createAnnouncementForm' => $form->createView()
        ]);
    }
}
