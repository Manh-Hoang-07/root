import { Body, Controller, Get, Headers, HttpCode, HttpStatus, Post, Res, ValidationPipe, UsePipes, BadRequestException, UseGuards } from '@nestjs/common';
import { Response } from 'express';
import { AuthService } from './auth.service';
import { LoginDto } from './dto/login.dto';
import { RegisterDto } from './dto/register.dto';
import { RefreshTokenDto } from './dto/refresh-token.dto';
import { Auth } from '../../common/utils/auth.util';
import { ResponseUtil } from '../../common/utils/response.util';

@Controller()
export class AuthController {
  constructor(private readonly authService: AuthService) { }

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

  @Post('register')
  async register(@Body() dto: RegisterDto) {
    return this.authService.register(dto);
  }

  @Get('me')
  async me() {
    const userId = Auth.id();
    return this.authService.me(userId as number);
  }

  @Post('logout')
  async logout(@Headers('authorization') authHeader: string, @Res({ passthrough: true }) res: Response) {
    const userId = Auth.id() as number;
    // Extract token from authorization header
    let token = null;
    if (authHeader && authHeader.startsWith('Bearer ')) {
      token = authHeader.substring(7); // Remove 'Bearer ' prefix
    }

    const result = await this.authService.logout(userId, token);
    const domain = (res.req.hostname === 'localhost') ? 'localhost' : undefined;
    res.clearCookie('auth_token', { domain, path: '/' });
    return result;
  }

  @Post('refresh')
  async refresh(@Res({ passthrough: true }) res: Response) {
    const userId = Auth.id() as number;
    const result: any = await this.authService.refreshToken(userId);

    if (result?.success && result?.data?.token) {
      const domain = (res.req.hostname === 'localhost') ? 'localhost' : undefined;
      res.cookie('auth_token', result.data.token, { maxAge: 60 * 60 * 1000, httpOnly: false, secure: false, domain, path: '/' });
    }
    return result;
  }
}


