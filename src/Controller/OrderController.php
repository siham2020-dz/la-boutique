<?php

namespace App\Controller;

use App\Form\OrderType;
use App\Classe\Cart;
use App\Entity\Order;
use App\Entity\OrderDetail;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class OrderController extends AbstractController
{
    /*
     * 1ère étape du tunnel d'achat
     * Choix de l'adresse de livraison et du transporteur
     */
    #[Route('/commande/livraison', name: 'app_order')]
    public function index(): Response
    {
        if (!$this->getUser()) {
            return $this->redirectToRoute('app_login'); // Redirection si l'utilisateur n'est pas connecté
        }

        $addresses = $this->getUser()->getAdresses();
        if (count($addresses) === 0) {
            return $this->redirectToRoute('app_account_address_form');
        }

        $form = $this->createForm(OrderType::class, null, [
            'addresses' =>  $addresses,
            'action' => $this->generateUrl('app_order_summary') // Correction du nom de la route
        ]);

        return $this->render('order/index.html.twig', [
            'deliverForm' => $form->createView(),
        ]);
    }

    /*
     * 2ème étape du tunnel d'achat
     * Récapitulatif de la commande
     * Insertion en base de données
     * Préparation du paiement vers Stripe
     */
    #[Route('/commande/recapitulatif', name: 'app_order_summary')]
    public function add(Request $request, Cart $cart,EntityManagerInterface $entityManager): Response
    {
        if ($request->getMethod() !== 'POST') {
            return $this->redirectToRoute('app_cart');
        }
        $products = $cart->getCart();
        $form = $this->createForm(OrderType::class, null, [
            'addresses' => $this->getUser()->getAdresses(),
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Récupération des informations du formulaire
            $addressObj = $form->get('adresses')->getData();
            
            if (!$addressObj) {
                $this->addFlash('warning', 'Veuillez sélectionner une adresse.');
                return $this->redirectToRoute('app_order');
            }

            // Construction de l'adresse en format texte
            $address = $addressObj->getFirstName() . ' ' . $addressObj->getLastName() . "\n";
            $address .= $addressObj->getPostal() . ' ' . $addressObj->getCity() . "\n";
            $address .= $addressObj->getCountry() . "\n";
            $address .= 'Tel: ' . $addressObj->getPhone();

            // Création de la commande
           
            $order = new Order();
            $order->setUser($this->getUser());
            $order->setCreatedAt(new \DateTime());
            $order->setState(1);
            $order->setCarrierName($form->get('carriers')->getData()->getName());
            $order->setCarrierPrice($form->get('carriers')->getData()->getPrice());
            $order->setDelivery($address);
            foreach ($products as $product){
                $orderDetail = new OrderDetail(); 
                $orderDetail->setProductName($product['object']->getName());
                $orderDetail->setProductIllustration($product['object']->getIllustration());
                $orderDetail->setProductPrice($product['object']->getPrice());
                $orderDetail->setProductTva($product['object']->getTva());
                $orderDetail->setproductQuantity ($product['qty']);
                $order->addOrderDetail($orderDetail);
            }

            $entityManager->persist($order);
            $entityManager->flush();
            // Debug temporaire pour vérifier les données
            //dd($order, $address);
        }

        return $this->render('order/summary.html.twig', [
            'choices' => $form->getData(),
            'cart'=>$products,
            'cart' => $cart->getCart(),
        ]);
    }
}
