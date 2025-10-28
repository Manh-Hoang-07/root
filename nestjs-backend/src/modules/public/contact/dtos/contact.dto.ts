import { IsString, IsEmail, IsOptional, MaxLength, MinLength } from 'class-validator';

export class ContactDto {
  @IsString()
  @IsOptional()
  @MaxLength(255)
  name?: string;

  @IsEmail()
  @IsOptional()
  email?: string;

  @IsString()
  @IsOptional()
  phone?: string;

  @IsString()
  @IsOptional()
  subject?: string;

  @IsString()
  @IsOptional()
  @MaxLength(5000)
  message?: string;
}
