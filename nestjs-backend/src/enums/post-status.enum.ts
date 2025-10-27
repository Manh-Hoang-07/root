export enum PostStatus {
  DRAFT = 'draft',
  SCHEDULED = 'scheduled',
  PUBLISHED = 'published',
  ARCHIVED = 'archived',
}

export const PostStatusLabels: Record<PostStatus, string> = {
  [PostStatus.DRAFT]: 'Nháp',
  [PostStatus.SCHEDULED]: 'Lên lịch',
  [PostStatus.PUBLISHED]: 'Đã xuất bản',
  [PostStatus.ARCHIVED]: 'Đã lưu trữ',
};

export const getPostStatusLabel = (status: PostStatus): string => {
  return PostStatusLabels[status];
};

export const isPublished = (status: PostStatus): boolean => {
  return status === PostStatus.PUBLISHED;
};

export const isDraft = (status: PostStatus): boolean => {
  return status === PostStatus.DRAFT;
};

