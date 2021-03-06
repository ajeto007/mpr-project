<?php

namespace App;

use Nette;
use Nette\Application\Routers\RouteList;
use Nette\Application\Routers\Route;


class RouterFactory
{
    /**
     * @return Nette\Application\IRouter
     */
    public static function createRouter()
    {
        $router = new RouteList;

        $router[] = new Route('prihlasit', 'Sign:in');

        $router[] = new Route('zapomenute-heslo', 'Sign:forgottenPassword');
        
        $router[] = new Route('odhlasit', 'Sign:out');

        $router[] = new Route('<presenter>/<action>[/<id>]', array(
            'presenter' => array(
                Route::VALUE => 'Homepage',
                Route::FILTER_TABLE => array(
                    'zamestnanci' => 'Employees',
                    'projekty' => 'Projects',
                    'rizika' => 'Risks',
                    'klienti' => 'Clients'
                )
            ),
            'action' => array(
                Route::VALUE => 'default',
                Route::FILTER_TABLE => array(
                    'pridat' => 'add',
                    'upravit' => 'edit',
                    'vytvorit' => 'create',
                    'detail' => 'detail',
                    'kategorie' => 'categories',
                    'matice' => 'matrix',
                    'profil' => 'profile',
                    'zmena-hesla' => 'changePassword'
                )
            ),
            'id' => null,
        ));

        return $router;
    }

}
