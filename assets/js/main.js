document.addEventListener('DOMContentLoaded', function() {
    const menuToggle = document.getElementById('menuToggle');
    const mainNav = document.getElementById('mainNav');
    if (menuToggle && mainNav) {
        menuToggle.addEventListener('click', function() {
            mainNav.classList.toggle('open');
        });
        document.addEventListener('click', function(e) {
            if (!mainNav.contains(e.target) && !menuToggle.contains(e.target)) {
                mainNav.classList.remove('open');
            }
        });
    }

    document.querySelectorAll('.qty-plus, .qty-minus').forEach(function(btn) {
        btn.addEventListener('click', function() {
            var input = this.parentElement.querySelector('input[type="number"]');
            if (!input) return;
            var val = parseInt(input.value) || 1;
            var min = parseInt(input.min) || 1;
            var max = parseInt(input.max) || 999;
            if (this.classList.contains('qty-plus')) {
                if (val < max) input.value = val + 1;
            } else {
                if (val > min) input.value = val - 1;
            }
        });
    });

    document.querySelectorAll('.add-to-cart-form').forEach(function(form) {
        form.addEventListener('submit', function(e) {
            var btn = this.querySelector('button[type="submit"]');
            if (btn) btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> جاري الإضافة...';
        });
    });
});
