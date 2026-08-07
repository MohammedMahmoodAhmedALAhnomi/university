document.addEventListener('DOMContentLoaded', function () {

  // Sidebar toggle (desktop collapse + mobile open/close)
  var sidebar = document.getElementById('adminSidebar');
  var toggleBtn = document.getElementById('sidebarToggle');
  var backdrop = document.getElementById('sidebarBackdrop');
  var wrapper = document.querySelector('.admin-wrapper');
  var isMobile = function () { return window.innerWidth < 992; };

  // Restore desktop collapsed state from localStorage
  var savedSidebarState = localStorage.getItem('adminSidebarCollapsed');
  if (!isMobile() && wrapper && savedSidebarState === 'true') {
    wrapper.classList.add('sidebar-collapsed');
  }

  function openSidebar() {
    if (sidebar) sidebar.classList.add('open');
    if (backdrop) backdrop.classList.add('show');
    document.body.style.overflow = 'hidden';
  }

  function closeSidebar() {
    if (sidebar) sidebar.classList.remove('open');
    if (backdrop) backdrop.classList.remove('show');
    document.body.style.overflow = '';
  }

  function toggleSidebar() {
    if (isMobile()) {
      if (sidebar && sidebar.classList.contains('open')) {
        closeSidebar();
      } else {
        openSidebar();
      }
    } else if (wrapper) {
      wrapper.classList.toggle('sidebar-collapsed');
      localStorage.setItem('adminSidebarCollapsed', wrapper.classList.contains('sidebar-collapsed'));
    }
  }

  var mobileCloseBtn = document.getElementById('mobileSidebarClose');

  if (toggleBtn) {
    toggleBtn.addEventListener('click', toggleSidebar);
  }
  if (backdrop) {
    backdrop.addEventListener('click', closeSidebar);
  }
  if (mobileCloseBtn) {
    mobileCloseBtn.addEventListener('click', closeSidebar);
  }

  // Close mobile sidebar on nav link click
  if (sidebar) {
    sidebar.querySelectorAll('.sidebar-a').forEach(function (link) {
      link.addEventListener('click', function () {
        if (isMobile()) closeSidebar();
      });
    });
  }

  // Close sidebar on Escape key
  document.addEventListener('keydown', function (e) {
    if (e.key === 'Escape' && isMobile() && sidebar && sidebar.classList.contains('open')) {
      closeSidebar();
    }
  });

  // Handle resize: sync sidebar state
  window.addEventListener('resize', function () {
    if (!isMobile()) {
      // Going to desktop: close mobile sidebar
      if (sidebar && sidebar.classList.contains('open')) {
        closeSidebar();
      }
    } else {
      // Going to mobile: remove collapsed class
      if (wrapper && wrapper.classList.contains('sidebar-collapsed')) {
        wrapper.classList.remove('sidebar-collapsed');
      }
    }
  });

  // Sidebar: highlight active link based on current path
  if (sidebar) {
    var currentPath = window.location.pathname;
    sidebar.querySelectorAll('.sidebar-a').forEach(function (link) {
      var href = link.getAttribute('href');
      if (href && href !== '#' && currentPath.indexOf(href) >= 0) {
        link.classList.add('active');
      }
    });
  }

  // Auto-dismiss flash messages after 5s
  var flashMessages = document.querySelectorAll('.alert-dismissible');
  flashMessages.forEach(function (msg) {
    setTimeout(function () {
      if (msg && msg.parentNode) {
        var bsAlert = bootstrap.Alert.getOrCreateInstance(msg);
        bsAlert.close();
      }
    }, 5000);
  });

  // Delete confirmation buttons
  document.querySelectorAll('[data-confirm]').forEach(function (btn) {
    btn.addEventListener('click', function (e) {
      var message = btn.getAttribute('data-confirm') || 'هل أنت متأكد من الحذف؟';
      if (!confirm(message)) {
        e.preventDefault();
      }
    });
  });

  // Delete confirmation forms
  document.querySelectorAll('form[data-confirm]').forEach(function (form) {
    form.addEventListener('submit', function (e) {
      var message = form.getAttribute('data-confirm') || 'هل أنت متأكد من الحذف؟';
      if (!confirm(message)) {
        e.preventDefault();
      }
    });
  });

  // Table search filter
  var tableSearch = document.querySelector('.table-search input');
  if (tableSearch) {
    tableSearch.addEventListener('keyup', function () {
      var query = tableSearch.value.toLowerCase();
      var table = document.querySelector('.admin-table');
      if (!table) return;
      var rows = table.querySelectorAll('tbody tr');
      rows.forEach(function (row) {
        var text = row.textContent.toLowerCase();
        row.style.display = text.indexOf(query) > -1 ? '' : 'none';
      });
    });
  }

  // Toggle switches auto-submit
  document.querySelectorAll('.toggle-switch input[type="checkbox"]').forEach(function (toggle) {
    toggle.addEventListener('change', function () {
      var form = toggle.closest('form');
      if (form) {
        form.submit();
      } else {
        var url = toggle.getAttribute('data-url');
        if (url) {
          var body = 'active=' + (toggle.checked ? '1' : '0');
          fetch(url, {
            method: 'POST',
            headers: {
              'X-Requested-With': 'XMLHttpRequest',
              'Content-Type': 'application/x-www-form-urlencoded'
            },
            body: body
          })
          .then(function (r) { return r.json(); })
          .then(function (data) {
            if (!data.success) { toggle.checked = !toggle.checked; }
          })
          .catch(function () { toggle.checked = !toggle.checked; });
        }
      }
    });
  });

  // Image upload preview
  document.querySelectorAll('.image-upload-wrapper input[type="file"]').forEach(function (input) {
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
          var el = uploadArea.querySelector('i, span');
          if (el) el.style.display = 'none';
          var img = document.createElement('img');
          img.src = event.target.result;
          img.alt = 'معاينة الصورة';
          uploadArea.appendChild(img);
        }
      };
      reader.readAsDataURL(file);
    });
  });

  // Color picker preview
  document.querySelectorAll('input[type="color"]').forEach(function (picker) {
    var preview = document.createElement('div');
    preview.style.cssText = 'width:40px;height:40px;border-radius:8px;border:2px solid #e5e7eb;margin-top:8px;';
    preview.style.backgroundColor = picker.value;
    picker.parentNode.appendChild(preview);
    picker.addEventListener('input', function () {
      preview.style.backgroundColor = picker.value;
    });
  });

  // Bootstrap tooltips
  if (typeof bootstrap !== 'undefined' && bootstrap.Tooltip) {
    var tooltipTriggers = [].slice.call(document.querySelectorAll('[data-toggle="tooltip"]'));
    tooltipTriggers.forEach(function (el) {
      new bootstrap.Tooltip(el);
    });
  }

  // Select placeholder color
  document.querySelectorAll('select.form-control:not(.no-enhance)').forEach(function (select) {
    select.addEventListener('change', function () {
      select.style.color = select.value ? '' : 'var(--gray-400)';
    });
    if (!select.value) select.style.color = 'var(--gray-400)';
  });

  // Date inputs default to today
  document.querySelectorAll('input[type="date"]').forEach(function (input) {
    if (!input.value) {
      var today = new Date();
      var yyyy = today.getFullYear();
      var mm = String(today.getMonth() + 1).padStart(2, '0');
      var dd = String(today.getDate()).padStart(2, '0');
      input.value = yyyy + '-' + mm + '-' + dd;
    }
  });

  // Auto-submit filter forms on change
  document.querySelectorAll('.filter-form').forEach(function (form) {
    form.querySelectorAll('select, input[type="text"], input[type="date"]').forEach(function (input) {
      input.addEventListener('change', function () { form.submit(); });
    });
  });

  // Select all checkbox
  var selectAll = document.querySelector('[data-select-all]');
  if (selectAll) {
    selectAll.addEventListener('change', function () {
      var target = selectAll.getAttribute('data-select-all');
      document.querySelectorAll(target).forEach(function (cb) {
        cb.checked = selectAll.checked;
      });
    });
  }

  // Bulk delete
  var bulkBtn = document.querySelector('[data-bulk-delete]');
  if (bulkBtn) {
    bulkBtn.addEventListener('click', function (e) {
      var target = bulkBtn.getAttribute('data-bulk-delete');
      var checked = document.querySelectorAll(target + ':checked');
      if (checked.length === 0) {
        alert('يرجى اختيار عناصر للحذف');
        e.preventDefault();
        return;
      }
      if (!confirm('هل أنت متأكد من حذف ' + checked.length + ' عنصر؟')) {
        e.preventDefault();
      }
    });
  }

  // Sortable tables
  document.querySelectorAll('.admin-table[data-sortable]').forEach(function (table) {
    table.querySelectorAll('th[data-sort]').forEach(function (header) {
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
