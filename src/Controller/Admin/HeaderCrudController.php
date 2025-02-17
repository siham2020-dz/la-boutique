<?php

namespace App\Controller\Admin;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use App\Entity\Header;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;

use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class HeaderCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Header::class;
    }

   
    public function configureFields(string $pageName): iterable
    {
        $require = true;
        if ($pageName =='edit')
        {
            $require= false;
        }
        return [
           
            TextField::new('title','Titre'),
            TextareaField::new('content','Contenu'),
            TextField::new('buttonTitle','Titre du bouton'),
            TextField::new('buttonLink','URL du bouton'),
            ImageField::new('illustration')->setLabel('Image de fond du header')
                  ->setHelp('Image du fond en JPG')
                  ->setUploadedFileNamePattern('[year]-[month]-[day]-[contenthash].[extension]')
                  ->setBasePath('uploads')
                  ->setUploadDir('/public/uploads')
                  ->setRequired($require),
        ];
    }

}
