<?php

namespace App\Services\Admin\Order;

use App\Services\BaseService;
use App\Repositories\Order\OrderRepository;
use Illuminate\Support\Facades\DB;

class OrderService extends BaseService
{
    /**
     * @var OrderRepository
     */
    protected $repo;

    public function __construct(OrderRepository $repo)
    {
        parent::__construct($repo);
    }


    /**
     * Update payment status
     */
    public function updatePaymentStatus($id, string $status): array
    {
        $result = [
            'success' => false,
            'message' => 'Không thể cập nhật trạng thái thanh toán',
            'data' => null
        ];
        
        try {
            $updateResult = $this->repo->updatePaymentStatus($id, $status);
            
            if ($updateResult) {
                $result = [
                    'success' => true,
                    'message' => 'Cập nhật trạng thái thanh toán thành công',
                    'data' => $updateResult
                ];
            } else {
                $result['message'] = 'Không tìm thấy đơn hàng';
            }
        } catch (\Exception $e) {
            // Exception already handled by default result
        }
        
        return $result;
    }

    /**
     * Update shipping status
     */
    public function updateShippingStatus($id, string $status, ?string $trackingNumber = null): array
    {
        $result = [
            'success' => false,
            'message' => 'Không thể cập nhật trạng thái vận chuyển',
            'data' => null
        ];
        
        try {
            $updateResult = $this->repo->updateShippingStatus($id, $status, $trackingNumber);
            
            if ($updateResult) {
                $result = [
                    'success' => true,
                    'message' => 'Cập nhật trạng thái vận chuyển thành công',
                    'data' => $updateResult
                ];
            } else {
                $result['message'] = 'Không tìm thấy đơn hàng';
            }
        } catch (\Exception $e) {
            // Exception already handled by default result
        }
        
        return $result;
    }

    public function addItem($orderId, array $itemData): array
    {
        $result = [
            'success' => false,
            'message' => 'Không thể thêm sản phẩm vào đơn hàng',
            'data' => null
        ];
        
        try {
            $transactionResult = DB::transaction(function () use ($orderId, $itemData) {
                $order = $this->find($orderId);
                if (!$order) return null;
                $this->repo->addItem($orderId, $itemData);
                return $this->recalculateTotals($orderId);
            });
            
            if ($transactionResult) {
                $result = [
                    'success' => true,
                    'message' => 'Thêm sản phẩm vào đơn hàng thành công',
                    'data' => $transactionResult
                ];
            } else {
                $result['message'] = 'Không tìm thấy đơn hàng';
            }
        } catch (\Exception $e) {
            // Exception already handled by default result
        }
        
        return $result;
    }

    public function updateItem($orderId, $itemId, array $data): array
    {
        $result = [
            'success' => false,
            'message' => 'Không thể cập nhật sản phẩm trong đơn hàng',
            'data' => null
        ];
        
        try {
            $transactionResult = DB::transaction(function () use ($orderId, $itemId, $data) {
                $order = $this->find($orderId);
                if (!$order) return null;
                $ok = $this->repo->updateItem($orderId, $itemId, $data);
                if (!$ok) return null;
                return $this->recalculateTotals($orderId);
            });
            
            if ($transactionResult) {
                $result = [
                    'success' => true,
                    'message' => 'Cập nhật sản phẩm trong đơn hàng thành công',
                    'data' => $transactionResult
                ];
            } else {
                $result['message'] = 'Không tìm thấy dữ liệu';
            }
        } catch (\Exception $e) {
            // Exception already handled by default result
        }
        
        return $result;
    }

    public function removeItem($orderId, $itemId): array
    {
        $result = [
            'success' => false,
            'message' => 'Không thể xóa sản phẩm khỏi đơn hàng',
            'data' => null
        ];
        
        try {
            $transactionResult = DB::transaction(function () use ($orderId, $itemId) {
                $order = $this->find($orderId);
                if (!$order) return null;
                $ok = $this->repo->removeItem($orderId, $itemId);
                if (!$ok) return null;
                return $this->recalculateTotals($orderId);
            });
            
            if ($transactionResult) {
                $result = [
                    'success' => true,
                    'message' => 'Xóa sản phẩm khỏi đơn hàng thành công',
                    'data' => $transactionResult
                ];
            } else {
                $result['message'] = 'Không tìm thấy dữ liệu';
            }
        } catch (\Exception $e) {
            // Exception already handled by default result
        }
        
        return $result;
    }

