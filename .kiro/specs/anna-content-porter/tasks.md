# Implementation Plan

## Overview

This plan implements the Anna Content Porter WordPress plugin in 9 sequential tasks, covering the plugin bootstrap, all four PHP classes, the admin UI template, CSS, JavaScript, and theme wiring.

## Task Dependency Graph

```json
{
  "waves": [
    { "wave": 1, "tasks": [1] },
    { "wave": 2, "tasks": [2] },
    { "wave": 3, "tasks": [3, 4, 5] },
    { "wave": 4, "tasks": [6, 7, 8] },
    { "wave": 5, "tasks": [9] }
  ]
}
```

## Tasks

- [x] 1. Plugin bootstrap
  - Create the `anna-content-porter/` directory with `includes/`, `assets/css/`, and `assets/js/` subdirectories
  - Create `anna-content-porter/anna-content-porter.php` with the WordPress plugin header (`Plugin Name`, `Description`, `Version`, `Author`, `Text Domain`)
  - Define constants `ANNA_PORTER_DIR` (plugin directory path) and `ANNA_PORTER_URL` (plugin directory URL)
  - Add `require_once` calls for all four class files: `class-anna-porter-registry.php`, `class-anna-porter-exporter.php`, `class-anna-porter-importer.php`, `class-anna-porter-admin.php`
  - Add a theme-active guard: check that the active theme slug matches the expected Anna Baylis theme, and if not, hook into `admin_notices` to display a dismissible error notice and return early without initialising the plugin
  - Instantiate `Anna_Porter_Admin` and call `->init()` on the `plugins_loaded` action
  - _Requirements: 1.1_

- [ ] 2. Section Registry class
  - Create `anna-content-porter/includes/class-anna-porter-registry.php` defining the `Anna_Porter_Registry` class
  - Implement static `get_sections()` returning the full 11-section map: Home (prefixes `hero_`, `intro_`, `recognition_`, `services_`, `about_`, `testimonials_`, `cta_`), About Page (`about_pg_`), Coaching Page (`coaching_pg_`), Oasis Page (`oasis_pg_`), Speaking Page (`speaking_pg_`), Mental Health Support Page (`mhs_pg_`), Move Page (`move_pg_`), Reviews Page (`reviews_pg_`), Contact Page (`contact_pg_`), Global Brand (prefixes `color_`, `font_`, `container_`, `header_`; exact keys `site_logo_id`, `footer_logo_id`, `border_radius_btn`, `section_padding_md`, `discovery_call_url`), and Footer & Social (prefixes `footer_`, `social_`, `contact_`, `newsletter_`, `copyright_`; exact keys `privacy_url`, `terms_url`)
  - Merge dynamically registered scaffolded pages by calling `anna_get_scaffolded_pages()` (guarded with `function_exists`) and appending each page using its `code` as section key, `option_prefix` as sole prefix, and `title` as label
  - Implement static `get_keys_for_sections(array $section_ids, array $all_options): array` using the longest-prefix-wins algorithm: for each key in `$all_options`, check exact-key entries first (immediate win), then find the longest matching prefix across all requested sections; collect and deduplicate matching keys into a flat array
  - Implement static `get_section_for_key(string $key, array $all_options): ?string` using the same resolution logic across all registered sections
  - _Requirements: 2.1, 2.2, 2.3, 2.4_

- [~] 3. Exporter class
  - Create `anna-content-porter/includes/class-anna-porter-exporter.php` defining the `Anna_Porter_Exporter` class
  - Implement `export(array $section_ids): void` — calls `get_keys_for_sections`, aborts with `wp_die` if no keys found, calls `build_package`, sends `Content-Type: application/json`, `Content-Disposition: attachment; filename="anna-content-porter-{Y-m-d}.json"`, and `Cache-Control: no-cache` headers, then `echo json_encode($package, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES)` and `exit`
  - Implement `build_package(array $section_ids): array` — reads `get_option('anna_theme_options')`, filters to matched keys, iterates each value: for scalar int > 0 call `resolve_image()` and replace value with string key or keep original int + add warning; for arrays recursively walk sub-fields looking for sub-keys ending in `_id` with int > 0 and apply the same replace logic; assemble `meta`, `content`, `images`, and `export_warnings` arrays
  - Implement private `resolve_image(int $attachment_id): ?array` — calls `get_attached_file`, checks `is_readable`, reads `get_post_mime_type`, `wp_get_attachment_url`, `base64_encode(file_get_contents(...))`, returns `['original_filename', 'mime_type', 'source_url', 'base64_data']` or `null`
  - Implement private `get_theme_version(): string` — uses `wp_get_theme()` on the active theme and returns its `Version` header
  - _Depends on: task 2_
  - _Requirements: 3.3, 3.4, 3.5, 3.6, 4.1, 4.2, 4.3, 4.4, 4.5_

