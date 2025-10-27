export enum AttributeType {
  TEXT = 'text',
  SELECT = 'select',
  MULTISELECT = 'multiselect',
  COLOR = 'color',
  IMAGE = 'image',
}

export const AttributeTypeLabels: Record<AttributeType, string> = {
  [AttributeType.TEXT]: 'Văn bản',
  [AttributeType.SELECT]: 'Chọn một',
  [AttributeType.MULTISELECT]: 'Chọn nhiều',
  [AttributeType.COLOR]: 'Màu sắc',
  [AttributeType.IMAGE]: 'Hình ảnh',
};

export const AttributeTypeIcons: Record<AttributeType, string> = {
  [AttributeType.TEXT]: 'fas fa-font',
  [AttributeType.SELECT]: 'fas fa-list',
  [AttributeType.MULTISELECT]: 'fas fa-list-check',
  [AttributeType.COLOR]: 'fas fa-palette',
  [AttributeType.IMAGE]: 'fas fa-image',
};

export const getAttributeTypeLabel = (type: AttributeType): string => {
  return AttributeTypeLabels[type];
};

export const getAttributeTypeIcon = (type: AttributeType): string => {
  return AttributeTypeIcons[type];
};

