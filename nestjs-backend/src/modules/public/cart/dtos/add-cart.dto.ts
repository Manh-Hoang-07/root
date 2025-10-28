import { IsNumber, IsOptional, Min } from 'class-validator';

export class AddCartDto {
  @IsNumber()
  @IsOptional()
  user_id?: number;

  @IsNumber()
  product_variant_id: number;

  @IsNumber()
  @Min(1)
  quantity: number;

  @IsNumber()
  @IsOptional()
  price?: number;
}
