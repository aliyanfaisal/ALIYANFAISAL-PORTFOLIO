# Research Portfolio: To Do

Goal: a portfolio that supports my applications to research-oriented Master's programs in AI and ML.
Title: **AI Engineer & Aspiring Researcher**.

## Done
- [x] Title changed from "Software engineer · MSc applicant in AI" to "AI Engineer & Aspiring Researcher" (`config/research.php`, home meta description, structured-data `jobTitle`).
- [x] Master's applications sentence is in the summary.
- [x] Project dates removed from the homepage, projects index and project pages. The `period` values still sit in `config/research.php` (unused; delete if not wanted).
- [x] Reverted the accidental edits made in the main site (aliyanfaisal.com).

## 1. Fix first
- [ ] `.env` line 30 is a stray `FB_POSG:  -123`. It breaks `php artisan test` and `view:clear`. Delete the line.
- [ ] Run the tests (earlier notes say 8 passed; not re-run since the title and date changes).
- [ ] Run the site locally and check every page in a browser: home, research, projects (index and each project), reports, open source, CV, contact. Nothing has been seen rendered yet.

## 2. Content that is missing
- [ ] **CV PDF.** `public/cv/aliyan-faisal-cv.pdf` does not exist, so the CV page says "coming soon". Build it from `CV/aliyan-faisal-cv.tex` in the main repo, and make the CV title match "AI Engineer & Aspiring Researcher".
- [ ] **Reports.** All four `reports` entries have `'file' => null`.
  - InvoiceInspect evaluation results are marked available but have no PDF. Write and add it, with real numbers.
  - Cuelara, ManaJet and Provincial data dashboard say "In preparation". Add real PDFs or hide them until ready.
- [ ] **Evidence of research ability.** No papers or publications, so one finished evaluation write-up is the strongest proof. Prioritise the InvoiceInspect report.
- [ ] Project pages with "Coming soon." sections (`projects/show.blade.php`): fill or remove.
- [ ] Optional: a short page or section on target programs, research questions and a proposed direction, for statement-of-purpose support.

## 3. Check for accuracy
- [ ] Read all of `config/research.php` for wrong facts, as the project dates were wrong:
  - CGPA (3.80 / 4.00)
  - role titles and employers
  - experience years ("5+ years", 2020 to present)
  - project descriptions, claims and status
  - open source details (Notemind version, license)
- [ ] Do not claim research or publications that do not exist.
- [ ] Rewrite any remaining product-pitch phrasing for a professor audience.

## 4. Launch
- [ ] README says "No deployment workflow is configured yet", but `.github/workflows/deploy.yml` and `deploy/` exist. Update the README.
- [ ] One-time cPanel setup for research.aliyanfaisal.com (see `deploy/README.md`):
  - subdomain with no redirects
  - PHP 8.3 or newer
  - AutoSSL certificate
  - server `.env` with an `APP_KEY` generated locally (`php artisan key:generate --show`)
- [ ] First deploy, then check the site and `/sitemap.xml`.
- [ ] Commit the work. The `deploy/` edits, the title change and the date removal are uncommitted. The whole site is a single commit and is not on a remote yet.
