<?php

namespace App\Controller;

use App\Form\ChangePasswordType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Routing\Annotation\Route;

class AccountPasswordController extends AbstractController
{
    private $entityManager;

    /**
     * @param $entityManager
     */
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    #[Route('/compte/modifier-mon-mot-de-passe', name: 'app_account_password')]
    public function index(Request $request,
                          UserPasswordHasherInterface $passwordHasher): Response
    {
        $notification = null;

        $user = $this ->getUser();
        $form = $this ->createForm(ChangePasswordType::class,$user);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()){
            $old_pwd = $form->get('old_password')->getData();

            if ($passwordHasher->isPasswordValid($user, $old_pwd)){
                $new_pwd = $form->get('new_password')->getdata();
                $password = $passwordHasher->hashPassword($user, $new_pwd);

                $user->setPassword($password);
                $this->entityManager->persist($user);
                $this->entityManager->flush();
                $notification= "votre mot de passe a bien été mise à jour";
            }else{
                $notification="votre mot de passe actuel n'est pas le bon";
            }

        }
        return $this->render('account/password.html.twig', [
            'form' => $form->createView(),
            'notification' => $notification
        ]);
    }
}
