# Blueprint: Notifications and Messaging

## الهدف

إدارة الإشعارات والمراسلات الداخلية.

## Endpoints

- `GET /notifications`
- `POST /notifications/{id}/read`
- `POST /notifications/read-all`
- `GET /messages`
- `GET /messages/thread/{threadKey}`
- `POST /messages`
- `POST /messages/{id}/read`

## الطبقات

- `NotificationController`
- `MessageController`
- `NotificationService`
- `MessagingService`
- `MessagePolicy`

## قواعد

- الإشعارات تقرأ حسب المستخدم الحالي فقط.
- الرسائل تبنى على `thread_key` ثابت وقابل للاسترجاع.
