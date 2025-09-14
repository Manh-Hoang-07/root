<?php

namespace App\Http\Controllers\Api\Core\Config;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\Core\SystemConfigV2\SystemConfigV2Service;
use App\Traits\ResponseTrait;
use App\Traits\LoggingTrait;
use Illuminate\Http\JsonResponse;
use Exception;

class SystemConfigV2Controller extends Controller
{
    use ResponseTrait;
    use LoggingTrait;

    protected SystemConfigV2Service $configService;

    public function __construct(SystemConfigV2Service $configService)
    {
        $this->configService = $configService;
    }

    /**
     * Lấy cấu hình chung
     */
    public function getGeneralConfig(): JsonResponse
    {
        try {
            $data = $this->configService->getGeneralConfig();
            return $this->apiResponse(true, $data, 'Lấy cấu hình chung thành công');
        } catch (Exception $e) {
            $this->logError('GetGeneralConfig', $e);
            return $this->apiResponse(false, null, 'Không thể lấy cấu hình chung', 500);
        }
    }

    /**
     * Lấy cấu hình email
     */
    public function getEmailConfig(): JsonResponse
    {
        try {
            $data = $this->configService->getEmailConfig();
            return $this->apiResponse(true, $data, 'Lấy cấu hình email thành công');
        } catch (Exception $e) {
            $this->logError('GetEmailConfig', $e);
            return $this->apiResponse(false, null, 'Không thể lấy cấu hình email', 500);
        }
    }

    /**
     * Lấy cấu hình theo key
     */
    public function getByKey(Request $request): JsonResponse
    {
        try {
            $key = $request->get('key');
            $default = $request->get('default');
            
            if (!$key) {
                return $this->apiResponse(false, null, 'Key là bắt buộc', 400);
            }
            
            $data = $this->configService->getByKey($key, $default);
            return $this->apiResponse(true, $data, 'Lấy cấu hình theo key thành công');
        } catch (Exception $e) {
            $this->logError('GetByKey', $e, ['key' => $request->get('key')]);
            return $this->apiResponse(false, null, 'Không thể lấy cấu hình theo key', 500);
        }
    }

    /**
     * Cập nhật cấu hình chung
     */
    public function updateGeneralConfig(Request $request): JsonResponse
    {
        try {
            $data = $request->all();
            $result = $this->configService->updateGeneralConfig($data);
            return $this->apiResponse(true, $result, 'Cập nhật cấu hình chung thành công');
        } catch (Exception $e) {
            $this->logError('UpdateGeneralConfig', $e);
            return $this->apiResponse(false, null, 'Không thể cập nhật cấu hình chung', 500);
        }
    }

    /**
     * Cập nhật cấu hình email
     */
    public function updateEmailConfig(Request $request): JsonResponse
    {
        try {
            $data = $request->all();
            $result = $this->configService->updateEmailConfig($data);
            return $this->apiResponse(true, $result, 'Cập nhật cấu hình email thành công');
        } catch (Exception $e) {
            $this->logError('UpdateEmailConfig', $e);
            return $this->apiResponse(false, null, 'Không thể cập nhật cấu hình email', 500);
        }
    }

    /**
     * Cập nhật cấu hình theo key
     */
    public function updateByKey(Request $request): JsonResponse
    {
        try {
            $key = $request->get('key');
            $value = $request->get('value');
            
            if (!$key) {
                return $this->apiResponse(false, null, 'Key là bắt buộc', 400);
            }
            
            $result = $this->configService->updateByKey($key, $value);
            return $this->apiResponse(true, $result, 'Cập nhật cấu hình theo key thành công');
        } catch (Exception $e) {
            $this->logError('UpdateByKey', $e, ['key' => $request->get('key')]);
            return $this->apiResponse(false, null, 'Không thể cập nhật cấu hình theo key', 500);
        }
    }
}
