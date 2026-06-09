# Anna Content Porter — Technical Design

## 1. Plugin File Structure

```
anna-content-porter/
├── anna-content-porter.php              # Bootstrap: header, constants, requires, hook registration
└── includes/
│   ├── class-anna-porter-registry.php  # Section_Registry: page → key prefix/exact-key map
│   ├── class-anna-porter-exporter.php  # Export: key collection, image bundling, JSON download
│   ├── class-anna-porter-importer.php  # Import: validation, image re-creation, option write
│   └── class-anna-porter-admin.php     # Admin page: menu, UI rendering, form dispatch
└── assets/
    ├── css/
    │   └── admin.css                   # Minimal styles for the porter admin page
    └── js/
        └── admin.js                    # Select-all checkbox toggle, export button disable
```

---

## 2. Data Flow Diagrams

### Export Flow

```
Admin clicks "Export"
        │
        ▼
anna_porter_export (admin-post.php)
        │
        ├─ check_admin_referer('anna_porter_export')
        ├─ current_user_can('manage_options')
        │
        ▼
Anna_Porter_Registry::get_keys_for_sections($section_ids, $all_options)
        │  prefix-match + exact-match against live anna_theme_options
        │  longest-prefix-wins disambiguation
        ▼
$matched_keys  (flat array of option keys)
        │
        ▼
Anna_Porter_Exporter::export($section_ids)
        │
        ├─ get_option('anna_theme_options')        — read all options
        ├─ filter to $matched_keys
        │
        ├─ foreach Media_Field (int > 0):
        │       get_attached_file($id)
        │       base64_encode(file_get_contents($path))
        │       build Image_Payload → $images[$id]
        │       replace field value with string "$id"
        │
        ├─ build Export_Package array
        │       { meta, content, images, export_warnings }
        │
        └─ send download headers
           echo json_encode($package, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)
           exit
```

### Import Flow

```
Step 1 — Preview
        │
Admin uploads .json file
        │
        ▼
anna_porter_import_preview (admin-post.php)
        │
        ├─ check_admin_referer('anna_porter_import_preview')
        ├─ current_user_can('manage_options')
        │
        ▼
Anna_Porter_Importer::preview($package)
        │
        ├─ validate MIME (application/json or text/plain)
        ├─ json_decode → validate parse
        ├─ validate meta.plugin === 'anna-content-porter'
        │
        └─ return preview data (sections, source URL, date)
               → Admin page re-renders with preview panel
                  + Overwrite / Skip radio
                  + hidden field: base64-encoded package
                  + nonce for confirm step

Step 2 — Confirm
        │
Admin clicks "Confirm Import"
        │
        ▼
anna_porter_import_confirm (admin-post.php)
        │
        ├─ check_admin_referer('anna_porter_import_confirm')
        ├─ current_user_can('manage_options')
        │
        ▼
Anna_Porter_Importer::import($package, $mode)
        │
        ├─ foreach image in $package['images']:
        │       base64_decode → wp_tempnam()
        │       file_put_contents($tmp, $data)
        │       wp_insert_attachment() + wp_generate_attachment_metadata()
        │       build $image_map[old_id] = new_attachment_id
        │
        ├─ foreach key in $package['content']:
        │       reject if not in Registry allowlist   → record warning
        │       if value is string key in $images:
        │           resolve to $image_map[key] or 0
        │       sanitise by field type
        │       apply Import_Mode (Overwrite / Skip) against live options
        │
        ├─ $merged = array_merge($live_options, $resolved_keys)  — Overwrite
        │  OR
        │  $merged = fill-in-blanks($live_options, $resolved_keys) — Skip
        │
        ├─ update_option('anna_theme_options', $merged)
        │
        └─ return result array
               → Admin page renders summary notice
```

---

## 3. Classes

### 3.1 `Anna_Porter_Registry`

**Responsibility:** Single source of truth for which `anna_theme_options` keys belong to which page section. Used by both exporter and importer.

