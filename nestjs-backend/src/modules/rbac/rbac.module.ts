import { Module } from '@nestjs/common';
import { TypeOrmModule } from '@nestjs/typeorm';
import { Role } from '../../shared/entities/role.entity';
import { Permission } from '../../shared/entities/permission.entity';
import { User } from '../../shared/entities/user.entity';
import { RbacService } from './rbac.service';
import { UserPermissionService } from './services/user-permission.service';
import { UserPermissionController } from './controllers/user-permission.controller';
import { RbacAdminController } from './rbac.admin.controller';

@Module({
  imports: [TypeOrmModule.forFeature([Role, Permission, User])],
  providers: [
    RbacService,
    UserPermissionService,
  ],
  controllers: [
    RbacAdminController,
    UserPermissionController,
  ],
  exports: [
    RbacService,
    UserPermissionService,
    TypeOrmModule,
  ],
})
export class RbacModule {}


