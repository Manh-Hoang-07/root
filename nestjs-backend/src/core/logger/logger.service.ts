import { Injectable, LoggerService as NestLoggerService } from '@nestjs/common';

@Injectable()
export class Logger implements NestLoggerService {
  private context?: string;

  setContext(context: string) {
    this.context = context;
  }

  log(message: any, context?: string) {
    context = context || this.context;
    console.log(`[${context}]`, message);
  }

  error(message: any, trace?: string, context?: string) {
    context = context || this.context;
    console.error(`[${context}]`, message, trace);
  }

  warn(message: any, context?: string) {
    context = context || this.context;
    console.warn(`[${context}]`, message);
  }

  debug?(message: any, context?: string) {
    context = context || this.context;
    console.debug(`[${context}]`, message);
  }

  verbose?(message: any, context?: string) {
    context = context || this.context;
    console.log(`[${context}]`, message);
  }
}
