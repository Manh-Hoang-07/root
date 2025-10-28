export class DateUtil {
  /**
   * Format date to ISO string
   */
  static toISO(date: Date | string): string {
    return new Date(date).toISOString();
  }

  /**
   * Get current timestamp
   */
  static now(): Date {
    return new Date();
  }

  /**
   * Add days to a date
   */
  static addDays(date: Date, days: number): Date {
    const result = new Date(date);
    result.setDate(result.getDate() + days);
    return result;
  }

  /**
   * Check if date is valid
   */
  static isValid(date: any): boolean {
    return date instanceof Date && !isNaN(date.getTime());
  }
}
