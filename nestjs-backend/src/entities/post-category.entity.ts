import {
  Entity,
  PrimaryGeneratedColumn,
  Column,
  CreateDateColumn,
  UpdateDateColumn,
  DeleteDateColumn,
  ManyToMany,
} from 'typeorm';
import { Post } from './post.entity';

@Entity('postcategory')
export class PostCategory {
  @PrimaryGeneratedColumn()
  id: number;

  @Column({ length: 255 })
  name: string;

  @Column({ length: 255, unique: true })
  slug: string;

  @Column('text', { nullable: true })
  description: string;

  @Column({ length: 255, nullable: true })
  image: string;

  @Column({ type: 'varchar', length: 50, nullable: true })
  status: string;

  @Column({ length: 255, nullable: true })
  meta_title: string;

  @Column('text', { nullable: true })
  meta_description: string;

  @Column({ length: 255, nullable: true })
  canonical_url: string;

  @Column({ nullable: true })
  created_user_id: number;

  @Column({ nullable: true })
  updated_user_id: number;

  @CreateDateColumn()
  created_at: Date;

  @UpdateDateColumn()
  updated_at: Date;

  @DeleteDateColumn()
  deleted_at: Date;

  // Relations
  @ManyToMany(() => Post, (post) => post.categories)
  posts: Post[];
}
