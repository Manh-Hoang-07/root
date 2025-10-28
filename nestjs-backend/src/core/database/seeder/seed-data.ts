import { DataSource } from 'typeorm';
import { Injectable, Logger } from '@nestjs/common';

@Injectable()
export class SeedService {
  private readonly logger = new Logger(SeedService.name);

  constructor(private readonly dataSource: DataSource) {}

  async seedAll(): Promise<void> {
    this.logger.log('Starting database seeding...');

    try {
      await this.seedRoles();
      await this.seedUsers();
      // Add more seed methods as needed

      this.logger.log('Database seeding completed successfully');
    } catch (error) {
      this.logger.error('Database seeding failed', error);
      throw error;
    }
  }

  private async seedRoles(): Promise<void> {
    this.logger.log('Seeding roles...');
    // Implementation will be added when entities are created
  }

  private async seedUsers(): Promise<void> {
    this.logger.log('Seeding users...');
    // Implementation will be added when entities are created
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
