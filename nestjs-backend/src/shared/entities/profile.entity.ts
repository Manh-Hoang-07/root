import { Entity, PrimaryGeneratedColumn, Column, ManyToOne, JoinColumn, CreateDateColumn, UpdateDateColumn, DeleteDateColumn } from 'typeorm';
import { User } from './user.entity';

@Entity('profiles')
export class Profile {
    @PrimaryGeneratedColumn({ type: 'bigint', unsigned: true })
    id: number;

    @Column({ name: 'user_id' })
    userId: number;

    @ManyToOne(() => User, { onDelete: 'CASCADE' })
    @JoinColumn({ name: 'user_id' })
    user: User;

    @Column({ length: 255, nullable: true })
    name?: string | null;

    @Column({ length: 255, nullable: true })
    image?: string | null;

    @Column({ type: 'date', nullable: true })
    birthday?: Date | null;

    @Column({ length: 50, nullable: true })
    gender?: string | null;

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