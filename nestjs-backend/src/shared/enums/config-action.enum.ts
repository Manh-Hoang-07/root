export enum ConfigAction {
  CREATED = 'created',
  UPDATED = 'updated',
  DELETED = 'deleted',
  RESTORED = 'restored',
}

export const ConfigActionLabels: Record<ConfigAction, string> = {
  [ConfigAction.CREATED]: 'Tạo mới',
  [ConfigAction.UPDATED]: 'Cập nhật',
  [ConfigAction.DELETED]: 'Xóa',
  [ConfigAction.RESTORED]: 'Khôi phục',
};

export const ConfigActionColors: Record<ConfigAction, string> = {
  [ConfigAction.CREATED]: 'success',
  [ConfigAction.UPDATED]: 'info',
  [ConfigAction.DELETED]: 'danger',
  [ConfigAction.RESTORED]: 'warning',
};

export const getConfigActionLabel = (action: ConfigAction): string => {
  return ConfigActionLabels[action];
};

export const getConfigActionColor = (action: ConfigAction): string => {
  return ConfigActionColors[action];
};

export const getConfigActionOptions = () => {
  return Object.values(ConfigAction).map((action) => ({
    value: action,
    label: getConfigActionLabel(action),
    color: getConfigActionColor(action),
  }));
};