```php
class Anna_Porter_Registry {

    /**
     * Returns the static section map merged with any dynamically registered
     * scaffolded pages from anna_get_scaffolded_pages().
     *
     * Each entry:
     *   'label'    => string  Human-readable name shown in the UI
     *   'prefixes' => string[]  Key prefixes to match (e.g. 'hero_')
     *   'exact'    => string[]  Exact key names (e.g. 'site_logo_id')
     *
     * @return array<string, array{label: string, prefixes: string[], exact: string[]}>
     */
    public static function get_sections(): array;

    /**
     * Given a list of section IDs and the full live anna_theme_options array,
     * returns all matching option keys using:
     *   1. Exact-key match (highest priority)
     *   2. Longest-prefix-wins for prefix matches
     *   3. Each key assigned to exactly one section (no duplicates)
     *
     * @param string[] $section_ids  Keys from get_sections() to include.
     * @param array    $all_options  Full anna_theme_options array.
     * @return string[]  Flat list of matching option key names.
     */
    public static function get_keys_for_sections(
        array $section_ids,
        array $all_options
    ): array;

    /**
     * Returns the section ID that owns a given key, or null if unregistered.
     * Uses the same longest-prefix-wins + exact-match logic.
     *
     * @param string $key
     * @param array  $all_options
     * @return string|null
     */
    public static function get_section_for_key(
        string $key,
        array $all_options
    ): ?string;
}
```

**Longest-prefix-wins algorithm** (`get_keys_for_sections`):

```
For each $key in $all_options:
    best_match_length = 0
    best_match_section = null

    foreach section in requested sections:
        if $key in section['exact']:
            → assign to this section immediately (exact beats all prefixes)
            break

        foreach $prefix in section['prefixes']:
            if str_starts_with($key, $prefix) AND len($prefix) > best_match_length:
                best_match_length = len($prefix)
                best_match_section = section_id

    if best_match_section is not null:
        → include $key in results
```

---

### 3.2 `Anna_Porter_Exporter`

**Responsibility:** Pulls keys from `anna_theme_options`, resolves Media_Fields to base64 Image_Payloads, assembles the Export_Package JSON, and sends it as a browser download.

```php
class Anna_Porter_Exporter {

    /**
     * Main entry point. Called from the admin-post handler.
     * Sends download headers and exits.
     *
     * @param string[] $section_ids  Section IDs selected by the admin.
     */
    public function export( array $section_ids ): void;

    /**
     * Builds the Export_Package array without sending headers.
     * Used internally and useful for testing.
     *
     * @param string[] $section_ids
     * @return array  The full Export_Package structure.
     */
    public function build_package( array $section_ids ): array;

    /**
     * Resolves a single attachment ID to an Image_Payload array.
     * Returns null if file is unreadable.
     *
     * @param int $attachment_id
     * @return array{
     *   original_filename: string,
     *   mime_type: string,
     *   source_url: string,
     *   base64_data: string
     * }|null
     */
    private function resolve_image( int $attachment_id ): ?array;

    /**
     * Returns the theme Version header string.
     * Reads the style.css of the active theme.
     *
     * @return string
     */
    private function get_theme_version(): string;
}
```

**`export()` internals:**

1. Call `Anna_Porter_Registry::get_keys_for_sections($section_ids, $all_options)`
2. If no keys found → `wp_die()` with error message
3. Call `build_package()` to assemble the array
4. Send headers:
   ```php
   header('Content-Type: application/json; charset=utf-8');
   header('Content-Disposition: attachment; filename="anna-content-porter-' . gmdate('Y-m-d') . '.json"');
   header('Cache-Control: no-cache, must-revalidate');
   ```
5. `echo json_encode($package, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES)`
6. `exit`

**`build_package()` internals:**

```
$content = []  (keys and values to export)
$images  = []  (image payloads keyed by attachment ID string)
$warnings = []

foreach $key in matched_keys:
    $value = $all_options[$key]

    if is_array($value):             // Repeater_Field
        foreach sub-field in $value recursively:
            if sub-field looks like an attachment ID (int > 0):
                resolve image, populate $images, replace with string key
        $content[$key] = $processed_value

    elseif is_integer($value) AND $value > 0:   // Media_Field
        $payload = resolve_image($value)
        if $payload:
            $images[(string)$value] = $payload
            $content[$key] = (string)$value      // string key reference
        else:
            $content[$key] = $value              // keep raw int, add warning
            $warnings[] = "Could not read file for attachment $value (key: $key)"

    else:
        $content[$key] = $value

return [
    'meta'            => [ ... ],
    'content'         => $content,
    'images'          => $images,
    'export_warnings' => $warnings,
]
```

---

### 3.3 `Anna_Porter_Importer`

**Responsibility:** Validates, sanitises, re-creates images, and writes an Export_Package into `anna_theme_options`.

