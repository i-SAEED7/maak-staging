# دليل انطلاق المطور

## 1. اقرأ التوثيق الأساسي

- [README.md](../README.md)
- [docs/IMPLEMENTATION_HANDOFF.md](./IMPLEMENTATION_HANDOFF.md)
- [docs/PROJECT_STATUS.md](./PROJECT_STATUS.md)
- [docs/PHASE_1_EXECUTION_SEQUENCE.md](./PHASE_1_EXECUTION_SEQUENCE.md)
- [docs/PHASE_2_EXECUTION_SEQUENCE.md](./PHASE_2_EXECUTION_SEQUENCE.md)

## 2. جهّز الأدوات

- PHP 8.3
- Composer
- Node 20+
- npm
- Docker

## 3. فعّل الخلفية

1. توليد Laravel داخل `backend`
2. نقل ملفات `migration stubs`
3. إعداد `.env`
4. تشغيل migrations + seeders

## 4. فعّل الواجهة

1. توليد React/Vite داخل `frontend`
2. إعداد `.env`
3. تفعيل routing وstores والخدمات الموجودة كبنية

## 5. ابدأ بالتنفيذ حسب الأولوية

1. Auth
2. Schools
3. Users
4. Students
5. IEP
