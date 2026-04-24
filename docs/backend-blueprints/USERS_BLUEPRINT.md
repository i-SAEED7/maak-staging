# Blueprint: Users Module

## الهدف

إنشاء المستخدمين وربطهم بالأدوار والمدارس.

## Endpoints

- `GET /users`
- `POST /users`
- `GET /users/{id}`
- `PUT /users/{id}`
- `PATCH /users/{id}/status`
- `POST /users/{id}/schools`

## الطبقات

- `UserController`
- `StoreUserRequest`
- `UpdateUserRequest`
- `ChangeUserStatusRequest`
- `AssignUserSchoolsRequest`
- `UserService`
- `UserResource`
- `UserPolicy`

## قواعد مهمة

- المدير العام يمكنه إنشاء أي دور.
- مدير المدرسة لا ينشئ `super_admin` أو `supervisor`.
- كلمة المرور الأولية يمكن أن تفرض `must_change_password=true`.
