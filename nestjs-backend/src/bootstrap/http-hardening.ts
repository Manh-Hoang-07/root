import { INestApplication } from '@nestjs/common';
import helmet from 'helmet';
import * as hpp from 'hpp';
import * as compression from 'compression';
import * as bodyParser from 'body-parser';

export function applyHttpHardening(app: INestApplication, payloadLimit = '1mb') {
  // Temporarily comment out to test
  app.use(helmet());
  app.use(hpp());
  app.use(bodyParser.json({ limit: payloadLimit }));
  app.use(bodyParser.urlencoded({ limit: payloadLimit, extended: true }));
  app.use(compression());
}


