const storageKey = 'ngopi-dulur-theme';

function applyTheme(theme) {
    document.documentElement.classList.toggle('dark', theme === 'dark');
    document.documentElement.dataset.theme = theme;
}

function currentTheme() {
    return localStorage.getItem(storageKey) || document.documentElement.dataset.theme || 'light';
}

applyTheme(currentTheme());

document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('[data-theme-toggle]').forEach((button) => {
        button.addEventListener('click', () => {
            const nextTheme = document.documentElement.classList.contains('dark') ? 'light' : 'dark';
            localStorage.setItem(storageKey, nextTheme);
            applyTheme(nextTheme);
        });
    });

    const mobileMenu = document.querySelector('[data-mobile-menu]');
    const mobileMenuPanel = document.querySelector('[data-mobile-menu-panel]');
    const mobileMenuBackdrop = document.querySelector('[data-mobile-menu-backdrop]');
    const mobileMenuToggle = document.querySelector('[data-mobile-menu-toggle]');
    const mobileMenuCloseButtons = document.querySelectorAll('[data-mobile-menu-close], [data-mobile-menu-link]');

    const openMobileMenu = () => {
        if (!mobileMenu || !mobileMenuPanel || !mobileMenuBackdrop || !mobileMenuToggle) {
            return;
        }

        mobileMenu.classList.remove('hidden');
        mobileMenu.classList.remove('pointer-events-none');
        mobileMenuToggle.setAttribute('aria-expanded', 'true');
        document.body.classList.add('overflow-hidden');

        requestAnimationFrame(() => {
            mobileMenuBackdrop.classList.remove('opacity-0');
            mobileMenuPanel.classList.remove('translate-y-3', 'opacity-0');
        });
    };

    const closeMobileMenu = () => {
        if (!mobileMenu || !mobileMenuPanel || !mobileMenuBackdrop || !mobileMenuToggle) {
            return;
        }

        mobileMenuBackdrop.classList.add('opacity-0');
        mobileMenuPanel.classList.add('translate-y-3', 'opacity-0');
        mobileMenuToggle.setAttribute('aria-expanded', 'false');
        document.body.classList.remove('overflow-hidden');

        window.setTimeout(() => {
            mobileMenu.classList.add('hidden', 'pointer-events-none');
        }, 220);
    };

    mobileMenuToggle?.addEventListener('click', openMobileMenu);
    mobileMenuBackdrop?.addEventListener('click', closeMobileMenu);
    mobileMenuCloseButtons.forEach((button) => button.addEventListener('click', closeMobileMenu));

    document.addEventListener('keydown', (event) => {
        if (event.key === 'Escape') {
            closeMobileMenu();
        }
    });

    document.querySelectorAll('[data-focus-search="true"]').forEach((link) => {
        link.addEventListener('click', (event) => {
            const searchField = document.getElementById('home-search');

            if (!searchField) {
                return;
            }

            event.preventDefault();
            searchField.scrollIntoView({ behavior: 'smooth', block: 'center' });
            window.setTimeout(() => searchField.focus(), 220);
            closeMobileMenu();
        });
    });
});
