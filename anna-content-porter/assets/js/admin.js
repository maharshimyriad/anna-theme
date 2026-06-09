/**
 * Anna Content Porter — Admin JS
 *
 * Handles the export button enable/disable state and the
 * Select All / Deselect All toggle for section checkboxes.
 *
 * No jQuery dependency — vanilla JS only.
 *
 * @package Anna_Content_Porter
 * @since   1.0.0
 */

document.addEventListener( 'DOMContentLoaded', function () {

	var exportBtn   = document.getElementById( 'anna-porter-export-btn' );
	var selectAll   = document.getElementById( 'anna-porter-select-all' );
	var checkboxes  = document.querySelectorAll( 'input[name="sections[]"]' );

	// Guard: exit early if the export button is not present (not on the porter page).
	if ( ! exportBtn ) {
		return;
	}

	// ── Export button enable / disable ────────────────────────────────────────

	/**
	 * Enables the export button when at least one section checkbox is checked;
	 * disables it when none are checked.
	 */
	function updateExportButton() {
		var anyChecked = false;

		for ( var i = 0; i < checkboxes.length; i++ ) {
			if ( checkboxes[ i ].checked ) {
				anyChecked = true;
				break;
			}
		}

		exportBtn.disabled = ! anyChecked;
	}

	// Set initial state on page load.
	updateExportButton();

	// Update state whenever a checkbox changes.
	for ( var i = 0; i < checkboxes.length; i++ ) {
		checkboxes[ i ].addEventListener( 'change', updateExportButton );
	}

	// ── Select All / Deselect All toggle ──────────────────────────────────────

	if ( selectAll ) {
		selectAll.addEventListener( 'click', function ( event ) {
			event.preventDefault();

			// Check whether every checkbox is currently checked.
			var allChecked = true;

			for ( var j = 0; j < checkboxes.length; j++ ) {
				if ( ! checkboxes[ j ].checked ) {
					allChecked = false;
					break;
				}
			}

			if ( allChecked ) {
				// All are checked — uncheck everything and reset link text.
				for ( var j = 0; j < checkboxes.length; j++ ) {
					checkboxes[ j ].checked = false;
				}
				selectAll.textContent = 'Select All';
			} else {
				// At least one is unchecked — check everything and update link text.
				for ( var j = 0; j < checkboxes.length; j++ ) {
					checkboxes[ j ].checked = true;
				}
				selectAll.textContent = 'Deselect All';
			}

			updateExportButton();
		} );
	}

} );
