<?php

namespace App\Controller;

use \DateTime;
use App\Classe\Mail;
use App\Repository\UserRepository;
use App\Form\ResetPasswordFormType;
use App\Form\ForgotPasswordFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

final class ForgotPasswordController extends AbstractController
{
    private $em;
    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }
    #[Route('/mot-de-passe-oublier', name: 'app_password')]
    public function index(Request $request, UserRepository $userRepository): Response
    {
        //1. Formulaire
        $form = $this->createForm(ForgotPasswordFormType::class);
        $form->handleRequest($request);
        //2. Traitement du formulaire
        if ($form->isSubmitted() && $form->isValid()) {
            //3. si l'eail renseigné par l'utilisateur est en base de donnée
            $email = $form->get('email')->getData();

            $user = $userRepository->findOneByEmail($email);

            //4. Envoyer une notification à lutulisateur  
            $this->addFlash('success',"Si votre email existe vour resevrez un email pour rénitialise votre mot de passe . " );
            //5. si aucun email trouvé, on push une notification : Email introuvable.
            if ($user) {
                //5.a Crée un token qu'on  va stocker en bdd

                $token = bin2hex(random_bytes(15));

                $user->setToken($token);
                $date = new DateTime();

                $date->modify('+10 minutes');

                $user->setTokenExpireAt($date);

                $this->em->flush();
                $url = $this->generateUrl('app_password-update', ['token' => $token], UrlGeneratorInterface::ABSOLUTE_URL);
                $mail = new Mail();
                $vars = [
                    'link' => $url,
                ];

                $mail->send($user->getEmail(), $user->getFirstname() . '' . $user->getLastname(), 'Modification de votre mot de passe  ,', 'ForgotPassword.html', $vars);
            }
        }


        return $this->render('password/index.html.twig', [
            'forgotpasswordForm' => $form->createView(),
        ]);
    }
    #[Route('/mot-de-passe/reset/{token}', name: 'app_password-update')]
    public function update(Request $request, UserRepository $userRepository, $token): Response
    {
        if (!$token) {
            return $this->redirectToRoute('app_password');
        }
        $user = $userRepository->findOneByToken($token);
        $now = new DateTime();
        if (!$user || $now > $user->getTokenExpireAt()) {
            return $this->redirectToRoute('app_password');
        }


        $form = $this->createForm(ResetPasswordFormType::class, $user);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $user->setToken(null);
            $user->setTokenExpireAt(null);
            // Traitement à effectuer 
            $this->em->flush();
            $this->addFlash(
                'success',
                "votre mot de pass est correctement mis à jour ."
            );
            return $this->redirectToRoute('app_login');
        }
        return $this->render('password/reset.html.twig', [

            'form' => $form->createView()
        ]);
    }
}
