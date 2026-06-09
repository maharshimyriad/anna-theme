# Requirements Document

## Introduction

The Anna Content Porter is a WordPress plugin that lives inside the Anna Baylis theme directory at `anna-content-porter/`. It enables a site administrator to export selected page content from the `anna_theme_options` WordPress option on one Anna Baylis installation and import that content into another installation running the same theme. The plugin handles text, textarea, toggle, color, select, media (attachment ID), and repeater fields. It adds a "Content Porter" submenu page under the existing "Anna Theme" admin menu and is protected by the `manage_options` capability and WordPress nonces.

## Glossary

- **Anna_Theme_Options**: The single WordPress option (`anna_theme_options`) that stores all theme content as a flat associative array.
- **Content_Porter**: The plugin being specified — responsible for export and import operations.
- **Export_Package**: A `.json` file produced by an export operation containing content data, bundled image payloads, and metadata.
- **Section_Registry**: The Content_Porter's internal map of human-readable page names to their `anna_theme_options` key prefixes and exact keys.
- **Page_Section**: A logical grouping of `anna_theme_options` keys identified by a shared prefix (e.g. `coaching_pg_*`, `oasis_pg_*`), as registered in the Section_Registry.
- **Scalar_Field**: A single-value option entry — text, textarea, toggle, color, or select.
- **Repeater_Field**: An option entry whose value is an indexed array of sub-objects (e.g. testimonial items, service cards).
- **Media_Field**: An option entry whose value is a WordPress attachment ID (integer). Attachment IDs are site-local and not portable across installations.
- **Image_Payload**: Base64-encoded image binary data bundled inside an Export_Package, alongside original filename, MIME type, and source URL.
- **Import_Mode**: The strategy applied when a key from the Export_Package already exists on the destination site — either **Overwrite** (replace existing value) or **Skip** (keep existing value).
- **Scaffolded_Page**: A dynamically created page registered in the `anna_scaffolded_pages` WordPress option, with its own `option_prefix` and `title`.
- **Source_Site**: The WordPress installation from which an Export_Package was produced.
- **Destination_Site**: The WordPress installation receiving an imported Export_Package.

---

## Requirements

### Requirement 1: Access Control

**User Story:** As a site administrator, I want the Content Porter to be accessible only to authorised users, so that content cannot be exported or modified by anyone without the appropriate capability.

#### Acceptance Criteria

1. WHEN WordPress loads the admin area, THE Content_Porter SHALL add a "Content Porter" submenu page under the top-level "Anna Theme" admin menu (slug `anna-theme-settings`).
2. WHEN a user without the `manage_options` capability navigates to the Content Porter page, THE Content_Porter SHALL deny access using the WordPress `wp_die` permission-denied mechanism.
3. WHEN a form submission is received by the Content_Porter, THE Content_Porter SHALL verify both the `manage_options` capability and a valid WordPress nonce before reading or writing any data.
4. IF a nonce verification fails on a form submission, THEN THE Content_Porter SHALL abort processing, leave all stored data unchanged, and display an error message.
5. IF a capability check fails on a form submission, THEN THE Content_Porter SHALL abort processing, leave all stored data unchanged, and display an error message.

---

### Requirement 2: Section Registry

**User Story:** As a site administrator, I want to select individual pages for export, so that I can transfer only the content I need.

#### Acceptance Criteria

1. THE Content_Porter SHALL maintain a Section_Registry that maps each human-readable page name to its `anna_theme_options` key prefix(es) and exact key(s), covering:
   - **Home** — prefixes: `hero_*`, `intro_*`, `recognition_*`, `services_*`, `about_*` (home section only), `testimonials_*`, `cta_*`
   - **About Page** — prefix: `about_pg_*`
   - **Coaching Page** — prefix: `coaching_pg_*`
   - **Oasis Page** — prefix: `oasis_pg_*`
   - **Speaking Page** — prefix: `speaking_pg_*`
   - **Mental Health Support Page** — prefix: `mhs_pg_*`
   - **Move Page** — prefix: `move_pg_*`
   - **Reviews Page** — prefix: `reviews_pg_*`
   - **Contact Page** — prefix: `contact_pg_*`
   - **Global Brand** — prefixes: `color_*`, `font_*`, `container_*`, `header_*`; exact keys: `site_logo_id`, `footer_logo_id`
   - **Footer & Social** — prefixes: `footer_*`, `social_*`, `contact_*`, `newsletter_*`, `copyright_*`
