import { INestApplication } from '@nestjs/common';
import helmet from 'helmet';
import hpp from 'hpp';
import compression from 'compression';
import * as bodyParser from 'body-parser';

export function applyHttpHardening(app: INestApplication, payloadLimit = '1mb') {
  app.use(helmet());
  app.use(hpp());
  app.use(bodyParser.json({ limit: payloadLimit }));
  app.use(bodyParser.urlencoded({ limit: payloadLimit, extended: true }));
  app.use(compression());
}


