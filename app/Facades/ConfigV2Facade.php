<?php

namespace App\Facades;

use Illuminate\Support\Facades\Facade;
use App\Helpers\ConfigV2Helper;

class ConfigV2Facade extends Facade
{
    protected static function getFacadeAccessor()
    {
        return ConfigV2Helper::class;
    }

    public static function get($key, $default = null)
    {
        return ConfigV2Helper::get($key, $default);
    }

    public static function getGeneralConfig()
    {
        return ConfigV2Helper::getGeneralConfig();
    }

    public static function getEmailConfig()
    {
        return ConfigV2Helper::getEmailConfig();
    }

    // General config helpers - Thông tin website cơ bản
    public static function getAppName($default = 'Laravel App')
    {
        return ConfigV2Helper::getAppName($default);
    }

    public static function getSiteName($default = 'Website Demo')
    {
        return ConfigV2Helper::getSiteName($default);
    }

    public static function getSiteLogo($default = '/images/logo.png')
    {
        return ConfigV2Helper::getSiteLogo($default);
    }

    public static function getSiteFavicon($default = '/images/favicon.ico')
    {
        return ConfigV2Helper::getSiteFavicon($default);
    }

    public static function getSiteEmail($default = 'admin@demo.com')
    {
        return ConfigV2Helper::getSiteEmail($default);
    }

    public static function getSitePhone($default = '0123456789')
    {
        return ConfigV2Helper::getSitePhone($default);
    }

    public static function getSiteAddress($default = 'Địa chỉ website')
    {
        return ConfigV2Helper::getSiteAddress($default);
    }

    public static function getSiteDescription($default = 'Mô tả website')
    {
        return ConfigV2Helper::getSiteDescription($default);
    }

    public static function getTimezone($default = 'Asia/Ho_Chi_Minh')
    {
        return ConfigV2Helper::getTimezone($default);
    }

    public static function getLanguage($default = 'vi')
    {
        return ConfigV2Helper::getLanguage($default);
    }

    public static function getMaintenanceMode($default = false)
    {
        return ConfigV2Helper::getMaintenanceMode($default);
    }

    public static function getItemsPerPage($default = 20)
    {
        return ConfigV2Helper::getItemsPerPage($default);
    }

    // Email config helpers
    public static function getSmtpHost($default = 'localhost')
    {
        return ConfigV2Helper::getSmtpHost($default);
    }

    public static function getSmtpPort($default = 587)
    {
        return ConfigV2Helper::getSmtpPort($default);
    }

    public static function getSmtpUsername($default = '')
    {
        return ConfigV2Helper::getSmtpUsername($default);
    }

    public static function getSmtpPassword($default = '')
    {
        return ConfigV2Helper::getSmtpPassword($default);
    }

    public static function getSmtpEncryption($default = 'tls')
    {
        return ConfigV2Helper::getSmtpEncryption($default);
    }

    public static function getFromEmail($default = 'noreply@example.com')
    {
        return ConfigV2Helper::getFromEmail($default);
    }

    public static function getFromName($default = 'Laravel App')
    {
        return ConfigV2Helper::getFromName($default);
    }
}
