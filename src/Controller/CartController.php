<?php

namespace App\Controller;
use App\Classe\Cart;
use App\Repository\ProductRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class CartController extends AbstractController
{
    #[Route('/mon-panier', name: 'app_cart')]
    public function index(Cart $cart): Response
    {
        return $this->render('cart/index.html.twig', [
            'cart'=>$cart->getCart()
            
        ]);
    }
    #[Route('/cart/add/{id}', name: 'app_cart_add')]
    public function add($id, Cart $cart, ProductRepository $productRepository): Response
    {
        $product = $productRepository->findOneById($id);

        $cart->add($product);
        
        $this->addFlash(
            'success',
            'Produit correctement ajouté à votre pannier.'
        );

        return $this->redirectToRoute('app_product',[
                'slug'=>$product->getSlug()
              ]);
      
    }
    #[Route('/cart/remove', name: 'app_cart_remove')]
    public function remove(Cart $cart): Response
    {
        $cart->remove();
        $this->addFlash(
        'success',
        'Votre panier a été vidé.'
    );

        return $this->redirectToRoute('app_cart');
    }

    #[Route('/cart/update/{id}/{action}', name: 'app_cart_update', methods: ['POST'])]
    public function update($id, $action, Cart $cart): JsonResponse
    
        {
            return $cart->updateQuantityAndReturnJson($id, $action);
        }
        #[Route('/cart/increase/{id}', name: 'app_cart_increase', methods: ['POST'])]
public function increase($id, Cart $cart): JsonResponse
{
    return $cart->updateQuantityAndReturnJson($id, 'increase');
}

#[Route('/cart/decrease/{id}', name: 'app_cart_decrease', methods: ['POST'])]
public function decrease($id, Cart $cart): JsonResponse
{
    return $cart->updateQuantityAndReturnJson($id, 'decrease');
}
}
