# Requirements Document

## Introduction

The Anna Content Porter is a WordPress plugin that lives inside the Anna Baylis theme directory at `anna-content-porter/`. It enables a site administrator (developer-level) to export selected page content from the `anna_theme_options` WordPress option on one Anna Baylis installation and import that content into another installation running the same theme. The plugin handles text fields, repeater arrays, toggle/select fields, and media attachments (images stored as WordPress attachment IDs). It surfaces a dedicated admin UI under the existing "Anna Theme" menu and is protected by the `manage_options` capability.

## Glossary

- **Anna_Theme_Options**: The single WordPress option (`anna_theme_options`) that stores all theme content as a flat associative array.
- **Content_Porter**: The plugin being specified — responsible for export and import operations.
- **Export_Package**: A `.json` file produced by an export operation containing content data, image data, and metadata.
- **Page_Section**: A logical grouping of `anna_theme_options` keys identified by a shared prefix (e.g. `coaching_pg_*`, `oasis_pg_*`).
- **Scalar_Field**: A single-value option entry — text, textarea, toggle, color, or select.
- **Repeater_Field**: An option entry whose value is an indexed array of sub-objects (e.g. FAQ items, pillar cards, pricing plans).
- **Media_Field**: An option entry whose value is a WordPress attachment ID (integer) referencing an image in the uploads directory.
- **Attachment_ID**: An integer identifying a WordPress media library item on a specific site. Attachment IDs are site-local and are not portable across installations.
- **Image_Payload**: Base64-encoded image binary data bundled inside an Export_Package alongside the original file name, MIME type, and source URL for fallback reference.
- **Import_Mode**: The strategy applied when importing content that already exists on the destination site — either **Overwrite** (replace existing values) or **Skip** (keep existing values and ignore the incoming ones).
- **Scaffolded_Page**: A dynamically created page registered in the `anna_scaffolded_pages` WordPress option, with its own option prefix.
- **Source_Site**: The WordPress installation from which an Export_Package was produced.
- **Destination_Site**: The WordPress installation receiving an imported Export_Package.
- **Section_Registry**: The Content_Porter's internal map of human-readable page names to their `anna_theme_options` key prefixes.

---

## Requirements

### Requirement 1: Plugin Initialisation and Access Control

**User Story:** As a site administrator, I want the Content Porter plugin to be available only to users with appropriate permissions, so that content data and media files cannot be exported or modified by unauthorised users.

#### Acceptance Criteria

1. THE Content_Porter SHALL register itself as a WordPress plugin loadable from within the Anna Baylis theme directory and appear in the WordPress admin Plugins list.
2. WHEN WordPress loads the admin area, THE Content_Porter SHALL add a submenu page titled "Content Porter" under the existing "Anna Theme" top-level menu (slug `anna-theme-settings`).
3. WHEN a user without the `manage_options` capability navigates to the Content Porter page, THE Content_Porter SHALL terminate page rendering using the WordPress permission-denied mechanism and display a message indicating the user does not have permission to access this page.
4. WHEN a user with the `manage_options` capability navigates to the Content Porter page, THE Content_Porter SHALL load the page without an error and render both the export section and the import section visible on screen.
5. WHEN an AJAX request is received by the Content_Porter, THE Content_Porter SHALL verify the `manage_options` capability for the current user before reading or writing any data.
6. WHEN a form submission is received by the Content_Porter, THE Content_Porter SHALL verify the `manage_options` capability for the current user before reading or writing any data.
7. WHEN an AJAX request is received by the Content_Porter, THE Content_Porter SHALL verify a valid WordPress nonce before reading or writing any data.
8. WHEN a form submission is received by the Content_Porter, THE Content_Porter SHALL verify a valid WordPress nonce before reading or writing any data.
9. IF a nonce verification fails, THEN THE Content_Porter SHALL return an error response, halt all further processing of that request, and leave all previously stored data unchanged.
10. IF a capability check fails on an AJAX or form request, THEN THE Content_Porter SHALL return an error response, halt all further processing of that request, and leave all previously stored data unchanged.

---

