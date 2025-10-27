export enum ConfigType {
  STRING = 'string',
  INTEGER = 'integer',
  BOOLEAN = 'boolean',
  JSON = 'json',
  ARRAY = 'array',
  FLOAT = 'float',
}

export const ConfigTypeLabels: Record<ConfigType, string> = {
  [ConfigType.STRING]: 'Chuỗi',
  [ConfigType.INTEGER]: 'Số nguyên',
  [ConfigType.BOOLEAN]: 'Boolean',
  [ConfigType.JSON]: 'JSON',
  [ConfigType.ARRAY]: 'Mảng',
  [ConfigType.FLOAT]: 'Số thực',
};

export const ConfigTypeValidationRules: Record<ConfigType, string> = {
  [ConfigType.STRING]: 'string',
  [ConfigType.INTEGER]: 'integer',
  [ConfigType.BOOLEAN]: 'boolean',
  [ConfigType.JSON]: 'json',
  [ConfigType.ARRAY]: 'array',
  [ConfigType.FLOAT]: 'numeric',
};

export const getConfigTypeLabel = (type: ConfigType): string => {
  return ConfigTypeLabels[type];
};

export const getConfigTypeValidationRule = (type: ConfigType): string => {
  return ConfigTypeValidationRules[type];
};

export const getConfigTypeOptions = () => {
  return Object.values(ConfigType).map((type) => ({
    value: type,
    label: getConfigTypeLabel(type),
  }));
};

