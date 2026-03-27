/**
 * Theme Switcher - Gère le light/dark mode avec persistence
 * Respecte les normes WCAG 2.1 et les préférences système
 */

class ThemeSwitcher {
    constructor() {
        this.STORAGE_KEY = 'eco-tracker-theme';
        this.LIGHT_THEME = 'light';
        this.DARK_THEME = 'dark';
        this.init();
    }

    /**
     * Initialisation du thème au chargement
     */
    init() {
        // Récupérer le thème stocké ou utiliser la préférence système
        const savedTheme = this.getSavedTheme();
        const preferredTheme = savedTheme || this.getSystemPreference();
        
        // Appliquer le thème
        this.setTheme(preferredTheme);
        
        // Écouter les changements de préférences système
        this.listenToSystemPreference();
    }

    /**
     * Récupère le thème sauvegardé en localStorage
     */
    getSavedTheme() {
        try {
            return localStorage.getItem(this.STORAGE_KEY);
        } catch (e) {
            console.warn('localStorage non disponible:', e);
            return null;
        }
    }

    /**
     * Détecte la préférence système (light/dark)
     */
    getSystemPreference() {
        if (window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches) {
            return this.DARK_THEME;
        }
        return this.LIGHT_THEME;
    }

    /**
     * Applique le thème au document
     */
    setTheme(theme) {
        const isValid = theme === this.LIGHT_THEME || theme === this.DARK_THEME;
        const themeToApply = isValid ? theme : this.LIGHT_THEME;
        
        if (themeToApply === this.LIGHT_THEME) {
            document.documentElement.removeAttribute('data-theme');
        } else {
            document.documentElement.setAttribute('data-theme', 'dark');
        }

        // Sauvegarder la préférence
        try {
            localStorage.setItem(this.STORAGE_KEY, themeToApply);
        } catch (e) {
            console.warn('Impossible de sauvegarder le thème:', e);
        }

        // Mettre à jour l'attribut aria pour l'accessibilité
        this.updateToggleButton(themeToApply);
        
        // Dispatch un événement pour les autres scripts
        window.dispatchEvent(new CustomEvent('theme-changed', { detail: { theme: themeToApply } }));
    }

    /**
     * Bascule entre light et dark mode
     */
    toggleTheme() {
        const currentTheme = this.getCurrentTheme();
        const newTheme = currentTheme === this.LIGHT_THEME ? this.DARK_THEME : this.LIGHT_THEME;
        this.setTheme(newTheme);
    }

    /**
     * Récupère le thème actuel
     */
    getCurrentTheme() {
        return document.documentElement.getAttribute('data-theme') === 'dark' 
            ? this.DARK_THEME 
            : this.LIGHT_THEME;
    }

    /**
     * Met à jour le bouton toggle avec le bon label aria
     */
    updateToggleButton(theme) {
        const button = document.querySelector('.theme-toggle-btn');
        if (button) {
            const icon = theme === this.DARK_THEME ? '🌙' : '☀️';
            const label = theme === this.DARK_THEME 
                ? 'Basculer au thème clair' 
                : 'Basculer au thème sombre';
            
            button.textContent = icon;
            button.setAttribute('aria-label', label);
            button.setAttribute('title', label);
        }
    }

    /**
     * Écoute les changements de préférence système
     */
    listenToSystemPreference() {
        if (window.matchMedia) {
            const darkModeQuery = window.matchMedia('(prefers-color-scheme: dark)');
            
            // Support des navigateurs modernes
            if (darkModeQuery.addEventListener) {
                darkModeQuery.addEventListener('change', (e) => {
                    // Ne changer automatiquement que si l'utilisateur n'a pas choisi explicitement
                    if (!this.getSavedTheme()) {
                        const newTheme = e.matches ? this.DARK_THEME : this.LIGHT_THEME;
                        this.setTheme(newTheme);
                    }
                });
            }
            // Support des anciens navigateurs
            else if (darkModeQuery.addListener) {
                darkModeQuery.addListener((e) => {
                    if (!this.getSavedTheme()) {
                        const newTheme = e.matches ? this.DARK_THEME : this.LIGHT_THEME;
                        this.setTheme(newTheme);
                    }
                });
            }
        }
    }

    /**
     * Force un thème spécifique (utile pour les tests)
     */
    forceTheme(theme) {
        if (theme === 'system') {
            try {
                localStorage.removeItem(this.STORAGE_KEY);
            } catch (e) {}
            this.setTheme(this.getSystemPreference());
        } else {
            this.setTheme(theme);
        }
    }
}

// Initialiser le theme switcher au chargement du DOM
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', () => {
        window.themeSwitcher = new ThemeSwitcher();
    });
} else {
    window.themeSwitcher = new ThemeSwitcher();
}

// Ajouter l'écouteur au bouton toggle si fourni
document.addEventListener('DOMContentLoaded', () => {
    const toggleBtn = document.querySelector('.theme-toggle-btn');
    if (toggleBtn && window.themeSwitcher) {
        toggleBtn.addEventListener('click', () => {
            window.themeSwitcher.toggleTheme();
        });
    }
});
