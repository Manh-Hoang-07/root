export enum UserStatus {
  Active = 'active',
  Pending = 'pending',
  Inactive = 'inactive',
}

export const UserStatusLabels: Record<UserStatus, string> = {
  [UserStatus.Active]: 'Hoạt động',
  [UserStatus.Pending]: 'Chờ xác nhận',
  [UserStatus.Inactive]: 'Đã khóa',
};

export const getUserStatusLabel = (status: UserStatus): string => {
  return UserStatusLabels[status];
};

export const getUsersStatusArray = () => {
  return Object.values(UserStatus).map((status) => ({
    id: status,
    name: getUserStatusLabel(status),
  }));
};

