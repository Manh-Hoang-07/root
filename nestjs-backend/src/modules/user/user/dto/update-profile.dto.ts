import { IsOptional, IsString, IsPhoneNumber, MaxLength } from 'class-validator';

export class UpdateProfileDto {
  @IsOptional()
  @IsString()
  @MaxLength(100)
  name?: string;

  @IsOptional()
  @IsPhoneNumber('VN')
  phone?: string;

  @IsOptional()
  @IsString()
  @MaxLength(255)
  avatarUrl?: string;
}