- [~] 4. Importer class
  - Create `anna-content-porter/includes/class-anna-porter-importer.php` defining the `Anna_Porter_Importer` class
  - Implement `preview(array $package): array` — validates `meta.plugin === 'anna-content-porter'`, validates `content` is an array, returns `['exported_sections', 'source_site_url', 'exported_at', 'content_key_count']`; throws `InvalidArgumentException` on failure
  - Implement `import(array $package, string $mode): array` — calls `recreate_images`, iterates `$package['content']`, rejects keys not in the Registry allowlist (records warning), resolves image string references via `$image_map`, resolves unresolvable raw int media references to 0 with warning, calls `sanitise_value` on each value, calls `should_write` to apply mode logic, then calls `update_option('anna_theme_options', $merged)` and returns `['written', 'skipped', 'images_created', 'warnings']`
  - Implement private `recreate_images(array $images): array` — for each payload: `base64_decode` (strict), `wp_tempnam`, `file_put_contents`, `wp_check_filetype`, `wp_upload_dir`, `wp_unique_filename`, `rename` to uploads dir, `wp_insert_attachment`, `require_once` image.php, `wp_generate_attachment_metadata`, `wp_update_attachment_metadata`; records warning and continues on any failure; returns `$image_map`
  - Implement private `sanitise_value(string $key, mixed $value): mixed` — applies rules by key pattern: `_url` suffix → `esc_url_raw`; `_id` suffix → `absint`; `_color` suffix or `color_` prefix → hex pattern validate or empty string; `_enabled`/`_toggle` suffix → `(bool)(int)`; array → recurse; keys containing `body`, `description`, or `items_text` → `sanitize_textarea_field`; all other strings → `sanitize_text_field`
  - Implement private `should_write(string $key, mixed $incoming, array $live_options, string $mode): bool` — for `overwrite` always return true; for `skip` return true only if the live value is empty (empty string for scalars, empty array for repeaters, 0 or empty string for media)
  - _Depends on: task 2_
  - _Requirements: 5.2, 5.3, 5.4, 5.7, 5.8, 6.1, 6.2, 7.1, 7.2, 8.1, 8.2, 8.3, 8.4, 8.5, 8.6, 9.1, 9.2, 9.3, 9.4, 9.5_

- [~] 5. Admin page class
  - Create `anna-content-porter/includes/class-anna-porter-admin.php` defining the `Anna_Porter_Admin` class
  - Implement `init(): void` — registers `admin_menu`, `admin_enqueue_scripts`, and three `admin_post_*` actions: `anna_porter_export`, `anna_porter_import_preview`, `anna_porter_import_confirm`
  - Implement `register_menu(): void` — calls `add_submenu_page` under parent slug `anna-theme-settings`, page slug `anna-porter`, capability `manage_options`, title "Content Porter", callback `[$this, 'render_page']`; store the returned hook suffix for asset enqueuing
  - Implement `enqueue_assets(string $hook): void` — enqueues `assets/css/admin.css` and `assets/js/admin.js` only when `$hook` matches the porter page hook suffix; use `ANNA_PORTER_URL` and `filemtime(ANNA_PORTER_DIR . 'assets/...')` for cache-busting
  - Implement `handle_export(): void` — `check_admin_referer('anna_porter_export')`, `current_user_can('manage_options')`, sanitise `$_POST['sections']` with `array_map('sanitize_key', ...)`, optionally set `memory_limit` to `256M`, call `(new Anna_Porter_Exporter())->export($section_ids)`
  - Implement `handle_import_preview(): void` — `check_admin_referer('anna_porter_import_preview')`, validate `$_FILES['import_file']` for upload errors and MIME type, `json_decode` file contents, call `(new Anna_Porter_Importer())->preview($package)`, generate a random token with `wp_generate_password(32, false)`, store package in transient `anna_porter_pkg_{$token}` for 30 minutes, redirect to `admin.php?page=anna-porter&porter_preview=1&token={$token}` (or with `porter_error` on failure)
  - Implement `handle_import_confirm(): void` — `check_admin_referer('anna_porter_import_confirm')`, retrieve and delete transient using `sanitize_key($_POST['porter_token'])`, call `(new Anna_Porter_Importer())->import($package, $mode)`, redirect to `admin.php?page=anna-porter` with result query params
  - _Depends on: task 2_
  - _Requirements: 1.1, 1.2, 1.3, 1.4, 1.5, 5.1, 5.2, 5.3, 5.4_

