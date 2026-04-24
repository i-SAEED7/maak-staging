# التثبيت فور توفر الأدوات

## الخلفية

```bash
cd backend
composer create-project laravel/laravel . "^11.0"
composer require laravel/sanctum spatie/laravel-permission
composer require spatie/laravel-medialibrary maatwebsite/excel
composer require barryvdh/laravel-dompdf
```

## الواجهة

```bash
cd frontend
npm create vite@latest . -- --template react-ts
npm install react-router-dom @tanstack/react-query zustand
npm install axios react-hook-form @hookform/resolvers zod
npm install tailwindcss postcss autoprefixer
```

## بعد التوليد

1. انقل أو طبّق الـ stubs الحالية
2. أنشئ المايغريشنز الفعلية
3. فعّل seeders
4. شغّل الاختبارات
