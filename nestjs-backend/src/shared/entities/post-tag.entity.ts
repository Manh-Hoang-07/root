import {
  Entity,
  Column,
  ManyToMany,
  Index,
} from 'typeorm';
import { BaseEntity } from '../../common/base/entities/base.entity';
import { Post } from './post.entity';

@Entity('posttag')
@Index('idx_name', ['name'])
@Index('idx_slug', ['slug'])
@Index('idx_status', ['status'])
@Index('idx_created_at', ['createdAt'])
@Index('idx_status_created_at', ['status', 'createdAt'])
export class PostTag extends BaseEntity {

  @Column({ type: 'varchar', length: 255 })
  name: string;

  @Column({ type: 'varchar', length: 255, unique: true })
  slug: string;

  @Column({ type: 'text', nullable: true })
  description?: string | null;

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

  @ManyToMany(() => Post, (post) => post.tags)
  posts?: Post[];
}

