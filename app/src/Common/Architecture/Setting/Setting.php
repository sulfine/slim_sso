<?php

declare(strict_types=1);

namespace App\Common\Architecture\Setting;

class Setting
{
    public static function getRoot()
    {
        return dirname(__DIR__, 4);
    }
}
