<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\SystemConfigV2\SystemConfigV2Service;

class SystemConfigV2Controller extends Controller
{
    protected $configService;

    public function __construct(SystemConfigV2Service $configService)
    {
        $this->configService = $configService;
    }

    public function getGeneralConfig()
    {
        return $this->configService->getGeneralConfig();
    }

    public function getEmailConfig()
    {
        return $this->configService->getEmailConfig();
    }

    public function getByKey(Request $request)
    {
        $key = $request->get('key');
        $default = $request->get('default');
        
        if (!$key) {
            return response()->json([
                'success' => false,
                'message' => 'Key là bắt buộc'
            ], 400);
        }
        
        return $this->configService->getByKey($key, $default);
    }

    public function updateGeneralConfig(Request $request)
    {
        $data = $request->all();
        return $this->configService->updateGeneralConfig($data);
    }

    public function updateEmailConfig(Request $request)
    {
        $data = $request->all();
        return $this->configService->updateEmailConfig($data);
    }

    public function updateByKey(Request $request)
    {
        $key = $request->get('key');
        $value = $request->get('value');
        
        if (!$key) {
            return response()->json([
                'success' => false,
                'message' => 'Key là bắt buộc'
            ], 400);
        }
        
        return $this->configService->updateByKey($key, $value);
    }
}