2. WHEN the `anna_scaffolded_pages` option contains one or more Scaffolded_Pages, THE Content_Porter SHALL dynamically add each scaffolded page to the Section_Registry using its `option_prefix` field as the key prefix and its `title` field as the human-readable label.
3. THE Content_Porter SHALL resolve key membership by prefix-matching against the live `anna_theme_options` array at the time of export or import, so newly added keys are automatically included without code changes; exact-key entries SHALL be matched by equality.
4. WHEN a key in `anna_theme_options` matches more than one Section_Registry prefix, THE Content_Porter SHALL assign that key to the entry with the longest (most specific) matching prefix; exact-key matches SHALL take precedence over any prefix match.

---

### Requirement 3: Export

**User Story:** As a site administrator, I want to select one or more page sections and download a JSON file containing their content and images, so that I can migrate or back up specific pages.

#### Acceptance Criteria

1. THE Content_Porter SHALL render a checkbox list on the export UI with one checkbox per Section_Registry entry, labelled with the entry's human-readable name.
2. WHEN no sections are checked, THE Content_Porter SHALL disable the export submit button.
3. WHEN the user submits the export form with one or more sections selected, THE Content_Porter SHALL collect all `anna_theme_options` keys belonging to those sections.
4. WHEN no keys match the selected sections, THE Content_Porter SHALL abort the export and display an error message.
5. WHEN keys are found, THE Content_Porter SHALL produce a UTF-8 encoded JSON Export_Package and deliver it to the browser as a file download named `anna-content-porter-{YYYY-MM-DD}.json` where the date is the UTC date at export time.
6. THE Content_Porter SHALL include a `meta` object in every Export_Package containing: `plugin` (string `"anna-content-porter"`), `theme_version` (from the theme `Version` header), `exported_at` (ISO 8601 UTC datetime), `source_site_url` (home URL), and `exported_sections` (array of selected human-readable section names).

---

### Requirement 4: Image Bundling on Export

**User Story:** As a site administrator, I want images included in the export file, so that I do not have to manually re-upload every photo on the destination site.

#### Acceptance Criteria

1. WHEN exporting, THE Content_Porter SHALL identify every Media_Field value (integer greater than zero) within the selected sections.
2. WHEN a Media_Field contains a non-zero Attachment_ID that resolves to a readable file, THE Content_Porter SHALL encode it as an Image_Payload containing `original_filename`, `mime_type`, `source_url`, and `base64_data`, stored in a top-level `images` object keyed by the Attachment_ID cast to string.
3. WHEN a Media_Field resolves to a readable image, THE Content_Porter SHALL replace that field's value in the exported content data with the string key referencing its entry in the `images` object.
4. WHEN a Media_Field contains a non-zero Attachment_ID that does not resolve to a readable file, THE Content_Porter SHALL export the field with its original integer value and record a warning in an `export_warnings` array in the Export_Package.
5. WHEN a Media_Field contains a zero or empty value, THE Content_Porter SHALL export it as-is without attempting image resolution.

---

### Requirement 5: Import

**User Story:** As a site administrator, I want to upload an Export_Package, review its contents, choose an import mode, and write the data to the destination site, so that I can populate a new installation.

#### Acceptance Criteria

1. THE Content_Porter SHALL render a file upload form on the import UI that accepts `.json` files.
2. WHEN the user submits the import form, THE Content_Porter SHALL validate that the uploaded file's MIME type is `application/json` or `text/plain`; IF neither, THE Content_Porter SHALL reject the file with an error message and halt the import.
3. WHEN the user submits the import form, THE Content_Porter SHALL validate that the uploaded file parses as valid JSON; IF it does not, THE Content_Porter SHALL display an error message and halt the import.
4. WHEN the parsed JSON does not contain a `meta.plugin` field equal to `"anna-content-porter"`, THE Content_Porter SHALL display an error message and halt the import.
5. THE Content_Porter SHALL display `meta.exported_sections`, `meta.source_site_url`, and `meta.exported_at` from the uploaded Export_Package to the user before the import is confirmed.
6. THE Content_Porter SHALL present two Import_Mode options — **Overwrite** and **Skip** — before the user confirms the import.
7. WHEN the user confirms the import, THE Content_Porter SHALL sanitise all incoming data and write the resolved content to `anna_theme_options` in a single `update_option` call.
8. IF the `update_option` call fails, THEN THE Content_Porter SHALL display an error message and leave `anna_theme_options` in its pre-import state.

