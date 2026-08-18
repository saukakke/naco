# NACO Domain Layer

The production Laravel implementation should keep business rules out of controllers where possible. Recommended domain services:

- `PromotionService` — validates rank ordering and creates promotion records/documents.
- `DemotionService` — validates rank ordering and creates demotion records.
- `WarrantService` — issues/revokes/verifies instructor warrants.
- `InstructorEligibilityService` — determines whether a cadet has the required valid warrant.
- `PostAssignmentService` — manages appointments and assignment history.
- `CourseCompletionService` — records course completion and qualifications.

Use Form Requests for input validation, Policies for authorization, Events/Listeners for notifications and document generation, and database transactions for all rank/warrant/personnel state changes.