### Requirement 2: Section Registry

**User Story:** As a site administrator, I want to select individual pages or sections for export, so that I can transfer only the content I need without overwriting unrelated data on the destination site.

#### Acceptance Criteria

1. THE Content_Porter SHALL maintain a Section_Registry that maps each human-readable page name to its `anna_theme_options` key prefix(es) and exact key(s), covering at minimum:
   - **Home** — prefixes: `hero_*`, `intro_*`, `recognition_*`, `services_*`, `about_*` (home-section keys only), `testimonials_*`, `cta_*`
   - **About Page** — prefix: `about_pg_*`
   - **Coaching Page** — prefix: `coaching_pg_*`
   - **Oasis Page** — prefix: `oasis_pg_*`
   - **Speaking Page** — prefix: `speaking_pg_*`
   - **Mental Health Support Page** — prefix: `mhs_pg_*`
   - **Move Page** — prefix: `move_pg_*`
   - **Reviews Page** — prefix: `reviews_pg_*`
   - **Contact Page** — prefix: `contact_pg_*`
   - **Global Brand & Layout** — prefixes: `color_*`, `font_*`, `container_*`, `section_padding_*`, `border_radius_*`, `header_*`; exact keys: `site_logo_id`, `footer_logo_id`
   - **Footer & Social** — prefixes: `footer_*`, `social_*`, `contact_*`, `newsletter_*`, `copyright_*`, `privacy_*`, `terms_*`
2. WHEN the `anna_scaffolded_pages` option contains one or more Scaffolded_Pages, THE Content_Porter SHALL dynamically add each scaffolded page's `option_prefix` field value (in the form `{code}_pg_*`) as a new entry in the Section_Registry, using the scaffolded page's `title` as the human-readable label.
3. THE Content_Porter SHALL resolve key membership by prefix matching against the live `anna_theme_options` array, so newly added keys are automatically included under their section without code changes; exact-key entries in the Section_Registry SHALL be matched by equality, not prefix.
4. WHEN a key in `anna_theme_options` matches the prefix of more than one Section_Registry entry, THE Content_Porter SHALL assign that key exclusively to the entry whose prefix is the longest (most specific) match; exact-key matches SHALL take precedence over any prefix match of equal or lesser specificity.

---

### Requirement 3: Export UI

**User Story:** As a site administrator, I want to select one or more page sections and export their content to a JSON file, so that I can migrate or back up specific pages.

#### Acceptance Criteria

1. THE Content_Porter SHALL render a list of checkboxes on the export UI, one per entry in the Section_Registry, with labels matching each entry's registered human-readable name.
2. WHEN no sections are checked, THE Content_Porter SHALL disable the export submit button and display an inline validation message instructing the user to select at least one section.
3. WHEN the user submits the export form with one or more sections selected, THE Content_Porter SHALL collect all `anna_theme_options` keys matching the selected section prefixes and exact keys.
4. WHEN no `anna_theme_options` keys match the selected sections, THE Content_Porter SHALL abort the export and display an error message stating that no content was found for the selected sections.
5. WHEN the user submits the export form with one or more sections selected and matching keys are found, THE Content_Porter SHALL generate a UTF-8 encoded JSON Export_Package.
6. WHEN the Export_Package is generated, THE Content_Porter SHALL deliver it to the browser as a file download.
7. THE Content_Porter SHALL name the exported file using the pattern `anna-content-porter-{YYYY-MM-DD}.json`, where the date reflects the UTC date at the time of export.

---

### Requirement 4: Export Package Metadata

**User Story:** As a site administrator, I want the export file to contain descriptive metadata, so that I can identify what it contains and verify compatibility before importing.

#### Acceptance Criteria

1. THE Content_Porter SHALL include a `meta` object at the root of every Export_Package containing:
   - `plugin`: the string `"anna-content-porter"`
   - `schema_version`: an integer starting at `1` and incremented with each breaking change to the Export_Package format
   - `theme_name`: the value of the `Name` header from the Anna Baylis theme; an empty string if the header is absent
   - `theme_version`: the value of the `Version` header from the Anna Baylis theme; an empty string if the header is absent
   - `exported_at`: an ISO 8601 UTC datetime string representing the moment the export was initiated
   - `source_site_url`: the source site's canonical home URL
   - `exported_sections`: an array of the human-readable section names that were exported, in the order they were selected by the user
