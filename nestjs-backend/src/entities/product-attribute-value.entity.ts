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
import { ProductAttribute } from './product-attribute.entity';

@Entity('product_attribute_values')
export class ProductAttributeValue {
  @PrimaryGeneratedColumn()
  id: number;

  @Column({ nullable: true })
  attribute_id: number;

  @Column({ length: 255 })
  value: string;

  @Column({ length: 255, nullable: true })
  color: string;

  @Column({ type: 'int', default: 0 })
  sort_order: number;

  @CreateDateColumn()
  created_at: Date;

  @UpdateDateColumn()
  updated_at: Date;

  @DeleteDateColumn()
  deleted_at: Date;

  // Relations
  @ManyToOne(() => ProductAttribute, (attribute) => attribute.values)
  @JoinColumn({ name: 'attribute_id' })
  attribute: ProductAttribute;
}
