import { Entity, Column, ManyToOne, JoinColumn } from 'typeorm';
import { BaseEntity } from '../../common/base/base.entity';
import { User } from './user.entity';

export enum PostStatus {
  DRAFT = 'draft',
  PUBLISHED = 'published',
  ARCHIVED = 'archived',
  DELETED = 'deleted',
}

export enum PostType {
  ARTICLE = 'article',
  NEWS = 'news',
  TUTORIAL = 'tutorial',
  ANNOUNCEMENT = 'announcement',
}

@Entity('posts')
export class Post extends BaseEntity {
  @Column({
    type: 'varchar',
    length: 255,
  })
  title: string;

  @Column({
    type: 'varchar',
    length: 255,
    unique: true,
  })
  slug: string;

  @Column({
    type: 'text',
    nullable: true,
  })
  excerpt?: string;

  @Column({
    type: 'longtext',
  })
  content: string;

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
    type: 'enum',
    enum: PostStatus,
    default: PostStatus.DRAFT,
  })
  status: PostStatus;

  @Column({
    type: 'enum',
    enum: PostType,
    default: PostType.ARTICLE,
  })
  type: PostType;

  @Column({
    type: 'json',
    nullable: true,
  })
  tags?: string[];

  @Column({
    type: 'json',
    nullable: true,
  })
  categories?: string[];

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
    default: true,
  })
  allowComments: boolean;

  @Column({
    type: 'boolean',
    default: false,
  })
  isFeatured: boolean;

  @Column({
    type: 'boolean',
    default: false,
  })
  isSticky: boolean;

  @Column({
    type: 'int',
    default: 0,
  })
  viewCount: number;

  @Column({
    type: 'int',
    default: 0,
  })
  likeCount: number;

  @Column({
    type: 'int',
    default: 0,
  })
  shareCount: number;

  @Column({
    type: 'int',
    default: 0,
  })
  commentCount: number;

  @Column({
    type: 'timestamp',
    nullable: true,
  })
  publishedAt?: Date;

  @Column({
    type: 'timestamp',
    nullable: true,
  })
  scheduledAt?: Date;

  @Column({
    type: 'varchar',
    length: 5,
    default: 'en',
  })
  language: string;

  @Column({
    type: 'int',
    default: 0,
  })
  sortOrder: number;

  // Relationships
  @ManyToOne(() => User, (user) => user.posts, {
    onDelete: 'CASCADE',
  })
  @JoinColumn({ name: 'author_id' })
  author: User;

  @Column({
    type: 'uuid',
  })
  authorId: string;

  // Virtual properties
  get isPublished(): boolean {
    return this.status === PostStatus.PUBLISHED && 
           this.publishedAt && 
           this.publishedAt <= new Date();
  }

  get isDraft(): boolean {
    return this.status === PostStatus.DRAFT;
  }

  get isScheduled(): boolean {
    return this.status === PostStatus.PUBLISHED && 
           this.scheduledAt && 
           this.scheduledAt > new Date();
  }

  get readingTime(): number {
    // Estimate reading time based on content length (average 200 words per minute)
    const wordCount = this.content.split(/\s+/).length;
    return Math.ceil(wordCount / 200);
  }

  get wordCount(): number {
    return this.content.split(/\s+/).length;
  }

  get characterCount(): number {
    return this.content.length;
  }

  // Helper methods
  publish(publishedAt?: Date): void {
    this.status = PostStatus.PUBLISHED;
    this.publishedAt = publishedAt || new Date();
    this.scheduledAt = null;
  }

  schedule(scheduledAt: Date): void {
    this.status = PostStatus.PUBLISHED;
    this.scheduledAt = scheduledAt;
    this.publishedAt = null;
  }

  unpublish(): void {
    this.status = PostStatus.DRAFT;
    this.publishedAt = null;
    this.scheduledAt = null;
  }

  archive(): void {
    this.status = PostStatus.ARCHIVED;
  }

  incrementViewCount(): void {
    this.viewCount += 1;
  }

  incrementLikeCount(): void {
    this.likeCount += 1;
  }

  incrementShareCount(): void {
    this.shareCount += 1;
  }

  incrementCommentCount(): void {
    this.commentCount += 1;
  }

  decrementCommentCount(): void {
    if (this.commentCount > 0) {
      this.commentCount -= 1;
    }
  }

  hasTag(tag: string): boolean {
    return this.tags?.includes(tag) || false;
  }

  hasCategory(category: string): boolean {
    return this.categories?.includes(category) || false;
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
}
