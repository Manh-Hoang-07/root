export enum BasicStatus {
  Active = 'active',
  Inactive = 'inactive',
  Test = 'test',
}

export const BasicStatusLabels: Record<BasicStatus, string> = {
  [BasicStatus.Active]: 'Hoạt động',
  [BasicStatus.Inactive]: 'Không hoạt động',
  [BasicStatus.Test]: 'Test',
};

export const getBasicStatusLabel = (status: BasicStatus): string => {
  return BasicStatusLabels[status];
};

export const getBasicStatusArray = () => {
  return Object.values(BasicStatus).map((status) => ({
    id: status,
    name: getBasicStatusLabel(status),
  }));
};

