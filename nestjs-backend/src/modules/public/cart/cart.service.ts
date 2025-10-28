import { Injectable } from '@nestjs/common';
import { InjectRepository } from '@nestjs/typeorm';
import { Repository, FindOptionsWhere, FindManyOptions } from 'typeorm';
import { Cart } from '../../../entities/cart.entity';

@Injectable()
export class CartService {
  constructor(
    @InjectRepository(Cart)
    private readonly cartRepository: Repository<Cart>,
  ) {}

  // Generic list method
  private async list<T>(
    repository: Repository<T>,
    perPage: number = 20,
    page: number = 1,
  ) {
    const validPerPage = Math.min(perPage, 100);
    const skip = (page - 1) * validPerPage;

    const findOptions: FindManyOptions<T> = {
      order: { created_at: 'DESC' } as any,
      skip,
      take: validPerPage,
    };

    const [data, total] = await repository.findAndCount(findOptions);

    return {
      data,
      meta: {
        total,
        per_page: validPerPage,
        current_page: page,
        last_page: Math.ceil(total / validPerPage),
        from: skip + 1,
        to: Math.min(skip + validPerPage, total),
      },
    };
  }

  // Carts
  async getCarts(perPage: number = 20, page: number = 1) {
    return this.list(this.cartRepository, perPage, page);
  }

  async getCart(id: string) {
    return this.cartRepository.findOne({
      where: { id: Number(id) } as any,
    });
  }

  async createCart(createCartDto: any) {
    const cart = this.cartRepository.create(createCartDto as any);
    return this.cartRepository.save(cart);
  }

  async updateCart(id: string, updateCartDto: any) {
    const existingCart = await this.cartRepository.findOne({
      where: { id: Number(id) } as any,
    });

    if (!existingCart) {
      return null;
    }

    const updatedCart = this.cartRepository.merge(existingCart, updateCartDto as any);
    return this.cartRepository.save(updatedCart);
  }

  async deleteCart(id: string) {
    const existingCart = await this.cartRepository.findOne({
      where: { id: Number(id) } as any,
    });

    if (!existingCart) {
      return null;
    }

    return this.cartRepository.remove(existingCart);
  }
}

