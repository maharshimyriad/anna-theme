# Anna Page Scaffolder

WordPress plugin (bundled with Anna Baylis theme) that generates a **complete page structure** from a slug.

## What it creates

For a slug like `contact`:

| Output | Path |
|--------|------|
| Page template | `page-contact.php` |
| Section loader | `template-parts/pages/contact/index.php` |
| Section partials | `hero.php`, `intro.php` (text+image), `cta.php`, … |
| Page CSS | `assets/css/pages/contact.css` |
| Helpers | `inc/contact-helpers.php` |
| Theme settings tab | `inc/admin/contact-settings-fields.php` |
| WP page | Published page at `/contact/` with template assigned |
| Page editor meta | **Anna Contact Page Content** meta box (via **Anna Content Manager**) |

**Split of responsibility**

- **Anna Page Scaffolder** — generates theme files and registers the page in `anna_scaffolded_pages`
- **Anna Content Manager** — page editor meta box, save/load to `_anna_content_{code}_page`, media UI (same as Oasis / MHS / MOVE)
- **`inc/page-registry.php`** — theme settings tab, CSS enqueue, auto-create WP page

## Default sections

Section templates are not generated automatically. After scaffolding, add section template parts manually under `template-parts/pages/{slug}/` and include them in `template-parts/pages/{slug}/index.php`.

## How to use

1. Ensure the theme is active and this plugin is loaded (included from `functions.php`).
2. Go to **Anna Theme → Page Scaffolder**.
3. Enter slug (`contact`), title (`Contact`), optional code prefix (`contact`).
4. Click **Generate Page Structure**.
5. Create a WordPress page manually, assign the generated page template, then add section partials and content.

**Pages are not auto-created** when you activate the theme or plugins — create them yourself in **Pages → Add New**.

## Code prefix

- Used in PHP: `anna_get_contact_page_content()`, `contact_pg_*` options.
- Must be unique and match `[a-z][a-z0-9_]*`.
- Auto-derived from slug (`mental-health` → `mental_health`) if left empty.

## Notes

- Will not overwrite existing files; use a new slug or remove generated files first.
- Reserved slugs/codes: `coaching`, `oasis`, `speaking`, `about`, `mhs`, `move`, etc.
- After scaffolding, refine CSS in `assets/css/pages/{slug}.css` to match final design.
