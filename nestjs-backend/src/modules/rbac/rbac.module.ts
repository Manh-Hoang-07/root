import { Module } from '@nestjs/common';
import { TypeOrmModule } from '@nestjs/typeorm';
import { Role } from '../../shared/entities/role.entity';
import { Permission } from '../../shared/entities/permission.entity';
import { User } from '../../shared/entities/user.entity';
import { RbacService } from './services/rbac.service';
import { UserPermissionController } from './controllers/user-permission.controller';

@Module({
  imports: [TypeOrmModule.forFeature([Role, Permission, User])],
  providers: [
    RbacService,
  ],
  controllers: [
    UserPermissionController,
  ],
  exports: [
    RbacService,
    TypeOrmModule,
  ],
})
export class RbacModule {}


