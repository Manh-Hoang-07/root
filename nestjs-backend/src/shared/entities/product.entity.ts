import { Entity, Column } from 'typeorm';
import { BaseEntity } from '../../common/base/base.entity';

export enum ProductStatus {
  ACTIVE = 'active',
  INACTIVE = 'inactive',
  OUT_OF_STOCK = 'out_of_stock',
  DISCONTINUED = 'discontinued',
}

export enum ProductType {
  PHYSICAL = 'physical',
  DIGITAL = 'digital',
  SERVICE = 'service',
  SUBSCRIPTION = 'subscription',
}

@Entity('products')
export class Product extends BaseEntity {
  @Column({
    type: 'varchar',
    length: 255,
  })
  name: string;

  @Column({
    type: 'varchar',
    length: 255,
    unique: true,
  })
  slug: string;

  @Column({
    type: 'varchar',
    length: 100,
    unique: true,
    nullable: true,
  })
  sku?: string;

  @Column({
    type: 'text',
    nullable: true,
  })
  shortDescription?: string;

  @Column({
    type: 'longtext',
    nullable: true,
  })
  description?: string;

  @Column({
    type: 'enum',
    enum: ProductType,
    default: ProductType.PHYSICAL,
  })
  type: ProductType;

  @Column({
    type: 'enum',
    enum: ProductStatus,
    default: ProductStatus.ACTIVE,
  })
  status: ProductStatus;

  @Column({
    type: 'decimal',
    precision: 10,
    scale: 2,
    default: 0,
  })
  price: number;

  @Column({
    type: 'decimal',
    precision: 10,
    scale: 2,
    nullable: true,
  })
  comparePrice?: number; // Original price for sale displays

  @Column({
    type: 'decimal',
    precision: 10,
    scale: 2,
    nullable: true,
  })
  costPrice?: number; // Cost for profit calculations

  @Column({
    type: 'varchar',
    length: 3,
    default: 'USD',
  })
  currency: string;

  @Column({
    type: 'int',
    default: 0,
  })
  stock: number;

  @Column({
    type: 'int',
    nullable: true,
  })
  lowStockThreshold?: number;

  @Column({
    type: 'boolean',
    default: true,
  })
  trackStock: boolean;

  @Column({
    type: 'boolean',
    default: false,
  })
  allowBackorder: boolean;

  @Column({
    type: 'decimal',
    precision: 8,
    scale: 2,
    nullable: true,
  })
  weight?: number;

  @Column({
    type: 'varchar',
    length: 10,
    default: 'kg',
  })
  weightUnit: string;

  @Column({
    type: 'json',
    nullable: true,
  })
  dimensions?: {
    length: number;
    width: number;
    height: number;
    unit: string;
  };

  @Column({
    type: 'text',
    nullable: true,
  })
  featuredImage?: string;

  @Column({
    type: 'json',
    nullable: true,
  })
  images?: string[];

  @Column({
    type: 'json',
    nullable: true,
  })
  gallery?: {
    url: string;
    alt?: string;
    title?: string;
  }[];

  @Column({
    type: 'json',
    nullable: true,
  })
  categories?: string[];

  @Column({
    type: 'json',
    nullable: true,
  })
  tags?: string[];

  @Column({
    type: 'json',
    nullable: true,
  })
  attributes?: {
    name: string;
    value: string;
    visible: boolean;
  }[];

  @Column({
    type: 'json',
    nullable: true,
  })
  variants?: {
    id: string;
    name: string;
    price: number;
    stock: number;
    sku?: string;
    attributes: { [key: string]: string };
  }[];

  @Column({
    type: 'text',
    nullable: true,
  })
  metaTitle?: string;

  @Column({
    type: 'text',
    nullable: true,
  })
  metaDescription?: string;

  @Column({
    type: 'json',
    nullable: true,
  })
  metaKeywords?: string[];

  @Column({
    type: 'boolean',
    default: false,
  })
  isFeatured: boolean;

  @Column({
    type: 'boolean',
    default: false,
  })
  isDigital: boolean;

  @Column({
    type: 'boolean',
    default: true,
  })
  requiresShipping: boolean;

  @Column({
    type: 'boolean',
    default: false,
  })
  taxable: boolean;

