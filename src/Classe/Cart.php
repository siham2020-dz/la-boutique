<?php

namespace App\Classe;

use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RequestStack;

class Cart
{
    public function __construct(
        private RequestStack $requestStack,
        private LoggerInterface $logger
    ) {}

    public function add($product)
    {
        $cart = $this->requestStack->getSession()->get('cart', []);

        if (isset($cart[$product->getId()])) {
            $cart[$product->getId()]['qty']++;
        } else {
            $cart[$product->getId()] = [
                'object' => $product,
                'qty' => 1,
            ];
        }

        $this->requestStack->getSession()->set('cart', $cart);
        $this->logger->info("Produit ajouté au panier : ID {$product->getId()}");
    }

    public function remove()
    {
        $this->requestStack->getSession()->remove('cart');
        $this->logger->info("Le panier a été vidé.");
    }

    public function increaseQuantity($productId)
    {
        $cart = $this->requestStack->getSession()->get('cart', []);
        if (isset($cart[$productId])) {
            $cart[$productId]['qty']++;
            $this->requestStack->getSession()->set('cart', $cart);
            $this->logger->info("Quantité augmentée pour le produit ID {$productId}. Nouvelle quantité : {$cart[$productId]['qty']}");
        } else {
            $this->logger->warning("Tentative d'augmentation de quantité pour un produit inexistant dans le panier : ID {$productId}");
        }
    }

    public function decreaseQuantity($productId)
    {
        $cart = $this->requestStack->getSession()->get('cart', []);
        if (isset($cart[$productId])) {
            if ($cart[$productId]['qty'] > 1) {
                $cart[$productId]['qty']--;
                $this->logger->info("Quantité réduite pour le produit ID {$productId}. Nouvelle quantité : {$cart[$productId]['qty']}");
            } else {
                unset($cart[$productId]);
                $this->logger->info("Produit ID {$productId} retiré du panier car la quantité est tombée à zéro.");
            }
            $this->requestStack->getSession()->set('cart', $cart);
        } else {
            $this->logger->warning("Tentative de réduction de quantité pour un produit inexistant dans le panier : ID {$productId}");
        }
    }

    public function getCart()
    {
        return $this->requestStack->getSession()->get('cart', []);
    }

    public function calculateTotal()
    {
        $total = 0;
        foreach ($this->getCart() as $item) {
            $total += $item['qty'] * $item['object']->getPricewt();
        }
        return $total;
    }
     public function fullquantity(){
        $cart = $this->requestStack->getSession()->get('cart', []);
        $total = 0;
        foreach ($this->getCart() as $item) {
            $total += $item['qty'] ;
        }
        return $total;
    }
    public function updateQuantityAndReturnJson($productId, $action)
    {
        $this->logger->info("Mise à jour de la quantité pour le produit ID {$productId}. Action : {$action}");
    
        // Mettre à jour la quantité
        if ($action === 'increase') {
            $this->increaseQuantity($productId);
        } elseif ($action === 'decrease') {
            $this->decreaseQuantity($productId);
        } else {
            $this->logger->error("Action inconnue : {$action} pour le produit ID {$productId}");
        }
    
        // Calculer le total
        $cartItems = $this->getCart();
        $total = $this->calculateTotal();
        $fullCartQuantity = $this->fullquantity(); // Obtenir la quantité totale
    
        $this->logger->info("Nouvel état du panier : " . json_encode($cartItems));
    
        // Renvoyer une réponse JSON
        return new JsonResponse([
            'success' => true,
            'qty' => isset($cartItems[$productId]) ? $cartItems[$productId]['qty'] : 0,
            'total' => number_format($total, 2, ',', ' '),
            'fullCartQuantity' => $fullCartQuantity // Ajouter la quantité totale d'articles
        ]);
    }
    
}
