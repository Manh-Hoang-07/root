import { Entity, PrimaryGeneratedColumn, Column, OneToOne, JoinColumn, CreateDateColumn, UpdateDateColumn, DeleteDateColumn, Index } from 'typeorm';
import { User } from './user.entity';
import { Gender } from '../enums/gender.enum';

@Entity('profiles')
export class Profile {
    @PrimaryGeneratedColumn({ type: 'bigint', unsigned: true })
    id: number;

    @Column({ name: 'user_id', unique: true })
    @Index('UQ_profiles_user_id', { unique: true })
    userId: number;

    @OneToOne(() => User, user => user.profile, { onDelete: 'CASCADE' })
    @JoinColumn({ name: 'user_id' })
    user: User;

    @Column({ length: 255, nullable: true, type: 'varchar' })
    name?: string | null;

    @Column({ length: 255, nullable: true, type: 'varchar' })
    image?: string | null;

    @Column({ type: 'date', nullable: true })
    birthday?: Date | null;

    @Column({ length: 50, nullable: true, type: 'varchar' })
    gender?: Gender | null;

    @Column({ type: 'text', nullable: true })
    address?: string | null;

    @Column({ type: 'text', nullable: true })
    about?: string | null;

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