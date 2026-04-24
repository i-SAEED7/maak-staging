# خريطة العلاقات بين النماذج

## Core Access

- `Role` 1..* `User`
- `Role` *..* `Permission`
- `User` *..* `School` عبر `UserSchoolAssignment`

## Student Domain

- `School` 1..* `Student`
- `Student` *..* `User` عبر `StudentGuardian`
- `Student` *..* `User` عبر `TeacherStudentAssignment`
- `Student` 1..* `StudentReport`

## IEP Domain

- `Student` 1..* `IepPlan`
- `IepPlan` 1..* `IepPlanVersion`
- `IepPlan` 1..* `IepPlanGoal`
- `IepPlan` 1..* `IepPlanComment`
- `IepPlan` 1..* `IepPlanApproval`
- `IepTemplate` يرتبط اختياريًا بـ `DisabilityCategory` و`EducationProgram`

## Portfolio and Files

- `Portfolio` 1..* `PortfolioItem`
- `PortfolioItem` قد يرتبط بـ `File`
- `File` 1..* `FileAccessToken`

## Supervision

- `SupervisorVisit` 1..* `SupervisorVisitItem`
- `SupervisorVisit` 1..* `SupervisorVisitRecommendation`
- `SupervisionTemplate` يعرّف schema لعناصر التقييم

## Communication

- `Message` 1..* `MessageRecipient`
- `User` 1..* `Notification`
- `AuditLog` يرتبط اختياريًا بأي كيان عبر `target_type/target_id`
