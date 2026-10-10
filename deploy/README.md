# Deploying research.aliyanfaisal.com

Deployment is the same GitHub Actions + rsync-over-SSH system as the main site. Pushing to the
`research-portfolio` branch deploys; so does Actions -> "Deploy research site" -> Run workflow.
It reuses the repository secrets/variables (`CPANEL_SSH_KEY`, `CPANEL_SSH_KEY_PASSPHRASE`,
`CPANEL_SSH_HOST`, `CPANEL_SSH_PORT`, `CPANEL_SSH_USERNAME`).

## One-time server setup (cPanel), before the first deploy
1. **Subdomain:** cPanel -> Domains -> create `research.aliyanfaisal.com`. Set its **document root** to
   `/home/wowzario/research.aliyanfaisal.com/public` (the `public/` folder, never the project root).
2. **No redirects** on this subdomain (Domains -> Redirects). A redirect to itself causes a loop.
3. **PHP version:** cPanel -> MultiPHP Manager -> set the subdomain to PHP 8.3 or newer.
4. **SSL:** make sure AutoSSL has issued a certificate for the subdomain.
5. **`.env`:** create `.env` in `/home/wowzario/research.aliyanfaisal.com/` from `deploy/server.env.example`, then in the
   project folder run `php artisan key:generate`. The workflow stops with a clear message if `.env` is missing.

## After the first deploy
- Visit https://research.aliyanfaisal.com and check `/sitemap.xml`.
- Put the compiled CV at `public/cv/aliyan-faisal-cv.pdf` (commit it); the download links then appear.
- Add `research@aliyanfaisal.com` as a mailbox, and check SPF/DKIM in cPanel -> Email Deliverability.

## Notes
- `--delete` removes anything in the target folder that is not in the repository, apart from the excluded
  items (`.env`, `storage/*` runtime folders, a root `.htaccess`). Do not store other files in that folder.
- No database is used, so there is no `migrate` step.
