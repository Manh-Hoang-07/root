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
  @PrimaryGeneratedColumn('uuid')
  id: string;

  @CreateDateColumn()
  createdAt: Date;

  @UpdateDateColumn()
  updatedAt: Date;

  @DeleteDateColumn()
  deletedAt?: Date;

  @Column({ nullable: true })
  createdBy?: string;

  @Column({ nullable: true })
  updatedBy?: string;

  @Column({ nullable: true })
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
  getEntityInfo(): { id: string; type: string; createdAt: Date } {
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

