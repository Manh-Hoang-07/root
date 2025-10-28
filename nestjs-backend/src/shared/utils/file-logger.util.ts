import { appendFileSync, existsSync, mkdirSync, readFileSync } from 'fs';
import { join, resolve, dirname } from 'path';

// Try multiple paths to find a reliable logs directory under the NestJS project
function getProjectRoot(start: string): string | null {
  let current = start;
  const visited = new Set<string>();
  for (let i = 0; i < 8; i++) {
    if (visited.has(current)) break;
    visited.add(current);
    try {
      const pkgPath = resolve(current, 'package.json');
      if (existsSync(pkgPath)) {
        // If this is the NestJS backend package
        const pkg = JSON.parse(readFileSync(pkgPath, 'utf8')) as any;
        if (pkg?.name === 'nestjs-backend' || existsSync(resolve(current, 'src'))) {
          return current;
        }
      }
    } catch {}
    const parent = dirname(current);
    if (!parent || parent === current) break;
    current = parent;
  }
  return null;
}

function getLogPath() {
  const fromHere = __dirname;
  const projectRoot = getProjectRoot(fromHere) || process.cwd();
  const logsDir = resolve(projectRoot, 'logs');
  try {
    if (!existsSync(logsDir)) {
      mkdirSync(logsDir, { recursive: true });
    }
  } catch {}
  const logFile = join(logsDir, 'app.log');
  return { logsDir, logFile };
}

export function logToFile(message: string) {
  try {
    const { logsDir, logFile } = getLogPath();
    console.log('[LOGGER DEBUG] Logging to:', logFile);
    console.log('[LOGGER DEBUG] Logs dir:', logsDir);
    console.log('[LOGGER DEBUG] Logs dir exists:', existsSync(logsDir));
    
    if (!existsSync(logsDir)) {
      console.log('[LOGGER DEBUG] Creating logs directory:', logsDir);
      mkdirSync(logsDir, { recursive: true });
    }
    
    const line = `[${new Date().toISOString()}] ${message}\n`;
    appendFileSync(logFile, line, { encoding: 'utf8' });
    console.log(`[LOG FILE] ${message}`);
  } catch (error: any) {
    console.error('[LOG FILE ERROR]', error?.message || error);
    console.error('[LOG FILE ERROR] Stack:', error?.stack);
    // Fallback: always log to console
    console.log(`[LOG CONSOLE] ${message}`);
  }
}


