import {
  Entity,
  PrimaryGeneratedColumn,
  Column,
  CreateDateColumn,
  UpdateDateColumn,
  DeleteDateColumn,
  ManyToMany,
  ManyToOne,
  OneToMany,
  Index,
  JoinColumn,
} from 'typeorm';
import { Role } from './role.entity';
import { User } from './user.entity';

@Entity('permissions')
@Index(['code'], { unique: true })
export class Permission {
  @PrimaryGeneratedColumn({ type: 'bigint', unsigned: true })
  id: number;

  @Column({ type: 'varchar', length: 120 })
  code: string;

  @Column({ type: 'varchar', length: 150, nullable: true })
  name?: string | null;

  @Column({ type: 'varchar', length: 30, default: 'active' })
  status: string;

  @ManyToOne(() => Permission, (perm) => perm.children, { nullable: true, onDelete: 'SET NULL' })
  @JoinColumn({ name: 'parent_id' })
  parent?: Permission | null;

  @OneToMany(() => Permission, (perm) => perm.parent)
  children?: Permission[];

  @ManyToMany(() => Role, (role) => role.permissions, { cascade: false })
  roles?: Role[];

  @ManyToMany(() => User, (user) => user.direct_permissions, { cascade: false })
  users?: User[];

  @Column({ type: 'bigint', unsigned: true, nullable: true })
  created_user_id?: number | null;

  @Column({ type: 'bigint', unsigned: true, nullable: true })
  updated_user_id?: number | null;

  @CreateDateColumn({ name: 'created_at' })
  created_at: Date;

  @UpdateDateColumn({ name: 'updated_at' })
  updated_at: Date;

  @DeleteDateColumn({ name: 'deleted_at' })
  deleted_at?: Date;
}


