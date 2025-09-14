<?php

namespace App\Services\Admin\SystemConfig;

use App\Repositories\SystemConfigV2\SystemConfigV2Repository;
use App\Services\BaseService;
use App\Helpers\ApiResponse;

class SystemConfigV2Service extends BaseService
{
    protected $repository;

    public function __construct(SystemConfigV2Repository $repository)
    {
        $this->repository = $repository;
    }

    public function getByKey($key, $default = null)
    {
        try {
            $value = $this->repository->getByKey($key, $default);
            return ApiResponse::success($value, 'Lấy cấu hình thành công');
        } catch (\Exception $e) {
            return ApiResponse::error('Lỗi khi lấy cấu hình: ' . $e->getMessage());
        }
    }

    public function getGeneralConfig()
    {
        try {
            $configs = $this->repository->getByGroup('general');
            return ApiResponse::success($configs, 'Lấy cấu hình hệ thống thành công');
        } catch (\Exception $e) {
            return ApiResponse::error('Lỗi khi lấy cấu hình hệ thống: ' . $e->getMessage());
        }
    }

    public function getEmailConfig()
    {
        try {
            $configs = $this->repository->getByGroup('email');
            return ApiResponse::success($configs, 'Lấy cấu hình email thành công');
        } catch (\Exception $e) {
            return ApiResponse::error('Lỗi khi lấy cấu hình email: ' . $e->getMessage());
        }
    }

    public function updateGeneralConfig($data)
    {
        try {
            $result = $this->repository->updateGroupConfig('general', $data);
            return ApiResponse::success($result, 'Cập nhật cấu hình hệ thống thành công');
        } catch (\Exception $e) {
            return ApiResponse::error('Lỗi khi cập nhật cấu hình hệ thống: ' . $e->getMessage());
        }
    }

    public function updateEmailConfig($data)
    {
        try {
            $result = $this->repository->updateGroupConfig('email', $data);
            return ApiResponse::success($result, 'Cập nhật cấu hình email thành công');
        } catch (\Exception $e) {
            return ApiResponse::error('Lỗi khi cập nhật cấu hình email: ' . $e->getMessage());
        }
    }

    public function updateByKey($key, $value)
    {
        try {
            $result = $this->repository->updateByKey($key, $value);
            return ApiResponse::success($result, 'Cập nhật cấu hình thành công');
        } catch (\Exception $e) {
            return ApiResponse::error('Lỗi khi cập nhật cấu hình: ' . $e->getMessage());
        }
    }
}
