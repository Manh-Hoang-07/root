export function applyWhereConditions(queryBuilder: any, where: any): void {
  if (Array.isArray(where)) {
    where.forEach((condition, index) => {
      const conditions = Object.keys(condition)
        .map((key) => `entity.${key} = :${key}_${index}`)
        .join(' AND ');
      if (index === 0) {
        queryBuilder.andWhere(`(${conditions})`, condition);
      } else {
        queryBuilder.orWhere(`(${conditions})`, condition);
      }
    });
  } else if (where && typeof where === 'object') {
    Object.entries(where).forEach(([key, value]) => {
      queryBuilder.andWhere(`entity.${key} = :${key}`, { [key]: value });
    });
  }
}

export function applySelectColumns(queryBuilder: any, select?: string[], repository?: any): void {
  if (!Array.isArray(select) || select.length === 0 || !repository) return;
  
  // Lấy danh sách columns hợp lệ từ entity metadata
  const validColumns = repository.metadata.columns.map((c: any) => c.propertyName);
  const primaryProps = repository.metadata.primaryColumns.map((c: any) => c.propertyName);
  
  const uniq = new Set<string>();
  // Luôn thêm primary keys
  for (const p of primaryProps) uniq.add(p);
  
  // Chỉ thêm các columns hợp lệ từ select array
  for (const col of select) {
    if (typeof col === 'string') {
      const trimmed = col.trim();
      // Chỉ thêm nếu column tồn tại trong entity
      if (trimmed && validColumns.includes(trimmed)) {
        uniq.add(trimmed);
      }
      // Nếu không hợp lệ, sẽ bị bỏ qua (không throw error để linh hoạt hơn)
    }
  }
  
  const columns = Array.from(uniq).map(col => `entity.${col}`);
  queryBuilder.select(columns);
}

export function applyRelations(
  queryBuilder: any,
  relations: Array<string | { name: string; select?: string[]; where?: Record<string, any> }>
): void {
  if (!Array.isArray(relations) || relations.length === 0) return;
  for (const rel of relations) {
    if (typeof rel === 'string') {
      queryBuilder.leftJoinAndSelect(`entity.${rel}`, rel);
    } else if (rel && typeof rel === 'object' && rel.name) {
      const alias = rel.name;
      if (rel.select && Array.isArray(rel.select) && rel.select.length > 0) {
        queryBuilder.leftJoin(`entity.${alias}`, alias);
        for (const field of rel.select) {
          queryBuilder.addSelect(`${alias}.${field}`, `${alias}_${field}`);
        }
      } else {
        queryBuilder.leftJoinAndSelect(`entity.${alias}`, alias);
      }
      // Nếu có where thì andWhere alias.field = :field cho từng key trong where
      if (rel.where && typeof rel.where === 'object') {
        Object.entries(rel.where).forEach(([key, val]) => {
          // where hỗ trợ giá trị là array (IN)
          if (Array.isArray(val)) {
            queryBuilder.andWhere(`${alias}.${key} IN (:...${alias}_${key})`, { [`${alias}_${key}`]: val });
          } else {
            queryBuilder.andWhere(`${alias}.${key} = :${alias}_${key}`, { [`${alias}_${key}`]: val });
          }
        });
      }
    }
  }
}

export function applySorting(queryBuilder: any, sort?: any, repository?: any): void {
  const parseSort = (sortParam?: any): Array<{ field: string; direction: 'ASC' | 'DESC' }> => {
    if (!sortParam) return [];
    const asArray = Array.isArray(sortParam) ? sortParam : [sortParam];
    const map = new Map<string, 'ASC' | 'DESC'>();
    for (const item of asArray) {
      if (!item) continue;
      if (typeof item === 'string') {
        const [fieldRaw, dirRaw] = item.split(':');
        const field = (fieldRaw || '').trim();
        if (!field) continue;
        const direction: 'ASC' | 'DESC' = (dirRaw || 'DESC').toUpperCase() === 'ASC' ? 'ASC' : 'DESC';
        if (!map.has(field)) map.set(field, direction);
      } else if (typeof item === 'object' && 'field' in item) {
        const f = String((item as any).field || '').trim();
        if (!f) continue;
        const direction: 'ASC' | 'DESC' = ((item as any).direction || 'DESC').toUpperCase() === 'ASC' ? 'ASC' : 'DESC';
        if (!map.has(f)) map.set(f, direction);
      }
    }
    return Array.from(map.entries()).map(([field, direction]) => ({ field, direction }));
  };
  const parsed = parseSort(sort);
  if (!repository) return;
  const validFields = repository.metadata.columns.map((col: any) => col.propertyName);
  parsed.forEach((s: any, idx: number) => {
    if (!validFields.includes(s.field)) return;
    if (idx === 0) {
      queryBuilder.orderBy(`entity.${s.field}`, s.direction);
    } else {
      queryBuilder.addOrderBy(`entity.${s.field}`, s.direction);
    }
  });
}

export function prepareQuery(query: any = {}): { filters: any; options: any } {
  const filterInput: any = {};
  const optionInput: any = {};
  if (query && typeof query === 'object') {
    if (query.filters && typeof query.filters === 'object') {
      Object.assign(filterInput, query.filters);
    }
    if (query.options && typeof query.options === 'object') {
      Object.assign(optionInput, query.options);
    }
  }
  const rootCompat: any = {};
  if (query.page !== undefined) rootCompat.page = query.page;
  if (query.limit !== undefined) rootCompat.limit = query.limit;
  if (query.sort !== undefined) rootCompat.sort = query.sort;
  const options = { ...rootCompat, ...optionInput };
  return { filters: filterInput, options };
}