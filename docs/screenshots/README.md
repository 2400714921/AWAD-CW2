# Screenshots

Ten pictures of the working application. Start from a freshly seeded database:

```bash
php artisan migrate:fresh --seed
php artisan serve
```

Sign in at <http://localhost:8000/login> with the account named for each row — every seeded account uses
the password `password` — and keep the address bar inside the picture so the marker can see which page it
came from. Save each file in this folder under the exact name listed, then the README (section 9) picks
them up automatically.

| File | Sign in as | Address | What must be visible |
|---|---|---|---|
| `administrator-desk.png` | `registrar@makerere.ac.ug` | `/administrator` | the heading *Dashboard — Administrator desk*, the four counts (students, courses, live registrations, accounts), the sidebar showing the administrator's links, and the sidebar foot naming the signed-in account |
| `administrator-students.png` | `registrar@makerere.ac.ug` | `/students` | all eight students with their admission numbers, programmes and years of study, plus the Edit and Delete links that only the administrator sees |
| `administrator-courses.png` | `registrar@makerere.ac.ug` | `/courses` | the heading *All courses* and the six courses with codes, titles, credit units, semester and teaching lecturer |
| `administrator-registrations.png` | `registrar@makerere.ac.ug` | `/registrations` | the twenty registrations with the student, the course, the status and the `registered_at` date from the pivot table |
| `form-validation-errors.png` | `registrar@makerere.ac.ug` | `/students/create` | the red messages after submitting an admission number that is already on the register (`2400709637`), an `@`-less email, a year of study of `9` and a date of birth in the future — this is the marker's check for validated form data |
| `lecturer-assigned-courses.png` | `g.asingya@makerere.ac.ug` | `/courses` | the heading *Courses assigned to you* with only the two courses that lecturer teaches, and the *Overview* link in the sidebar pointing at the lecturer desk |
| `student-desk.png` | `2400709637@student.makerere.ac.ug` | `/student` | the heading *Dashboard — My studies*, the three figures (your registrations, courses still open, your programme) and no administrator or lecturer link |
| `student-registration-form.png` | `2400709637@student.makerere.ac.ug` | `/registrations/create` | the course list limited to the courses she is not registered for yet, and the student fixed to her own record |
| `permission-refused.png` | `2400709637@student.makerere.ac.ug` | `/administrator` typed into the bar | the 403 response, which shows the permission middleware refusing a signed-in student rather than showing an empty page |
| `navigation-collapsed.png` | `2400709637@student.makerere.ac.ug` | `/dashboard` | the narrow (mobile) layout with the hamburger menu open, listing only Overview, Students, Courses, Registrations and *Register for a course* |

For the last one, shrink the browser window below the `lg` breakpoint (1024 px) or use the device toolbar
in the browser's developer tools, and capture the drawer rather than the sidebar. Every other picture
should be taken wider than 1024 px, where the left sidebar with the signed-in name and the Log Out link is
visible.

The three pictures that carry the most marks are `form-validation-errors.png` (question 4's validation
and question 5's form check), `permission-refused.png` (question 3's roles) and
`navigation-collapsed.png` (question 5's permission-aware navigation). If only four are wanted, take
those plus `administrator-desk.png`.
