import { Module } from '@nestjs/common';
import { TypeOrmModule } from '@nestjs/typeorm';
import { User } from '../../../shared/entities/user.entity';
import { Profile } from '../../../shared/entities/profile.entity';
import { UserService } from './services/user.service';
import { UserController } from './controllers/user.controller';

@Module({
  imports: [TypeOrmModule.forFeature([User, Profile])],
  providers: [UserService],
  controllers: [UserController],
  exports: [UserService],
})
export class UserModule {}


