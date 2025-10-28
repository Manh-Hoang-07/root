declare global {
  namespace NodeJS {
    interface ProcessEnv {
      NODE_ENV: 'development' | 'production' | 'test';
      PORT: string;
      
      // Database
      DB_TYPE: string;
      DB_HOST?: string;
      DB_PORT?: string;
      DB_USERNAME?: string;
      DB_PASSWORD?: string;
      DB_DATABASE?: string;
      DB_SYNCHRONIZE?: string;
      DB_LOGGING?: string;
      
      // JWT
      JWT_SECRET: string;
      JWT_EXPIRES_IN?: string;
      JWT_REFRESH_SECRET?: string;
      JWT_REFRESH_EXPIRES_IN?: string;
      
      // Mail
      MAIL_HOST?: string;
      MAIL_PORT?: string;
      MAIL_SECURE?: string;
      MAIL_USERNAME?: string;
      MAIL_PASSWORD?: string;
      MAIL_FROM?: string;
      
      // App
      APP_NAME?: string;
      APP_URL?: string;
    }
  }
}

export {};
