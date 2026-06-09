/* Anna Content Porter – Admin UI */
(function () {
  "use strict";

  document.addEventListener("DOMContentLoaded", function () {
    var checkboxes = document.querySelectorAll(".anna-porter-section-cb");
    var exportBtn = document.getElementById("anna-porter-export-btn");
    var selectAllLink = document.getElementById("anna-porter-select-all");

    if (!exportBtn || !checkboxes.length) {
      return;
    }

    // Labels are stored as data attributes on the link so PHP i18n works.
    var labelSelect = selectAllLink
      ? selectAllLink.dataset.labelSelect || "Select All"
      : "";
    var labelDeselect = selectAllLink
      ? selectAllLink.dataset.labelDeselect || "Deselect All"
      : "";

    function checkedCount() {
      var n = 0;
      checkboxes.forEach(function (cb) {
        if (cb.checked) {
          n++;
        }
      });
      return n;
    }

    function syncButton() {
      exportBtn.disabled = checkedCount() === 0;
    }

    function syncSelectAllLabel() {
      if (!selectAllLink) {
        return;
      }
      selectAllLink.textContent =
        checkedCount() === checkboxes.length ? labelDeselect : labelSelect;
    }

    // React to individual checkbox changes.
    checkboxes.forEach(function (cb) {
      cb.addEventListener("change", function () {
        syncButton();
        syncSelectAllLabel();
      });
    });

    // Select All / Deselect All toggle.
    if (selectAllLink) {
      selectAllLink.addEventListener("click", function (e) {
        e.preventDefault();
        var allChecked = checkedCount() === checkboxes.length;
        checkboxes.forEach(function (cb) {
          cb.checked = !allChecked;
        });
        syncButton();
        syncSelectAllLabel();
      });
    }

    // Set initial state.
    syncButton();
    syncSelectAllLabel();
  });
})();
