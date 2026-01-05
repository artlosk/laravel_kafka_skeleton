<?php

namespace App\Listeners;

use JeroenNoten\LaravelAdminLte\Events\BuildingMenu;

class AddMenuItems
{
    public function handle(BuildingMenu $event)
    {
        $menu = app('adminlte.menu');
        $event->menu->add(...$menu);
    }
}
