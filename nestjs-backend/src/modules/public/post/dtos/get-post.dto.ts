import { IsOptional, IsArray, IsString } from 'class-validator';

export class GetPostDto {
  @IsOptional()
  @IsString()
  page?: string = '1';

  @IsOptional()
  @IsString()
  per_page?: string = '20';

  @IsOptional()
  @IsString()
  search?: string;

  @IsOptional()
  @IsString()
  status?: string;

  @IsOptional()
  @IsString()
  relations?: string;
}
