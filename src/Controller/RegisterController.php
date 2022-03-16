<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegisterType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

class RegisterController extends AbstractController
{
    private $entityManager;
    public function __construct(EntityManagerInterface
                                $entityManager){
        $this->entityManager =$entityManager;
    }

    #[Route('/inscription', name: 'app_register')]
    public function index(Request $request,
                          UserPasswordHasherInterface $passwordHasher): Response
    {
        $user = new User();
        $form = $this->createForm(RegisterType::class, $user);
        /*Des que le formulaire est soumis je veux que tu traite
        l'information*/
        $form->handleRequest($request);
        /* Regarde si tout va bien*/
        if ($form->isSubmitted() && $form->isValid()){
            $user=$form->getData();
            $password = $passwordHasher->hashPassword($user,
                $user->getPassword());
            $user->setPassword($password);
            //dd($user);
            //dd($password);
            //Pour enrgistrer en bdd
            $this->entityManager->persist($user);
            $this->entityManager->flush();

        }

        return $this->render('register/index.html.twig',[
        'form' => $form->createView()]);
    }
}
