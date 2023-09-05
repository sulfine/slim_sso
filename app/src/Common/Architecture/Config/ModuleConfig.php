<?php

declare(strict_types=1);

namespace App\Common\Architecture\Config;

use App\Common\Architecture\Setting\Setting;

class ModuleConfig
{
    public static function getModules(): array
    {
        $modules = [];

        foreach (scandir(Setting::getRoot() . '/src') as $module) {
            if (is_dir(Setting::getRoot() . '/src/' . $module) && $module != '.' && $module != '..') {
                $modules[] = $module;
            }
        }
        return $modules;
    }

    public static function getEntitiesModules(): array
    {
        $entitiesModule = [];
        foreach (self::getModules() as $module) {
            if (is_dir(Setting::getRoot() . '/src/' . $module . '/Entity')) {
                $entitiesModule[] = Setting::getRoot() . '/src/' . $module . '/Entity';
            }
        }
        return $entitiesModule;
    }
}
