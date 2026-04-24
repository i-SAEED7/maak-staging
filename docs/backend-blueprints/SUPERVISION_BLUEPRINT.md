# Blueprint: Supervision Module

## الهدف

إدارة الزيارات الإشرافية والتقييمات والتوصيات.

## Endpoints

- `GET /supervisor-visits`
- `POST /supervisor-visits`
- `GET /supervisor-visits/{id}`
- `PUT /supervisor-visits/{id}`
- `POST /supervisor-visits/{id}/complete`
- `POST /supervisor-visits/{id}/recommendations`
- `PATCH /supervisor-visit-recommendations/{id}`
- `GET /supervision-templates`

## الطبقات

- `SupervisorVisitController`
- `StoreSupervisorVisitRequest`
- `UpdateSupervisorVisitRequest`
- `CompleteSupervisorVisitRequest`
- `StoreVisitRecommendationRequest`
- `SupervisionService`
- `SupervisorVisitPolicy`

## قواعد

- المشرف لا ينشئ زيارة خارج مدارسه
- إنهاء الزيارة يتطلب تعبئة عناصر التقييم الأساسية
