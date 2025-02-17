<?php

namespace App\Controller;

use App\Repository\OrderRepository;
use Dompdf\Dompdf;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class InvoiceController extends AbstractController
{
     /**
     * Impression de la facture PDF pour un utilisateur connecté.
     * Vérification de la commande pour un utilisateur donné.
     */

    #[Route('/compte/facture/impression/{id_order}', name: 'app_invoice_customer')]
    public function index(OrderRepository $orderRepository, $id_order): Response
    {
         // 1. Vérification que l'objet commande existe
         $order = $orderRepository->find($id_order);
        if (!$order) {
            return  $this->redirectToRoute('app_account');
        }
        // 2. Vérification que la commande appartient à l'utilisateur connecté
        if ($order->getUser() !== $this->getUser()) {
            $this->addFlash('danger', 'Vous n\'avez pas accès à cette facture.');
            return $this->redirectToRoute('app_account');
        }
        $dompdf = new Dompdf();
        $html = $this->renderView('invoice/index.html.twig', [
            'order' => $order
        ]);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();
        $dompdf->stream('facture.pdf', [
            'Attachment' => false
        ]);
        exit();
    }
    /**
     * Impression de la facture PDF pour un administrateur connecté.
     * Vérification de la commande pour un utilisateur donné.
     */

     #[Route('/admin/facture/impression/{id_order}', name: 'app_invoice_admin')]
     public function printForAdmin(OrderRepository $orderRepository, $id_order): Response
     {
          // 1. Vérification que l'objet commande existe
          $order = $orderRepository->find($id_order);
         if (!$order) {
             return  $this->redirectToRoute('admin');
         }
         
         $dompdf = new Dompdf();
         $html = $this->renderView('invoice/index.html.twig', [
             'order' => $order
         ]);
         $dompdf->loadHtml($html);
         $dompdf->setPaper('A4', 'portrait');
         $dompdf->render();
         $dompdf->stream('facture.pdf', [
             'Attachment' => false
         ]);
         exit();
     }
}
