import { Controller, Get, Param } from '@nestjs/common';
import { EnumHelperService } from '../../shared/services/enum-helper.service';

@Controller('public/enums')
export class EnumController {
  constructor(private readonly enumHelperService: EnumHelperService) {}

  @Get('types')
  getTypes() {
    return this.enumHelperService.getEnumTypes();
  }

  @Get('userStatus')
  getUserStatus() {
    return this.enumHelperService.getUserStatus();
  }

  @Get('productStatus')
  getProductStatus() {
    return this.enumHelperService.getProductStatus();
  }

  @Get('orderStatus')
  getOrderStatus() {
    return this.enumHelperService.getOrderStatus();
  }

  @Get('paymentStatus')
  getPaymentStatus() {
    return this.enumHelperService.getPaymentStatus();
  }

  @Get('shippingStatus')
  getShippingStatus() {
    return this.enumHelperService.getShippingStatus();
  }

  @Get('postStatus')
  getPostStatus() {
    return this.enumHelperService.getPostStatus();
  }

  @Get('contactStatus')
  getContactStatus() {
    return this.enumHelperService.getContactStatus();
  }

  @Get('roleStatus')
  getRoleStatus() {
    return this.enumHelperService.getRoleStatus();
  }

  @Get('gender')
  getGender() {
    return this.enumHelperService.getGender();
  }

  @Get('attributeType')
  getAttributeType() {
    return this.enumHelperService.getAttributeType();
  }

  @Get('configAction')
  getConfigAction() {
    return this.enumHelperService.getConfigAction();
  }

  @Get('configGroup')
  getConfigGroup() {
    return this.enumHelperService.getConfigGroup();
  }

  @Get('configType')
  getConfigType() {
    return this.enumHelperService.getConfigType();
  }

  // Generic endpoint to get any enum by type
  @Get(':type')
  getEnumByType(@Param('type') type: string) {
    return this.enumHelperService.getEnumByType(type);
  }
}
