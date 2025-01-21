<?php

namespace App\Tests;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class RegisterUserTest extends WebTestCase
{
    public function testSomething(): void
    {
        /*
        * 1. Crée un faux client (navigateur)de pointer ver une url 
        * 2. Remplire les champs de mon formulaire d'inscription
        * 3. Est-ce que tu peux regarder ma page ,jai une alert suivante: Votre compte est correctement créé,veuillez vous connecter.
        */
        /*etatpe 1*/
        $client = static::createClient();
        $client->request('GET','/inscription');
        /* remplire les champs de mon formulaire discription */
        $client->submitForm('Valider',[
              'register_user[email]'=>'siham@exemple.fr',
              'register_user[plainPassword][first]'=>'123456',
              'register_user[plainPassword][second]'=>'123456',
              'register_user[firstname]'=>'siham',
              'register_user[lastname]'=>'ghanous'
       ]);
       /* suive la direction */ 
       /* test la direction */
       $this->assertResponseRedirects('/Connexion');
       $client->followRedirect();
        /* etape 3 pour test */
         $this->assertSelectorExists('div:contains("votre compte est correctement  créé,veuiller vous connecter.")');

        
    }
}
