import { Controller } from '@nestjs/common';
import { CartService } from './cart.service';
import { AddCartDto } from './dtos/add-cart.dto';
import { BaseController } from '../../../common/base/base.controller';

@Controller('public/carts')
export class CartController extends BaseController<any> {
  protected service = this.cartService;
  
  constructor(private readonly cartService: CartService) {
    super();
  }

  // Override để customize entity name
  protected getEntityName(): string {
    return 'Cart';
  }

  // Override để customize method names
  protected getListMethodName(): string {
    return 'getCarts';
  }

  protected getGetMethodName(): string {
    return 'getCart';
  }

  protected getCreateMethodName(): string {
    return 'createCart';
  }

  protected getUpdateMethodName(): string {
    return 'updateCart';
  }

  protected getDeleteMethodName(): string {
    return 'deleteCart';
  }

  // Custom create method với success message
  @Post()
  async create(@Body() createCartDto: AddCartDto) {
    const result = await this.cartService.createCart(createCartDto);
    return this.handleResponse(result, undefined, 'Cart created successfully');
  }

  // Custom update method với success message
  @Put(':id')
  async update(@Param('id') id: string, @Body() updateCartDto: AddCartDto) {
    const result = await this.cartService.updateCart(id, updateCartDto);
    return this.handleResponse(result, 'Cart not found', 'Cart updated successfully');
  }

  // Custom delete method với success message
  @Delete(':id')
  async delete(@Param('id') id: string) {
    const result = await this.cartService.deleteCart(id);
    return this.handleResponse(result, 'Cart not found', 'Cart deleted successfully');
  }
}
