<?php

namespace App\Controller\Account;

use Doctrine\ORM\EntityManagerInterface;
use App\Repository\OrderRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    private $entityManager;
    public  function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager =$entityManager;
    }
    #[Route('/compte', name: 'app_account')]
    public function index(OrderRepository $orderRepository): Response
    {
       $orders= $orderRepository->findBy([
        'user'=>$this->getUser(),
        'state'=>[2,3]

       ]);
       
        return $this->render('account/index.html.twig',[
            'orders'=>$orders
        ]);
    }
   
   
}
