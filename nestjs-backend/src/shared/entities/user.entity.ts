import { Entity, PrimaryGeneratedColumn, Column, CreateDateColumn, UpdateDateColumn } from 'typeorm';
import { UserStatus } from '../enums/user-status.enum';
import { Gender } from '../enums/gender.enum';

@Entity('users')
export class User {
  @PrimaryGeneratedColumn()
  id: number;

  @Column({ length: 50, unique: true, nullable: true })
  username?: string | null;

  @Column({ length: 255, unique: true, nullable: true })
  email?: string | null;

  @Column({ length: 20, unique: true, nullable: true })
  phone?: string | null;

  @Column({ length: 255, nullable: true })
  password?: string | null;

  @Column({ type: 'varchar', length: 255, default: UserStatus.Active })
  status: UserStatus;

  @Column({ type: 'datetime', nullable: true })
  email_verified_at?: Date | null;

  @Column({ type: 'datetime', nullable: true })
  phone_verified_at?: Date | null;

  @Column({ type: 'datetime', nullable: true })
  last_login_at?: Date | null;

  @Column({ type: 'varchar', length: 100, nullable: true })
  remember_token?: string | null;

  @Column({ type: 'bigint', unsigned: true, nullable: true })
  created_user_id?: number | null;

  @Column({ type: 'bigint', unsigned: true, nullable: true })
  updated_user_id?: number | null;

  @CreateDateColumn({ name: 'created_at' })
  created_at: Date;

  @UpdateDateColumn({ name: 'updated_at' })
  updated_at: Date;

  // Note: Laravel migration does not include deleted_at
}