---

### Requirement 6: Import Mode — Overwrite

**User Story:** As a site administrator, I want an Overwrite mode that replaces existing values with imported ones, so that I can fully synchronise a destination site with the source.

#### Acceptance Criteria

1. WHEN Import_Mode is **Overwrite** and a key from the Export_Package already exists in `anna_theme_options`, THE Content_Porter SHALL replace the existing value with the imported value.
2. WHEN Import_Mode is **Overwrite** and a key from the Export_Package does not yet exist in `anna_theme_options`, THE Content_Porter SHALL add the key with the imported value.

---

### Requirement 7: Import Mode — Skip

**User Story:** As a site administrator, I want a Skip mode that only fills in missing or empty keys, so that I can add new content without overwriting existing customisations.

#### Acceptance Criteria

1. WHEN Import_Mode is **Skip** and a key from the Export_Package already exists in `anna_theme_options` with a non-empty value, THE Content_Porter SHALL leave that key unchanged; "non-empty" means a non-empty string for Scalar_Fields, a non-empty array for Repeater_Fields, and a non-zero integer for Media_Fields.
2. WHEN Import_Mode is **Skip** and a key either does not exist in `anna_theme_options` or exists with an empty value, THE Content_Porter SHALL set that key to the imported value; "empty" means an empty string for Scalar_Fields, an empty array for Repeater_Fields, and `0` or empty string for Media_Fields.

---

### Requirement 8: Image Re-creation on Import

**User Story:** As a site administrator, I want images from the export file to be created in the destination media library automatically, so that imported content references valid local attachments.

#### Acceptance Criteria

1. WHEN importing and the Export_Package contains an `images` object, THE Content_Porter SHALL process every content field whose exported value is a string key referencing an entry in the `images` object.
2. WHEN a content field references an image key and a corresponding Image_Payload exists, THE Content_Porter SHALL decode the base64 data and create a new WordPress media attachment for that image.
3. WHEN the attachment is successfully created, THE Content_Porter SHALL replace the image reference in the content data with the new integer Attachment_ID before writing to `anna_theme_options`.
4. IF Import_Mode is **Skip** and a Media_Field already contains a non-zero Attachment_ID on the Destination_Site, THEN THE Content_Porter SHALL skip image creation for that field and preserve the existing Attachment_ID.
5. IF image creation fails for any reason, THEN THE Content_Porter SHALL set the corresponding Media_Field value to `0`, record the failure in the import result summary, and continue processing remaining fields.
6. IF a content field references an image key but no Image_Payload exists in the `images` object, THEN THE Content_Porter SHALL set the corresponding Media_Field value to `0` and record a missing-payload warning in the import result summary.

---

### Requirement 9: Data Sanitisation on Import

**User Story:** As a site administrator, I want all imported data to be sanitised before it is written to the database, so that a malicious or malformed Export_Package cannot harm the site.

#### Acceptance Criteria

1. WHEN importing, THE Content_Porter SHALL reject any key whose name does not match a prefix or exact key registered in the Section_Registry, and SHALL record each rejected key in the import result summary.
2. WHEN importing Scalar_Field string values, THE Content_Porter SHALL apply `sanitize_textarea_field` to `textarea` fields and `sanitize_text_field` to all other string scalar types; toggle values SHALL be cast to boolean; color values SHALL additionally be validated against the pattern `#[0-9a-fA-F]{3,8}` and set to an empty string if they do not match.
3. WHEN importing URL values, THE Content_Porter SHALL pass them through `esc_url_raw` before writing.
4. WHEN importing Media_Field values, THE Content_Porter SHALL cast them to `absint` before writing.
5. WHEN importing Repeater_Field values, THE Content_Porter SHALL recursively apply the same per-field-type sanitisation rules from criteria 2, 3, and 4 to all sub-fields.

---

### Requirement 10: Import Result Summary

**User Story:** As a site administrator, I want a clear summary after import completes, so that I know exactly what was written, what was skipped, and whether any images failed.

#### Acceptance Criteria

1. WHEN an import operation completes, THE Content_Porter SHALL display a result summary on the admin page containing: the number of keys written, the number of keys skipped, the number of images successfully created, and the number of image failures or missing-payload warnings.
2. WHEN the import completed with zero failures and zero warnings, THE Content_Porter SHALL display a success notice.
3. WHEN the import completed with one or more failures or warnings, THE Content_Porter SHALL display a warning notice listing the affected key names.
