<?php

namespace App\Helpers;

use App\Repositories\SystemConfigV2\SystemConfigV2Repository;
use App\Models\SystemConfigV2;

class ConfigV2Helper
{
    protected static $repository;

    public static function getRepository()
    {
        if (!self::$repository) {
            self::$repository = new SystemConfigV2Repository(new SystemConfigV2());
        }
        return self::$repository;
    }

    public static function get($key, $default = null)
    {
        return self::getRepository()->getByKey($key, $default);
    }

    public static function getGeneralConfig()
    {
        return self::getRepository()->getByGroup('general');
    }

    public static function getEmailConfig()
    {
        return self::getRepository()->getByGroup('email');
    }

    // General config helpers - Thông tin website cơ bản
    public static function getAppName($default = 'Laravel App')
    {
        return self::get('app_name', $default);
    }

    public static function getSiteName($default = 'Website Demo')
    {
        return self::get('site_name', $default);
    }

    public static function getSiteLogo($default = '/images/logo.png')
    {
        return self::get('site_logo', $default);
    }

    public static function getSiteFavicon($default = '/images/favicon.ico')
    {
        return self::get('site_favicon', $default);
    }

    public static function getSiteEmail($default = 'admin@demo.com')
    {
        return self::get('site_email', $default);
    }

    public static function getSitePhone($default = '0123456789')
    {
        return self::get('site_phone', $default);
    }

    public static function getSiteAddress($default = 'Địa chỉ website')
    {
        return self::get('site_address', $default);
    }

    public static function getSiteDescription($default = 'Mô tả website')
    {
        return self::get('site_description', $default);
    }

    public static function getTimezone($default = 'Asia/Ho_Chi_Minh')
    {
        return self::get('timezone', $default);
    }

    public static function getLanguage($default = 'vi')
    {
        return self::get('language', $default);
    }

    public static function getMaintenanceMode($default = false)
    {
        return self::get('maintenance_mode', $default);
    }

    public static function getItemsPerPage($default = 20)
    {
        return self::get('items_per_page', $default);
    }

    // Email config helpers
    public static function getSmtpHost($default = 'localhost')
    {
        return self::get('smtp_host', $default);
    }

    public static function getSmtpPort($default = 587)
    {
        return self::get('smtp_port', $default);
    }

    public static function getSmtpUsername($default = '')
    {
        return self::get('smtp_username', $default);
    }

    public static function getSmtpPassword($default = '')
    {
        return self::get('smtp_password', $default);
    }

    public static function getSmtpEncryption($default = 'tls')
    {
        return self::get('smtp_encryption', $default);
    }

    public static function getFromEmail($default = 'noreply@example.com')
    {
        return self::get('from_email', $default);
    }

    public static function getFromName($default = 'Laravel App')
    {
        return self::get('from_name', $default);
    }
}
