import {
  Entity,
  Column,
  ManyToOne,
  OneToMany,
  ManyToMany,
  JoinColumn,
  JoinTable,
  Index,
} from 'typeorm';
import { BaseEntity } from '../../common/base/entities/base.entity';
import { Post } from './post.entity';
import { PostTag } from './post-tag.entity';

@Entity('postcategory')
@Index('idx_name', ['name'])
@Index('idx_slug', ['slug'])
@Index('idx_parent_id', ['parent_id'])
@Index('idx_status', ['status'])
@Index('idx_sort_order', ['sort_order'])
@Index('idx_created_at', ['createdAt'])
@Index('idx_status_sort_order', ['status', 'sort_order'])
@Index('idx_parent_status', ['parent_id', 'status'])
export class PostCategory extends BaseEntity {

  @Column({ type: 'varchar', length: 255 })
  name: string;

  @Column({ type: 'varchar', length: 255, unique: true })
  slug: string;

  @Column({ type: 'text', nullable: true })
  description?: string | null;

  @Column({ type: 'bigint', unsigned: true, nullable: true })
  parent_id?: number | null;

  @ManyToOne(() => PostCategory, (category) => category.children, {
    nullable: true,
    onDelete: 'SET NULL',
  })
  @JoinColumn({ name: 'parent_id' })
  parent?: PostCategory | null;

  @OneToMany(() => PostCategory, (category) => category.parent)
  children?: PostCategory[];

  @Column({ type: 'varchar', length: 255, nullable: true })
  image?: string | null;

  @Column({
    type: 'enum',
    enum: ['active', 'inactive'],
    default: 'active',
  })
  status: 'active' | 'inactive';

  @Column({ type: 'varchar', length: 255, nullable: true })
  meta_title?: string | null;

  @Column({ type: 'text', nullable: true })
  meta_description?: string | null;

  @Column({ type: 'varchar', length: 255, nullable: true })
  canonical_url?: string | null;

  @Column({ type: 'varchar', length: 255, nullable: true })
  og_image?: string | null;

  @Column({ type: 'int', default: 0 })
  sort_order: number;

  @ManyToMany(() => Post, (post) => post.categories)
  posts?: Post[];
}

