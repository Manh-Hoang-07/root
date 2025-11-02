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
    
    // Nếu route không có @Permission() → mặc định là public
    const isPublicByDefault = requiredPerms.length === 0;

    // Route public: @Permission('public') hoặc không có @Permission() nào
    // Vẫn validate token nếu có, nhưng không bắt buộc
    // Cho phép user đăng nhập vào public route để có thêm thông tin
    if (isPublicPermission || isPublicByDefault) {
      // Thử validate token nếu có, nhưng không bắt buộc
      // Nếu có lỗi trong quá trình validate, catch và vẫn cho phép truy cập
      const result = super.canActivate(context);
      
      // Nếu là Promise, catch error và vẫn cho phép truy cập
      if (result instanceof Promise) {
        return result.catch(() => true);
      }
      
      return result;
    }

    // Route protected (có @Permission()): bắt buộc phải có token hợp lệ
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
    
    // Nếu route không có @Permission() → mặc định là public
    const isPublicByDefault = requiredPerms.length === 0;

    // Route public: @Permission('public') hoặc không có @Permission() nào
    // Không bắt buộc authentication, nhưng nếu có user thì trả về user
    if (isPublicPermission || isPublicByDefault) {
      // Có lỗi nhưng route public/optional - không throw, chỉ trả về null
      if (err || !user) {
        return null;
      }
      // Có user hợp lệ - trả về user để set vào req.user
      return user;
    }

    // Route protected (có @Permission()): bắt buộc phải có user
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

    return user;
  }
}
