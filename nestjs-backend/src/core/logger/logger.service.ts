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

  private writeToFile(level: LogLevel, formattedMessage: string): void {
    const fileName = `${level}.log`;
    const filePath = path.join(this.logDirectory, fileName);
    
    fs.appendFileSync(filePath, formattedMessage + '\n', { encoding: 'utf8' });

    // Also write to general app.log
    const appLogPath = path.join(this.logDirectory, 'app.log');
    fs.appendFileSync(appLogPath, formattedMessage + '\n', { encoding: 'utf8' });
  }

  log(message: any, context?: LogContext): void {
    const formattedMessage = this.formatMessage('log', message, context);
    console.log(formattedMessage);
    this.writeToFile('log', formattedMessage);
  }

  error(message: any, trace?: string, context?: LogContext): void {
    const contextWithTrace = { ...context, trace };
    const formattedMessage = this.formatMessage('error', message, contextWithTrace);
    console.error(formattedMessage);
    if (trace) {
      console.error(trace);
    }
    this.writeToFile('error', formattedMessage + (trace ? `\n${trace}` : ''));
  }

  warn(message: any, context?: LogContext): void {
    const formattedMessage = this.formatMessage('warn', message, context);
    console.warn(formattedMessage);
    this.writeToFile('warn', formattedMessage);
  }

  debug(message: any, context?: LogContext): void {
    if (this.configService.get('NODE_ENV') !== 'production') {
      const formattedMessage = this.formatMessage('debug', message, context);
      console.debug(formattedMessage);
      this.writeToFile('debug', formattedMessage);
    }
  }

  verbose(message: any, context?: LogContext): void {
    if (this.configService.get('NODE_ENV') === 'development') {
      const formattedMessage = this.formatMessage('verbose', message, context);
      console.log(formattedMessage);
      this.writeToFile('verbose', formattedMessage);
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
