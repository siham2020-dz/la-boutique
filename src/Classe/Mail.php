<?php

namespace App\Classe;

use Mailjet\Client;
use Mailjet\Resources;

class Mail
{
    public function send($to_email, $to_name, $subject, $template, $vars = null)
    {
        // Récupération du chemin complet vers le fichier
        $filePath = __DIR__ . '/../Mail/' . $template;

        // Vérification de l'existence du fichier
        if (!file_exists($filePath)) {
            throw new \Exception("Le fichier de template n'existe pas : " . $filePath);
        }

        // Lecture du contenu du template
        $content = file_get_contents($filePath);

        // Remplacement des variables (si présentes)
        if ($vars) {
            foreach ($vars as $key => $var) {
                $content = str_replace('{'.$key.'}', $var, $content);
            }
        }

        // Initialisation du client Mailjet
        $mj = new Client($_ENV['MJ_APIKEY_PUBLIC'], $_ENV['MJ_APIKEY_PRIVATE'], true, ['version' => 'v3.1']);

        // Configuration du corps de la requête
        $body = [
            'Messages' => [
                [
                    'From' => [
                        'Email' => "ghanous.fetouma2018@gmail.com",
                        'Name'  => "La boutique Française"
                    ],
                    'To' => [
                        [
                            'Email' => $to_email,
                            'Name'  => $to_name
                        ]
                    ],
                    'TemplateID'      => 6723688,
                    'TemplateLanguage' => true,
                    'Subject'         => $subject,
                    'Variables'       => [
                        'content' => $content
                    ],
                ]
            ]
        ];

        // Envoi de l'e-mail
        $mj->post(Resources::$Email, ['body' => $body]);
    }
}
