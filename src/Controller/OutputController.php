<?php

namespace App\Controller;

use App\Entity\Campus;
use App\Entity\Filter;
use App\Entity\Output;
use App\Entity\State;
use App\Form\FilterType;
use App\Form\OutputType;
use App\Form\UserType;
use App\Repository\CampusRepository;
use App\Repository\OutputRepository;
use App\Repository\StateRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\String\Slugger\SluggerInterface;

/**
 * @Route("/", name="main_")
 */

class OutputController extends AbstractController
{
    #[Route('create_output', name: 'create_output')]
    public function createOutput(Request $request, EntityManagerInterface $entityManager,
                                 CampusRepository $campusRepository, Security $security,
                                 StateRepository $stateRepository): Response
    {
        $output = new Output();
        $campus = $campusRepository->findAll();
        $outputForm = $this->createForm(OutputType::class, $output);
        $outputForm->handleRequest($request);
        $output->setCampus($security->getUser()->getCampus());
        $output->setState($stateRepository->find(1));
        $output->setOrganizer($security->getUser());

        if ($outputForm->isSubmitted() && $outputForm->isValid()) {
            $entityManager->persist($output);
            $entityManager->flush();
            return $this->redirectToRoute('main_home');
        };
        return $this->render('main/create_output.html.twig', [
            'outputForm' => $outputForm->createView(),
            'campus' => $campus,
        ]);
    }

    #[Route('update_output/{id}', name: 'update_output')]
    public function updateOutput(int $id, Request $request, EntityManagerInterface $entityManager,
                                 CampusRepository $campusRepository, Security $security,
                                 OutputRepository $outputRepository, StateRepository $stateRepository): Response
    {
        $output = $outputRepository->find($id);
        $campus = $campusRepository->findAll();
        $outputForm = $this->createForm(OutputType::class, $output);
        $outputForm->handleRequest($request);

        if ($outputForm->isSubmitted() && $outputForm->isValid()) {
            if ($request->get('submit') === 'published') {
                $output->setState($stateRepository->find(2));
            };
            $entityManager->persist($output);
            $entityManager->flush();
            return $this->redirectToRoute('main_home');
        };
        return $this->render('main/create_output.html.twig', [
            'outputForm' => $outputForm->createView(),
            'campus' => $campus,
            'output' => $output,
        ]);
    }

    #[Route('delete_output/{id}', name: 'delete_output')]
    public function deleteOutput(int $id, EntityManagerInterface $entityManager,
                                 OutputRepository $outputRepository): Response
    {
        $output = $outputRepository->find($id);
        $entityManager->remove($output);
        $entityManager->flush();
        return $this->redirectToRoute('main_home');
    }

    #[Route('cancel_output/{id}', name: 'cancel_output')]
    public function cancelOutput(int $id, EntityManagerInterface $entityManager,
                                 OutputRepository $outputRepository, StateRepository $stateRepository): Response
    {
        $output = $outputRepository->find($id);
        $output->setState($stateRepository->find(6));
        $entityManager->persist($output);
        $entityManager->flush();
        return $this->redirectToRoute('main_home');
    }

    #[Route('publish_output/{id}', name: 'publish_output')]
    public function publishOutput(int $id, EntityManagerInterface $entityManager,
                                 OutputRepository $outputRepository, StateRepository $stateRepository): Response
    {
        $output = $outputRepository->find($id);
        $output->setState($stateRepository->find(2));
        $entityManager->persist($output);
        $entityManager->flush();
        return $this->redirectToRoute('main_home');
    }

}
