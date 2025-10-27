import { Controller, Get, Put, Param, Body, UseGuards } from '@nestjs/common';
import { ProfilesService } from '../services/profiles.service';
import { JwtAuthGuard } from '../../auth/guards/jwt-auth.guard';

@Controller('user/profiles')
@UseGuards(JwtAuthGuard)
export class ProfilesController {
  constructor(private readonly profilesService: ProfilesService) {}

  @Get()
  async getProfile() {
    const result = await this.profilesService.getProfile();

    if (!result) {
      return {
        statusCode: 404,
        message: 'Profile not found',
        data: null,
      };
    }

    return { data: result };
  }

  @Put()
  async updateProfile(@Body() updateProfileDto: any) {
    const result = await this.profilesService.updateProfile(updateProfileDto);
    
    if (!result) {
      return {
        statusCode: 404,
        message: 'Profile not found',
        data: null,
      };
    }

    return { data: result };
  }
}
