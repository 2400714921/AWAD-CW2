# Contribution Report

**BUC3131 Advanced Web Application Development — Coursework Two 2026/2027, Student Management System.**
One repository (<https://github.com/2400714921/AWAD-CW2>), one feature branch per member, every piece
joined through a pull request. `main` holds the stable integration, `develop` holds the coursework as the
six questions land.

Every member committed with their own GitHub name and email, so the history attributes the work without
this report. The counts can be checked with:

```bash
git shortlog -se develop            # commits per author
git log --oneline --merges develop  # the pull requests that reached develop
```

Merge commits are attributed to the account that pressed Merge on GitHub, not to the author of the work.

## 1. The members and the questions

| Member | Full name and admission number | Commit identity | GitHub account | Question | Marks |
|---|---|---|---|---|---|
| 1 | Byonga Shadia Patience — 2400714921 | `Byonga Shadia Patience <2400714921@mubs.ac.ug>` | the repository account | Question 2 and Question 6 | 4 + 3 |
| 2 | Nansikombi Kirsten Angel — 2400709637 | `NANSIKOMBI Kirsten Angel <angelkirsten05@gmail.com>` | to confirm | Question 1 | 10 |
| 3 | Nakimbugwe Daphne — 2400708517 | `nakimbugwedaphine49-del <nakimbugwedaphine49@gmail.com>` | `nakimbugwedaphine49-del` | Question 3 | 6 |
| 4 | Munywanyi Muniirah — 2400707324 | `Munywanyi Muniirah <2400707324@mubs.ac.ug>` | to confirm | Question 4 | 12 |
| 5 | Ntege Janat — 2400710338 | `NtegeJanat <j33198464@gmail.com>` | `NtegeJanat` | Question 5 | 5 |
| — | Sixth member | no commits in this repository | — | individual presentation | 10 each |

Names and admission numbers are the ones used for the demonstration data in
`database/seeders/DemoDataSeeder.php`, which seeded one account per member so the group could sign in as
each other's records while testing. **Check them against the cover page of the report before
submission.** Five members wrote code here; if a sixth member is on the group, their share is the
individual presentation, which produces no commits, and this table should be corrected to name them.

## 2. Pull requests as they were merged

| Pull request | Branch | Question | Merged into | Merge commit |
|---|---|---|---|---|
| #1 | `feature/q1-schema-and-foreign-keys` | 1 — schema | `main` | `ab4e388` |
| #2 | `feature/q3-authentication-and-roles` | 3 — authentication and roles | `main` | `cd26b95` |
| #3 | `feature/q3-authentication-and-roles` | 3, re-integrated with the working branch | `develop` | `a45893b` |
| #4 | `feature/q4-core-crud-flows` | 4 — CRUD | `develop` | `3a85075` |
| #5 | `feature/q2-factories-and-seeders` | 2 — factories and seeders | `develop` | `9c99129` |
| #6 | `feature/q5-views-and-components` | 5 — views and navigation | `develop` | `36058be` |
| #7 (this work) | `feature/q6-reporting-and-docs` | 6 — documentation | `develop`, then `main` | pending |

The order was not the question order. Question 1 had to be merged first because everything else writes
against those tables; question 3 came next because the other members needed the roles to guard their
screens; question 4 was the largest piece and was merged before 2 and 5 so that the seeders and the
layout could be tested against real CRUD flows. Question 2 was merged before question 5 because the
navigation test needed the seeded accounts to sign in as.

## 3. What each member built

### Member 1 — Byonga Shadia Patience (Questions 2 and 6)

**Branch `feature/q2-factories-and-seeders`, pull request #5 — Question 2 (4 marks).**

- `database/factories/` — `StudentFactory`, `LecturerFactory`, `CourseFactory`,
  `CourseRegistrationFactory`, and the extended `UserFactory` with `administrator()`, `lecturer()` and
  `student()` states that attach the role as the account is created. The student factory generates
  admission numbers in the `2400######` shape and a `withAccount()` state that links a real login.
- `database/seeders/DemoDataSeeder.php` — twelve accounts (one administrator, three lecturers, eight
  students), three lecturer records, six courses, eight students and twenty registrations, all written
  with `updateOrCreate` against the column the schema already makes unique. This is the part that answers
  Question 2d: `php artisan db:seed` can be run any number of times and the row counts do not move.
- `database/seeders/DatabaseSeeder.php` — calls `RolePermissionSeeder` then `DemoDataSeeder`, in the
  order that makes the role assignments resolve.
- `tests/Feature/FactoriesAndSeedersTest.php` — 13 tests: at least five students and four courses
  seeded (2a), the accounts and roles present (2b), registrations visible from both sides with their
  pivot data (2c), a snapshot of eight tables taken, seeded again and compared (2d), plus the factory
  states and bulk uniqueness.

**Branch `feature/q6-reporting-and-docs` — Question 6 (3 marks).**

- `README.md` — installation, database setup, the roles and permissions matrix, sample logins, the
  entity-relationship diagram and column tables, the screen list, how to run the tests, and the
  screenshot index.
- `CONTRIBUTIONS.md` — this report.
- `docs/screenshots/README.md` — the account, page and required content for each screenshot in the
  submission.
- One functional fix found while documenting the roles for section 4 of the README: an account created
  through `/register` was left with no role at all, so it reached a desk that offered it nothing and
  could not register for a course. `app/Http/Controllers/Auth/RegisteredUserController.php` now gives
  every new account the `student` role, and `tests/Feature/Auth/RegistrationTest.php` was extended from
  two tests to four to prove the role lands, that the account reaches the student desk and the course
  list, that it is refused the administrator's page, and that the register tells it when no student
  record is linked yet.

### Member 2 — Nansikombi Kirsten Angel (Question 1)

**Branch `feature/q1-schema-and-foreign-keys`, pull request #1 — Question 1 (10 marks).**

- Four migrations: `students` (unique `student_number` and `email`, `programme`, `year_of_study`,
  nullable `date_of_birth`), `lecturers` (unique `staff_number`, `department`), `courses` (unique
  `course_code`, `credits`, `semester`, nullable `description`) and `course_registrations`
  (`status`, `registered_at` and the unique pair on `student_id`, `course_id`).
- The delete rules the coursework asks for: `user_id` on `students` and `lecturers` is nullable and set
  to null when the login is deleted, `lecturer_id` on `courses` cascades with the lecturer, and both
  sides of a registration cascade with the student or the course.
- `app/Models/{Student,Lecturer,Course,CourseRegistration}.php` with `belongsTo`, `hasMany` and
  `belongsToMany` (explicit pivot table plus `withPivot('status', 'registered_at')` and
  `withTimestamps()`), the `casts()` method style, and the `full_name` accessor used across the screens.
- `tests/Feature/DatabaseRelationshipsTest.php` — 7 tests that read every relationship from both sides
  and show a duplicate registration failing on the unique index.

### Member 3 — Nakimbugwe Daphne (Question 3)

**Branch `feature/q3-authentication-and-roles`, pull requests #2 and #3 — Question 3 (6 marks).**

- The fresh Laravel application itself (the first commit on `main`) and the Breeze Blade stack: login,
  registration, password reset, password confirmation, email verification and profile screens.
- `spatie/laravel-permission` installed and configured, `HasRoles` on `app/Models/User.php`, and the
  `create_permission_tables` migration.
- `database/seeders/RolePermissionSeeder.php` — the three roles and the nine permissions listed in the
  README, `findOrCreate` so a re-run does not duplicate them, and the cache forgotten before seeding.
- `app/Policies/{Student,Course,CourseRegistration}Policy.php` — record-level decisions that the
  permissions cannot make: a student sees only their own record and registrations, a lecturer sees the
  courses they teach and the students on them, and only the administrator writes.
- The `role` and `permission` middleware aliases in `bootstrap/app.php`, the guards on the routes in
  `routes/web.php`, and `config/permission.php`.
- `tests/Feature/RoleAndPermissionTest.php` (8) and `tests/Feature/RecordOwnershipTest.php` (7), which
  prove that a signed-in student opening another student's record is refused even though they hold the
  read permission.

### Member 4 — Munywanyi Muniirah (Question 4)

**Branch `feature/q4-core-crud-flows`, pull request #4 — Question 4 (12 marks).**

- `StudentController`, `CourseController` and `RegistrationController`: the full index, create, store,
  show, edit, update and destroy flows, each list narrowed by role before it is rendered and each write
  guarded by the policies.
- Five form requests — `StoreStudentRequest`, `UpdateStudentRequest`, `StoreCourseRequest`,
  `UpdateCourseRequest`, `StoreRegistrationRequest` — with `Rule::unique(...)->ignore()` for the admission
  number, email and course code, `between:1,6` for the year of study, `between:1,12` for credit units,
  `exists` for the lecturer, a duplicate-registration rule scoped to the student, and a plain-language
  `messages()` block for every rule.
- `prepareForValidation()` on `StoreRegistrationRequest` decides whose registration is being written: an
  administrator may name any student, a student can only ever write themselves.
- Thirteen Blade views under `resources/views/{students,courses,registrations}/`, including the shared
  `fields` partials used by both create and edit, and `resources/views/components/alerts.blade.php` for
  the flash messages.
- Deletion guards that refuse to strand data and name the records to clear first, and the success
  messages quoted in the README.
- `tests/Feature/StudentCrudTest.php` (14), `CourseCrudTest.php` (14) and
  `CourseRegistrationTest.php` (13) — happy paths, every validation message, per-role list filtering,
  form-spoofing rejected, and `assertForbidden` for cross-role writes.

### Member 5 — Ntege Janat (Question 5)

**Branch `feature/q5-views-and-components`, pull request #6 — Question 5 (5 marks).**

- `resources/views/layouts/app.blade.php` — the reusable layout with the four parts the question asks
  for: a header, a navigation bar, a content section that receives the page through the slot, and a
  footer.
- `resources/views/components/nav-links.blade.php` — one permission-aware link list that fills both the
  desktop bar and the mobile drawer, so a link to a page the account cannot open is never rendered.
- `resources/views/components/footer.blade.php` — the coursework attribution and the role the account is
  acting as.
- `app/Http/Controllers/DashboardController.php` and `routes/web.php` — `/dashboard`, `/administrator`,
  `/lecturer` and `/student` resolve to a desk that counts only the figures that role may read,
  replacing the placeholder routes that pointed every role at the same static view.
- `resources/views/dashboard.blade.php` — the role title, the figure cards and an actions panel gated
  with `@can`, so the create buttons only appear for the role that may press them.
- `tests/Feature/LayoutAndNavigationTest.php` — 9 tests that check the four layout parts on every page,
  the per-role desk titles, figures, links and actions, that the desktop bar and the drawer are filled
  from the same list, that a guest page carries neither, and that an account with no role still reaches
  a desk.

## 4. Testing across the whole system

The suite belongs to all five members; each question's branch added the tests for its own area. Current
state of the branch this report is written on, `feature/q6-reporting-and-docs`: **112 tests, 450
assertions, all passing** — run once against the in-memory SQLite database in `phpunit.xml` and once
against the MariaDB database `awad_cw2` that the application actually uses:

```bash
php artisan test                                      # 112 passed, SQLite in memory
DB_CONNECTION=mysql DB_DATABASE=awad_cw2 php artisan test
```

The same pass checked the seeder idempotency on the real engine — `migrate:fresh --seed` followed by
`db:seed` twice leaves users 12, students 8, lecturers 3, courses 6, registrations 20, model_has_roles 12
and permissions 9 — and confirmed that the front-end assets compile (`npm run build`) and that the
permission-aware navigation renders from a live server rather than only from the test doubles.

Two bugs were found by running the tests rather than by reading the code:

- `StoreRegistrationRequest` typed its uniqueness closure as an Eloquent builder, but Laravel's presence
  verifier hands it a query builder, so a duplicate registration raised a 500 instead of a validation
  message. Fixed in the Question 4 branch.
- An account created through `/register` was left with no role, so it could not register for a course.
  Fixed in the Question 6 branch, as described above.

## 5. Individual presentations

Ten marks per member. Each member presents the question they built, in the order of the table in section
1: the schema and the delete rules (member 2), the accounts, roles and refusal of another student's
record (member 3), the CRUD screens with their validation messages and deletion guards (member 4), the
seeders and the second run that adds nothing (member 1), the layout and the navigation that hides what
the role may not open (member 5). Everyone demonstrates on the same installed copy, signed in with the
accounts in section 5 of the README.
