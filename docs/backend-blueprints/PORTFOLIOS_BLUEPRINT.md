# Blueprint: Portfolios Module

## الهدف

إدارة ملفات الإنجاز للمعلمين والطلاب.

## Endpoints

- `GET /portfolios`
- `POST /portfolios`
- `GET /portfolios/{id}`
- `POST /portfolios/{id}/items`
- `PUT /portfolio-items/{id}`
- `DELETE /portfolio-items/{id}`

## الطبقات

- `PortfolioController`
- `StorePortfolioRequest`
- `PortfolioService`
- `PortfolioResource`
- `PortfolioPolicy`

## قواعد

- يمكن أن يكون الملف مرتبطًا بمعلم أو طالب.
- العناصر قد تحتوي ملفات مرفقة أو مجرد وصف.
