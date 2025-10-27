import { Controller, Get, Param } from '@nestjs/common';

@Controller('public/enums')
export class EnumController {
  @Get('types')
  getTypes() {
    return {
      types: [
        'userStatus',
        'productStatus',
        'orderStatus',
        'paymentStatus',
        'shippingStatus',
        'postStatus',
        'contactStatus',
        'roleStatus',
        'gender',
        'attributeType',
        'configGroup',
        'configType',
      ],
    };
  }

  @Get('userStatus')
  getUserStatus() {
    return [
      { id: 'active', name: 'Hoạt động' },
      { id: 'pending', name: 'Chờ xác nhận' },
      { id: 'inactive', name: 'Đã khóa' },
    ];
  }

  @Get('productStatus')
  getProductStatus() {
    return [
      { id: 'active', name: 'Hoạt động' },
      { id: 'inactive', name: 'Không hoạt động' },
      { id: 'draft', name: 'Bản nháp' },
    ];
  }

  @Get('orderStatus')
  getOrderStatus() {
    return [
      { id: 'pending', name: 'Chờ xử lý' },
      { id: 'confirmed', name: 'Đã xác nhận' },
      { id: 'processing', name: 'Đang xử lý' },
      { id: 'shipped', name: 'Đã giao hàng' },
      { id: 'delivered', name: 'Đã nhận hàng' },
      { id: 'cancelled', name: 'Đã hủy' },
    ];
  }

  @Get('paymentStatus')
  getPaymentStatus() {
    return [
      { id: 'pending', name: 'Chờ thanh toán' },
      { id: 'paid', name: 'Đã thanh toán' },
      { id: 'failed', name: 'Thanh toán thất bại' },
      { id: 'refunded', name: 'Đã hoàn tiền' },
      { id: 'partially_refunded', name: 'Hoàn tiền một phần' },
    ];
  }

  @Get('shippingStatus')
  getShippingStatus() {
    return [
      { id: 'pending', name: 'Chờ vận chuyển' },
      { id: 'preparing', name: 'Đang chuẩn bị' },
      { id: 'shipped', name: 'Đã giao hàng' },
      { id: 'delivered', name: 'Đã nhận hàng' },
      { id: 'returned', name: 'Đã trả hàng' },
    ];
  }

  @Get('postStatus')
  getPostStatus() {
    return [
      { id: 'draft', name: 'Nháp' },
      { id: 'scheduled', name: 'Lên lịch' },
      { id: 'published', name: 'Đã xuất bản' },
      { id: 'archived', name: 'Đã lưu trữ' },
    ];
  }

  @Get('contactStatus')
  getContactStatus() {
    return [
      { id: 'pending', name: 'Chờ xử lý' },
      { id: 'in_progress', name: 'Đang xử lý' },
      { id: 'completed', name: 'Hoàn thành' },
      { id: 'cancelled', name: 'Đã hủy' },
    ];
  }

  @Get('roleStatus')
  getRoleStatus() {
    return [
      { id: 'active', name: 'Hoạt động' },
      { id: 'inactive', name: 'Không hoạt động' },
    ];
  }

  @Get('gender')
  getGender() {
    return [
      { id: 'male', name: 'Nam' },
      { id: 'female', name: 'Nữ' },
      { id: 'other', name: 'Khác' },
    ];
  }
}
