<?php

namespace App\Controller\Admin;

use App\Entity\Order;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use App\Classe\Mail;
use App\Classe\State;
use EasyCorp\Bundle\EasyAdminBundle\Context\AdminContext;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\NumberField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use Symfony\Component\HttpFoundation\Request;


class OrderCrudController extends AbstractCrudController
{
    private $em;
    public function __construct(EntityManagerInterface $entityMangerInterface)
    {
        $this->em=$entityMangerInterface;
    }
    public static function getEntityFqcn(): string
    {
        return Order::class;
    }
    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('Commande')
            ->setEntityLabelInPlural('Commandes')

        ;
    }
    public function configureActions(Actions $actions): Actions
    {
        $show = Action::new('Afficher')->linkToCrudAction('show');
        return $actions
            ->add(Crud::PAGE_INDEX, $show)
            ->remove(Crud::PAGE_INDEX, Action::NEW)
            ->remove(Crud::PAGE_INDEX, Action::EDIT)
            ->remove(Crud::PAGE_INDEX, Action::DELETE);
    }
    //Function permettant le changement de statut de la commmande 
    public function changeState($order,$state)
    {
       
        //1.Modification du statut de la commande
        $order->setState($state);
        $this->em->flush();
        //2. Affichage du Flash Message pour informer l'administarateur
        $this->addFlash('success',"Statut de la commande correctement mise jour .");
        //3. Informer l'utulisateur par email de la modidication du statut de ça commande 
         
             
      
        $mail= new Mail();
        $vars =[
          'firstname'=> $order->getUser()->getFirstname(),
          'id_order' =>$order->getId()
        ];

         $mail->send($order->getUser()->getEmail(),$order->getUser()->getFirstname().''.$order->getUser()->getLastname(),State::STATE[$state]['email_subject'],State::STATE[$state]['email_template'],$vars);
    }
    public function show(AdminContext $context ,AdminUrlGenerator $adminUrlGenerator, Request $request) 
    {
        
        $order =$context->getEntity()->getInstance();
        // R&cupérer l'URL de notre action "SHOW"
        $url = $adminUrlGenerator->setController(self::class)->setAction('show')->setEntityId($order->getId())->generateUrl();

        // Traitement des changement de statut
        if($request->get('state'))
        {
            $this->changeState($order,$request->get('state'));
        }


        return $this->render('admin/order.html.twig',[
            'order' => $order,
            'current_url'=>$url
        ]);
    }


    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id'),
            DateField::new('createdAT')->setLabel('Date'),
            NumberField::new('state')->setLabel('Status')->setTemplatePath("admin/state.html.twig"),
            AssociationField::new('user')->setLabel('Utilisateur'),
            TextField::new('carrierName')->setLabel('Transporteur'),
            NumberField::new('TotalTva')->setLabel('Total TVA'),
            NumberField::new('TotalWt')->setLabel('Total TTC'),

        ];
    }

}
