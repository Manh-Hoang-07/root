import { DataSource } from 'typeorm';
import { Injectable, Logger } from '@nestjs/common';
import { SeedRoles } from './seed-roles';
import { SeedPermissions } from './seed-permissions';
import { SeedUsers } from './seed-users';
import { SeedPostCategories } from './seed-post-categories';
import { SeedPostTags } from './seed-post-tags';
import { SeedPosts } from './seed-posts';

@Injectable()
export class SeedService {
  private readonly logger = new Logger(SeedService.name);

  constructor(
    private readonly dataSource: DataSource,
    private readonly seedPermissions: SeedPermissions,
    private readonly seedRoles: SeedRoles,
    private readonly seedUsers: SeedUsers,
    private readonly seedPostCategories: SeedPostCategories,
    private readonly seedPostTags: SeedPostTags,
    private readonly seedPosts: SeedPosts,
  ) {}

  async seedAll(): Promise<void> {
    this.logger.log('Starting database seeding...');

    try {
      // Seed in order: permissions -> roles -> users -> post categories -> post tags -> posts
      await this.seedPermissions.seed();
      await this.seedRoles.seed();
      await this.seedUsers.seed();
      await this.seedPostCategories.seed();
      await this.seedPostTags.seed();
      await this.seedPosts.seed();

      this.logger.log('Database seeding completed successfully');
    } catch (error) {
      this.logger.error('Database seeding failed', error);
      throw error;
    }
  }

  async clearAll(): Promise<void> {
    this.logger.log('Clearing database...');

    try {
      const queryRunner = this.dataSource.createQueryRunner();
      await queryRunner.connect();
      await queryRunner.startTransaction();

      try {
        // Disable foreign key checks temporarily
        await queryRunner.query('SET FOREIGN_KEY_CHECKS = 0');

        // Helper function to safely truncate table
        const truncateTable = async (tableName: string) => {
          try {
            await queryRunner.query(`TRUNCATE TABLE \`${tableName}\``);
          } catch (error: any) {
            // If table doesn't exist, just log and continue
            if (error.code === 'ER_NO_SUCH_TABLE') {
              this.logger.warn(`Table ${tableName} does not exist, skipping...`);
            } else {
              throw error;
            }
          }
        };

        // Clear in reverse order (children first, then parents)
        // Clear junction tables first (many-to-many)
        await truncateTable('post_posttag');
        await truncateTable('post_postcategory');
        await truncateTable('user_permissions');
        await truncateTable('user_roles');
        await truncateTable('role_has_permissions');
        
        // Clear main tables
        await truncateTable('posts');
        await truncateTable('posttag');
        await truncateTable('postcategory');
        await truncateTable('profiles');
        await truncateTable('users');
        await truncateTable('roles');
        await truncateTable('permissions');

        // Re-enable foreign key checks
        await queryRunner.query('SET FOREIGN_KEY_CHECKS = 1');

        await queryRunner.commitTransaction();
        this.logger.log('Database cleared successfully');
      } catch (error) {
        await queryRunner.rollbackTransaction();
        throw error;
      } finally {
        await queryRunner.release();
      }
    } catch (error) {
      this.logger.error('Database clearing failed', error);
      throw error;
    }
  }

  async clearDatabase(): Promise<void> {
    this.logger.log('Clearing database...');
    
    const entities = this.dataSource.entityMetadatas;
    
    for (const entity of entities) {
      const repository = this.dataSource.getRepository(entity.name);
      await repository.clear();
    }
    
    this.logger.log('Database cleared successfully');
  }
}
