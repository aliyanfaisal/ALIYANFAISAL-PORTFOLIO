# Research Portfolio

Source for **research.aliyanfaisal.com**: the research-focused portfolio of Aliyan Faisal
(applied LLMs, retrieval-augmented generation, data-centric systems).

A separate Laravel 13 app, kept on its own branch (`research-portfolio`) with its own history.
It shares no code, database or assets with aliyanfaisal.com or the freelance site.

## Local development

```bash
composer install
cp .env.example .env && php artisan key:generate
touch database/database.sqlite && php artisan migrate
npm install && npm run dev      # in one terminal
php artisan serve               # in another
```

## Deployment

No deployment workflow is configured yet. Add `.github/workflows/` once the server folder,
database and `.env` for research.aliyanfaisal.com exist.
