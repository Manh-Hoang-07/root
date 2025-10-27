# Installation Guide

## Prerequisites
- Node.js 18+ 
- npm or yarn
- SQLite (included with better-sqlite3)

## Quick Start

### 1. Install Dependencies
```bash
cd nestjs-backend
npm install
```

### 2. Set Up Environment
```bash
# Copy environment template
cp env.template .env

# Edit .env file with your configuration
```

### 3. Create Database Directory
```bash
mkdir -p database
```

### 4. Run Application
```bash
# Development mode
npm run start:dev

# Production mode
npm run build
npm run start:prod
```

The application will be available at `http://localhost:3000`

## Project Structure

```
nestjs-backend/
├── src/
│   ├── modules/          # Feature modules (auth, product, order, etc.)
│   ├── common/          # Shared utilities
│   ├── enums/           # TypeScript enums
│   ├── app.module.ts    # Root module
│   └── main.ts          # Entry point
├── database/            # SQLite database file
├── storage/             # File uploads
└── dist/                # Compiled output
```

## Development

### Available Scripts

```bash
# Start development server
npm run start:dev

# Build project
npm run build

# Start production server
npm run start:prod

# Run tests
npm run test

# Format code
npm run format

# Lint code
npm run lint
```

## API Endpoints

### Public Endpoints
- `POST /api/login` - Login
- `POST /api/register` - Register
- `GET /api/products` - List products
- `GET /api/products/:id` - Get product

### User Endpoints (Auth Required)
- `GET /api/me` - Get current user
- `POST /api/logout` - Logout
- `GET /api/orders` - User orders

### Admin Endpoints (Admin Role Required)
- `GET /api/admin/products` - List products
- `POST /api/admin/products` - Create product
- `PUT /api/admin/products/:id` - Update product
- `DELETE /api/admin/products/:id` - Delete product

## Next Steps

1. Complete service implementations for each module
2. Implement controllers for all endpoints
3. Add authentication guards
4. Create DTOs with validation
5. Implement remaining features

See `CONVERSION_NOTES.md` for detailed conversion progress.

