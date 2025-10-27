import {
  Entity,
  PrimaryGeneratedColumn,
  Column,
  CreateDateColumn,
  UpdateDateColumn,
  DeleteDateColumn,
  ManyToOne,
  JoinColumn,
} from 'typeorm';
import { ProductVariant } from './product-variant.entity';
import { ProductAttribute } from './product-attribute.entity';
import { ProductAttributeValue } from './product-attribute-value.entity';

@Entity('product_variant_attributes')
export class ProductVariantAttribute {
  @PrimaryGeneratedColumn()
  id: number;

  @Column({ nullable: true })
  variant_id: number;

  @Column({ nullable: true })
  attribute_id: number;

  @Column({ nullable: true })
  value_id: number;

  @Column('text', { nullable: true })
  value: string;

  @CreateDateColumn()
  created_at: Date;

  @UpdateDateColumn()
  updated_at: Date;

  @DeleteDateColumn()
  deleted_at: Date;

  // Relations
  @ManyToOne(() => ProductVariant, (variant) => variant.attributes)
  @JoinColumn({ name: 'variant_id' })
  variant: ProductVariant;

  @ManyToOne(() => ProductAttribute)
  @JoinColumn({ name: 'attribute_id' })
  attribute: ProductAttribute;

  @ManyToOne(() => ProductAttributeValue)
  @JoinColumn({ name: 'value_id' })
  attributeValue: ProductAttributeValue;
}
