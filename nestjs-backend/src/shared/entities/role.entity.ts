import {
  Entity,
  PrimaryGeneratedColumn,
  Column,
  CreateDateColumn,
  UpdateDateColumn,
  DeleteDateColumn,
  ManyToMany,
  JoinTable,
  ManyToOne,
  OneToMany,
  Index,
  JoinColumn,
} from 'typeorm';
import { Permission } from './permission.entity';
import { User } from './user.entity';

@Entity('roles')
@Index(['code'], { unique: true })
export class Role {
  @PrimaryGeneratedColumn({ type: 'bigint', unsigned: true })
  id: number;

  @Column({ type: 'varchar', length: 100 })
  code: string;

  @Column({ type: 'varchar', length: 150, nullable: true })
  name?: string | null;

  @Column({ type: 'varchar', length: 30, default: 'active' })
  status: string;

  @ManyToOne(() => Role, (role) => role.children, { nullable: true, onDelete: 'SET NULL' })
  @JoinColumn({ name: 'parent_id' })
  parent?: Role | null;

  @OneToMany(() => Role, (role) => role.parent)
  children?: Role[];

  @ManyToMany(() => Permission, (permission) => permission.roles, { cascade: false })
  @JoinTable({
    name: 'role_has_permissions',
    joinColumn: { name: 'role_id', referencedColumnName: 'id' },
    inverseJoinColumn: { name: 'permission_id', referencedColumnName: 'id' },
  })
  permissions?: Permission[];

  @ManyToMany(() => User, (user) => user.roles, { cascade: false })
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