```php
class Anna_Porter_Importer {

    /**
     * Validates the package and returns preview data for display.
     * Does NOT write anything to the database.
     *
     * @param array $package  Decoded Export_Package.
     * @return array{
     *   exported_sections: string[],
     *   source_site_url: string,
     *   exported_at: string,
     *   content_key_count: int
     * }
     * @throws InvalidArgumentException  If package is invalid.
     */
    public function preview( array $package ): array;

    /**
     * Performs the full import.
     *
     * @param array  $package  Decoded Export_Package.
     * @param string $mode     'overwrite' or 'skip'.
     * @return array{
     *   written: int,
     *   skipped: int,
     *   images_created: int,
     *   warnings: string[]
     * }
     */
    public function import( array $package, string $mode ): array;

    /**
     * Processes the images object: decodes base64, creates WP attachments.
     * Returns a map of exported-string-key → new local attachment ID.
     *
     * @param array $images  The 'images' object from the Export_Package.
     * @return array<string, int>  e.g. ['42' => 187, '55' => 188]
     */
    private function recreate_images( array $images ): array;

    /**
     * Sanitises a single value according to its inferred field type.
     * Handles scalar, URL, color, toggle, media ID, and repeater recursively.
     *
     * @param string $key    Option key (used to infer type hints from key name).
     * @param mixed  $value  Raw value from the package.
     * @return mixed  Sanitised value.
     */
    private function sanitise_value( string $key, mixed $value ): mixed;

    /**
     * Applies Import_Mode logic for a single key.
     * Returns whether the key should be written.
     *
     * @param string $key
     * @param mixed  $incoming_value  Sanitised value from package.
     * @param array  $live_options    Current anna_theme_options.
     * @param string $mode            'overwrite' or 'skip'.
     * @return bool
     */
    private function should_write(
        string $key,
        mixed $incoming_value,
        array $live_options,
        string $mode
    ): bool;
}
```

**`recreate_images()` internals:**

```
foreach $string_key => $payload in $images:
    $decoded = base64_decode($payload['base64_data'], strict: true)
    if $decoded === false:
        $warnings[] = "Base64 decode failed for image key $string_key"
        continue

    $tmp = wp_tempnam($payload['original_filename'])
    file_put_contents($tmp, $decoded)

    $wp_filetype = wp_check_filetype($payload['original_filename'])
    $attachment = [
        'post_mime_type' => $wp_filetype['type'] ?: $payload['mime_type'],
        'post_title'     => sanitize_file_name($payload['original_filename']),
        'post_status'    => 'inherit',
    ]

    $upload_dir  = wp_upload_dir()
    $dest        = $upload_dir['path'] . '/' . wp_unique_filename($upload_dir['path'], $payload['original_filename'])
    rename($tmp, $dest)

    $attach_id = wp_insert_attachment($attachment, $dest)
    if is_wp_error($attach_id):
        unlink($dest)
        $warnings[] = "Failed to create attachment for image key $string_key"
        continue

    require_once ABSPATH . 'wp-admin/includes/image.php'
    $metadata = wp_generate_attachment_metadata($attach_id, $dest)
    wp_update_attachment_metadata($attach_id, $metadata)

    $image_map[$string_key] = $attach_id

return $image_map
```

**`sanitise_value()` field-type rules:**

| Key pattern | Sanitisation |
|---|---|
| ends with `_url` | `esc_url_raw($value)` |
| ends with `_id` | `absint($value)` |
| ends with `_color` OR key starts with `color_` | `preg_match('/^#[0-9a-fA-F]{3,8}$/', $value) ? $value : ''` |
| `_enabled` / `_toggle` | `(bool)(int)$value` |
| is array (repeater) | recurse into each sub-field with sub-key |
| textarea keys (body, description, items_text) | `sanitize_textarea_field($value)` |
| all other strings | `sanitize_text_field($value)` |

**`should_write()` — Skip mode empty-check:**

| Field type | "empty" definition |
|---|---|
| Scalar_Field | `'' === trim((string)$live_value)` |
| Repeater_Field | `empty($live_value)` (empty array) |
| Media_Field | `0 === absint($live_value)` OR `'' === $live_value` |

---

### 3.4 `Anna_Porter_Admin`

**Responsibility:** Registers the submenu, enqueues assets, renders the export/import UI, and dispatches form submissions.

