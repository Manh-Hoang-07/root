import {
  ExecutionContext,
  Injectable,
  HttpException,
  HttpStatus,
  Optional,
  Inject,
  forwardRef,
} from '@nestjs/common';
import { Reflector } from '@nestjs/core';
import { AuthGuard } from '@nestjs/passport';
import { PERMS_REQUIRED_KEY, PUBLIC_PERMISSION } from '../decorators/rbac.decorators';
import { ResponseUtil } from '../utils/response.util';
import { AuthService } from '../../modules/auth/auth.service';
import { RequestContext } from '../utils/request-context.util';

@Injectable()
export class JwtAuthGuard extends AuthGuard('jwt') {
  constructor(
    private reflector: Reflector,
    @Optional() @Inject(forwardRef(() => AuthService)) private authService?: AuthService,
  ) {
    super();
  }

  canActivate(context: ExecutionContext) {
    // Kiểm tra token blacklist trước khi validate JWT
    if (this.authService) {
      const request = context.switchToHttp().getRequest();
      const authHeader = request.headers.authorization;
      
      if (authHeader && authHeader.startsWith('Bearer ')) {
        const token = authHeader.substring(7);
        if (token && this.authService.isTokenBlacklisted(token)) {
          // Token bị blacklist - từ chối truy cập
          return false;
        }
      }
    }

    // Kiểm tra route có @Permission() không
    const requiredPerms = this.reflector.getAllAndOverride<string[]>(PERMS_REQUIRED_KEY, [
      context.getHandler(),
      context.getClass(),
    ]) || [];

    // Kiểm tra có @Permission('public') không
    const isPublicPermission = requiredPerms.includes(PUBLIC_PERMISSION);

    // Đổi mặc định: protected-by-default. Chỉ public khi có @Permission('public')
    if (isPublicPermission) {
      // Thử validate token nếu có, nhưng không bắt buộc
      // Nếu có lỗi trong quá trình validate, catch và vẫn cho phép truy cập
      const result = super.canActivate(context);
      
      // Nếu là Promise, catch error và vẫn cho phép truy cập
      if (result instanceof Promise) {
        return result.catch(() => true);
      }
      
      return result;
    }

    // Route protected (mặc định): bắt buộc phải có token hợp lệ
    return super.canActivate(context);
  }

  handleRequest(err: any, user: any, info: any, context: ExecutionContext) {
    // Kiểm tra route có @Permission() không
    const requiredPerms = this.reflector.getAllAndOverride<string[]>(PERMS_REQUIRED_KEY, [
      context.getHandler(),
      context.getClass(),
    ]) || [];

    // Kiểm tra có @Permission('public') không
    const isPublicPermission = requiredPerms.includes(PUBLIC_PERMISSION);

    // Route public: chỉ khi có @Permission('public')
    if (isPublicPermission) {
      // Có lỗi nhưng route public/optional - không throw, chỉ trả về null
      if (err || !user) {
        return null;
      }
      // Có user hợp lệ - trả về user để set vào req.user
      try {
        RequestContext.set('user', user);
      } catch {}
      return user;
    }

    // Route protected (mặc định): bắt buộc phải có user
    if (err || !user) {
      let message = 'Unauthorized';
      
      if (info?.name === 'TokenExpiredError') {
        message = 'Token expired';
      } else if (info?.name === 'JsonWebTokenError') {
        message = 'Invalid token';
      } else if (info?.message) {
        message = info.message;
      }

      // Dùng ResponseUtil thay vì UnauthorizedException
      if (err) {
        throw err;
      }
      
      const response = ResponseUtil.unauthorized(message);
      throw new HttpException(response, response.httpStatus || HttpStatus.UNAUTHORIZED);
    }

    try {
      RequestContext.set('user', user);
    } catch {}
    return user;
  }
}
