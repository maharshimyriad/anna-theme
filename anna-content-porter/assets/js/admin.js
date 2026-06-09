/* Anna Content Porter – Admin UI */
(function () {
  "use strict";

  /**
   * Initialise all porter UI interactions.
   * Uses the readyState pattern so it works whether the script runs
   * synchronously (footer), or deferred (DOMContentLoaded already fired).
   */
  function init() {
    initExportPanel();
    initUploadArea();
    initImportModeCards();
  }

  /* ── Export panel ─────────────────────────────────────────────────────── */

  function initExportPanel() {
    var checkboxes = document.querySelectorAll(".anna-porter-section-cb");
    var exportBtn = document.getElementById("anna-porter-export-btn");
    var selectAllLink = document.getElementById("anna-porter-select-all");
    var countEl = document.getElementById("anna-porter-selected-count");
    var hintEl = document.getElementById("anna-porter-sel-hint");
    var selBar = document.getElementById("anna-porter-sel-bar");
    var exportForm = document.getElementById("anna-porter-export-form");

    if (!exportBtn || !checkboxes.length) {
      return;
    }

    var labelSelect = selectAllLink
      ? selectAllLink.dataset.labelSelect || "Select All"
      : "";
    var labelDeselect = selectAllLink
      ? selectAllLink.dataset.labelDeselect || "Deselect All"
      : "";
    var hintReady = hintEl ? hintEl.dataset.hintReady || "Ready to export" : "";
    var hintPending = hintEl
      ? hintEl.dataset.hintPending || "Select at least one section"
      : "";

    function checkedCount() {
      var n = 0;
      checkboxes.forEach(function (cb) {
        if (cb.checked) n++;
      });
      return n;
    }

    function syncAll() {
      var n = checkedCount();
      var total = checkboxes.length;
      var ready = n > 0;

      // Export button
      exportBtn.disabled = !ready;

      // Count display
      if (countEl) countEl.textContent = n;

      // Hint text
      if (hintEl) {
        hintEl.textContent = ready ? hintReady : hintPending;
        hintEl.classList.toggle("is-ready", ready);
      }

      // Selection bar highlight
      if (selBar) {
        selBar.classList.toggle("has-selection", ready);
      }

      // Select-all link label
      if (selectAllLink) {
        selectAllLink.textContent = n === total ? labelDeselect : labelSelect;
      }
    }

    // Sync label card state (is-checked class) with the checkbox state.
    function syncLabelState(cb) {
      var label = cb.closest(".anna-porter-section-label");
      if (label) label.classList.toggle("is-checked", cb.checked);
    }

    // Bind individual checkbox changes.
    checkboxes.forEach(function (cb) {
      cb.addEventListener("change", function () {
        syncLabelState(cb);
        syncAll();
      });
    });

    // Select All / Deselect All.
    if (selectAllLink) {
      selectAllLink.addEventListener("click", function (e) {
        e.preventDefault();
        var allChecked = checkedCount() === checkboxes.length;
        checkboxes.forEach(function (cb) {
          cb.checked = !allChecked;
          syncLabelState(cb);
        });
        syncAll();
      });
    }

    // Loading state on export submit.
    if (exportForm) {
      exportForm.addEventListener("submit", function () {
        exportBtn.classList.add("is-loading");
        // Re-enable after a generous timeout to handle errors / browser stays on page.
        setTimeout(function () {
          exportBtn.classList.remove("is-loading");
        }, 12000);
      });
    }

    // Set initial state.
    checkboxes.forEach(function (cb) {
      syncLabelState(cb);
    });
    syncAll();
  }

  /* ── File upload drag-and-drop area ───────────────────────────────────── */

  function initUploadArea() {
    var area = document.getElementById("anna-porter-upload-area");
    var fileInput = document.getElementById("anna-porter-file-input");
    var fileLabel = document.getElementById("anna-porter-upload-filename");

    if (!area || !fileInput) return;

    function showFile(name) {
      area.classList.add("has-file");
      if (fileLabel) fileLabel.textContent = name;
    }

    fileInput.addEventListener("change", function () {
      if (fileInput.files && fileInput.files[0]) {
        showFile(fileInput.files[0].name);
      } else {
        area.classList.remove("has-file");
        if (fileLabel) fileLabel.textContent = "";
      }
    });

    area.addEventListener("dragover", function (e) {
      e.preventDefault();
      area.classList.add("is-drag-over");
    });

    area.addEventListener("dragleave", function () {
      area.classList.remove("is-drag-over");
    });

    area.addEventListener("drop", function (e) {
      e.preventDefault();
      area.classList.remove("is-drag-over");
      var files = e.dataTransfer && e.dataTransfer.files;
      if (files && files[0]) {
        // Assign the dropped file to the input via DataTransfer.
        try {
          var dt = new DataTransfer();
          dt.items.add(files[0]);
          fileInput.files = dt.files;
        } catch (err) {
          // DataTransfer not available in all browsers — silently ignore.
        }
        showFile(files[0].name);
      }
    });
  }

  /* ── Import mode radio cards ──────────────────────────────────────────── */

  function initImportModeCards() {
    var radios = document.querySelectorAll(
      ".anna-porter-mode-option input[type='radio']",
    );
    if (!radios.length) return;

    // Sync visual card highlight with the checked radio.
    function syncCards() {
      radios.forEach(function (r) {
        var card = r.parentElement.querySelector(".anna-porter-mode-card");
        if (card) card.classList.toggle("is-selected", r.checked);
      });
    }

    radios.forEach(function (r) {
      r.addEventListener("change", syncCards);
    });

    syncCards();
  }

  /* ── Bootstrap ────────────────────────────────────────────────────────── */

  if (document.readyState === "loading") {
    // Script loaded synchronously before DOMContentLoaded.
    document.addEventListener("DOMContentLoaded", init);
  } else {
    // Script was deferred or DOM is already parsed — run immediately.
    init();
  }
})();
