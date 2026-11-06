# Secret Santa 🎁

A small, account-free Secret Santa app for the family, built on Laravel 13.

- The organiser creates a party and shares the invite link.
- Everyone joins with a name and email, and gets a **private link** by email.
- On their private page, each person fills in a short, optional **questionnaire**.
  They can edit it until the party's deadline.
- The organiser draws names (respecting any exclusions). Everyone is emailed who
  they're buying for. Their private page then shows their recipient's answers
  as a profile card.
- Questions are managed at **`/admin`**. Add, edit, reorder, switch off, or limit
  a question to a single year, so a couple can be rotated each Christmas.

## How it fits together

| Thing | Where |
| --- | --- |
| Parties, participants, exclusions, draws | `app/Models`, `app/Http/Controllers/PartyController.php` |
| Draw/matching algorithm | `app/Services/SecretSantaRandomizer.php` |
| Questionnaire + Santa view | `app/Http/Controllers/ParticipantController.php`, `resources/views/participant/*` |
| Question admin | `app/Http/Controllers/Admin/QuestionController.php`, `resources/views/admin/*` |
| Admin auth (HTTP Basic) | `app/Http/Middleware/AdminBasicAuth.php`, `config/santa.php` |
| Schema + default questions | `database/migrations` |

There are no user accounts. Access is by unguessable links:

- `/party/{invitation_token}`: public invite page.
- `/party/{invitation_token}?edit_token=…`: the organiser's controls (emailed to them).
- `/you/{access_token}`: a participant's private page (emailed to them, re-sendable from the invite page).

Each party has a `year`. The questionnaire for a party is every active question
whose `year` is empty (asked every year) or matches the party's year. Answers are
stored per participant, question and year in `answers`. The old free-text
`participants.wishlist` column is kept, and is still shown to Santas when present.

## Requirements

- PHP 8.3+ with `pdo_sqlite` (or `pdo_mysql`), `mbstring`, `openssl`, `intl`
- Composer 2
- Node 20+ (only to build assets)

## Fresh server install

```sh
git clone <repo> secret-santa && cd secret-santa
cp .env.example .env
# Edit .env: APP_URL, APP_ENV=production, APP_DEBUG=false, mail settings,
# ADMIN_PASSWORD (php artisan santa:hash-password once composer has run)
bin/install.sh
```

SQLite is the default and is plenty for a family. To use MySQL, set
`DB_CONNECTION=mysql` and the `DB_*` values instead.

Point your web server's document root at `public/`. Emails are queued, so run a
worker (e.g. with systemd or Supervisor):

```sh
php artisan queue:work --tries=3
```

Or set `QUEUE_CONNECTION=sync` to send emails during the request instead.

## Development

```sh
composer setup     # install, .env, key, migrate, build
composer dev       # app server, queue, logs and Vite together
composer test      # run the test suite
vendor/bin/pint    # code style
```

Laravel Boost is installed as a dev dependency for AI-assisted development
(`php artisan boost:install` to (re)configure your editor/agent).
