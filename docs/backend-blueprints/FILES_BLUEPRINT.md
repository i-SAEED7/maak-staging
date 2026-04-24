# Blueprint: Files Module

## الهدف

إدارة الرفع والتخزين المؤقت والتحميل الآمن للملفات.

## Endpoints

- `POST /files`
- `GET /files/{id}`
- `POST /files/{id}/temporary-link`
- `DELETE /files/{id}`

## الطبقات

- `FileController`
- `UploadFileRequest`
- `CreateTemporaryFileLinkRequest`
- `FileUploadService`
- `TemporaryFileAccessService`
- `FilePolicy`

## قواعد

- حجم الملف حتى 20MB في المرحلة الأولى
- الأسماء التخزينية تعتمد UUID
- الملفات الحساسة تحتاج روابط مؤقتة فقط
