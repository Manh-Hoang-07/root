import { Injectable, ExecutionContext } from '@nestjs/common';
import { AuthGuard } from '@nestjs/passport';
import { AuthService } from '../../modules/auth/auth.service';

@Injectable()
export class TokenBlacklistGuard extends AuthGuard('jwt') {
    constructor(private authService: AuthService) {
        super();
    }

    async canActivate(context: ExecutionContext): Promise<boolean> {
        const request = context.switchToHttp().getRequest();
        const authHeader = request.headers.authorization;

        // Extract token from authorization header
        let token = null;
        if (authHeader && authHeader.startsWith('Bearer ')) {
            token = authHeader.substring(7); // Remove 'Bearer ' prefix
        }

        // Check if token is blacklisted
        if (token && this.authService.isTokenBlacklisted(token)) {
            return false;
        }

        // Continue with normal JWT validation
        return super.canActivate(context) as Promise<boolean>;
    }
}