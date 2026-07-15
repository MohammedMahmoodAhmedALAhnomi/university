document.addEventListener('DOMContentLoaded', function () {

  var flashMessages = document.querySelectorAll('.flash-message');
  flashMessages.forEach(function (msg) {
    setTimeout(function () {
      msg.style.transition = 'opacity 0.5s ease';
      msg.style.opacity = '0';
      setTimeout(function () {
        msg.style.display = 'none';
      }, 500);
    }, 5000);
  });

  flashMessages.forEach(function (msg) {
    var closeBtn = msg.querySelector('.close-btn');
    if (closeBtn) {
      closeBtn.addEventListener('click', function () {
        msg.style.transition = 'opacity 0.3s ease';
        msg.style.opacity = '0';
        setTimeout(function () {
          msg.style.display = 'none';
        }, 300);
      });
    }
  });

  var deleteButtons = document.querySelectorAll('[data-confirm]');
  deleteButtons.forEach(function (btn) {
    btn.addEventListener('click', function (e) {
      var message = btn.getAttribute('data-confirm') || '\u0647\u0644 \u0623\u0646\u062A \u0645\u062A\u0623\u0643\u062F \u0645\u0646 \u0627\u0644\u062D\u0630\u0641\u061F';
      if (!confirm(message)) {
        e.preventDefault();
      }
    });
  });

  var deleteForms = document.querySelectorAll('form[data-confirm]');
  deleteForms.forEach(function (form) {
    form.addEventListener('submit', function (e) {
      var message = form.getAttribute('data-confirm') || '\u0647\u0644 \u0623\u0646\u062A \u0645\u062A\u0623\u0643\u062F \u0645\u0646 \u0627\u0644\u062D\u0630\u0641\u061F';
      if (!confirm(message)) {
        e.preventDefault();
      }
    });
  });

  var sidebar = document.querySelector('.sidebar');
  var sidebarToggle = document.querySelector('.sidebar-toggle');
  var sidebarOverlay = document.querySelector('.sidebar-overlay');

  if (sidebarToggle && sidebar) {
    sidebarToggle.addEventListener('click', function () {
      sidebar.classList.toggle('open');
      if (sidebarOverlay) {
        sidebarOverlay.classList.toggle('show');
      }
      document.body.style.overflow = sidebar.classList.contains('open') ? 'hidden' : '';
    });
  }

  if (sidebarOverlay) {
    sidebarOverlay.addEventListener('click', function () {
      sidebar.classList.remove('open');
      sidebarOverlay.classList.remove('show');
      document.body.style.overflow = '';
    });
  }

  var tableSearch = document.querySelector('.table-search input');
  if (tableSearch) {
    tableSearch.addEventListener('keyup', function () {
      var query = tableSearch.value.toLowerCase();
      var table = document.querySelector('.admin-table');
      if (!table) return;
      var rows = table.querySelectorAll('tbody tr');
      rows.forEach(function (row) {
        var text = row.textContent.toLowerCase();
        if (text.indexOf(query) > -1) {
          row.style.display = '';
        } else {
          row.style.display = 'none';
        }
      });
    });
  }

  var toggleSwitches = document.querySelectorAll('.toggle-switch input[type="checkbox"]');
  toggleSwitches.forEach(function (toggle) {
    toggle.addEventListener('change', function () {
      var form = toggle.closest('form');
      if (form) {
        form.submit();
      } else {
        var url = toggle.getAttribute('data-url');
        if (url) {
          fetch(url, {
            method: 'POST',
            headers: {
              'X-Requested-With': 'XMLHttpRequest',
              'Content-Type': 'application/x-www-form-urlencoded'
            },
            body: 'active=' + (toggle.checked ? '1' : '0')
          })
          .then(function (r) { return r.json(); })
          .then(function (data) {
            if (!data.success) {
              toggle.checked = !toggle.checked;
            }
          })
          .catch(function () {
            toggle.checked = !toggle.checked;
          });
        }
      }
    });
  });

  var imageUploads = document.querySelectorAll('.image-upload-wrapper input[type="file"]');
  imageUploads.forEach(function (input) {
    input.addEventListener('change', function (e) {
      var file = e.target.files[0];
      if (!file) return;
      var reader = new FileReader();
      reader.onload = function (event) {
        var wrapper = input.closest('.image-upload-wrapper');
        if (!wrapper) return;
        var uploadArea = wrapper.querySelector('.upload-area');
        if (!uploadArea) return;
        var existingImg = uploadArea.querySelector('img');
        if (existingImg) {
          existingImg.src = event.target.result;
        } else {
          var icons = uploadArea.querySelectorAll('i, span');
          icons.forEach(function (el) { el.style.display = 'none'; });
          var img = document.createElement('img');
          img.src = event.target.result;
          img.alt = '\u0645\u0639\u0627\u064A\u0646\u0629 \u0627\u0644\u0635\u0648\u0631\u0629';
          uploadArea.appendChild(img);
        }
      };
      reader.readAsDataURL(file);
    });
  });

  var colorPickers = document.querySelectorAll('input[type="color"]');
  colorPickers.forEach(function (picker) {
    var preview = document.createElement('div');
    preview.style.cssText = 'width:40px;height:40px;border-radius:8px;border:2px solid #e5e7eb;margin-top:8px;';
    preview.style.backgroundColor = picker.value;
    picker.parentNode.appendChild(preview);
    picker.addEventListener('input', function () {
      preview.style.backgroundColor = picker.value;
    });
  });

  var adminForms = document.querySelectorAll('.admin-form form, form.needs-validation');
  adminForms.forEach(function (form) {
    form.addEventListener('submit', function (e) {
      var valid = true;
      var requiredInputs = form.querySelectorAll('[required]');
      requiredInputs.forEach(function (input) {
        var value = input.value.trim();
        if (input.type === 'email') {
          var emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
          if (!emailRegex.test(value)) {
            valid = false;
            input.classList.add('is-invalid');
            var feedback = input.parentNode.querySelector('.invalid-feedback');
            if (feedback) {
              feedback.textContent = '\u064A\u0631\u062C\u0649 \u5f62\u062E\u0627\u0644 \u0628\u0631\u064A\u062F \u5f62\u0644\u0643\u062A\u0631\u0648\u0646\u064A \u635D\u062D\u064A\u062D';
            }
          } else {
            input.classList.remove('is-invalid');
          }
        } else if (input.type === 'number') {
          if (value === '' || isNaN(parseFloat(value))) {
            valid = false;
            input.classList.add('is-invalid');
          } else {
            input.classList.remove('is-invalid');
            var min = parseFloat(input.getAttribute('min'));
            var max = parseFloat(input.getAttribute('max'));
            var num = parseFloat(value);
            if (!isNaN(min) && num < min) {
              valid = false;
              input.classList.add('is-invalid');
            } else if (!isNaN(max) && num > max) {
              valid = false;
              input.classList.add('is-invalid');
            } else {
              input.classList.remove('is-invalid');
            }
          }
        } else if (input.type === 'url') {
          try {
            new URL(value);
            input.classList.remove('is-invalid');
          } catch (_) {
            if (value !== '') {
              valid = false;
              input.classList.add('is-invalid');
            } else {
              input.classList.add('is-invalid');
            }
          }
        } else {
          if (value === '') {
            valid = false;
            input.classList.add('is-invalid');
          } else {
            input.classList.remove('is-invalid');
          }
        }
      });
      if (!valid) {
        e.preventDefault();
        var firstInvalid = form.querySelector('.is-invalid');
        if (firstInvalid) {
          firstInvalid.focus();
          firstInvalid.scrollIntoView({ behavior: 'smooth', block: 'center' });
        }
      }
    });

    var formInputs = form.querySelectorAll('.form-control');
    formInputs.forEach(function (input) {
      input.addEventListener('input', function () {
        if (input.classList.contains('is-invalid')) {
          if (input.value.trim() !== '') {
            input.classList.remove('is-invalid');
          }
        }
      });
      input.addEventListener('blur', function () {
        if (input.hasAttribute('required') && input.value.trim() === '') {
          input.classList.add('is-invalid');
        }
      });
    });
  });

  if (typeof bootstrap !== 'undefined' && bootstrap.Tooltip) {
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-toggle="tooltip"]'));
    tooltipTriggerList.forEach(function (el) {
      new bootstrap.Tooltip(el);
    });
  }

  var selectElements = document.querySelectorAll('select.form-control:not(.no-enhance)');
  selectElements.forEach(function (select) {
    select.addEventListener('change', function () {
      if (select.value) {
        select.style.color = 'var(--dark)';
      } else {
        select.style.color = 'var(--gray-400)';
      }
    });
    if (!select.value) {
      select.style.color = 'var(--gray-400)';
    }
  });

  var numberInputs = document.querySelectorAll('input[type="number"][data-format]');
  numberInputs.forEach(function (input) {
    input.addEventListener('blur', function () {
      var val = parseFloat(input.value);
      if (!isNaN(val)) {
        var decimals = parseInt(input.getAttribute('data-decimals'), 10) || 2;
        input.value = val.toFixed(decimals);
      }
    });
  });

  var dateInputs = document.querySelectorAll('input[type="date"]');
  dateInputs.forEach(function (input) {
    if (!input.value) {
      var today = new Date();
      var yyyy = today.getFullYear();
      var mm = String(today.getMonth() + 1).padStart(2, '0');
      var dd = String(today.getDate()).padStart(2, '0');
      input.value = yyyy + '-' + mm + '-' + dd;
    }
  });

  var sidebarLinks = document.querySelectorAll('.sidebar-item');
  var currentPath = window.location.pathname;
  sidebarLinks.forEach(function (link) {
    var href = link.getAttribute('href');
    if (href && currentPath.indexOf(href) > -1 && href !== '#') {
      link.classList.add('active');
    }
  });

  var parentMenus = document.querySelectorAll('.sidebar-item.has-submenu');
  parentMenus.forEach(function (item) {
    item.addEventListener('click', function (e) {
      e.preventDefault();
      var submenu = item.nextElementSibling;
      if (submenu && submenu.classList.contains('sidebar-submenu')) {
        submenu.classList.toggle('show');
        item.classList.toggle('expanded');
      }
    });
  });

  var filterForms = document.querySelectorAll('.filter-form');
  filterForms.forEach(function (form) {
    var inputs = form.querySelectorAll('select, input[type="text"], input[type="date"]');
    inputs.forEach(function (input) {
      input.addEventListener('change', function () {
        form.submit();
      });
    });
  });

  var selectAllCheckbox = document.querySelector('[data-select-all]');
  if (selectAllCheckbox) {
    selectAllCheckbox.addEventListener('change', function () {
      var target = selectAllCheckbox.getAttribute('data-select-all');
      var checkboxes = document.querySelectorAll(target);
      checkboxes.forEach(function (cb) {
        cb.checked = selectAllCheckbox.checked;
      });
    });
  }

  var bulkDeleteBtn = document.querySelector('[data-bulk-delete]');
  if (bulkDeleteBtn) {
    bulkDeleteBtn.addEventListener('click', function (e) {
      var target = bulkDeleteBtn.getAttribute('data-bulk-delete');
      var checked = document.querySelectorAll(target + ':checked');
      if (checked.length === 0) {
        alert('\u064A\u0631\u062C\u0649 \u0627\u062E\u062A\u064A\u0627\u0631 \u0639\u0646\u0627\u0635\u0631 \u0644\u0644\u062D\u0630\u0641');
        e.preventDefault();
        return;
      }
      if (!confirm('\u0647\u0644 \u0623\u0646\u062A \u0645\u062A\u0623\u0643\u062F \u0645\u0646 \u062D\u0630\u0641 ' + checked.length + ' \u0639\u0646\u0635\u0631\u061F')) {
        e.preventDefault();
      }
    });
  }

  var sortableTables = document.querySelectorAll('.admin-table[data-sortable]');
  sortableTables.forEach(function (table) {
    var headers = table.querySelectorAll('th[data-sort]');
    headers.forEach(function (header) {
      header.style.cursor = 'pointer';
      header.addEventListener('click', function () {
        var key = header.getAttribute('data-sort');
        var tbody = table.querySelector('tbody');
        var rows = Array.from(tbody.querySelectorAll('tr'));
        var direction = header.getAttribute('data-order') === 'asc' ? 'desc' : 'asc';
        header.setAttribute('data-order', direction);
        rows.sort(function (a, b) {
          var aVal = a.querySelector('[data-' + key + ']');
          var bVal = b.querySelector('[data-' + key + ']');
          var aText = aVal ? aVal.getAttribute('data-' + key) || aVal.textContent.trim() : '';
          var bText = bVal ? bVal.getAttribute('data-' + key) || bVal.textContent.trim() : '';
          if (!isNaN(parseFloat(aText)) && !isNaN(parseFloat(bText))) {
            return direction === 'asc' ? parseFloat(aText) - parseFloat(bText) : parseFloat(bText) - parseFloat(aText);
          }
          return direction === 'asc' ? aText.localeCompare(bText, 'ar') : bText.localeCompare(aText, 'ar');
        });
        rows.forEach(function (row) { tbody.appendChild(row); });
      });
    });
  });

});
