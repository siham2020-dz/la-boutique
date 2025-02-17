<?php

namespace App\Classe;


class State 
{
    public const STATE = [
        '3' => [
            'labet' => 'En cours de préparation',
            'email_subject' => 'Commande en cours de préparation',
            'email_template' => 'order_state_3.html',
        ],
        '4' => [
            'labet' => 'Expédié"',
            'email_subject' => 'Commande expediée',
            'email_template' => 'order_state_4.html',
        ],
        '5' => [
            'labet' => 'Annulée',
            'email_subject' => 'Commande annulée',
            'email_template' => 'order_state_5.html',
        ],
    ];
}