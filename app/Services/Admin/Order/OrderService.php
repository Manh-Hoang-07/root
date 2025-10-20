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
        try {
            $result = $this->repo->updatePaymentStatus($id, $status);
            
            if ($result) {
                return [
                    'success' => true,
                    'message' => 'Cập nhật trạng thái thanh toán thành công',
                    'data' => $result
                ];
            } else {
                return [
                    'success' => false,
                    'message' => 'Không tìm thấy đơn hàng',
                    'data' => null
                ];
            }
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Không thể cập nhật trạng thái thanh toán',
                'data' => null
            ];
        }
    }

    /**
     * Update shipping status
     */
    public function updateShippingStatus($id, string $status, ?string $trackingNumber = null): array
    {
        try {
            $result = $this->repo->updateShippingStatus($id, $status, $trackingNumber);
            
            if ($result) {
                return [
                    'success' => true,
                    'message' => 'Cập nhật trạng thái vận chuyển thành công',
                    'data' => $result
                ];
            } else {
                return [
                    'success' => false,
                    'message' => 'Không tìm thấy đơn hàng',
                    'data' => null
                ];
            }
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Không thể cập nhật trạng thái vận chuyển',
                'data' => null
            ];
        }
    }

    public function addItem($orderId, array $itemData): array
    {
        try {
            $result = DB::transaction(function () use ($orderId, $itemData) {
                $order = $this->find($orderId);
                if (!$order) return null;
                $this->repo->addItem($orderId, $itemData);
                return $this->recalculateTotals($orderId);
            });
            
            if ($result) {
                return [
                    'success' => true,
                    'message' => 'Thêm sản phẩm vào đơn hàng thành công',
                    'data' => $result
                ];
            } else {
                return [
                    'success' => false,
                    'message' => 'Không tìm thấy đơn hàng',
                    'data' => null
                ];
            }
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Không thể thêm sản phẩm vào đơn hàng',
                'data' => null
            ];
        }
    }

    public function updateItem($orderId, $itemId, array $data): array
    {
        try {
            $result = DB::transaction(function () use ($orderId, $itemId, $data) {
                $order = $this->find($orderId);
                if (!$order) return null;
                $ok = $this->repo->updateItem($orderId, $itemId, $data);
                if (!$ok) return null;
                return $this->recalculateTotals($orderId);
            });
            
            if ($result) {
                return [
                    'success' => true,
                    'message' => 'Cập nhật sản phẩm trong đơn hàng thành công',
                    'data' => $result
                ];
            } else {
                return [
                    'success' => false,
                    'message' => 'Không tìm thấy dữ liệu',
                    'data' => null
                ];
            }
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Không thể cập nhật sản phẩm trong đơn hàng',
                'data' => null
            ];
        }
    }

    public function removeItem($orderId, $itemId): array
    {
        try {
            $result = DB::transaction(function () use ($orderId, $itemId) {
                $order = $this->find($orderId);
                if (!$order) return null;
                $ok = $this->repo->removeItem($orderId, $itemId);
                if (!$ok) return null;
                return $this->recalculateTotals($orderId);
            });
            
            if ($result) {
                return [
                    'success' => true,
                    'message' => 'Xóa sản phẩm khỏi đơn hàng thành công',
                    'data' => $result
                ];
            } else {
                return [
                    'success' => false,
                    'message' => 'Không tìm thấy dữ liệu',
                    'data' => null
                ];
            }
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Không thể xóa sản phẩm khỏi đơn hàng',
                'data' => null
            ];
        }
    }

    public function recalculateTotals($orderId): array
    {
        try {
            $result = DB::transaction(function () use ($orderId) {
                $order = $this->repo->recalculateTotals($orderId);
                return $order;
            });
            
            if ($result) {
                return [
                    'success' => true,
                    'message' => 'Tính lại tổng tiền thành công',
                    'data' => $result
                ];
            } else {
                return [
                    'success' => false,
                    'message' => 'Không tìm thấy đơn hàng',
                    'data' => null
                ];
            }
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Không thể tính lại tổng tiền',
                'data' => null
            ];
        }
    }

    public function confirmOrder($orderId, ?string $note = null): array
    {
        try {
            $result = DB::transaction(function () use ($orderId, $note) {
                // Placeholder: stock reservations can be handled in repo in future
                $updated = $this->repo->confirmOrder($orderId, $note);
                return $updated;
            });
            
            if ($result) {
                return [
                    'success' => true,
                    'message' => 'Xác nhận đơn hàng thành công',
                    'data' => $result
                ];
            } else {
                return [
                    'success' => false,
                    'message' => 'Không tìm thấy đơn hàng',
                    'data' => null
                ];
            }
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Không thể xác nhận đơn hàng',
                'data' => null
            ];
        }
    }

    public function cancelOrder($orderId, string $reason): array
    {
        try {
            $result = DB::transaction(function () use ($orderId, $reason) {
                // Placeholder: restock logic can be handled in repo in future
                $updated = $this->repo->cancelOrder($orderId, $reason);
                return $updated;
            });
            
            if ($result) {
                return [
                    'success' => true,
                    'message' => 'Hủy đơn hàng thành công',
                    'data' => $result
                ];
            } else {
                return [
                    'success' => false,
                    'message' => 'Không tìm thấy đơn hàng',
                    'data' => null
                ];
            }
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Không thể hủy đơn hàng',
                'data' => null
            ];
        }
    }

    public function bulkUpdateStatus(array $ids, string $status): array
    {
        try {
            $count = $this->repo->bulkUpdateStatus($ids, $status);
            
            return [
                'success' => true,
                'message' => 'Cập nhật trạng thái hàng loạt thành công',
                'data' => ['updated' => $count]
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Không thể cập nhật trạng thái hàng loạt',
                'data' => null
            ];
        }
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
