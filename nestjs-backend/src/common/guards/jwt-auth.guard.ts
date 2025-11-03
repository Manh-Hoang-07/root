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
import { TokenBlacklistService } from '../../core/security/token-blacklist.service';
import { RequestContext } from '../utils/request-context.util';

@Injectable()
export class JwtAuthGuard extends AuthGuard('jwt') {
  constructor(
    private reflector: Reflector,
    private tokenBlacklist: TokenBlacklistService,
  ) {
    super();
  }

  canActivate(context: ExecutionContext) {
    // Kiểm tra token blacklist trước khi validate JWT
    if (this.tokenBlacklist) {
      const request = context.switchToHttp().getRequest();
      const authHeader = request.headers.authorization;
      
      if (authHeader && authHeader.startsWith('Bearer ')) {
        const token = authHeader.substring(7);
        if (token && this.tokenBlacklist.isBlacklistedSync(token)) {
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

    // Protect-by-default: chỉ optional khi có @Permission('public')
    if (isPublicPermission) {
      // Thử validate token nếu có, nhưng không bắt buộc
      const result = super.canActivate(context);

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

    // Route public/optional auth: chỉ khi có @Permission('public')
    if (isPublicPermission) {
      if (err || !user) {
        return null; // optional auth
      }
      try {
        RequestContext.set('user', user);
      } catch {}
      return user;
    }

    // Route protected: bắt buộc phải có user hợp lệ
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