- [~] 6. Admin UI template
  - Implement the full `render_page()` method body in `class-anna-porter-admin.php`
  - Render the page wrapper with `<div class="wrap">` and an `<h1>` title; check for `porter_error` query param and display a dismissible `notice-error` div with `esc_html` output
  - Render the Export section: `<form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>">` with hidden `action=anna_porter_export`, `wp_nonce_field('anna_porter_export')`, a 2-column checkbox grid iterating `Anna_Porter_Registry::get_sections()` with checkboxes named `sections[]` valued by section key, a "Select All" toggle link (`id="anna-porter-select-all"`), and a submit button (`id="anna-porter-export-btn"`)
  - Render the Import section: `<form method="post" enctype="multipart/form-data">` with hidden `action=anna_porter_import_preview`, `wp_nonce_field('anna_porter_import_preview')`, a file input named `import_file` accepting `.json`, and an "Upload & Preview" submit button
  - When `porter_preview=1` and a valid `token` param are present, retrieve the transient, then render the preview panel card showing source site URL (`esc_html`), export date, sections list, and key count; Overwrite/Skip radio inputs; and a confirm `<form>` posting to `admin-post.php?action=anna_porter_import_confirm` with `wp_nonce_field('anna_porter_import_confirm')`, hidden `porter_token`, hidden `import_mode`, and Confirm/Cancel buttons
  - When `porter_result` query param is present, decode the result data and render a `notice-success` div if zero warnings, or a `notice-warning` div listing affected key names if warnings exist; include written/skipped/images_created counts in both cases
  - Ensure all dynamic output uses `esc_html`, `esc_attr`, or `esc_url` as appropriate throughout
  - _Depends on: task 5_
  - _Requirements: 1.1, 3.1, 3.2, 5.1, 5.5, 5.6, 10.1, 10.2, 10.3_

- [~] 7. Admin CSS
  - Create `anna-content-porter/assets/css/admin.css`
  - Style the checkbox grid as a 2-column CSS grid (`display: grid; grid-template-columns: 1fr 1fr; gap: 8px 24px`) inside the export section
  - Style the "Select All" link with reduced font size and appropriate margin
  - Style the preview panel as a card: white background, `border: 1px solid #c3c4c7`, `border-radius: 4px`, padding, and `max-width: 640px`
  - Style the import mode radio row with `display: flex; gap: 24px; margin: 12px 0`
  - Style result notice variants: success with `border-left: 4px solid #00a32a`; warning with `border-left: 4px solid #dba617`
  - Follow the naming and variable conventions used in `assets/css/admin/admin-settings.css`
  - _Depends on: task 5_
  - _Requirements: 3.1, 5.5, 5.6, 10.2, 10.3_

- [~] 8. Admin JS
  - Create `anna-content-porter/assets/js/admin.js` using vanilla JS with no jQuery dependency
  - On `DOMContentLoaded`, locate the export submit button (`#anna-porter-export-btn`) and all checkboxes named `sections[]`; add a `change` listener to each checkbox that disables the button when zero are checked and enables it when one or more are checked; evaluate initial state immediately on load
  - Implement Select All / Deselect All toggle: locate `#anna-porter-select-all`; on click, if not all checkboxes are checked then check all and set link text to "Deselect All"; if all are checked then uncheck all and set link text to "Select All"; dispatch a `change` event on one checkbox afterwards to update button state; call `event.preventDefault()` on the link
  - _Depends on: task 5_
  - _Requirements: 3.2_

- [~] 9. Wire up plugin
  - Verify the plugin header in `anna-content-porter/anna-content-porter.php` is valid so WordPress can detect it as a standalone plugin
  - Inspect `functions.php` for the pattern used to load `anna-page-scaffolder` or other co-located plugins; if such a pattern exists, add a matching `require_once` or `include_once` for `anna-content-porter/anna-content-porter.php` using the same conditional guard so the plugin loads from the theme directory without requiring separate WP plugin activation
  - Confirm the plugin initialises without PHP errors, that the "Content Porter" submenu appears under "Anna Theme", and that the admin page renders at `wp-admin/admin.php?page=anna-porter`
  - _Depends on: task 1_
  - _Requirements: 1.1_

## Notes

- Tasks 3, 4, 5 all depend on the Section Registry (task 2) and can be developed in parallel once task 2 is complete.
- Tasks 6, 7, and 8 depend on the Admin class skeleton (task 5) but are largely independent of each other.
- Task 9 is a wiring/verification step and should be done last, after all classes and assets are in place.
- The two-step import flow (preview → confirm) uses WordPress transients to avoid passing the raw JSON package through a hidden form field, keeping request sizes small and the confirm step more secure.
