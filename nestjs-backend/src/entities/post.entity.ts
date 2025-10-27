import {
  Entity,
  PrimaryGeneratedColumn,
  Column,
  CreateDateColumn,
  UpdateDateColumn,
  DeleteDateColumn,
  ManyToOne,
  JoinColumn,
  ManyToMany,
  JoinTable,
} from 'typeorm';
import { PostStatus } from '../enums/post-status.enum';
import { User } from './user.entity';
import { PostCategory } from './post-category.entity';
import { PostTag } from './post-tag.entity';

@Entity('posts')
export class Post {
  @PrimaryGeneratedColumn()
  id: number;

  @Column({ length: 255 })
  name: string;

  @Column({ length: 255, unique: true })
  slug: string;

  @Column('text', { nullable: true })
  excerpt: string;

  @Column('longtext', { nullable: true })
  content: string;

  @Column({ length: 255, nullable: true })
  image: string;

  @Column({ length: 255, nullable: true })
  cover_image: string;

  @Column({ nullable: true })
  primary_postcategory_id: number;

  @Column({
    type: 'enum',
    enum: PostStatus,
    default: PostStatus.DRAFT,
  })
  status: PostStatus;

  @Column({ type: 'boolean', default: false })
  is_featured: boolean;

  @Column({ type: 'boolean', default: false })
  is_pinned: boolean;

  @Column({ type: 'datetime', nullable: true })
  published_at: Date;

  @Column({ type: 'int', default: 0 })
  view_count: number;

  @Column({ length: 255, nullable: true })
  meta_title: string;

  @Column('text', { nullable: true })
  meta_description: string;

  @Column({ length: 255, nullable: true })
  canonical_url: string;

  @Column({ length: 255, nullable: true })
  og_title: string;

  @Column('text', { nullable: true })
  og_description: string;

  @Column({ length: 255, nullable: true })
  og_image: string;

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
  @ManyToOne(() => User, { nullable: true })
  @JoinColumn({ name: 'created_user_id' })
  createdUser: User;

  @ManyToOne(() => User, { nullable: true })
  @JoinColumn({ name: 'updated_user_id' })
  updatedUser: User;

  @ManyToMany(() => PostCategory, (category) => category.posts)
  @JoinTable({
    name: 'post_postcategory',
    joinColumn: { name: 'post_id', referencedColumnName: 'id' },
    inverseJoinColumn: { name: 'postcategory_id', referencedColumnName: 'id' },
  })
  categories: PostCategory[];

  @ManyToMany(() => PostTag, (tag) => tag.posts)
  @JoinTable({
    name: 'post_posttag',
    joinColumn: { name: 'post_id', referencedColumnName: 'id' },
    inverseJoinColumn: { name: 'posttag_id', referencedColumnName: 'id' },
  })
  tags: PostTag[];
}
