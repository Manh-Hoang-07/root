import {
  PrimaryGeneratedColumn,
  CreateDateColumn,
  UpdateDateColumn,
  DeleteDateColumn,
  Column,
  BeforeInsert,
  BeforeUpdate,
} from 'typeorm';

/**
 * Base Entity với các trường chung cho tất cả entities
 * Bao gồm: id, timestamps, audit fields, soft delete
 */
export abstract class BaseEntity {
  @PrimaryGeneratedColumn({ unsigned: true })
  id: number;

  @CreateDateColumn({ name: 'created_at' })
  createdAt: Date;

  @UpdateDateColumn({ name: 'updated_at' })
  updatedAt: Date;

  @DeleteDateColumn({ name: 'deleted_at' })
  deletedAt?: Date;

  @Column({ name: 'created_user_id', type: 'bigint', unsigned: true, nullable: true })
  createdBy?: number;

  @Column({ name: 'updated_user_id', type: 'bigint', unsigned: true, nullable: true })
  updatedBy?: number;

  @Column({ name: 'deleted_by', nullable: true, select: false })
  deletedBy?: string;

  /**
   * Soft delete entity
   */
  async softDelete(deletedBy?: string): Promise<void> {
    this.deletedAt = new Date();
    this.deletedBy = deletedBy;
  }

  /**
   * Restore soft deleted entity
   */
  async restore(): Promise<void> {
    this.deletedAt = undefined;
    this.deletedBy = undefined;
  }

  /**
   * Check if entity is soft deleted
   */
  isDeleted(): boolean {
    return !!this.deletedAt;
  }

  /**
   * Convert entity to JSON (exclude sensitive fields)
   */
  toJSON(): Record<string, any> {
    const { deletedAt, deletedBy, ...rest } = this;
    return rest;
  }

  /**
   * Get entity info for logging
   */
  getEntityInfo(): { id: number; type: string; createdAt: Date } {
    return {
      id: this.id,
      type: this.constructor.name,
      createdAt: this.createdAt,
    };
  }

  @BeforeInsert()
  beforeInsert(): void {
    // Override trong entity con nếu cần
  }

  @BeforeUpdate()
  beforeUpdate(): void {
    // Override trong entity con nếu cần
  }
}

