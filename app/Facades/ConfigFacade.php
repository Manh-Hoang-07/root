<?php

namespace App\Facades;

use Illuminate\Support\Facades\Facade;
use App\Helpers\ConfigHelper;

/**
 * @method static mixed get(string $key, $default = null)
 * @method static array getGroup(string $group)
 * @method static array getPublicConfigs()
 * @method static array getMultiple(array $keys, $default = null)
 * @method static array getMultipleGroups(array $groups)
 * @method static void clearCache(string $key)
 * @method static void clearGroupCache(string $group)
 * @method static void clearAllCache()
 */
class ConfigFacade extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'app-config';
    }

    public static function getFacadeRoot()
    {
        return new ConfigHelper();
    }
}
