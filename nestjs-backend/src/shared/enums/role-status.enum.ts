export enum RoleStatus {
  Active = 'active',
  Inactive = 'inactive',
}

export const RoleStatusLabels: Record<RoleStatus, string> = {
  [RoleStatus.Active]: 'Hoạt động',
  [RoleStatus.Inactive]: 'Không hoạt động',
};

export const getRoleStatusLabel = (status: RoleStatus): string => {
  return RoleStatusLabels[status];
};

export const getRoleStatusArray = () => {
  return Object.values(RoleStatus).map((status) => ({
    id: status,
    name: getRoleStatusLabel(status),
  }));
};

