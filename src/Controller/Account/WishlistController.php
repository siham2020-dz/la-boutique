<?php

namespace App\Controller\Account;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\BrowserKit\Request as BrowserKitRequest;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class WishlistController extends AbstractController
{
    #[Route('/compte/liste-de-souhait', name: 'app_account_wishlist')]
    public function index(): Response
    {
        return $this->render('account/wishlist/index.html.twig', [
            
        ]);
    }
    #[Route('/compte/liste-de-souhait/{id}', name: 'app_account_wishlist_add')]
    public function add($id,  ProductRepository $productRepository ,EntityManagerInterface $entityManager , Request $request) : Response
    {
        //1. Récupérer l'objet du produit souhaité

        $product = $productRepository->findOneById($id);

        //2. Si le produit existant, ajouter le produit a ma liste favoris 

        if ($product){
            $this->getUser()->addWishlist($product);
        }

       
        
        $this->addFlash(
            'success',
            'Produit correctement ajouté à votre Liste.'
        );
//3. sauvgarder en BDD 
$entityManager->flush();
        return $this->redirect($request->headers->get('referer'));
    }
    #[Route('/compte/liste-de-souhait/remove/{id}', name: 'app_account_wishlist_remove')]
    public function remove($id,  ProductRepository $productRepository ,EntityManagerInterface $entityManager ,Request $request) : Response
    {
        //1. Récupérer l'objet du produit  à supprimer

        $product = $productRepository->findOneById($id);

        //2. Si le produit existant, supprimer le produit a ma liste favoris 

        if ($product){
            $this->getUser()->removeWishlist($product);
        }

       if($product){
        $this->addFlash(
            'success',
            'Produit correctement Supprimer de  votre Liste souhait'
        );
       }else{
        $this->addFlash(
            'danger',
            'Produit introuvable .'
        );
       }
        
        
//3. sauvgarder en BDD 
$entityManager->flush();
return $this->redirect($request->headers->get('referer'));
        
    }
}