```php
class Anna_Porter_Admin {

    /**
     * Hooks into WordPress. Called from the main plugin file.
     */
    public function init(): void;
        // add_action('admin_menu',  [$this, 'register_menu'])
        // add_action('admin_enqueue_scripts', [$this, 'enqueue_assets'])
        // add_action('admin_post_anna_porter_export',         [$this, 'handle_export'])
        // add_action('admin_post_anna_porter_import_preview', [$this, 'handle_import_preview'])
        // add_action('admin_post_anna_porter_import_confirm', [$this, 'handle_import_confirm'])

    /**
     * Registers the "Content Porter" submenu page under anna-theme-settings.
     */
    public function register_menu(): void;

    /**
     * Enqueues admin.css and admin.js only on the porter page.
     *
     * @param string $hook  Current admin page hook suffix.
     */
    public function enqueue_assets( string $hook ): void;

    /**
     * Renders the full admin page (export section + import section).
     */
    public function render_page(): void;

    /**
     * Handles the export POST. Validates nonce/caps, calls Exporter, sends download.
     */
    public function handle_export(): void;

    /**
     * Handles the import preview POST. Validates nonce/caps, parses file,
     * stores package in a transient, redirects back to admin page with preview state.
     */
    public function handle_import_preview(): void;

    /**
     * Handles the import confirm POST. Validates nonce/caps, reads transient,
     * calls Importer::import(), redirects with result summary.
     */
    public function handle_import_confirm(): void;
}
```

---

## 4. Export Package JSON Format

```json
{
  "meta": {
    "plugin": "anna-content-porter",       // Always "anna-content-porter" — used for validation on import
    "theme_version": "1.4.2",              // From active theme's style.css Version header
    "exported_at": "2025-07-15T04:22:00Z", // gmdate('c') — ISO 8601 UTC
    "source_site_url": "https://staging.annabaylis.com.au",  // get_home_url()
    "exported_sections": [                 // Human-readable labels of selected sections
      "Coaching Page",
      "Global Brand"
    ]
  },
  "content": {
    // Scalar fields — stored as-is
    "coaching_pg_hero_eyebrow": "1-1 Life Coaching · Melbourne and Online",
    "coaching_pg_hero_heading": "Real change.\nFrom the inside out.",
    "coaching_pg_hero_button_url": "https://calendly.com/anna/discovery",

    // Repeater field — array of sub-objects preserved as-is
    "coaching_pg_faqs": [
      { "question": "How long are sessions?", "answer": "60 minutes." }
    ],

    // Media field — value replaced with string key referencing images object
    // (was integer 42 before export; now the string "42")
    "coaching_pg_hero_image_id": "42",

    // Media field that failed to resolve — kept as original integer, warning recorded
    "coaching_pg_other_image_id": 99,

    // Color field
    "color_primary": "#007063",

    // Exact key match (global brand section)
    "site_logo_id": "7"
  },
  "images": {
    // Keyed by the original attachment ID cast to string
    "42": {
      "original_filename": "coaching-hero.jpg",
      "mime_type": "image/jpeg",
      "source_url": "https://staging.annabaylis.com.au/wp-content/uploads/2025/06/coaching-hero.jpg",
      "base64_data": "/9j/4AAQSkZJRgABAQAAAQABAAD..."  // full base64 string
    },
    "7": {
      "original_filename": "anna-logo.png",
      "mime_type": "image/png",
      "source_url": "https://staging.annabaylis.com.au/wp-content/uploads/anna-logo.png",
      "base64_data": "iVBORw0KGgoAAAANSUhEUgAA..."
    }
  },
  "export_warnings": [
    // Populated when a Media_Field ID does not resolve to a readable file
    "Could not read file for attachment ID 99 (key: coaching_pg_other_image_id)"
  ]
}
```

**Validation rules on import:**

| Field | Rule |
|---|---|
| `meta.plugin` | Must equal `"anna-content-porter"` exactly |
| `meta.exported_at` | Informational only; displayed to user |
| `content` | Must be a JSON object (associative array) |
| `images` | Optional; if present must be a JSON object |
| Image `base64_data` | Must decode successfully via `base64_decode(..., true)` |

---

## 5. Section Registry Map