2. IF the `schema_version` in an uploaded Export_Package differs from the `schema_version` the Content_Porter on the Destination_Site understands, THEN THE Content_Porter SHALL display a warning message that states both version numbers and require the user to explicitly acknowledge the warning before the import form becomes submittable.

---

### Requirement 5: Image Handling During Export

**User Story:** As a site administrator, I want images to be included in the export file, so that I do not have to manually re-upload every photo on the destination site.

#### Acceptance Criteria

1. WHEN exporting, THE Content_Porter SHALL identify every Media_Field value (integer Attachment_ID greater than zero) within the selected sections.
2. WHEN a Media_Field contains a non-zero Attachment_ID that resolves to a readable file in the WordPress uploads directory, THE Content_Porter SHALL encode the image as an Image_Payload containing: `original_filename`, `mime_type`, `source_url`, `base64_data`.
3. WHEN a Media_Field contains a non-zero Attachment_ID that does not resolve to a readable file, THE Content_Porter SHALL include the field in the export with its original integer value and SHALL add an entry to an `export_warnings` array in the Export_Package noting the unresolvable attachment.
4. WHEN a Media_Field contains a zero or empty value, THE Content_Porter SHALL export it as-is without attempting image resolution.
5. THE Content_Porter SHALL store all Image_Payloads in a top-level `images` object in the Export_Package, keyed by the original Attachment_ID cast to a string.
6. WHEN an Image_Payload is stored for a Media_Field, THE Content_Porter SHALL replace that Media_Field's value in the exported content data with the string key referencing the corresponding entry in the `images` object rather than the original integer ID.

---

### Requirement 6: Import UI

**User Story:** As a site administrator, I want to upload an Export_Package and choose how conflicts are handled, so that I can safely populate the destination site.

#### Acceptance Criteria

1. THE Content_Porter SHALL render a file upload form on the import UI accepting files with a `.json` extension.
2. WHEN the user submits the import form, THE Content_Porter SHALL validate that the uploaded file content parses as valid JSON before proceeding.
3. WHEN the uploaded file does not parse as valid JSON, THE Content_Porter SHALL display an error message stating that the file is not a valid JSON document and halt the import without writing any data.
4. WHEN the uploaded file is valid JSON but the parsed object does not contain a `meta.plugin` field equal to the string `"anna-content-porter"`, THE Content_Porter SHALL display an error message stating that the file was not produced by the Anna Content Porter plugin and halt the import without writing any data.
5. THE Content_Porter SHALL present the user with two Import_Mode options before writing any data: **Overwrite** and **Skip**.
6. THE Content_Porter SHALL display the values of `meta.exported_sections`, `meta.source_site_url`, and `meta.exported_at` from the uploaded Export_Package to the user before they confirm the import.
7. WHEN the user confirms the import, THE Content_Porter SHALL write the sanitised and resolved imported content to `anna_theme_options`.
8. IF the write to `anna_theme_options` fails, THEN THE Content_Porter SHALL display an error message stating that the database write failed, leave `anna_theme_options` in its pre-import state, and not display a success notice.

---

### Requirement 7: Import Mode — Overwrite

**User Story:** As a site administrator, I want an "Overwrite" mode that replaces existing content with imported values, so that I can fully synchronise a destination site with the source.

#### Acceptance Criteria

1. WHEN Import_Mode is **Overwrite** and a key from the Export_Package already exists in `anna_theme_options` on the Destination_Site, THE Content_Porter SHALL replace the existing value with the imported value.
2. WHEN Import_Mode is **Overwrite** and a key from the Export_Package does not exist in `anna_theme_options` on the Destination_Site, THE Content_Porter SHALL add the key with the imported value.
3. WHEN Import_Mode is **Overwrite**, THE Content_Porter SHALL commit all keys sourced from the Export_Package in a single all-or-nothing write; if the write fails partway through, no keys from the current import operation SHALL remain in `anna_theme_options`.
4. IF the write operation in Overwrite mode fails, THEN THE Content_Porter SHALL restore `anna_theme_options` to the state it held immediately before the import was initiated and display an error message to the user.

