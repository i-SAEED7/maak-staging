# خريطة الحالة في الواجهة

## Stores

- `authStore`
  - user
  - token state
  - permissions
  - current role

- `uiStore`
  - sidebarOpen
  - locale

- `notificationsStore`
  - unreadCount

- `messagesStore`
  - activeThreadKey

## Hooks

- `useAuth`
- `useStudents`
- `useIepPlans`
- `useNotifications`
- `useMessages`
- `useReports`

## قاعدة عامة

- البيانات البعيدة تدار عبر query hooks
- الحالة العالمية الخفيفة تدار عبر stores
- لا توضع صلاحيات المستخدم داخل مكونات متفرقة؛ تعتمد من auth store فقط
