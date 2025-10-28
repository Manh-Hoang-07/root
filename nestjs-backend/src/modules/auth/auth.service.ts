import { Injectable, UnauthorizedException } from '@nestjs/common';
import { InjectRepository } from '@nestjs/typeorm';
import { Repository } from 'typeorm';
import { JwtService } from '@nestjs/jwt';
import * as bcrypt from 'bcryptjs';
import { User } from '../../shared/entities/user.entity';
import { LoginDto } from './dto/login.dto';
import { RegisterDto } from './dto/register.dto';

@Injectable()
export class AuthService {
  constructor(
    @InjectRepository(User)
    private readonly userRepository: Repository<User>,
    private readonly jwtService: JwtService,
  ) {}

  async login(loginDto: LoginDto) {
    const user = await this.userRepository.findOne({
      where: { email: loginDto.email },
    });

    if (!user) {
      throw new UnauthorizedException('Invalid credentials');
    }

    const isPasswordValid = await bcrypt.compare(
      loginDto.password,
      user.password,
    );

    if (!isPasswordValid) {
      throw new UnauthorizedException('Invalid credentials');
    }

    const roles: string[] = await this.getUserRoles(user);
    const payload = { sub: user.id, email: user.email, roles };
    const token = this.jwtService.sign(payload);

    return {
      accessToken: token,
      tokenType: 'Bearer',
      user: user,
    };
  }

  async register(registerDto: RegisterDto) {
    const hashedPassword = await bcrypt.hash(registerDto.password, 10);

    const user = this.userRepository.create({
      email: registerDto.email,
      password: hashedPassword,
      username: registerDto.username,
    } as any);

    const savedUser = await this.userRepository.save(user) as unknown as User;

    const roles: string[] = await this.getUserRoles(savedUser);
    const payload = { sub: savedUser.id, email: savedUser.email, roles };
    const token = this.jwtService.sign(payload);

    return {
      accessToken: token,
      tokenType: 'Bearer',
      user: savedUser,
    };
  }

  async validateUser(userId: string) {
    const user = await this.userRepository.findOne({ where: { id: Number(userId) } });
    return user || null;
  }

  private async getUserRoles(user: User): Promise<string[]> {
    // Simple mapping: inject admin role by email list from env
    const adminEmails = (process.env.ADMIN_EMAILS || '').split(',').map((e) => e.trim()).filter(Boolean);
    const roles: string[] = [];
    if (adminEmails.includes(user.email)) {
      roles.push('admin');
    } else {
      roles.push('user');
    }
    return roles;
  }
}

