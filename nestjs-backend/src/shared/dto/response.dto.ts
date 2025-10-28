export class ResponseDto<T = any> {
  statusCode: number;
  message: string;
  data: T;
  timestamp: string;

  constructor(data: T, message: string = 'Success', statusCode: number = 200) {
    this.statusCode = statusCode;
    this.message = message;
    this.data = data;
    this.timestamp = new Date().toISOString();
  }
}
