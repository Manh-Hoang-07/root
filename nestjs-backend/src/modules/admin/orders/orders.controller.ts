import { Controller, UseGuards } from '@nestjs/common';
import { OrdersService } from '../services/orders.service';
import { JwtAuthGuard } from '../../../common/guards/jwt-auth.guard';
import { RolesGuard } from '../../../common/guards/roles.guard';
import { Roles } from '../../../common/decorators/roles.decorator';
import { BaseController } from '../../../common/base/base.controller';

@Controller('admin/orders')
@UseGuards(JwtAuthGuard, RolesGuard)
@Roles('admin')
export class OrdersController extends BaseController<any> {
  protected service = this.ordersService;
  
  constructor(private readonly ordersService: OrdersService) {
    super();
  }

  // Override để customize entity name
  protected getEntityName(): string {
    return 'Order';
  }

  // Override để customize method names
  protected getListMethodName(): string {
    return 'getOrders';
  }

  protected getGetMethodName(): string {
    return 'getOrder';
  }

  // Orders không có create và delete, chỉ có update
  protected getCreateMethodName(): string {
    return null; // Disable create
  }

  protected getUpdateMethodName(): string {
    return 'updateOrder';
  }

  protected getDeleteMethodName(): string {
    return null; // Disable delete
  }
}
