// Fonction pour appliquer le thème
function updateTheme() {
    const htmlTag = document.getElementById('html-tag') || document.documentElement;
    const themeIcon = document.getElementById('theme-icon');
    const savedTheme = localStorage.getItem('theme');

    if (savedTheme === 'dark') {
        htmlTag.setAttribute('data-theme', 'dark');
        if (themeIcon) {
            themeIcon.classList.replace('bi-moon-stars-fill', 'bi-sun-fill');
        }
    } else {
        htmlTag.removeAttribute('data-theme');
        if (themeIcon) {
            themeIcon.classList.replace('bi-sun-fill', 'bi-moon-stars-fill');
        }
    }
}

// Attendre que tout soit prêt
document.addEventListener('DOMContentLoaded', () => {
    // 1. Appliquer le thème au chargement
    updateTheme();

    // 2. Gérer le clic sur le bouton de thème
    const toggleBtn = document.getElementById('theme-toggle');
    if (toggleBtn) {
        toggleBtn.addEventListener('click', (e) => {
            e.preventDefault();
            const isDark = localStorage.getItem('theme') === 'dark';
            localStorage.setItem('theme', isDark ? 'light' : 'dark');
            updateTheme();
        });
    }
});