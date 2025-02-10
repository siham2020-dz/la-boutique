<?php

namespace App\Controller\Account;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Adress;
use App\Repository\AdressRepository;
use App\Form\AddressUserType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Doctrine\ORM\EntityManagerInterface;


class AdressController extends AbstractController
{
    private $entityManager;
    public  function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager =$entityManager;
    }
    #[Route('/compte/adresses', name: 'app_account_address')]
    public function index(): Response
    {
        return $this->render('account/address/index.html.twig');
    }
    #[Route('/compte/adresses/delete/{id}', name: 'app_account_address_delete')]
    public function Delete($id,AdressRepository $adressRepository): Response
    {
        $address =$adressRepository->findOneById($id);
        if(! $address OR  $address->getUser() !=$this->getUser()){
            return $this->redirectToRoute('app_account_address');
        }
            $this->addFlash
            ('success',
              "votre Adress est correctement  supprimer. "
            );
            $this->entityManager->remove($address);
            $this->entityManager->flush();
            return $this->redirectToRoute('app_account_address');
    }
    #[Route('/compte/adresse/ajouter/{id}', name: 'app_account_address_form',defaults:['id'=>null] )]
    public function Form(Request $request,$id,AdressRepository $adressRepository,Cart $cart): Response
    {   
        if($id){

            $address = $adressRepository->findOneById($id);
            if(! $address OR  $address->getUser() !=$this->getUser()){
                //die('ok');
                return $this->redirectToRoute('app_account_address');
            }
        }else{

            $address=new Adress();
            $address->setUser($this->getUser());
        }
       
        $form =$this->createForm(AddressUserType::class,$address);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            $this->entityManager->persist($address);
            $this->entityManager->flush();
            $this->addFlash
                ('success',
                "votre Adress est correctement  sauvegardée. "
                  );
        if($cart->fullQantity() >0){
            return $this->redirectToRoute('app_order');
        }

            return $this->redirectToRoute("app_account_address");
        }
        return $this->render('account/address/Form.html.twig',[
            'adressForm'=>$form,
        ]);
    }
}




?>