```php
Anna_Porter_Registry::get_sections() returns:

[
  'home' => [
    'label'    => 'Home',
    'prefixes' => [
      'hero_',            // hero section fields and stats
      'intro_',           // intro/approach section
      'recognition_',     // recognition list section
      'services_',        // services section
      'about_',           // home page about section (NOT about page)
      'testimonials_',    // testimonials section
      'cta_',             // final CTA section
    ],
    'exact' => [],
  ],

  'about_pg' => [
    'label'    => 'About Page',
    'prefixes' => ['about_pg_'],
    'exact'    => [],
  ],

  'coaching_pg' => [
    'label'    => 'Coaching Page',
    'prefixes' => ['coaching_pg_'],
    'exact'    => [],
  ],

  'oasis_pg' => [
    'label'    => 'Oasis Page',
    'prefixes' => ['oasis_pg_'],
    'exact'    => [],
  ],

  'speaking_pg' => [
    'label'    => 'Speaking Page',
    'prefixes' => ['speaking_pg_'],
    'exact'    => [],
  ],

  'mhs_pg' => [
    'label'    => 'Mental Health Support Page',
    'prefixes' => ['mhs_pg_'],
    'exact'    => [],
  ],

  'move_pg' => [
    'label'    => 'Move Page',
    'prefixes' => ['move_pg_'],
    'exact'    => [],
  ],

  'reviews_pg' => [
    'label'    => 'Reviews Page',
    'prefixes' => ['reviews_pg_'],
    'exact'    => [],
  ],

  'contact_pg' => [
    'label'    => 'Contact Page',
    'prefixes' => ['contact_pg_'],
    'exact'    => [],
  ],

  'brand' => [
    'label'    => 'Global Brand',
    'prefixes' => [
      'color_',           // color_primary, color_accent, etc.
      'font_',            // font_heading, font_body, font_size_base, etc.
      'container_',       // container_max, container_wide
      'header_',          // header_style, header_cta_text, header_cta_url
    ],
    'exact' => [
      'site_logo_id',     // exact key — would otherwise match nothing or 'about_' prefix
      'footer_logo_id',   // exact key — would otherwise fall to footer section
      'border_radius_btn',
      'section_padding_md',
      'discovery_call_url',
    ],
  ],

  'footer_social' => [
    'label'    => 'Footer & Social',
    'prefixes' => [
      'footer_',          // footer_description, footer_logo_id (overridden by brand exact)
      'social_',          // social_links (array)
      'contact_',         // contact_email, contact_phone, contact_address, contact_hours
      'newsletter_',      // newsletter_heading, newsletter_text, etc.
      'copyright_',       // copyright_text
    ],
    'exact' => [
      'privacy_url',
      'terms_url',
    ],
  ],
]
```

**Disambiguation notes:**

- `about_*` keys on the home page (e.g. `about_eyebrow`, `about_image_id`) use prefix `about_` and belong to `home`.
- `about_pg_*` keys (About page template) use prefix `about_pg_` which is longer, so they always win over `about_` — correct assignment to `about_pg` section.
- `footer_logo_id` is listed as an exact key in `brand`, so it's pulled into Brand exports rather than the Footer section. This can be adjusted by moving the exact key.
- `contact_*` keys in `footer_social` cover the global footer contact details (email, phone, address). Contact *page* fields use prefix `contact_pg_` (separate `contact_pg` section).
- Scaffolded pages are appended dynamically. Their `option_prefix` is used as the sole prefix; no exact keys.

**Dynamic scaffold merge:**

```php
foreach ( anna_get_scaffolded_pages() as $page ) {
    $code   = $page['code']          ?? '';
    $prefix = $page['option_prefix'] ?? '';
    $title  = $page['title']         ?? $code;

    if ( ! $code || ! $prefix ) continue;

    $sections[ $code ] = [
        'label'    => $title,
        'prefixes' => [ $prefix ],
        'exact'    => [],
    ];
}
```

---

## 6. Image Handling Flow

### 6.1 Export

```
For each $key => $value in matched content:

  Case: $value is int > 0  (Media_Field)
  ┌──────────────────────────────────────────────────────────────────┐
  │ $path = get_attached_file($value)                                │
  │                                                                  │
  │ if $path && is_readable($path):                                  │
  │     $mime = get_post_mime_type($value) ?: mime_content_type($path)│
  │     $src  = wp_get_attachment_url($value)                        │
  │     $b64  = base64_encode(file_get_contents($path))              │
  │                                                                  │
  │     $images[(string)$value] = [                                  │
  │         'original_filename' => basename($path),                  │
  │         'mime_type'         => $mime,                            │
  │         'source_url'        => $src,                             │
  │         'base64_data'       => $b64,                             │
  │     ]                                                            │
  │     $content[$key] = (string)$value   // string ref, not int    │
  │                                                                  │
  │ else:                                                            │
  │     $content[$key] = $value           // keep original int       │
  │     $warnings[] = "..."                                          │
  └──────────────────────────────────────────────────────────────────┘

  Case: $value is array  (Repeater_Field)
  ┌──────────────────────────────────────────────────────────────────┐
  │ Walk sub-fields recursively.                                     │
  │ Any sub-field whose key ends in _id and value is int > 0         │
  │ → same resolve-and-replace logic as above.                       │
  └──────────────────────────────────────────────────────────────────┘
```

