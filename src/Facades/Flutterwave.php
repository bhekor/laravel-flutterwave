<?php

/*
 * This file is part of the Laravel Flutterwave package.
 *
 * (c) Ibidapo Adeolu - Bhekor <adeoluibidapo@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Bhekor\LaravelFlutterwave\Facades;

use Illuminate\Support\Facades\Facade;

class Flutterwave extends Facade
{
    /**
     * Get the registered name of the component
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'laravelflutterwave';
    }
}