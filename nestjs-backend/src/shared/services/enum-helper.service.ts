import { Injectable } from '@nestjs/common';
import {
  UserStatus,
  ProductStatus,
  OrderStatus,
  PaymentStatus,
  ShippingStatus,
  PostStatus,
  ContactStatus,
  RoleStatus,
  BasicStatus,
  Gender,
  AttributeType,
  ConfigAction,
  ConfigGroup,
  ConfigType,
  getUserStatusLabel,
  getBasicStatusLabel,
  getOrderStatusLabel,
  getOrderStatusColor,
} from '../enums';

@Injectable()
export class EnumHelperService {
  // Get all enum types
  getEnumTypes() {
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
        'configAction',
      ],
    };
  }

  // Generic method to convert enum to array
  private enumToArray<T extends Record<string, string>>(
    enumObject: T,
    labelFunction?: (value: string) => string,
  ) {
    return Object.values(enumObject).map((value) => ({
      id: value,
      name: labelFunction ? labelFunction(value) : value,
    }));
  }

  // User Status
  getUserStatus() {
    return this.enumToArray(UserStatus, getUserStatusLabel);
  }

  // Product Status
  getProductStatus() {
    return this.enumToArray(ProductStatus, (status) => {
      const labels: Record<string, string> = {
        [ProductStatus.ACTIVE]: 'Hoạt động',
        [ProductStatus.INACTIVE]: 'Không hoạt động',
        [ProductStatus.DRAFT]: 'Bản nháp',
      };
      return labels[status] || status;
    });
  }

  // Order Status
  getOrderStatus() {
    return this.enumToArray(OrderStatus, getOrderStatusLabel);
  }

  // Payment Status
  getPaymentStatus() {
    return this.enumToArray(PaymentStatus, (status) => {
      const labels: Record<string, string> = {
        [PaymentStatus.PENDING]: 'Chờ thanh toán',
        [PaymentStatus.PAID]: 'Đã thanh toán',
        [PaymentStatus.FAILED]: 'Thanh toán thất bại',
        [PaymentStatus.REFUNDED]: 'Đã hoàn tiền',
        [PaymentStatus.PARTIALLY_REFUNDED]: 'Hoàn tiền một phần',
      };
      return labels[status] || status;
    });
  }

  // Shipping Status
  getShippingStatus() {
    return this.enumToArray(ShippingStatus, (status) => {
      const labels: Record<string, string> = {
        [ShippingStatus.PENDING]: 'Chờ vận chuyển',
        [ShippingStatus.PREPARING]: 'Đang chuẩn bị',
        [ShippingStatus.SHIPPED]: 'Đã giao hàng',
        [ShippingStatus.DELIVERED]: 'Đã nhận hàng',
        [ShippingStatus.RETURNED]: 'Đã trả hàng',
      };
      return labels[status] || status;
    });
  }

  // Post Status
  getPostStatus() {
    return this.enumToArray(PostStatus, (status) => {
      const labels: Record<string, string> = {
        [PostStatus.DRAFT]: 'Nháp',
        [PostStatus.SCHEDULED]: 'Lên lịch',
        [PostStatus.PUBLISHED]: 'Đã xuất bản',
        [PostStatus.ARCHIVED]: 'Đã lưu trữ',
      };
      return labels[status] || status;
    });
  }

  // Contact Status
  getContactStatus() {
    return this.enumToArray(ContactStatus, (status) => {
      const labels: Record<string, string> = {
        [ContactStatus.PENDING]: 'Chờ xử lý',
        [ContactStatus.IN_PROGRESS]: 'Đang xử lý',
        [ContactStatus.COMPLETED]: 'Hoàn thành',
        [ContactStatus.CANCELLED]: 'Đã hủy',
      };
      return labels[status] || status;
    });
  }

  // Role Status
  getRoleStatus() {
    return this.enumToArray(RoleStatus, getBasicStatusLabel);
  }

  // Gender
  getGender() {
    return this.enumToArray(Gender, (gender) => {
      const labels: Record<string, string> = {
        [Gender.MALE]: 'Nam',
        [Gender.FEMALE]: 'Nữ',
        [Gender.OTHER]: 'Khác',
      };
      return labels[gender] || gender;
    });
  }

  // Attribute Type
  getAttributeType() {
    return this.enumToArray(AttributeType, (type) => {
      const labels: Record<string, string> = {
        [AttributeType.TEXT]: 'Văn bản',
        [AttributeType.NUMBER]: 'Số',
        [AttributeType.BOOLEAN]: 'Boolean',
        [AttributeType.DATE]: 'Ngày tháng',
        [AttributeType.SELECT]: 'Lựa chọn',
        [AttributeType.MULTI_SELECT]: 'Lựa chọn nhiều',
      };
      return labels[type] || type;
    });
  }

  // Config Action
  getConfigAction() {
    return this.enumToArray(ConfigAction, (action) => {
      const labels: Record<string, string> = {
        [ConfigAction.CREATE]: 'Tạo mới',
        [ConfigAction.UPDATE]: 'Cập nhật',
        [ConfigAction.DELETE]: 'Xóa',
        [ConfigAction.VIEW]: 'Xem',
        [ConfigAction.EXPORT]: 'Xuất',
        [ConfigAction.IMPORT]: 'Nhập',
      };
      return labels[action] || action;
    });
  }

  // Config Group
  getConfigGroup() {
    return this.enumToArray(ConfigGroup, (group) => {
      const labels: Record<string, string> = {
        [ConfigGroup.SYSTEM]: 'Hệ thống',
        [ConfigGroup.EMAIL]: 'Email',
        [ConfigGroup.SMS]: 'SMS',
        [ConfigGroup.PAYMENT]: 'Thanh toán',
        [ConfigGroup.SHIPPING]: 'Vận chuyển',
        [ConfigGroup.SOCIAL]: 'Mạng xã hội',
      };
      return labels[group] || group;
    });
  }

  // Config Type
  getConfigType() {
    return this.enumToArray(ConfigType, (type) => {
      const labels: Record<string, string> = {
        [ConfigType.STRING]: 'Chuỗi',
        [ConfigType.NUMBER]: 'Số',
        [ConfigType.BOOLEAN]: 'Boolean',
        [ConfigType.JSON]: 'JSON',
        [ConfigType.ARRAY]: 'Mảng',
        [ConfigType.OBJECT]: 'Đối tượng',
      };
      return labels[type] || type;
    });
  }

  // Get specific enum by type
  getEnumByType(type: string) {
    const enumMap: Record<string, () => any[]> = {
      userStatus: () => this.getUserStatus(),
      productStatus: () => this.getProductStatus(),
      orderStatus: () => this.getOrderStatus(),
      paymentStatus: () => this.getPaymentStatus(),
      shippingStatus: () => this.getShippingStatus(),
      postStatus: () => this.getPostStatus(),
      contactStatus: () => this.getContactStatus(),
      roleStatus: () => this.getRoleStatus(),
      gender: () => this.getGender(),
      attributeType: () => this.getAttributeType(),
      configAction: () => this.getConfigAction(),
      configGroup: () => this.getConfigGroup(),
      configType: () => this.getConfigType(),
    };

    const getter = enumMap[type];
    return getter ? getter() : [];
  }

  // Get label for specific enum value
  getLabel(type: string, value: string): string {
    const enumData = this.getEnumByType(type);
    const item = enumData.find(item => item.id === value);
    return item ? item.name : value;
  }

  // Get color for specific enum value (if available)
  getColor(type: string, value: string): string {
    if (type === 'orderStatus') {
      return getOrderStatusColor(value as OrderStatus);
    }
    // Add more color mappings as needed
    return 'default';
  }
}
