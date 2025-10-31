import { IsString, IsOptional, IsArray, IsNumber, IsBoolean, IsEnum, IsDateString, IsInt, Min, MaxLength } from 'class-validator';

export class CreatePostDto {
  @IsString()
  @MaxLength(255)
  name: string;

  @IsOptional()
  @IsString()
  @MaxLength(255)
  slug?: string;

  @IsOptional()
  @IsString()
  excerpt?: string;

  @IsString()
  content: string;

  @IsOptional()
  @IsString()
  image?: string;

  @IsOptional()
  @IsString()
  cover_image?: string;

  @IsOptional()
  @IsInt()
  primary_postcategory_id?: number;

  @IsOptional()
  @IsEnum(['draft', 'scheduled', 'published', 'archived'])
  status?: 'draft' | 'scheduled' | 'published' | 'archived';

  @IsOptional()
  @IsBoolean()
  is_featured?: boolean;

  @IsOptional()
  @IsBoolean()
  is_pinned?: boolean;

  @IsOptional()
  @IsDateString()
  published_at?: string;

  @IsOptional()
  @IsString()
  meta_title?: string;

  @IsOptional()
  @IsString()
  meta_description?: string;

  @IsOptional()
  @IsString()
  canonical_url?: string;

  @IsOptional()
  @IsString()
  og_title?: string;

  @IsOptional()
  @IsString()
  og_description?: string;

  @IsOptional()
  @IsString()
  og_image?: string;

  @IsOptional()
  @IsArray()
  @IsNumber({}, { each: true })
  tag_ids?: number[];

  @IsOptional()
  @IsArray()
  @IsNumber({}, { each: true })
  category_ids?: number[];
}

