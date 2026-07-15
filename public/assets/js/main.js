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

  var searchForm = document.querySelector('.search-form');
  if (searchForm) {
    var searchInput = searchForm.querySelector('input[type="text"], input[type="search"]');
    if (searchInput) {
      var searchTimer;
      searchInput.addEventListener('input', function () {
        clearTimeout(searchTimer);
        searchTimer = setTimeout(function () {
          searchForm.submit();
        }, 600);
      });
    }
  }

  var confirmDeleteBtns = document.querySelectorAll('[data-confirm]');
  confirmDeleteBtns.forEach(function (btn) {
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

  var backToTopBtn = document.querySelector('.back-to-top');
  if (backToTopBtn) {
    window.addEventListener('scroll', function () {
      if (window.pageYOffset > 300) {
        backToTopBtn.classList.add('visible');
      } else {
        backToTopBtn.classList.remove('visible');
      }
    });
    backToTopBtn.addEventListener('click', function () {
      window.scrollTo({ top: 0, behavior: 'smooth' });
    });
  }

  var fileIcons = document.querySelectorAll('.file-icon');
  fileIcons.forEach(function (icon) {
    var type = icon.getAttribute('data-type');
    if (type) {
      icon.className = 'file-icon ' + type.toLowerCase();
    }
  });

  var fileBadges = document.querySelectorAll('.badge[data-file-type]');
  fileBadges.forEach(function (badge) {
    var type = badge.getAttribute('data-file-type').toLowerCase();
    var classMap = {
      pdf: 'badge-pdf',
      doc: 'badge-doc',
      docx: 'badge-doc',
      jpg: 'badge-image',
      jpeg: 'badge-image',
      png: 'badge-image',
      gif: 'badge-image',
      mp4: 'badge-video',
      avi: 'badge-video',
      zip: 'badge-zip',
      rar: 'badge-zip',
      '7z': 'badge-zip'
    };
    if (classMap[type]) {
      badge.className = 'badge ' + classMap[type];
    }
  });

  if (typeof bootstrap !== 'undefined' && bootstrap.Tooltip) {
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-toggle="tooltip"]'));
    tooltipTriggerList.forEach(function (el) {
      new bootstrap.Tooltip(el);
    });
  }

  var ajaxForms = document.querySelectorAll('form[data-ajax]');
  ajaxForms.forEach(function (form) {
    form.addEventListener('submit', function (e) {
      e.preventDefault();
      var formData = new FormData(form);
      var url = form.getAttribute('action') || window.location.href;
      var method = form.getAttribute('method') || 'POST';
      var submitBtn = form.querySelector('[type="submit"]');
      if (submitBtn) {
        submitBtn.disabled = true;
        submitBtn.innerHTML = '\u062C\u0627\u0631\u064D \u0627\u0644\u0625\u0631\u0633\u0627\u0644...';
      }
      fetch(url, {
        method: method.toUpperCase(),
        body: formData,
        headers: {
          'X-Requested-With': 'XMLHttpRequest'
        }
      })
      .then(function (response) { return response.json(); })
      .then(function (data) {
        if (data.success) {
          if (data.redirect) {
            window.location.href = data.redirect;
          } else if (data.message) {
            var flashContainer = document.querySelector('.flash-messages');
            if (flashContainer) {
              flashContainer.innerHTML = '<div class="flash-message flash-message-success">' + data.message + '<button class="close-btn">&times;</button></div>';
            }
          }
        } else {
          if (data.message) {
            var flashContainer = document.querySelector('.flash-messages');
            if (flashContainer) {
              flashContainer.innerHTML = '<div class="flash-message flash-message-error">' + data.message + '<button class="close-btn">&times;</button></div>';
            }
          }
        }
        if (submitBtn) {
          submitBtn.disabled = false;
          submitBtn.innerHTML = submitBtn.getAttribute('data-original-text') || '\u0625\u0631\u0633\u0627\u0644';
        }
      })
      .catch(function () {
        if (submitBtn) {
          submitBtn.disabled = false;
          submitBtn.innerHTML = submitBtn.getAttribute('data-original-text') || '\u0625\u0631\u0633\u0627\u0644';
        }
      });
    });
    var origSubmitText = form.querySelector('[type="submit"]');
    if (origSubmitText) {
      origSubmitText.setAttribute('data-original-text', origSubmitText.innerHTML);
    }
  });

  var modalTriggers = document.querySelectorAll('[data-modal-target]');
  modalTriggers.forEach(function (trigger) {
    trigger.addEventListener('click', function () {
      var targetId = trigger.getAttribute('data-modal-target');
      var modal = document.querySelector(targetId);
      if (modal) {
        modal.classList.add('show');
        document.body.style.overflow = 'hidden';
      }
    });
  });

  var modalCloseBtns = document.querySelectorAll('.modal-overlay .modal-close, .modal-overlay .modal-cancel');
  modalCloseBtns.forEach(function (btn) {
    btn.addEventListener('click', function () {
      var modal = btn.closest('.modal-overlay');
      if (modal) {
        modal.classList.remove('show');
        document.body.style.overflow = '';
      }
    });
  });

  var modalOverlays = document.querySelectorAll('.modal-overlay');
  modalOverlays.forEach(function (overlay) {
    overlay.addEventListener('click', function (e) {
      if (e.target === overlay) {
        overlay.classList.remove('show');
        document.body.style.overflow = '';
      }
    });
  });

  document.addEventListener('keydown', function (e) {
    if (e.key === 'Escape') {
      modalOverlays.forEach(function (overlay) {
        if (overlay.classList.contains('show')) {
          overlay.classList.remove('show');
          document.body.style.overflow = '';
        }
      });
    }
  });

  var galleryItems = document.querySelectorAll('.gallery-item');
  galleryItems.forEach(function (item) {
    item.addEventListener('click', function () {
      var img = item.querySelector('img');
      if (img) {
        var src = img.getAttribute('src');
        var overlay = document.createElement('div');
        overlay.className = 'modal-overlay show';
        overlay.innerHTML = '<div style="display:flex;align-items:center;justify-content:center;width:100%;height:100%;padding:20px;"><img src="' + src + '" style="max-width:90%;max-height:90%;border-radius:8px;box-shadow:0 20px 60px rgba(0,0,0,0.3);"></div>';
        document.body.appendChild(overlay);
        document.body.style.overflow = 'hidden';
        overlay.addEventListener('click', function () {
          overlay.remove();
          document.body.style.overflow = '';
        });
      }
    });
  });

  var accordionHeaders = document.querySelectorAll('.accordion-header');
  accordionHeaders.forEach(function (header) {
    header.addEventListener('click', function () {
      var content = header.nextElementSibling;
      if (content && content.classList.contains('accordion-content')) {
        content.classList.toggle('open');
        var icon = header.querySelector('.accordion-icon');
        if (icon) {
          icon.style.transform = content.classList.contains('open') ? 'rotate(180deg)' : 'rotate(0deg)';
        }
      }
    });
  });

  var countUpElements = document.querySelectorAll('[data-count]');
  countUpElements.forEach(function (el) {
    var target = parseInt(el.getAttribute('data-count'), 10);
    if (isNaN(target)) return;
    var duration = parseInt(el.getAttribute('data-duration'), 10) || 1500;
    var start = 0;
    var increment = target / (duration / 16);
    var current = start;
    var timer = setInterval(function () {
      current += increment;
      if (current >= target) {
        current = target;
        clearInterval(timer);
      }
      el.textContent = Math.floor(current).toLocaleString('ar-SA');
    }, 16);
  });

});