---

### Requirement 8: Import Mode — Skip

**User Story:** As a site administrator, I want a "Skip" mode that preserves existing content and only fills in missing keys, so that I can safely add new page content without overwriting customisations.

#### Acceptance Criteria

1. WHEN Import_Mode is **Skip** and a key from the Export_Package already exists in `anna_theme_options` on the Destination_Site with a non-empty value — where "non-empty" means: a non-empty string for Scalar_Fields, a non-empty array for Repeater_Fields, and a non-zero integer for Media_Fields — THE Content_Porter SHALL NOT modify that key.
2. WHEN Import_Mode is **Skip** and a key from the Export_Package either does not exist in `anna_theme_options` on the Destination_Site, or exists with an empty value — where "empty" means: an empty string or the value `"empty--"` for Scalar_Fields, an empty array `[]` for Repeater_Fields, and `0` or empty string for Media_Fields — THE Content_Porter SHALL set that key to the imported value.
3. WHEN Import_Mode is **Skip**, THE Content_Porter SHALL commit all keys set during the Skip operation (both newly added keys and keys that existed with empty values) in a single all-or-nothing write; if the write fails, no keys from the current import operation SHALL remain changed in `anna_theme_options`.

---

### Requirement 9: Image Handling During Import

**User Story:** As a site administrator, I want images to be automatically imported into the destination media library, so that images referenced by content fields are immediately available on the new site.

#### Acceptance Criteria

1. WHEN importing and an Export_Package `images` object is present, THE Content_Porter SHALL iterate over every Media_Field — defined as any `anna_theme_options` key whose exported value is a string key referencing an entry in the `images` object — in the imported content data.
2. WHEN a content field references an image key and a corresponding Image_Payload exists in the `images` object, THE Content_Porter SHALL decode the base64 data and create a new WordPress media attachment for that image.
3. WHEN the image attachment is successfully created on the Destination_Site, THE Content_Porter SHALL replace the image reference in the content data with the new integer Attachment_ID before writing to `anna_theme_options`.
4. IF Import_Mode is **Skip** and a Media_Field already contains a non-zero Attachment_ID on the Destination_Site, THEN THE Content_Porter SHALL NOT decode, upload, or replace that Media_Field with an imported image; the existing Attachment_ID SHALL be preserved.
5. IF an image creation fails for any reason (write permission error, invalid base64 data, or other), THEN THE Content_Porter SHALL record both the image key and the specific failure reason in the import result summary, set the corresponding Media_Field value to `0`, and continue processing the remaining fields without halting the import.
6. IF a content field references an image key but no corresponding Image_Payload exists in the `images` object, THEN THE Content_Porter SHALL record the image key and a missing-payload warning in the import result summary and set the corresponding Media_Field value to `0`.
7. THE Content_Porter SHALL evaluate the Skip-mode check (criterion 4) for each Media_Field before attempting to decode or create any image attachment for that field.

---

### Requirement 10: Import Result Summary

**User Story:** As a site administrator, I want a clear summary after import completes, so that I know exactly what was written, what was skipped, and whether any images failed to import.

#### Acceptance Criteria

1. WHEN an import operation completes (with or without errors), THE Content_Porter SHALL display a result summary on the admin page that persists until the user navigates away or initiates a new import.
2. THE Content_Porter SHALL include in the summary:
   - The number of content keys written (keys added or replaced, depending on Import_Mode)
   - The number of content keys skipped (keys preserved due to Skip mode or rejected due to allowlist enforcement per Requirement 11)
   - The number of images successfully created in the destination media library
   - The number of image import failures (failed creation attempts, as defined in Requirement 9 criterion 5)
   - The number of missing-payload warnings (image references with no corresponding Image_Payload, as defined in Requirement 9 criterion 6)
