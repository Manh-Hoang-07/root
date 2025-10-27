import { Injectable } from '@nestjs/common';
import { InjectRepository } from '@nestjs/typeorm';
import { Repository } from 'typeorm';
import { Profile } from '../../../entities/profile.entity';

@Injectable()
export class ProfilesService {
  constructor(
    @InjectRepository(Profile)
    private readonly profileRepository: Repository<Profile>,
  ) {}

  async getProfile() {
    // In a real app, you would get the user ID from the JWT token
    // For now, we'll just return the first profile
    return this.profileRepository.findOne({
      where: { id: 1 } as any,
    });
  }

  async updateProfile(updateProfileDto: any) {
    const existingProfile = await this.profileRepository.findOne({
      where: { id: 1 } as any,
    });

    if (!existingProfile) {
      return null;
    }

    const updatedProfile = this.profileRepository.merge(existingProfile, updateProfileDto as any);
    return this.profileRepository.save(updatedProfile);
  }
}
