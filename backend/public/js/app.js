document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('[data-menu-toggle]').forEach((button) => {
        button.addEventListener('click', () => {
            const target = document.querySelector(button.dataset.menuToggle);
            target?.classList.toggle('is-open');
        });
    });
});
