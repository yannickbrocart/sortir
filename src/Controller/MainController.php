<?php

namespace App\Controller;

use App\Entity\Campus;
use App\Entity\Filter;
use App\Entity\Output;
use App\Form\FilterType;
use App\Form\OutputType;
use App\Form\UserType;
use App\Repository\CampusRepository;
use App\Repository\OutputRepository;
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

class MainController extends AbstractController
{
    #[Route('', name: 'home')]
    public function index(Request $request, CampusRepository $campusRepository, OutputRepository $outputRepository): Response
    {
        $campus = $campusRepository->findAll();
        $filter = new Filter();
        $filterForm = $this ->createForm(FilterType::class, $filter);
        $filterForm->handleRequest($request);
        if ($filterForm->isSubmitted() && $filterForm->isValid()) {
/*            $output = $outputRepository->findAll();*/
/*            var_dump($filter);*/
            $output = $outputRepository->findByFilter($filter);
        } else {
            $output = $outputRepository->findAll();
        }
        return $this->render('main/index.html.twig', [
            'controller_name' => 'MainController',
            'campus' => $campus,
            'outputs' => $output,
            'filterForm' => $filterForm->createView(),
            ]);
    }

    #[Route('user', name: 'user_edit')]
    public function user(Request $request, EntityManagerInterface $entityManager, UserPasswordHasherInterface $userPasswordHasher,
                         Security $security, SluggerInterface $slugger): Response
    {
        $user=$security->getUser();
        $userForm = $this ->createForm(UserType::class, $user);
        $userForm->handleRequest($request);
        if ($userForm->isSubmitted() && $userForm->isValid()) {
            if ($userForm->get('plainpassword')->getData()!='') {
                $user->setPassword(
                    $userPasswordHasher->hashPassword(
                        $user,
                        $userForm->get('plainpassword')->getData()
                    )
                );
            }
            $pictureFile = $userForm->get('urlpicture')->getData();
            if ($pictureFile) {
                $originalFilename = pathinfo($pictureFile->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename.'.'.$pictureFile->guessExtension();
                $pictureFile->move('img/', $newFilename);
                $user->setUrlpicture($newFilename);
            }
            $entityManager->persist($user);
            $entityManager->flush();
            return $this->redirectToRoute('main_home');
        }
        return $this->render('main/user.html.twig', [
            'userForm' => $userForm->createView(),
        ]);
    }

    #[Route('output', name: 'create_output')]
    public function createOutput(Request $request, EntityManagerInterface $entityManager, OutputRepository $outputRepository, OutputType $outputType,
                                    Security $security): Response
    {
        $user=$security->getUser();
        $outputForm = $this ->createForm(OutputType::class, new Output());
/*        $outputForm->setOrganizer($user);*/
        if ($outputForm->isSubmitted() && $outputForm->isValid()) {
            $entityManager->persist($output);
            $entityManager->flush();
            return $this->redirectToRoute('main_home');
        }
        return $this->render('main/create_output.html.twig', [
            'outputForm' => $outputForm->createView(),
        ]);
    }
}
