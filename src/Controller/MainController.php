<?php

namespace App\Controller;

use App\Form\UserType;
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
    public function index(): Response
    {
        return $this->render('main/index.html.twig', [
            'controller_name' => 'MainController',
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
            //$this->addFlash('succes', 'Utilisateur mis à jour !');
            return $this->redirectToRoute('main_home');
        }

        return $this->render('main/user.html.twig', [
            'userForm' => $userForm->createView(),
        ]);
    }
}
