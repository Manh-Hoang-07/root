import { Injectable, LoggerService, LogLevel } from '@nestjs/common';
import { ConfigService } from '@nestjs/config';
import * as fs from 'fs';
import * as path from 'path';

export interface LogContext {
  context?: string;
  trace?: string;
  userId?: string;
  requestId?: string;
  [key: string]: any;
}

@Injectable()
export class CustomLoggerService implements LoggerService {
  private logLevels: LogLevel[] = ['log', 'error', 'warn', 'debug', 'verbose'];
  private logDirectory: string;

  constructor(private readonly configService: ConfigService) {
    this.logDirectory = this.configService.get('LOG_DIR') || './logs';
    this.ensureLogDirectory();
  }

  private ensureLogDirectory(): void {
    if (!fs.existsSync(this.logDirectory)) {
      fs.mkdirSync(this.logDirectory, { recursive: true });
    }
  }

  private formatMessage(level: LogLevel, message: any, context?: LogContext): string {
    const timestamp = new Date().toISOString();
    const contextStr = context?.context || 'Application';
    const userId = context?.userId ? `[User: ${context.userId}]` : '';
    const requestId = context?.requestId ? `[Request: ${context.requestId}]` : '';
    
    return `[${timestamp}] [${level.toUpperCase()}] [${contextStr}] ${userId} ${requestId} ${message}`;
  }

  private buildLogEntry(level: LogLevel, message: any, context?: LogContext & { trace?: string }) {
    return {
      timestamp: new Date().toISOString(),
      level: level.toUpperCase(),
      message,
      context: context?.context || 'Application',
      userId: context?.userId,
      requestId: context?.requestId,
      trace: context?.trace,
      extra: {},
    };
  }

  private writeJsonToFiles(level: LogLevel, entry: any): void {
    const line = JSON.stringify(entry);
    const date = new Date().toISOString().slice(0, 10); // YYYY-MM-DD
    const levelFilePath = path.join(this.logDirectory, `${level}.${date}.log`);
    fs.appendFileSync(levelFilePath, line + '\n', { encoding: 'utf8' });

    const appDailyPath = path.join(this.logDirectory, `app.${date}.log`);
    fs.appendFileSync(appDailyPath, line + '\n', { encoding: 'utf8' });
  }

  /**
   * Write arbitrary JSON value to a specific daily log file.
   * fileBaseName: without extension; file will be <fileBaseName>.<YYYY-MM-DD>.log
   */
  public writeJsonLine(fileBaseName: string, value: any): void {
    const date = new Date().toISOString().slice(0, 10);
    const filePath = path.join(this.logDirectory, `${fileBaseName}.${date}.log`);
    const line = JSON.stringify(value);
    fs.appendFileSync(filePath, line + '\n', { encoding: 'utf8' });
  }

  log(message: any, context?: LogContext): void {
    // Suppress console output
    // Only errors are persisted to files to reduce I/O
  }

  error(message: any, trace?: string, context?: LogContext): void {
    const contextWithTrace = { ...context, trace };
    // Suppress console output
    const entry = this.buildLogEntry('error', message, contextWithTrace);
    this.writeJsonToFiles('error', entry);
  }

  warn(message: any, context?: LogContext): void {
    // Suppress console output
    // Only errors are persisted to files to reduce I/O
  }

  debug(message: any, context?: LogContext): void {
    if (this.configService.get('NODE_ENV') !== 'production') {
      // Suppress console output
      // Only errors are persisted to files to reduce I/O
    }
  }

  verbose(message: any, context?: LogContext): void {
    if (this.configService.get('NODE_ENV') === 'development') {
      // Suppress console output
      // Only errors are persisted to files to reduce I/O
    }
  }

  // Additional utility methods
  logWithContext(level: LogLevel, message: any, context: LogContext): void {
    switch (level) {
      case 'log':
        this.log(message, context);
        break;
      case 'error':
        this.error(message, context.trace, context);
        break;
      case 'warn':
        this.warn(message, context);
        break;
      case 'debug':
        this.debug(message, context);
        break;
      case 'verbose':
        this.verbose(message, context);
        break;
    }
  }

  // Clear old logs (utility method)
  clearOldLogs(daysToKeep: number = 30): void {
    const files = fs.readdirSync(this.logDirectory);
    const cutoffDate = new Date();
    cutoffDate.setDate(cutoffDate.getDate() - daysToKeep);

    files.forEach(file => {
      const filePath = path.join(this.logDirectory, file);
      const stats = fs.statSync(filePath);
      
      if (stats.mtime < cutoffDate) {
        fs.unlinkSync(filePath);
        this.log(`Deleted old log file: ${file}`);
      }
    });
  }
}
