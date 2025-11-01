import { DataSource } from 'typeorm';
import { Injectable, Logger } from '@nestjs/common';
import { Post } from '../../../shared/entities/post.entity';
import { PostCategory } from '../../../shared/entities/post-category.entity';
import { PostTag } from '../../../shared/entities/post-tag.entity';
import { User } from '../../../shared/entities/user.entity';

@Injectable()
export class SeedPosts {
  private readonly logger = new Logger(SeedPosts.name);

  constructor(private readonly dataSource: DataSource) {}

  async seed(): Promise<void> {
    this.logger.log('Seeding posts...');

    const postRepo = this.dataSource.getRepository(Post);
    const categoryRepo = this.dataSource.getRepository(PostCategory);
    const tagRepo = this.dataSource.getRepository(PostTag);
    const userRepo = this.dataSource.getRepository(User);

    // Check if posts already exist
    const existingPosts = await postRepo.count();
    if (existingPosts > 0) {
      this.logger.log('Posts already seeded, skipping...');
      return;
    }

    // Get all categories, tags, and users for random assignment
    const allCategories = await categoryRepo.find();
    const allTags = await tagRepo.find();
    const allUsers = await userRepo.find();

    if (allCategories.length === 0 || allTags.length === 0 || allUsers.length === 0) {
      this.logger.warn('Categories, tags, or users not found. Please seed them first.');
      return;
    }

    // Generate 30 posts with diverse content
    const postTitles = [
      'Hướng dẫn lập trình NestJS từ cơ bản đến nâng cao',
      'Tìm hiểu TypeScript cho người mới bắt đầu',
      'Xây dựng RESTful API với Node.js và Express',
      'React Hooks: Cách sử dụng hiệu quả',
      'Vue.js 3 Composition API - Những điều cần biết',
      'GraphQL vs REST API: So sánh chi tiết',
      'Docker và Kubernetes cho ứng dụng production',
      'Tối ưu hóa hiệu suất database MySQL',
      'Xây dựng ứng dụng real-time với WebSocket',
      'Authentication và Authorization trong NestJS',
      'Testing với Jest và Supertest',
      'Microservices architecture pattern',
      'CI/CD với GitHub Actions',
      'Security best practices cho web application',
      'SEO optimization cho website',
      'Responsive design với CSS Grid và Flexbox',
      'State management với Redux',
      'TypeScript advanced types và generics',
      'Database design và normalization',
      'API documentation với Swagger',
      'Error handling và logging',
      'Code review best practices',
      'Git workflow cho team development',
      'Performance monitoring và optimization',
      'Cloud deployment với AWS',
      'Mobile app development với React Native',
      'Progressive Web App (PWA) development',
      'Machine Learning basics với Python',
      'Blockchain và cryptocurrency',
      'AI và ChatGPT integration',
    ];

    const statuses: Array<'draft' | 'scheduled' | 'published' | 'archived'> = ['published', 'published', 'published', 'draft', 'published'];
    
    for (let i = 0; i < 30; i++) {
      const title = postTitles[i] || `Bài viết số ${i + 1}`;
      const slug = this.generateSlug(title);
      const status = statuses[i % statuses.length];
      const isFeatured = i < 5; // First 5 posts are featured
      const isPinned = i < 3; // First 3 posts are pinned
      
      // Random user
      const randomUser = allUsers[Math.floor(Math.random() * allUsers.length)];
      
      // Random 1-3 categories
      const numCategories = Math.floor(Math.random() * 3) + 1;
      const shuffledCategories = [...allCategories].sort(() => 0.5 - Math.random());
      const postCategories = shuffledCategories.slice(0, numCategories);
      const primaryCategory = postCategories[0];
      
      // Random 2-5 tags
      const numTags = Math.floor(Math.random() * 4) + 2;
      const shuffledTags = [...allTags].sort(() => 0.5 - Math.random());
      const postTags = shuffledTags.slice(0, numTags);
      
      // Published date (if published)
      const publishedAt = status === 'published' ? new Date(Date.now() - Math.random() * 30 * 24 * 60 * 60 * 1000) : null;
      
      const post = postRepo.create({
        name: title,
        slug: slug,
        excerpt: `Đây là phần tóm tắt của bài viết "${title}". Bài viết này sẽ cung cấp những thông tin hữu ích về chủ đề liên quan.`,
        content: this.generateContent(title),
        status: status,
        is_featured: isFeatured,
        is_pinned: isPinned,
        published_at: publishedAt,
        view_count: Math.floor(Math.random() * 10000),
        primary_postcategory_id: primaryCategory?.id,
        createdBy: randomUser.id,
        categories: postCategories,
        tags: postTags,
      });

      const saved = await postRepo.save(post);
      this.logger.log(`Created post: ${saved.name} (${status}, categories: ${postCategories.length}, tags: ${postTags.length})`);
    }

    this.logger.log('Posts seeding completed - Total: 30');
  }

  private generateSlug(title: string): string {
    return title
      .toLowerCase()
      .normalize('NFD')
      .replace(/[\u0300-\u036f]/g, '')
      .replace(/đ/g, 'd')
      .replace(/Đ/g, 'D')
      .replace(/[^a-z0-9\s-]/g, '')
      .replace(/\s+/g, '-')
      .replace(/-+/g, '-')
      .trim();
  }

  private generateContent(title: string): string {
    return `
      <h1>${title}</h1>
      <p>Đây là nội dung chi tiết của bài viết về chủ đề "${title}".</p>
      <h2>Giới thiệu</h2>
      <p>Trong bài viết này, chúng ta sẽ tìm hiểu về các khía cạnh quan trọng của chủ đề này. Đây là một chủ đề rất thú vị và có nhiều ứng dụng thực tế.</p>
      <h2>Nội dung chính</h2>
      <p>Phần này sẽ trình bày chi tiết về các khái niệm, phương pháp, và kỹ thuật liên quan. Chúng ta sẽ đi sâu vào từng phần để có cái nhìn toàn diện.</p>
      <ul>
        <li>Điểm quan trọng thứ nhất</li>
        <li>Điểm quan trọng thứ hai</li>
        <li>Điểm quan trọng thứ ba</li>
      </ul>
      <h2>Kết luận</h2>
      <p>Hy vọng bài viết này đã cung cấp cho bạn những thông tin hữu ích. Nếu bạn có bất kỳ câu hỏi nào, đừng ngần ngại để lại comment bên dưới.</p>
    `.trim();
  }

  async clear(): Promise<void> {
    this.logger.log('Clearing posts...');
    const postRepo = this.dataSource.getRepository(Post);
    await postRepo.clear();
    this.logger.log('Posts cleared');
  }
}