3. WHEN the import completed with zero failures, zero missing-payload warnings, and zero rejected keys, THE Content_Porter SHALL display a success notice.
4. WHEN the import completed with one or more failures, missing-payload warnings, or rejected keys, THE Content_Porter SHALL display a warning notice listing the key name of each affected field, up to a maximum of 50 entries; if more than 50 entries exist, the notice SHALL indicate the total count and that not all entries are shown.
5. WHEN the import itself fails entirely due to a condition defined in Requirement 6 criterion 8 or Requirement 13, THE Content_Porter SHALL display an error notice with a human-readable description of the failure cause and SHALL NOT display a success or warning notice for that operation.

---

### Requirement 11: Data Sanitisation on Import

**User Story:** As a site administrator, I want all imported data to be sanitised before it is written to the database, so that malicious content in an Export_Package cannot compromise the site.

#### Acceptance Criteria

1. WHEN importing, THE Content_Porter SHALL sanitise all imported Scalar_Field string values as follows: values for keys whose registered field type is `textarea` SHALL be processed with `sanitize_textarea_field`; values for all other string Scalar_Field types (text, color, select) SHALL be processed with `sanitize_text_field`; toggle values SHALL be cast to boolean; color values SHALL additionally be validated to match the pattern `#[0-9a-fA-F]{3,8}` and set to an empty string if they do not match.
2. WHEN importing, THE Content_Porter SHALL pass all imported URL values through `esc_url_raw` before writing them.
3. WHEN importing, THE Content_Porter SHALL cast all Attachment_ID values to `absint` before writing them.
4. WHEN importing, THE Content_Porter SHALL recursively apply the same per-field-type sanitisation rules defined in criteria 1, 2, and 3 to all sub-fields within Repeater_Field arrays.
5. WHEN importing, THE Content_Porter SHALL reject and skip any key from the Export_Package whose name is not registered in the Section_Registry (by prefix match or exact-key match), and SHALL record each rejected key name in the import result summary.

---

### Requirement 12: Export File Size Awareness

**User Story:** As a site administrator, I want to be informed if an export file is likely to be very large, so that I can plan accordingly and avoid browser or server timeouts.

#### Acceptance Criteria

1. WHEN the sum of the uncompressed file sizes of all Image_Payloads to be included in the export exceeds 10 MB, THE Content_Porter SHALL display a warning notice in the export UI — before the download is triggered — stating the estimated size in MB rounded to one decimal place.
2. THE Content_Porter SHALL still allow the export to proceed after displaying the size warning; the warning is informational only and does not require acknowledgement.
3. IF the PHP `memory_limit` is less than four-thirds of the total uncompressed size of all selected images (the minimum memory required for base64 encoding), THEN THE Content_Porter SHALL abort the export before producing any output destined for the browser, and display an error message advising the user to export fewer sections or increase the PHP `memory_limit`.

---

### Requirement 13: Security — File Upload Validation

**User Story:** As a site administrator, I want the import process to reject invalid or malicious files, so that uploading an unexpected file cannot harm the site.

#### Acceptance Criteria

1. WHEN processing an uploaded import file, THE Content_Porter SHALL verify that the uploaded file's reported MIME type is `application/json` or `text/plain`; IF the MIME type is neither of these, THEN THE Content_Porter SHALL reject the file with an error message stating the received MIME type and halt the import without reading the file contents.
2. WHEN the uploaded file's size exceeds 50 MB, THE Content_Porter SHALL reject it with an error message stating the file is too large and the 50 MB limit, and halt the import without reading the file contents.
3. IF the WordPress `WP_Filesystem` abstraction is available in the current environment, THEN THE Content_Porter SHALL read the uploaded file contents using `WP_Filesystem` methods; IF `WP_Filesystem` is not available, THEN THE Content_Porter SHALL fall back to reading the file using standard PHP file-reading functions.
4. THE Content_Porter SHALL decode the imported JSON using a standard JSON parser and SHALL NOT pass any content from the imported file to `eval`, `preg_replace` with the `e` modifier, `create_function`, `call_user_func`, or any other code-execution mechanism.
