# ربط Requests وResources وServices

## Auth

- `LoginRequest` -> `AuthService@login` -> `UserResource`

## Schools

- `StoreSchoolRequest` -> `SchoolService@create` -> `SchoolResource`
- `UpdateSchoolRequest` -> `SchoolService@update` -> `SchoolResource`

## Users

- `StoreUserRequest` -> `UserService@create` -> `UserResource`
- `UpdateUserRequest` -> `UserService@update` -> `UserResource`

## Students

- `StoreStudentRequest` -> `StudentService@create` -> `StudentResource`
- `UpdateStudentRequest` -> `StudentService@update` -> `StudentResource`

## IEP

- `StoreIepPlanRequest` -> `IepPlanService@createDraft` -> `IepPlanResource`
- `UpdateIepPlanRequest` -> `IepPlanService@updateDraft` -> `IepPlanResource`
- `TransitionIepPlanRequest` -> `IepWorkflowService@transition` -> `IepPlanResource`

## Files

- `UploadFileRequest` -> `FileUploadService@upload` -> file payload

## Supervision

- `StoreSupervisorVisitRequest` -> `SupervisionService@createVisit` -> visit payload
