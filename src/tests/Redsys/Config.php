<?php

namespace Ferotres\RedsysBundle\Tests\Redsys;

/**
 * Class Config
 * @package Ferotres\RedsysBundle\Tests\Redsys
 */
class Config
{
    /**
     * @return array
     */
    public static function getConfig() :array
    {
        return [
            'url' => 'https://sis-t.redsys.es:25443/sis/realizarPago',
            'shops' => [
                'test' => [
                    'merchant_name' => 'AppTest',
                    'merchant_code' => '123456',
                    'success' => 'success',
                    'error' => 'error',
                    'terminals'  => [
                        ['secret' => 'sq7HjrUOBfKmC576ILgskD5srU870gJ7', 'ces' => true,  'num' => 1, 'iso_currency' =>  'EUR'],
                        ['secret' => 'sq7HjrUOBfKmC576ILgskD5srU870gJ7', 'ces' => false, 'num' => 2, 'iso_currency' =>  'EUR']
                    ]
                ]
            ]
        ];
    }
}