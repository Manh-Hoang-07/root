import * as fs from 'fs';
import * as path from 'path';

const logDir = path.join(process.cwd(), 'logs');
const logFile = path.join(logDir, 'app.log');

// Đảm bảo thư mục logs tồn tại
if (!fs.existsSync(logDir)) {
  fs.mkdirSync(logDir, { recursive: true });
}

export function logToFile(message: string): void {
  const timestamp = new Date().toISOString();
  const logEntry = `[${timestamp}] ${message}\n`;
  
  fs.appendFileSync(logFile, logEntry, 'utf8');
}

export function logRequest(method: string, url: string, statusCode?: number): void {
  const status = statusCode ? ` - ${statusCode}` : '';
  logToFile(`${method} ${url}${status}`);
}

export function logError(message: string, error?: any): void {
  let errorMsg = `ERROR: ${message}`;
  if (error) {
    errorMsg += ` - ${error.message || error}`;
  }
  logToFile(errorMsg);
}