**Memory note:** Large images can make the JSON very large. The exporter does not chunk or compress — it's a one-shot download. If memory limits are a concern, `ini_set('memory_limit', '256M')` may be set at the top of `handle_export()`.

### 6.2 Import

```
Precondition: $image_map built by recreate_images()
              $image_map = [ '42' => 187, '7' => 188 ]

For each $key => $value in $package['content']:

  Case: $value is a string AND isset($package['images'][$value])
  ┌──────────────────────────────────────────────────────────────────┐
  │ This field is a Media_Field reference.                           │
  │                                                                  │
  │ if Import_Mode === 'skip':                                       │
  │     $live_val = absint($live_options[$key] ?? 0)                 │
  │     if $live_val > 0: skip this field entirely, preserve live    │
  │                                                                  │
  │ $new_id = $image_map[$value] ?? 0                                │
  │ if $new_id === 0:                                                │
  │     $content[$key] = 0                                           │
  │     $warnings[] = "Image creation failed for key $key"          │
  │ else:                                                            │
  │     $content[$key] = $new_id   // new local attachment ID        │
  └──────────────────────────────────────────────────────────────────┘

  Case: $value is an int that appears in $package['images'] string keys
  ┌──────────────────────────────────────────────────────────────────┐
  │ (This handles the fallback where export recorded an unresolvable │
  │  media field as the original integer.)                           │
  │ Cast to 0 — cannot be used on destination site.                  │
  │ $warnings[] = "Non-portable attachment ID $value for key $key"  │
  └──────────────────────────────────────────────────────────────────┘
```

---

## 7. Admin Page Flow

### 7.1 Page Structure

The porter page is a single PHP file rendered by `Anna_Porter_Admin::render_page()`. It has two visible sections — Export and Import — displayed simultaneously. State (preview mode, result notices) is communicated via query string parameters after POST redirects.

```
URL: /wp-admin/admin.php?page=anna-porter

┌─────────────────────────────────────────────────────┐
│  Anna Theme  ▸  Content Porter                      │
├─────────────────────────────────────────────────────┤
│                                                     │
│  ┌──── EXPORT ───────────────────────────────────┐  │
│  │  [✓] Home           [✓] About Page            │  │
│  │  [✓] Coaching Page  [ ] Oasis Page            │  │
│  │  [ ] Speaking Page  [ ] Global Brand          │  │
│  │       ...                                     │  │
│  │  [Select All / Deselect All]  (JS toggle)     │  │
│  │                                               │  │
│  │  [Export Selected Sections ▾]  (disabled      │  │
│  │   when nothing checked — enforced in JS)      │  │
│  └───────────────────────────────────────────────┘  │
│                                                     │
│  ┌──── IMPORT ───────────────────────────────────┐  │
│  │  [Choose file]  anna-content-porter-*.json    │  │
│  │  [Upload & Preview]                           │  │
│  └───────────────────────────────────────────────┘  │
└─────────────────────────────────────────────────────┘
```

### 7.2 Export: Single-Step

```
1. Admin checks sections, clicks "Export Selected Sections"
2. Browser POSTs to admin-post.php?action=anna_porter_export
3. handle_export():
   a. check_admin_referer('anna_porter_export')
   b. current_user_can('manage_options') or wp_die()
   c. $section_ids = array_map('sanitize_key', $_POST['sections'] ?? [])
   d. if empty($section_ids) → wp_die('No sections selected')
   e. (new Anna_Porter_Exporter())->export($section_ids)
      → sends file download, exits
```

### 7.3 Import: Two-Step

**Step 1 — Preview**

