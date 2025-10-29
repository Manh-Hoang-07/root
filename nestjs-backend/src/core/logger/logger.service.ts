import { Injectable, LoggerService, LogLevel } from '@nestjs/common';
import { ConfigService } from '@nestjs/config';
import * as fs from 'fs';
import * as path from 'path';
import * as os from 'os';
import { performance } from 'perf_hooks';

export interface LogContext {
  context?: string;
  trace?: string;
  userId?: string;
  username?: string;
  requestId?: string;
  method?: string;
  url?: string;
  ip?: string;
  userAgent?: string;
  extra?: Record<string, any>;
  [key: string]: any;
}

export interface LogWriteOptions {
  /** Absolute or relative file path. If provided, write to this exact file. */
  filePath?: string;
  /** Custom base name; final file becomes <base>.<YYYY-MM-DD>.log if filePath is not provided */
  fileBaseName?: string;
}

export class CheckpointTracker {
  private readonly startTimeMs: number;
  private readonly checkpoints: Record<string, number> = {};

  constructor() {
    this.startTimeMs = performance.now();
  }

  addCheckpoint(key: string): void {
    const now = performance.now();
    this.checkpoints[key] = Math.round(now - this.startTimeMs);
  }

  toLogDetails(): Record<string, number> {
    return { ...this.checkpoints };
  }
}

@Injectable()
export class CustomLoggerService implements LoggerService {
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

  // Minimal API: build structured entry then write

  private buildLogEntry(level: LogLevel, message: any, context?: LogContext & { trace?: string }) {
    const environment = this.configService.get('app.environment') || this.configService.get('NODE_ENV') || process.env.NODE_ENV || 'development';
    const appName = this.configService.get('app.name') || 'NestJS App';
    const appVersion = this.configService.get('app.version') || '1.0.0';
    return {
      timestamp: new Date().toISOString(),
      level: level.toUpperCase(),
      message,
      context: context?.context || 'Application',
      account: {
        userId: context?.userId,
        username: context?.username,
      },
      api: {
        method: context?.method,
        url: context?.url,
        requestId: context?.requestId,
      },
      device: {
        ip: context?.ip,
        userAgent: context?.userAgent,
      },
      server: {
        hostname: os.hostname(),
        pid: process.pid,
        environment,
        appName,
        appVersion,
      },
      trace: context?.trace,
      extra: context?.extra || {},
    };
  }

  private extractErrorInfo(message: any, trace?: string): { errorMessage?: string; stackTrace?: string } | undefined {
    if (message instanceof Error) {
      return {
        errorMessage: message.message,
        stackTrace: message.stack,
      };
    }
    if (trace) {
      return {
        errorMessage: typeof message === 'string' ? message : undefined,
        stackTrace: trace,
      };
    }
    return undefined;
  }

  private writeJsonToFiles(level: LogLevel, entry: any, options?: LogWriteOptions): void {
    const line = JSON.stringify(entry);
    const date = new Date().toISOString().slice(0, 10); // YYYY-MM-DD

    if (options?.filePath) {
      const dir = path.dirname(options.filePath);
      if (!fs.existsSync(dir)) {
        fs.mkdirSync(dir, { recursive: true });
      }
      fs.appendFileSync(options.filePath, line + '\n', { encoding: 'utf8' });
      return;
    }

    const base = options?.fileBaseName;
    if (base) {
      const customPath = path.join(this.logDirectory, `${base}.${date}.log`);
      fs.appendFileSync(customPath, line + '\n', { encoding: 'utf8' });
    }

    const levelFilePath = path.join(this.logDirectory, `${level}.${date}.log`);
    fs.appendFileSync(levelFilePath, line + '\n', { encoding: 'utf8' });

    const appDailyPath = path.join(this.logDirectory, `app.${date}.log`);
    fs.appendFileSync(appDailyPath, line + '\n', { encoding: 'utf8' });
  }

  // ---

  /**
   * Generic structured log with metadata and optional custom file path.
   */
  public write(level: LogLevel, message: any, context?: LogContext, options?: LogWriteOptions): void {
    const entry = this.buildLogEntry(level, message, context);
    this.writeJsonToFiles(level, entry, options);
  }

  /**
   * Create a new checkpoint tracker to record step timings.
   */
  public createTracker(): CheckpointTracker {
    return new CheckpointTracker();
  }

  log(message: any, context?: LogContext, options?: LogWriteOptions): void {
    const entry = this.buildLogEntry('log', message, context);
    this.writeJsonToFiles('log', entry, options);
  }

  error(message: any, trace?: string, context?: LogContext, options?: LogWriteOptions): void {
    const contextWithTrace = { ...context, trace };
    const entry = this.buildLogEntry('error', message, contextWithTrace);
    const errInfo = this.extractErrorInfo(message, trace);
    if (errInfo) {
      entry.extra = { ...(entry.extra || {}), error: errInfo };
    }
    this.writeJsonToFiles('error', entry, options);
  }

  warn(message: any, context?: LogContext, options?: LogWriteOptions): void {
    const entry = this.buildLogEntry('warn', message, context);
    this.writeJsonToFiles('warn', entry, options);
  }

  debug(message: any, context?: LogContext, options?: LogWriteOptions): void {
    // no-op per requirement to remove debug logs
  }

  verbose(message: any, context?: LogContext, options?: LogWriteOptions): void {
    // no-op per requirement to remove verbose logs
  }

  // ---
}
