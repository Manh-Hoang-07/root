import { IsString, IsOptional, IsBoolean, IsArray, IsEnum } from 'class-validator';
import { PostStatus } from '../../../../shared/enums/post-status.enum';

export class UpdatePostDto {
  @IsString()
  @IsOptional()
  name?: string;

  @IsString()
  @IsOptional()
  slug?: string;

  @IsString()
  @IsOptional()
  excerpt?: string;

  @IsString()
  @IsOptional()
  content?: string;

  @IsString()
  @IsOptional()
  image?: string;

  @IsString()
  @IsOptional()
  cover_image?: string;

  @IsOptional()
  primary_postcategory_id?: number;

  @IsEnum(PostStatus)
  @IsOptional()
  status?: PostStatus;

  @IsBoolean()
  @IsOptional()
  is_featured?: boolean;

  @IsBoolean()
  @IsOptional()
  is_pinned?: boolean;

  @IsArray()
  @IsOptional()
  category_ids?: number[];

  @IsArray()
  @IsOptional()
  tag_ids?: number[];
}
