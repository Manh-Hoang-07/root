export enum ConfigGroup {
  GENERAL = 'general',
  EMAIL = 'email',
}

export const ConfigGroupLabels: Record<ConfigGroup, string> = {
  [ConfigGroup.GENERAL]: 'Cài đặt chung',
  [ConfigGroup.EMAIL]: 'Cấu hình Email',
};

export const ConfigGroupDescriptions: Record<ConfigGroup, string> = {
  [ConfigGroup.GENERAL]: 'Các cài đặt cơ bản của hệ thống',
  [ConfigGroup.EMAIL]: 'Cấu hình gửi email và SMTP',
};

export const ConfigGroupPublic: Record<ConfigGroup, boolean> = {
  [ConfigGroup.GENERAL]: true,
  [ConfigGroup.EMAIL]: false,
};

export const getConfigGroupLabel = (group: ConfigGroup): string => {
  return ConfigGroupLabels[group];
};

export const getConfigGroupDescription = (group: ConfigGroup): string => {
  return ConfigGroupDescriptions[group];
};

export const isConfigGroupPublic = (group: ConfigGroup): boolean => {
  return ConfigGroupPublic[group];
};

export const getConfigGroupOptions = () => {
  return Object.values(ConfigGroup).map((group) => ({
    value: group,
    label: getConfigGroupLabel(group),
    description: getConfigGroupDescription(group),
    is_public: isConfigGroupPublic(group),
  }));
};

