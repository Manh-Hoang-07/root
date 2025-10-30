import {
  Entity,
  Column,
  ManyToOne,
  ManyToMany,
  JoinColumn,
  JoinTable,
  Index,
  PrimaryGeneratedColumn,
  CreateDateColumn,
  UpdateDateColumn,
  DeleteDateColumn,
} from 'typeorm';
import { PostCategory } from './post-category.entity';
import { PostTag } from './post-tag.entity';

@Entity('posts')
@Index('idx_name', ['name'])
@Index('idx_slug', ['slug'])
@Index('idx_primary_postcategory_id', ['primary_postcategory_id'])
@Index('idx_status', ['status'])
@Index('idx_is_featured', ['is_featured'])
@Index('idx_is_pinned', ['is_pinned'])
@Index('idx_published_at', ['published_at'])
@Index('idx_view_count', ['view_count'])
@Index('idx_created_at', ['createdAt'])
@Index('idx_status_published_at', ['status', 'published_at'])
@Index('idx_is_featured_status', ['is_featured', 'status'])
@Index('idx_primary_category_status', ['primary_postcategory_id', 'status'])
export class Post {

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

  @Column({ type: 'varchar', length: 255 })
  name: string;

  @Column({ type: 'varchar', length: 255, unique: true })
  slug: string;

  @Column({ type: 'text', nullable: true })
  excerpt?: string | null;

  @Column({ type: 'longtext' })
  content: string;

  @Column({ type: 'varchar', length: 255, nullable: true })
  image?: string | null;

  @Column({ type: 'varchar', length: 255, nullable: true })
  cover_image?: string | null;

  @Column({ type: 'bigint', unsigned: true, nullable: true })
  primary_postcategory_id?: number | null;

  @ManyToOne(() => PostCategory, {
    nullable: true,
    onDelete: 'SET NULL',
  })
  @JoinColumn({ name: 'primary_postcategory_id' })
  primary_category?: PostCategory | null;

  @Column({
    type: 'enum',
    enum: ['draft', 'scheduled', 'published', 'archived'],
    default: 'draft',
  })
  status: 'draft' | 'scheduled' | 'published' | 'archived';

  @Column({ type: 'boolean', default: false })
  is_featured: boolean;

  @Column({ type: 'boolean', default: false })
  is_pinned: boolean;

  @Column({ type: 'datetime', nullable: true })
  published_at?: Date | null;

  @Column({ type: 'bigint', unsigned: true, default: 0 })
  view_count: number;

  @Column({ type: 'varchar', length: 255, nullable: true })
  meta_title?: string | null;

  @Column({ type: 'text', nullable: true })
  meta_description?: string | null;

  @Column({ type: 'varchar', length: 255, nullable: true })
  canonical_url?: string | null;

  @Column({ type: 'varchar', length: 255, nullable: true })
  og_title?: string | null;

  @Column({ type: 'text', nullable: true })
  og_description?: string | null;

  @Column({ type: 'varchar', length: 255, nullable: true })
  og_image?: string | null;

  @ManyToMany(() => PostCategory, (category) => category.posts)
  @JoinTable({
    name: 'post_postcategory',
    joinColumn: { name: 'post_id', referencedColumnName: 'id' },
    inverseJoinColumn: { name: 'postcategory_id', referencedColumnName: 'id' },
  })
  categories?: PostCategory[];

  @ManyToMany(() => PostTag, (tag) => tag.posts)
  @JoinTable({
    name: 'post_posttag',
    joinColumn: { name: 'post_id', referencedColumnName: 'id' },
    inverseJoinColumn: { name: 'posttag_id', referencedColumnName: 'id' },
  })
  tags?: PostTag[];
}

