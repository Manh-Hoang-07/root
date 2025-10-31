import { IsOptional, IsInt, Min, IsString, IsEnum, IsBoolean } from 'class-validator';
import { Type } from 'class-transformer';

export class GetPostsDto {
  @IsOptional()
  @Type(() => Number)
  @IsInt()
  @Min(1)
  page?: number = 1;

  @IsOptional()
  @Type(() => Number)
  @IsInt()
  @Min(1)
  limit?: number = 10;

  @IsOptional()
  @IsString()
  search?: string;

  @IsOptional()
  @IsString()
  category_slug?: string;

  @IsOptional()
  @IsString()
  tag_slug?: string;

  @IsOptional()
  @IsEnum(['draft', 'scheduled', 'published', 'archived'])
  status?: 'draft' | 'scheduled' | 'published' | 'archived';

  @IsOptional()
  @Type(() => Boolean)
  @IsBoolean()
  is_featured?: boolean;

  @IsOptional()
  @Type(() => Boolean)
  @IsBoolean()
  is_pinned?: boolean;

  @IsOptional()
  @IsString()
  sort?: string = 'created_at:DESC';
}

