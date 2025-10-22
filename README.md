<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

<p align="center">
<a href="https://github.com/laravel/framework/actions"><img src="https://github.com/laravel/framework/workflows/tests/badge.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>

## About Laravel

Laravel is a web application framework with expressive, elegant syntax. We believe development must be an enjoyable and creative experience to be truly fulfilling. Laravel takes the pain out of development by easing common tasks used in many web projects, such as:

- [Simple, fast routing engine](https://laravel.com/docs/routing).
- [Powerful dependency injection container](https://laravel.com/docs/container).
- Multiple back-ends for [session](https://laravel.com/docs/session) and [cache](https://laravel.com/docs/cache) storage.
- Expressive, intuitive [database ORM](https://laravel.com/docs/eloquent).
- Database agnostic [schema migrations](https://laravel.com/docs/migrations).
- [Robust background job processing](https://laravel.com/docs/queues).
- [Real-time event broadcasting](https://laravel.com/docs/broadcasting).

Laravel is accessible, powerful, and provides tools required for large, robust applications.

## Learning Laravel

Laravel has the most extensive and thorough [documentation](https://laravel.com/docs) and video tutorial library of all modern web application frameworks, making it a breeze to get started with the framework.

You may also try the [Laravel Bootcamp](https://bootcamp.laravel.com), where you will be guided through building a modern Laravel application from scratch.

If you don't feel like reading, [Laracasts](https://laracasts.com) can help. Laracasts contains thousands of video tutorials on a range of topics including Laravel, modern PHP, unit testing, and JavaScript. Boost your skills by digging into our comprehensive video library.

## Laravel Sponsors

We would like to extend our thanks to the following sponsors for funding Laravel development. If you are interested in becoming a sponsor, please visit the [Laravel Partners program](https://partners.laravel.com).

### Premium Partners

- **[Vehikl](https://vehikl.com)**
- **[Tighten Co.](https://tighten.co)**
- **[Kirschbaum Development Group](https://kirschbaumdevelopment.com)**
- **[64 Robots](https://64robots.com)**
- **[Curotec](https://www.curotec.com/services/technologies/laravel)**
- **[DevSquad](https://devsquad.com/hire-laravel-developers)**
- **[Redberry](https://redberry.international/laravel-development)**
- **[Active Logic](https://activelogic.com)**

## Contributing

Thank you for considering contributing to the Laravel framework! The contribution guide can be found in the [Laravel documentation](https://laravel.com/docs/contributions).

## Code of Conduct

In order to ensure that the Laravel community is welcoming to all, please review and abide by the [Code of Conduct](https://laravel.com/docs/contributions#code-of-conduct).

## Security Vulnerabilities

If you discover a security vulnerability within Laravel, please send an e-mail to Taylor Otwell via [taylor@laravel.com](mailto:taylor@laravel.com). All security vulnerabilities will be promptly addressed.

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).

# Laravel Vue SPA Application

Ứng dụng web hiện đại được xây dựng với Laravel 11 và Vue.js 3, sử dụng kiến trúc SPA (Single Page Application).

## 🚀 Tính năng

- **Frontend**: Vue.js 3 với Composition API
- **Backend**: Laravel 11 với RESTful API
- **Styling**: Tailwind CSS
- **State Management**: Pinia
- **Routing**: Vue Router 4
- **Build Tool**: Vite
- **Database**: MySQL với Eloquent ORM

## 📋 Yêu cầu hệ thống

- PHP >= 8.2
- Composer
- Node.js >= 18
- MySQL >= 8.0

## 🛠️ Cài đặt

### 1. Clone repository
```bash
git clone <repository-url>
cd laravel-vue-app
```

### 2. Cài đặt dependencies
```bash
# Backend dependencies
composer install

# Frontend dependencies
npm install
```

### 3. Cấu hình môi trường
```bash
cp .env.example .env
php artisan key:generate
```

Cập nhật file `.env` với thông tin database:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=laravel_vue_app
DB_USERNAME=root
DB_PASSWORD=
```

### 4. Chạy migrations
```bash
php artisan migrate
```

### 5. Tạo dữ liệu mẫu (tùy chọn)
```bash
php artisan db:seed
```

## 🚀 Chạy ứng dụng

### Development mode
```bash
# Terminal 1: Laravel server
php artisan serve

# Terminal 2: Vite dev server
npm run dev
```

Truy cập: http://localhost:8000

### Production build
```bash
npm run build
```

## 📁 Cấu trúc project

```
resources/
├── js/
│   ├── App.vue              # Component chính
│   ├── app.js               # Entry point
│   ├── views/               # Các trang
│   │   ├── Home.vue         # Trang chủ
│   │   ├── Users.vue        # Quản lý người dùng
│   │   └── About.vue        # Trang giới thiệu
│   ├── components/          # Components tái sử dụng
│   │   ├── UserCard.vue     # Card hiển thị user
│   │   └── LoadingSpinner.vue # Loading component
│   ├── router/              # Vue Router
│   │   └── index.js         # Cấu hình routes
│   └── stores/              # Pinia stores
│       └── userStore.js     # User state management
```

## 🎯 Các trang chính

### 1. Trang chủ (/)
- Hero section với gradient đẹp mắt
- Grid hiển thị tính năng
- Counter demo với Vue reactivity
- Todo list với CRUD operations

### 2. Quản lý người dùng (/users)
- Bảng hiển thị danh sách người dùng
- Modal tạo/sửa người dùng
- Xóa người dùng với confirmation
- Loading states và error handling

### 3. Trang giới thiệu (/about)
- Thông tin về công nghệ sử dụng
- Cấu trúc project
- Hướng dẫn cài đặt

## 🔧 API Endpoints

| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/api/users` | Lấy danh sách người dùng |
| POST | `/api/users` | Tạo người dùng mới |
| PUT | `/api/users/{id}` | Cập nhật người dùng |
| DELETE | `/api/users/{id}` | Xóa người dùng |

## 🎨 Components

### UserCard.vue
- Hiển thị thông tin người dùng dạng card
- Avatar với initials
- Actions: Edit, Delete
- Responsive design

### LoadingSpinner.vue
- Loading spinner với nhiều kích thước
- Customizable text
- Fullscreen mode option

## 📱 Responsive Design

Ứng dụng được thiết kế responsive với Tailwind CSS:
- Mobile-first approach
- Breakpoints: sm, md, lg, xl
- Flexible grid system
- Touch-friendly interactions

## 🔄 State Management

Sử dụng Pinia cho state management:
- User store với CRUD operations
- Loading states
- Error handling
- Reactive data

## 🚀 Deployment

### 1. Build production
```bash
npm run build
```

### 2. Optimize Laravel
```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

### 3. Deploy to server
- Upload files lên server
- Cấu hình web server (Apache/Nginx)
- Set up database
- Chạy migrations

## 🤝 Đóng góp

1. Fork project
2. Tạo feature branch (`git checkout -b feature/AmazingFeature`)
3. Commit changes (`git commit -m 'Add some AmazingFeature'`)
4. Push to branch (`git push origin feature/AmazingFeature`)
5. Tạo Pull Request

## 📄 License

Distributed under the MIT License. See `LICENSE` for more information.

## 📞 Liên hệ

- Email: your-email@example.com
- GitHub: [@yourusername](https://github.com/yourusername)

---

**Lưu ý**: Đây là project demo để học tập. Trong production, hãy đảm bảo:
- Bảo mật API endpoints
- Validation đầy đủ
- Error handling tốt hơn
- Testing coverage
- Performance optimization





- Cách chạy seed của tỉnh thành
# 1. Convert Excel → JSON
php artisan convert:admin-excel storage/app/vietnam.xlsx
# 2. Seed DB
php artisan db:seed --class=AdminUnitsSeeder

