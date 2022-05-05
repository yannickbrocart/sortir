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
    #[Route('output', name: 'create_output')]
    public function createOutput(Request $request, EntityManagerInterface $entityManager,
                                 OutputRepository $outputRepository, OutputType $outputType,
                                 CampusRepository $campusRepository): Response
    {
        $output = new Output();
        $campus = $campusRepository->findAll();
        $outputForm = $this->createForm(OutputType::class, $output);
        $outputForm->handleRequest($request);
        /*$state = new State();
        $state->setLabel('Créée');
        $output->setState($state);*/

        if ($outputForm->isSubmitted() /*&& $outputForm->isValid()*/) {
            /*$entityManager->persist($output);
            $entityManager->flush();
            return $this->redirectToRoute('main_home');
        };*/
            return $this->render('main/create_output.html.twig', [
                'outputForm' => $outputForm->createView(),
                'campus' => $campus,
            ]);
        }
    }
}
