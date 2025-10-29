import { Body, Controller, Get, HttpCode, HttpStatus, Post, Res } from '@nestjs/common';
import { Response } from 'express';
import { AuthService } from './auth.service';
import { LoginDto } from './dto/login.dto';
import { RegisterDto } from './dto/register.dto';
import { RefreshTokenDto } from './dto/refresh-token.dto';
import { Public } from '../../common/decorators/public.decorator';
import { User } from '../../common/decorators/user.decorator';
import { ResponseUtil } from '../../common/utils/response.util';

@Controller()
export class AuthController {
  constructor(private readonly authService: AuthService) {}

  @Public()
  @Post('login')
  @HttpCode(HttpStatus.OK)
  async login(@Body() dto: LoginDto, @Res({ passthrough: true }) res: Response) {
    const result: any = await this.authService.login(dto);
    if (result?.success && result?.data?.token) {
      const domain = (res.req.hostname === 'localhost') ? 'localhost' : undefined;
      res.cookie('auth_token', result.data.token, { maxAge: 60 * 60 * 1000, httpOnly: false, secure: false, domain, path: '/' });
    }
    return result;
  }

  @Public()
  @Post('register')
  async register(@Body() dto: RegisterDto) {
    return this.authService.register(dto);
  }

  @Get('me')
  async me(@User('id') userId: number) {
    return this.authService.me(userId);
  }

  @Post('logout')
  async logout(@Res({ passthrough: true }) res: Response) {
    const result = await this.authService.logout();
    const domain = (res.req.hostname === 'localhost') ? 'localhost' : undefined;
    res.clearCookie('auth_token', { domain, path: '/' });
    return result;
  }

  @Public()
  @Post('refresh')
  async refresh(@User('id') userId: number, @Res({ passthrough: true }) res: Response, @Body() _dto: RefreshTokenDto) {
    if (!userId) {
      return ResponseUtil.unauthorized('User not authenticated');
    }
    const result: any = await this.authService.refreshToken(userId);
    if (result?.success && result?.data?.token) {
      const domain = (res.req.hostname === 'localhost') ? 'localhost' : undefined;
      res.cookie('auth_token', result.data.token, { maxAge: 60 * 60 * 1000, httpOnly: false, secure: false, domain, path: '/' });
    }
    return result;
  }
}