  @Column({
    type: 'decimal',
    precision: 5,
    scale: 2,
    nullable: true,
  })
  taxRate?: number;

  @Column({
    type: 'int',
    default: 0,
  })
  viewCount: number;

  @Column({
    type: 'int',
    default: 0,
  })
  salesCount: number;

  @Column({
    type: 'decimal',
    precision: 3,
    scale: 2,
    default: 0,
  })
  rating: number;

  @Column({
    type: 'int',
    default: 0,
  })
  reviewCount: number;

  @Column({
    type: 'int',
    default: 0,
  })
  sortOrder: number;

  @Column({
    type: 'varchar',
    length: 255,
    nullable: true,
  })
  brand?: string;

  @Column({
    type: 'varchar',
    length: 255,
    nullable: true,
  })
  manufacturer?: string;

  @Column({
    type: 'varchar',
    length: 100,
    nullable: true,
  })
  condition?: string;

  @Column({
    type: 'json',
    nullable: true,
  })
  shippingOptions?: {
    method: string;
    cost: number;
    estimatedDays: number;
  }[];

  // Virtual properties
  get isAvailable(): boolean {
    return this.status === ProductStatus.ACTIVE && 
           (this.stock > 0 || this.allowBackorder || !this.trackStock);
  }

  get isOnSale(): boolean {
    return !!(this.comparePrice && this.comparePrice > this.price);
  }

  get discountAmount(): number {
    if (!this.isOnSale) return 0;
    return this.comparePrice - this.price;
  }

  get discountPercentage(): number {
    if (!this.isOnSale) return 0;
    return Math.round(((this.comparePrice - this.price) / this.comparePrice) * 100);
  }

  get isLowStock(): boolean {
    if (!this.trackStock || !this.lowStockThreshold) return false;
    return this.stock <= this.lowStockThreshold;
  }

  get isOutOfStock(): boolean {
    return this.trackStock && this.stock <= 0 && !this.allowBackorder;
  }

  get displayPrice(): number {
    return this.price;
  }

  get formattedPrice(): string {
    return `${this.currency} ${this.price.toFixed(2)}`;
  }

  get formattedComparePrice(): string | null {
    return this.comparePrice ? `${this.currency} ${this.comparePrice.toFixed(2)}` : null;
  }

  get averageRating(): number {
    return this.rating;
  }

  // Helper methods
  incrementViewCount(): void {
    this.viewCount += 1;
  }

  incrementSalesCount(quantity: number = 1): void {
    this.salesCount += quantity;
  }

  reduceStock(quantity: number): boolean {
    if (!this.trackStock) return true;
    
    if (this.stock >= quantity) {
      this.stock -= quantity;
      return true;
    } else if (this.allowBackorder) {
      this.stock -= quantity; // Can go negative
      return true;
    }
    
    return false;
  }

  increaseStock(quantity: number): void {
    if (this.trackStock) {
      this.stock += quantity;
    }
  }

  updateRating(newRating: number, reviewCount: number): void {
    // Simple average calculation
    const totalRating = (this.rating * this.reviewCount) + newRating;
    this.reviewCount = reviewCount;
    this.rating = totalRating / this.reviewCount;
  }

  hasCategory(category: string): boolean {
    return this.categories?.includes(category) || false;
  }

  hasTag(tag: string): boolean {
    return this.tags?.includes(tag) || false;
  }

  addCategory(category: string): void {
    if (!this.categories) {
      this.categories = [];
    }
    if (!this.categories.includes(category)) {
      this.categories.push(category);
    }
  }

  removeCategory(category: string): void {
    if (this.categories) {
      this.categories = this.categories.filter(c => c !== category);
    }
  }

  addTag(tag: string): void {
    if (!this.tags) {
      this.tags = [];
    }
    if (!this.tags.includes(tag)) {
      this.tags.push(tag);
    }
  }

  removeTag(tag: string): void {
    if (this.tags) {
      this.tags = this.tags.filter(t => t !== tag);
    }
  }

  activate(): void {
    this.status = ProductStatus.ACTIVE;
  }

  deactivate(): void {
    this.status = ProductStatus.INACTIVE;
  }

  markOutOfStock(): void {
    this.status = ProductStatus.OUT_OF_STOCK;
  }

  discontinue(): void {
    this.status = ProductStatus.DISCONTINUED;
  }
}
