<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegisterUserType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class RegisterController extends AbstractController
{
    #[Route('/inscription', name: 'app_register')]
    public function index(Request $request,EntityManagerInterface $entityManager): Response
    {
           //dd($request);
           $user = new User();
           $form =$this->createForm(RegisterUserType::class,$user);
           //si le formulaire esy soumis alors:
           // tu enregistre les datas en bcaddtu envoies un message de confirmation du compte bien créé
           $form->handleRequest($request);
           if ($form->isSubmitted() && $form->isValid())
           {
            
             $entityManager->persist($user);
             $entityManager->flush();

           }
           //enregister dan la base de donnees
        return $this->render('register/index.html.twig',[
            'registerForm' =>$form->createView()
        ]);
    }
}
