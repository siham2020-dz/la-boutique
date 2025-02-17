<?php

namespace App\Controller\Admin;

use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use App\Entity\User;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Dto;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class UserCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return User::class;
    }
    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('Utilisateur')
            ->setEntityLabelInPlural('Utilisateurs')
            ->setPaginatorPageSize(5)
            
           
        ;
    }
 
    public function configureFields(string $pageName): iterable
    {
        return [
          TextField::new('firstname')->setLabel('Prenom'),
          TextField::new('lastname')->setLabel('Nom'),
          DateField::new('lastLoginAt')->setLabel('Derniere connexion')->onlyOnIndex(),
          ChoiceField::new('roles')->setLabel('Permission')->setHelp('Vous pouvez choisir votre role')->setChoices([
           'ROLE_USER'=>'ROLE_USER' ,
           'ROLE_ADMIN'=>'ROLE_ADMIN',
          ])->allowMultipleChoices(),
          TextField::new('Email')->setLabel('Email')->onlyOnIndex(),
        
        ];
    }
  
}
