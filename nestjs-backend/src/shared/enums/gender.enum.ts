export enum Gender {
  Male = 'male',
  Female = 'female',
  Other = 'other',
}

export const GenderLabels: Record<Gender, string> = {
  [Gender.Male]: 'Nam',
  [Gender.Female]: 'Nữ',
  [Gender.Other]: 'Khác',
};

export const getGenderLabel = (gender: Gender): string => {
  return GenderLabels[gender];
};

export const getGenderArray = () => {
  return Object.values(Gender).map((gender) => ({
    id: gender,
    name: getGenderLabel(gender),
  }));
};

