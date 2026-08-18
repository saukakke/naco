# NACO Cadet Management Portal — Domain Specification

## Organisational hierarchy

National → State → LGA → Ward

## Core entities

- **Cadet** — the central personnel record. Every active cadet belongs to exactly one of Units A–F and has one current rank.
- **Unit** — Unit A, B, C, D, E or F.
- **Rank** — an ordered rank belonging to one of four categories: Other Ranks, Junior Officers, Senior Officers and Superior Officers.
- **Post** — an organisational appointment at National, State, LGA or Ward level. Post and rank are independent.
- **Course** — training completed or attended by cadets. Initial examples: Drill, BF, DS, Islamic and Admin.
- **Course enrolment/result** — joins cadets to courses and records completion/qualification.
- **Warrant** — validates instructor authority and must be obtained through qualifying course training.
- **Instructor** — a cadet who has the required valid warrant; instructor level is Junior or Senior.
- **Promotion** — a rank change to a higher ordered rank and produces an official promotion document after approval.
- **Demotion** — a rank change to a lower ordered rank and preserves the previous rank in history.

## Rank order

| Order | Rank | Category |
|---:|---|---|
| 1 | Private | Other Ranks |
| 2 | Copral | Other Ranks |
| 3 | Sergeant | Other Ranks |
| 4 | Staff Sergeant | Other Ranks |
| 5 | Senior Staff Sergeant | Other Ranks |
| 6 | Warrant Officer 2 | Other Ranks |
| 7 | Warrant Officer 1 | Other Ranks |
| 8 | Second Lieutenant | Junior Officers |
| 9 | Lieutenant | Junior Officers |
| 10 | Captain | Junior Officers |
| 11 | Master | Senior Officers |
| 12 | Senior Master | Senior Officers |
| 13 | Right Comrade | Senior Officers |
| 14 | Engineer | Superior Officers |
| 15 | Chief Engineer | Superior Officers |
| 16 | Rear Marshal | Superior Officers |
| 17 | Cadet Marshal | Superior Officers |

## Post structure

### National
- General Officer
- Chief Instructor

### State
- State Controller
- Deputy State Controller
- National Medical Director
- Auditor
- Secretary
- National Parade Commander
- National Intelligent Director
- National Provost Marshal
- Unit Sergeant Major

### LGA
- Chairman Self-Reliance

### Ward
- HCS

The State-level classification above follows the organisational level supplied for the portal, even where a post title contains the word “National”.

## Business rules

1. Every active cadet belongs to exactly one Unit A–F.
2. A cadet can simultaneously be an instructor.
3. An instructor must have a valid warrant obtained through course training.
4. Instructor levels are Junior Instructor and Senior Instructor.
5. A rank belongs to exactly one rank category and has a unique numeric order.
6. Promotion is valid only when the new rank order is greater than the current rank order.
7. Demotion is valid only when the new rank order is lower than the current rank order.
8. A same-rank change is neither a promotion nor a demotion.
9. Rank changes preserve historical records; they never overwrite history.
10. An approved promotion produces an official promotion document with a unique reference.
11. Post assignment is independent of rank and should maintain appointment history.
12. Course participation is many-to-many: a cadet can attend many courses and a course can have many cadets.
13. A warrant references the course/training through which it was obtained.

## Planned portal modules

Dashboard, Cadets, Units, Ranks, Posts, Courses, Instructors, Warrants, Promotions, Demotions, Documents, Users/Roles/Permissions, Notifications and Audit Logs.

## Pending policy decisions

The production backend should confirm: exact qualifying courses for each warrant type, warrant expiry rules, promotion/demotion approval authorities, whether demotions generate formal documents, whether each post has one or multiple active holders, and the exact jurisdiction required for each post assignment.