```
1. Admin selects .json file, clicks "Upload & Preview"
2. Browser POSTs (multipart/form-data) to admin-post.php?action=anna_porter_import_preview
3. handle_import_preview():
   a. check_admin_referer('anna_porter_import_preview')
   b. current_user_can('manage_options') or wp_die()
   c. Validate $_FILES['import_file'] — check for upload errors
   d. Validate MIME (application/json or text/plain)
   e. $raw = file_get_contents($_FILES['import_file']['tmp_name'])
   f. $package = json_decode($raw, true)
   g. if JSON error or missing meta.plugin → redirect with error query param
   h. $preview = (new Anna_Porter_Importer())->preview($package)
   i. Store $package in transient: set_transient('anna_porter_pkg_' . $token, $package, 30 * MINUTE_IN_SECONDS)
   j. Redirect to admin page with ?porter_preview=1&token=$token
```

**Step 2 — Confirm**

```
4. Admin page renders in preview mode (from query params):
   ┌──── IMPORT PREVIEW ───────────────────────────────┐
   │  Source: staging.annabaylis.com.au                │
   │  Exported: 15 July 2025 at 04:22 UTC              │
   │  Sections: Coaching Page, Global Brand            │
   │  Keys: 47                                         │
   │                                                   │
   │  Import mode:                                     │
   │    (●) Overwrite  (○) Skip                        │
   │                                                   │
   │  [Confirm Import]  [Cancel]                       │
   └───────────────────────────────────────────────────┘

5. Admin clicks "Confirm Import"
6. Browser POSTs to admin-post.php?action=anna_porter_import_confirm
   POST body includes: nonce, token, mode (overwrite|skip)
7. handle_import_confirm():
   a. check_admin_referer('anna_porter_import_confirm')
   b. current_user_can('manage_options') or wp_die()
   c. $token = sanitize_key($_POST['token'])
   d. $package = get_transient('anna_porter_pkg_' . $token)
   e. if !$package → redirect with error "Session expired, please upload again"
   f. delete_transient('anna_porter_pkg_' . $token)
   g. $mode = 'skip' === ($_POST['mode'] ?? '') ? 'skip' : 'overwrite'
   h. $result = (new Anna_Porter_Importer())->import($package, $mode)
   i. Redirect to admin page with result summary in query params
      (written, skipped, images_created, warning_count)
```

**Result display (after redirect):**

```
?porter_done=1&written=47&skipped=3&images=5&warnings=1

→ Success notice (no warnings): "Import complete. 47 keys written, 3 skipped, 5 images created."
→ Warning notice (has warnings): same + "1 warning — [affected keys listed]"
```

### 7.4 Cancel Import

The Cancel button on the preview panel is a simple GET link back to `admin.php?page=anna-porter`. It does not delete the transient (which expires automatically after 30 minutes).

---

## 8. Main Plugin File (`anna-content-porter.php`)

```php
<?php
/**
 * Plugin Name: Anna Content Porter
 * Description: Export and import anna_theme_options content between Anna Baylis installations.
 * Version:     1.0.0
 * Author:      Anna Baylis
 * Requires at least: 6.0
 * Requires PHP: 8.0
 * Text Domain: anna-content-porter
 */

defined('ABSPATH') || exit;

define('ANNA_PORTER_DIR', plugin_dir_path(__FILE__));
define('ANNA_PORTER_URL', plugin_dir_url(__FILE__));

// Autoload
require_once ANNA_PORTER_DIR . 'includes/class-anna-porter-registry.php';
require_once ANNA_PORTER_DIR . 'includes/class-anna-porter-exporter.php';
require_once ANNA_PORTER_DIR . 'includes/class-anna-porter-importer.php';
require_once ANNA_PORTER_DIR . 'includes/class-anna-porter-admin.php';

/**
 * Guard: only activate when the Anna Baylis theme is active.
 */
function anna_porter_check_theme(): bool {
    $theme = wp_get_theme();
    return 'anna-baylis' === $theme->get_template();
}

if ( anna_porter_check_theme() ) {
    $admin = new Anna_Porter_Admin();
    $admin->init();
} else {
    add_action( 'admin_notices', function () {
        echo '<div class="notice notice-warning"><p>'
            . esc_html__( 'Anna Content Porter requires the Anna Baylis theme to be active.', 'anna-content-porter' )
            . '</p></div>';
    } );
}
```

**Hooks registered by `Anna_Porter_Admin::init()`:**

| Hook | Handler | Purpose |
|---|---|---|
| `admin_menu` | `register_menu` | Adds "Content Porter" submenu under `anna-theme-settings` |
| `admin_enqueue_scripts` | `enqueue_assets` | Loads `admin.css` + `admin.js` on the porter page only |
| `admin_post_anna_porter_export` | `handle_export` | Authenticated export POST handler |
| `admin_post_anna_porter_import_preview` | `handle_import_preview` | File upload + preview POST handler |
| `admin_post_anna_porter_import_confirm` | `handle_import_confirm` | Confirm import POST handler |

