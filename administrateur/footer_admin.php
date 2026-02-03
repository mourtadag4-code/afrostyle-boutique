</div> <footer class="mt-auto py-4">
        <div class="container-fluid">
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden" style="border-top: 3px solid #0d6efd !important;">
                <div class="card-body bg-white p-3">
                    <div class="row align-items-center">
                        
                        <div class="col-md-4 text-center text-md-start">
                            <div class="d-flex align-items-center justify-content-center justify-content-md-start">
                                <div class="bg-primary bg-opacity-10 text-primary rounded-circle p-2 me-2">
                                    <i class="bi bi-person-check-fill"></i>
                                </div>
                                <div>
                                    <small class="text-muted d-block" style="font-size: 0.7rem;">ADMINISTRATEUR ACTIF</small>
                                    <span class="fw-bold text-dark"><?= htmlspecialchars($_SESSION['user_nom'] ?? 'Admin') ?></span>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-8 text-center text-md-end mt-3 mt-md-0">
                            <div class="d-inline-flex gap-2">
                                <a href="commandes.php" class="btn btn-sm rounded-pill px-3 fw-bold btn-outline-primary">
                                    <i class="bi bi-cart3 me-1"></i> Commandes
                                </a>
                                <a href="produits.php" class="btn btn-sm rounded-pill px-3 fw-bold btn-outline-primary">
                                    <i class="bi bi-box-seam me-1"></i> Produits
                                </a>
                                <a href="index.php" class="btn btn-primary rounded-pill px-3 fw-bold shadow-sm">
                                    <i class="bi bi-house-door me-1"></i> Accueil
                                </a>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
            
            <div class="text-center mt-3">
                <small class="text-muted">© <?= date('Y') ?> AfroStyle Admin — Design & Management</small>
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        const toggleBtn = document.getElementById('theme-toggle');
        const htmlTag = document.getElementById('html-tag');
        const themeIcon = document.getElementById('theme-icon');

        function applyTheme(theme) {
            if (theme === 'dark') {
                htmlTag.setAttribute('data-theme', 'dark');
                if(themeIcon) themeIcon.classList.replace('bi-moon-stars-fill', 'bi-sun-fill');
            } else {
                htmlTag.removeAttribute('data-theme');
                if(themeIcon) themeIcon.classList.replace('bi-sun-fill', 'bi-moon-stars-fill');
            }
        }

        applyTheme(localStorage.getItem('theme'));

        if(toggleBtn) {
            toggleBtn.addEventListener('click', () => {
                const currentTheme = htmlTag.getAttribute('data-theme');
                const newTheme = currentTheme === 'dark' ? 'light' : 'dark';
                applyTheme(newTheme);
                localStorage.setItem('theme', newTheme);
            });
        }
    </script>
</body>
</html>