    public function recalculateTotals($orderId): array
    {
        $result = [
            'success' => false,
            'message' => 'Không thể tính lại tổng tiền',
            'data' => null
        ];
        
        try {
            $transactionResult = DB::transaction(function () use ($orderId) {
                $order = $this->repo->recalculateTotals($orderId);
                return $order;
            });
            
            if ($transactionResult) {
                $result = [
                    'success' => true,
                    'message' => 'Tính lại tổng tiền thành công',
                    'data' => $transactionResult
                ];
            } else {
                $result['message'] = 'Không tìm thấy đơn hàng';
            }
        } catch (\Exception $e) {
            // Exception already handled by default result
        }
        
        return $result;
    }

    public function confirmOrder($orderId, ?string $note = null): array
    {
        $result = [
            'success' => false,
            'message' => 'Không thể xác nhận đơn hàng',
            'data' => null
        ];
        
        try {
            $transactionResult = DB::transaction(function () use ($orderId, $note) {
                // Placeholder: stock reservations can be handled in repo in future
                $updated = $this->repo->confirmOrder($orderId, $note);
                return $updated;
            });
            
            if ($transactionResult) {
                $result = [
                    'success' => true,
                    'message' => 'Xác nhận đơn hàng thành công',
                    'data' => $transactionResult
                ];
            } else {
                $result['message'] = 'Không tìm thấy đơn hàng';
            }
        } catch (\Exception $e) {
            // Exception already handled by default result
        }
        
        return $result;
    }

    public function cancelOrder($orderId, string $reason): array
    {
        $result = [
            'success' => false,
            'message' => 'Không thể hủy đơn hàng',
            'data' => null
        ];
        
        try {
            $transactionResult = DB::transaction(function () use ($orderId, $reason) {
                // Placeholder: restock logic can be handled in repo in future
                $updated = $this->repo->cancelOrder($orderId, $reason);
                return $updated;
            });
            
            if ($transactionResult) {
                $result = [
                    'success' => true,
                    'message' => 'Hủy đơn hàng thành công',
                    'data' => $transactionResult
                ];
            } else {
                $result['message'] = 'Không tìm thấy đơn hàng';
            }
        } catch (\Exception $e) {
            // Exception already handled by default result
        }
        
        return $result;
    }

    public function bulkUpdateStatus(array $ids, string $status): array
    {
        $result = [
            'success' => false,
            'message' => 'Không thể cập nhật trạng thái hàng loạt',
            'data' => null
        ];
        
        try {
            $count = $this->repo->bulkUpdateStatus($ids, $status);
            
            $result = [
                'success' => true,
                'message' => 'Cập nhật trạng thái hàng loạt thành công',
                'data' => ['updated' => $count]
            ];
        } catch (\Exception $e) {
            // Exception already handled by default result
        }
        
        return $result;
    }

    protected function onCreateSuccess(array $result, array $data): void
    {
        DB::transaction(function () use ($result, $data) {
            $orderId = $result['id'];

            // Ensure order number exists
            if (empty($result['order_number'])) {
                $orderNumber = $this->generateOrderNumber($orderId);
                $this->repo->update($orderId, ['order_number' => $orderNumber]);
            }

            // Create items if provided
            if (!empty($data['items']) && is_array($data['items'])) {
                foreach ($data['items'] as $item) {
                    // Minimal required fields validated by request
                    $this->repo->addItem($orderId, $item);
                }
            }

            // Recalculate totals
            $this->repo->recalculateTotals($orderId);
        });
    }

    protected function onUpdateSuccess(array $result, $id, array $data): void
    {
        DB::transaction(function () use ($id, $data) {
            // If monetary fields changed, recalc total
            $monetaryKeys = ['shipping_amount', 'tax_amount', 'discount_amount'];
            foreach ($monetaryKeys as $key) {
                if (array_key_exists($key, $data)) {
                    $this->repo->recalculateTotals((int) $id);
                    break;
                }
            }
        });
    }

    private function generateOrderNumber(int $orderId): string
    {
        $datePart = date('Ymd');
        $seq = str_pad((string) ($orderId % 10000), 4, '0', STR_PAD_LEFT);
        return 'ORD-' . $datePart . '-' . $seq;
    }
}