---

## 9. Theme Integration

### How the plugin reads/writes theme options

The plugin uses the same storage layer as the theme itself. No custom tables, no custom meta.

| Operation | Theme function | Plugin equivalent |
|---|---|---|
| Read single option | `anna_get_option($key, $default)` | `get_option('anna_theme_options', [])[$key]` direct array access |
| Read all options | `get_option('anna_theme_options', [])` | same |
| Write all options | `update_option('anna_theme_options', $data)` | same — always write the full merged array |
| Get scaffolded pages | `anna_get_scaffolded_pages()` | called by Registry to build dynamic sections |
| Get theme version | `wp_get_theme()->get('Version')` | called by Exporter for meta |

The plugin does **not** call `anna_get_option()` directly — it reads from the raw option array to avoid any sentinel (`empty--`) or default-fallback logic interfering with export fidelity.

### Dependency on `anna_get_scaffolded_pages()`

`Anna_Porter_Registry::get_sections()` calls `anna_get_scaffolded_pages()` at runtime (not at class-load time), so it always reflects the current scaffolded page list. If the function doesn't exist (e.g. theme not active), the guard in `anna-content-porter.php` prevents this code from running.

### Admin menu parent

The submenu parent slug is `anna-theme-settings`, which matches the slug registered in the theme's `anna_add_admin_menu()` function (`inc/admin/settings-pages.php`). This places "Content Porter" directly under the "Anna Theme" top-level menu.

```php
add_submenu_page(
    'anna-theme-settings',       // parent slug
    'Content Porter',            // page title
    'Content Porter',            // menu title
    'manage_options',            // capability
    'anna-porter',               // menu slug
    [$this, 'render_page'],      // callback
);
```

### No theme code changes required

The plugin is entirely self-contained. It reads from and writes to `anna_theme_options` using standard WordPress functions. The theme requires zero modification to support the plugin. The plugin simply needs to be placed in the `anna-content-porter/` directory within the theme and activated as a WordPress plugin.

---

## 10. Assets

### `assets/js/admin.js`

Two behaviours:

1. **Export button disable** — listen for `change` on section checkboxes; if none are checked, add `disabled` attribute to the export submit button and update `aria-disabled`.
2. **Select All toggle** — one "Select All" / "Deselect All" link that checks/unchecks all section checkboxes and triggers the button-disable check.

```js
// Pseudo-code
const checkboxes = document.querySelectorAll('.anna-porter-section-cb');
const submitBtn  = document.getElementById('anna-porter-export-btn');
const selectAll  = document.getElementById('anna-porter-select-all');

function updateSubmitState() {
    const anyChecked = [...checkboxes].some(cb => cb.checked);
    submitBtn.disabled = !anyChecked;
}

checkboxes.forEach(cb => cb.addEventListener('change', updateSubmitState));
updateSubmitState(); // run on page load

selectAll.addEventListener('click', (e) => {
    e.preventDefault();
    const allChecked = [...checkboxes].every(cb => cb.checked);
    checkboxes.forEach(cb => cb.checked = !allChecked);
    selectAll.textContent = allChecked ? 'Select All' : 'Deselect All';
    updateSubmitState();
});
```

### `assets/css/admin.css`

Minimal styles. Inherits WordPress admin typography. Covers:
- Section grid layout for export checkboxes (2-column on wider screens)
- Preview panel card styling
- Import mode radio button row
- Result notice colours (success = green border, warning = orange border)

---

## 11. Security Checklist

| Concern | Mitigation |
|---|---|
| Unauthorised access | `current_user_can('manage_options')` on every handler |
| CSRF on export | `wp_nonce_field` + `check_admin_referer('anna_porter_export')` |
| CSRF on import preview | `wp_nonce_field` + `check_admin_referer('anna_porter_import_preview')` |
| CSRF on import confirm | `wp_nonce_field` + `check_admin_referer('anna_porter_import_confirm')` |
| Malicious JSON keys | Registry allowlist — keys not matched by any prefix/exact entry are discarded |
| XSS in imported text | Full sanitisation pipeline before `update_option` |
| File upload abuse | MIME check + `json_decode` must succeed + `meta.plugin` check |
| Session replay | Package stored in transient keyed by a random token; deleted after confirm |
| Unreadable temp files | `wp_tempnam()` + cleanup on failure path |
