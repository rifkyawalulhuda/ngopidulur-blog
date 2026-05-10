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

    const readingProgressRoot = document.querySelector('[data-reading-progress-root]');
    const readingProgressBar = document.querySelector('[data-reading-progress-bar]');
    const readingArticle = document.querySelector('[data-reading-article]');
    const scrollTopButton = document.querySelector('[data-scroll-top]');

    if (readingProgressRoot && readingProgressBar && readingArticle && scrollTopButton) {
        const updateReadingProgress = () => {
            const articleRect = readingArticle.getBoundingClientRect();
            const articleTop = window.scrollY + articleRect.top;
            const articleHeight = readingArticle.offsetHeight;
            const viewportHeight = window.innerHeight;
            const maxScrollable = Math.max(articleHeight - viewportHeight, 1);
            const currentOffset = Math.min(Math.max(window.scrollY - articleTop, 0), maxScrollable);
            const progress = Math.max(0, Math.min(currentOffset / maxScrollable, 1));
            const showScrollTop = window.scrollY > articleTop + 320;

            readingProgressBar.style.width = `${progress * 100}%`;

            if (showScrollTop) {
                scrollTopButton.classList.remove('pointer-events-none', 'translate-y-4', 'opacity-0');
                scrollTopButton.classList.add('pointer-events-auto', 'translate-y-0', 'opacity-100');
            } else {
                scrollTopButton.classList.add('pointer-events-none', 'translate-y-4', 'opacity-0');
                scrollTopButton.classList.remove('pointer-events-auto', 'translate-y-0', 'opacity-100');
            }
        };

        let ticking = false;

        const requestProgressUpdate = () => {
            if (ticking) {
                return;
            }

            ticking = true;

            window.requestAnimationFrame(() => {
                updateReadingProgress();
                ticking = false;
            });
        };

        scrollTopButton.addEventListener('click', () => {
            window.scrollTo({
                top: 0,
                behavior: 'smooth',
            });
        });

        window.addEventListener('scroll', requestProgressUpdate, { passive: true });
        window.addEventListener('resize', requestProgressUpdate);
        updateReadingProgress();
    }

    const liveSearchForm = document.querySelector('[data-live-search-form]');
    const liveSearchInput = document.getElementById('search-query');
    const liveSearchResults = document.querySelector('[data-live-search-results]');
    const liveSearchTitle = document.querySelector('[data-search-title]');
    const liveSearchStatus = document.querySelector('[data-live-search-status]');
    const liveSearchSubmit = document.querySelector('[data-live-search-submit]');

    if (liveSearchForm && liveSearchInput && liveSearchResults && liveSearchTitle && liveSearchStatus && liveSearchSubmit) {
        let debounceTimer = null;
        let activeController = null;
        let requestSequence = 0;

        const setLoadingState = (loading) => {
            liveSearchSubmit.disabled = loading;
            liveSearchSubmit.textContent = loading ? 'Mencari...' : 'Cari';

            if (loading) {
                liveSearchStatus.textContent = 'Sedang mencari artikel...';
            }
        };

        const runLiveSearch = (query) => {
            const endpoint = liveSearchForm.dataset.searchEndpoint || liveSearchForm.action;
            const normalizedQuery = query.trim();
            const currentRequest = ++requestSequence;

            activeController?.abort();
            activeController = new AbortController();
            setLoadingState(true);

            const url = new URL(endpoint, window.location.origin);

            if (normalizedQuery !== '') {
                url.searchParams.set('q', normalizedQuery);
            }

            url.searchParams.set('ajax', '1');

            fetch(url, {
                headers: {
                    Accept: 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                },
                signal: activeController.signal,
            })
                .then(async (response) => {
                    const payload = await response.json();

                    if (!response.ok) {
                        throw new Error(payload.message || 'Pencarian belum bisa dijalankan.');
                    }

                    if (currentRequest !== requestSequence) {
                        return;
                    }

                    liveSearchTitle.textContent = payload.title || 'Cari artikel';
                    liveSearchStatus.textContent = payload.count_label || 'Hasil pencarian diperbarui.';
                    liveSearchResults.innerHTML = payload.results_html || '';

                    const nextUrl = new URL(endpoint, window.location.origin);

                    if (normalizedQuery !== '') {
                        nextUrl.searchParams.set('q', normalizedQuery);
                    }

                    window.history.replaceState({}, '', `${nextUrl.pathname}${nextUrl.search}`);
                })
                .catch((error) => {
                    if (error.name === 'AbortError') {
                        return;
                    }

                    liveSearchStatus.textContent = error.message || 'Terjadi kesalahan saat mencari artikel.';
                })
                .finally(() => {
                    if (currentRequest === requestSequence) {
                        setLoadingState(false);
                    }
                });
        };

        liveSearchForm.addEventListener('submit', (event) => {
            event.preventDefault();
            window.clearTimeout(debounceTimer);
            runLiveSearch(liveSearchInput.value);
        });

        liveSearchInput.addEventListener('input', () => {
            window.clearTimeout(debounceTimer);
            debounceTimer = window.setTimeout(() => {
                runLiveSearch(liveSearchInput.value);
            }, 280);
        });
    }

    const homeSearchForm = document.querySelector('[data-home-live-search-form]');
    const homeSearchInput = document.getElementById('home-search');
    const homeSearchPanel = document.querySelector('[data-home-live-search-panel]');
    const homeSearchResults = document.querySelector('[data-home-live-search-results]');
    const homeSearchStatus = document.querySelector('[data-home-live-search-status]');
    const homeSearchSubmit = document.querySelector('[data-home-live-search-submit]');

    if (homeSearchForm && homeSearchInput && homeSearchPanel && homeSearchResults && homeSearchStatus && homeSearchSubmit) {
        let debounceTimer = null;
        let activeController = null;
        let requestSequence = 0;

        const openHomeSearchPanel = () => {
            homeSearchPanel.hidden = false;

            window.requestAnimationFrame(() => {
                homeSearchPanel.classList.remove('pointer-events-none', 'opacity-0');
            });
        };

        const closeHomeSearchPanel = () => {
            homeSearchPanel.classList.add('pointer-events-none', 'opacity-0');

            window.setTimeout(() => {
                if (homeSearchPanel.classList.contains('opacity-0')) {
                    homeSearchPanel.hidden = true;
                }
            }, 200);
        };

        const setLoadingState = (loading) => {
            homeSearchSubmit.disabled = loading;
            homeSearchSubmit.textContent = loading ? 'Mencari...' : 'Cari';

            if (loading) {
                homeSearchStatus.textContent = 'Sedang mencari artikel yang paling relevan...';
            }
        };

        const clearHomeSearch = () => {
            activeController?.abort();
            homeSearchResults.innerHTML = '';
            homeSearchStatus.textContent = 'Ketik kata kunci seperti AI, Laravel, atau kopi dan hasil akan muncul otomatis.';
            homeSearchSubmit.disabled = false;
            homeSearchSubmit.textContent = 'Cari';
            closeHomeSearchPanel();
        };

        const runHomeSearch = (query) => {
            const endpoint = homeSearchForm.dataset.searchEndpoint || homeSearchForm.action;
            const normalizedQuery = query.trim();
            const currentRequest = ++requestSequence;

            if (normalizedQuery === '') {
                clearHomeSearch();
                return;
            }

            activeController?.abort();
            activeController = new AbortController();
            setLoadingState(true);

            const url = new URL(endpoint, window.location.origin);
            url.searchParams.set('q', normalizedQuery);
            url.searchParams.set('ajax', '1');
            url.searchParams.set('context', 'hero');

            fetch(url, {
                headers: {
                    Accept: 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                },
                signal: activeController.signal,
            })
                .then(async (response) => {
                    const payload = await response.json();

                    if (!response.ok) {
                        throw new Error(payload.message || 'Pencarian belum bisa dijalankan.');
                    }

                    if (currentRequest !== requestSequence) {
                        return;
                    }

                    homeSearchResults.innerHTML = payload.suggestions_html || '';
                    homeSearchStatus.textContent = payload.count_label || 'Hasil pencarian diperbarui.';
                    openHomeSearchPanel();
                })
                .catch((error) => {
                    if (error.name === 'AbortError') {
                        return;
                    }

                    homeSearchStatus.textContent = error.message || 'Terjadi kesalahan saat mencari artikel.';
                    closeHomeSearchPanel();
                })
                .finally(() => {
                    if (currentRequest === requestSequence) {
                        setLoadingState(false);
                    }
                });
        };

        homeSearchInput.addEventListener('focus', () => {
            if (homeSearchInput.value.trim() !== '' && homeSearchResults.innerHTML.trim() !== '') {
                openHomeSearchPanel();
            }
        });

        homeSearchInput.addEventListener('input', () => {
            window.clearTimeout(debounceTimer);
            debounceTimer = window.setTimeout(() => {
                runHomeSearch(homeSearchInput.value);
            }, 220);
        });

        homeSearchForm.addEventListener('submit', () => {
            closeHomeSearchPanel();
        });

        document.addEventListener('click', (event) => {
            if (!homeSearchForm.contains(event.target)) {
                closeHomeSearchPanel();
            }
        });

        document.addEventListener('keydown', (event) => {
            if (event.key === 'Escape') {
                closeHomeSearchPanel();
            }
        });
    }
});
