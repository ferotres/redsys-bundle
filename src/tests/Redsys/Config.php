<?php

/*
 * This file is part of the FerotresRedsysBundle package.
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Ferotres\RedsysBundle\Tests\Redsys;

/**
 * Class Config.
 */
class Config
{
    /**
     * @return array
     */
    public static function getConfig(): array
    {
        return [
            'url' => 'https://sis-t.redsys.es:25443/sis/realizarPago',
            'shops' => [
                'test' => [
                    'merchant_name' => 'AppTest',
                    'merchant_code' => '123456',
                    'success' => 'success',
                    'error' => 'error',
                    'terminals' => [
                        ['secret' => 'sq7HprUOBfKmn576ILgskD5srU870gt8', 'ces' => true,  'num' => 1, 'iso_currency' => 'EUR'],
                        ['secret' => 'sq7HprUOBfKmn576ILgskD5srU870gt8', 'ces' => false, 'num' => 2, 'iso_currency' => 'EUR'],
                    ],
                ],
            ],
        ];
    }
}
