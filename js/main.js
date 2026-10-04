// main.js - client-side interactivity

// Mobile menu toggle
var menuBtn = document.getElementById('menuBtn');
if (menuBtn) {
  menuBtn.addEventListener('click', function () {
    document.getElementById('mainNav').classList.toggle('open');
  });
}

// Auto-hide flash messages after 4 seconds
document.querySelectorAll('.flash').forEach(function (el) {
  setTimeout(function () { el.style.display = 'none'; }, 4000);
});

// Register form validation
var regForm = document.getElementById('registerForm');
if (regForm) {
  regForm.addEventListener('submit', function (ev) {
    var pw = regForm.password.value, cf = regForm.confirm.value;
    if (pw.length < 6) { alert('Password must be at least 6 characters.'); ev.preventDefault(); }
    else if (pw !== cf) { alert('Passwords do not match.'); ev.preventDefault(); }
  });
}

// Checkout phone validation (10 digits)
var coForm = document.getElementById('checkoutForm');
if (coForm) {
  coForm.addEventListener('submit', function (ev) {
    if (!/^[0-9]{10}$/.test(coForm.phone.value)) {
      alert('Enter a valid 10-digit phone number.'); ev.preventDefault();
    }
  });
}

// Confirm before dangerous actions: <a data-confirm="Are you sure?">
document.querySelectorAll('[data-confirm]').forEach(function (el) {
  el.addEventListener('click', function (ev) {
    if (!confirm(el.getAttribute('data-confirm'))) ev.preventDefault();
  });
});

// Live image preview in admin product form
var imgInput = document.getElementById('imageInput');
if (imgInput) {
  imgInput.addEventListener('change', function () {
    var f = imgInput.files[0];
    if (f) document.getElementById('preview').src = URL.createObjectURL(f);
  });
}
