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
use Symfony\Component\Form\Extension\Core\Type\DateType;
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
    #[Route('user_view/{id}', name: 'user_view')]
    public function userView(int $id, Request $request, UserRepository $userRepository): Response
    {
        $user = $userRepository->find($id);
        return $this->render('main/user_view.html.twig', [
            'user' => $user,
        ]);
    }

    #[Route('output_view/{id}', name: 'output_view')]
    public function outputView(int $id, Request $request, OutputRepository $outputRepository): Response
    {
        $output = $outputRepository->find($id);
        return $this->render('main/output_view.html.twig', [
            'output' => $output,
        ]);
    }

    #[Route('', name: 'home')]
    public function index(Request          $request, CampusRepository $campusRepository,
                          OutputRepository $outputRepository, Security $security,
                          StateRepository $stateRepository, EntityManagerInterface $entityManager): Response
    {
        $user = $security->getUser();
        $campus = $campusRepository->findAll();
        $filter = new Filter();
        $filterForm = $this->createForm(FilterType::class, $filter);
        $filterForm->handleRequest($request);

        $outputs = $outputRepository->findAll();
        $dateNow = new \DateTime("now");
        foreach ($outputs as $output) {
            if ($output->getRegistrationdeadline() < $dateNow && $output->getState()->getLabel() != 'Annulée')
            {
                $output->setState($stateRepository->find(3));
                $entityManager->persist($output);
                $entityManager->flush();
            }
            if ($output->getStartdatetime() < $dateNow && $output->getState()->getLabel() != 'Annulée')
            {
                $output->setState($stateRepository->find(5));
                $entityManager->persist($output);
                $entityManager->flush();
            }
            $startDate = clone $output->getStartdatetime();
            if ($output->getStartdatetime() < $dateNow &&
                $startDate->add( new \DateInterval( 'PT' . ( (integer) $output->getDuration() ) . 'M' ) ) > $dateNow)
            {
                $output->setState($stateRepository->find(4));
                $entityManager->persist($output);
                $entityManager->flush();
            }
        }

        if ($filterForm->isSubmitted() && $filterForm->isValid()) {
            $output = $outputRepository->findByFilter($filter, $user);
        } else {
            $output = $outputRepository->findBy([],['startdatetime' => 'ASC']);
        }
        return $this->render('main/index.html.twig', [
            'controller_name' => 'MainController',
            'campus' => $campus,
            'outputs' => $output,
            'filterForm' => $filterForm->createView(),
        ]);
    }

    #[Route('user', name: 'user_edit')]
    public function user(Request  $request, EntityManagerInterface $entityManager, UserPasswordHasherInterface $userPasswordHasher,
                         Security $security, SluggerInterface $slugger): Response
    {
        $user = $security->getUser();
        $userForm = $this->createForm(UserType::class, $user);
        $userForm->handleRequest($request);
        if ($userForm->isSubmitted() && $userForm->isValid()) {
            if ($userForm->get('plainpassword')->getData() != '') {
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
                $newFilename = $safeFilename . '.' . $pictureFile->guessExtension();
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
}
