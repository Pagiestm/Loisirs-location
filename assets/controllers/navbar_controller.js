import { Controller } from '@hotwired/stimulus';

/**
 * Comportement hero uniquement sur lg+ (≥ 1024px).
 * En dessous : navbar fixe classique bg-secondary, aucune animation.
 *
 * 3 états (lg+ seulement) :
 *  - top     : scrollY ≈ 0 → visible, transparent
 *  - in-hero : scroll dans le hero → disparaît vers le haut
 *  - past    : dépassé le hero → slide-down + bg-secondary
 */
export default class extends Controller {
    static targets = ['menu', 'backdrop', 'burger', 'line1', 'line2', 'line3'];
    static values  = { hero: { type: Boolean, default: false } };

    connect() {
        if (!this.heroValue) return;

        this._updateThreshold();
        this._state    = null;
        this._onScroll = this._handleScroll.bind(this);
        this._onResize = this._onResizeHandler.bind(this);

        window.addEventListener('scroll', this._onScroll, { passive: true });
        window.addEventListener('resize', this._onResize, { passive: true });

        // état initial sans animation
        this._applyState(this._getState(), false);
    }

    disconnect() {
        window.removeEventListener('scroll', this._onScroll);
        window.removeEventListener('resize', this._onResize);
    }

    toggleMenu() {
        const isOpen = this.menuTarget.getAttribute('aria-hidden') === 'false';
        isOpen ? this.closeMenu() : this.openMenu();
    }

    openMenu() {
        // Drawer
        this.menuTarget.classList.remove('translate-x-full');
        this.menuTarget.classList.add('translate-x-0');
        this.menuTarget.setAttribute('aria-hidden', 'false');
        // Backdrop
        this.backdropTarget.classList.remove('opacity-0', 'pointer-events-none');
        this.backdropTarget.classList.add('opacity-100');
        // Burger → croix
        this.burgerTarget.setAttribute('aria-expanded', 'true');
        this.line1Target.classList.add('rotate-45', 'translate-y-2');
        this.line2Target.classList.add('opacity-0', 'scale-x-0');
        this.line3Target.classList.add('-rotate-45', '-translate-y-2');
        // Bloquer le scroll
        document.body.classList.add('overflow-hidden');
    }

    closeMenu() {
        // Drawer
        this.menuTarget.classList.add('translate-x-full');
        this.menuTarget.classList.remove('translate-x-0');
        this.menuTarget.setAttribute('aria-hidden', 'true');
        // Backdrop
        this.backdropTarget.classList.add('opacity-0', 'pointer-events-none');
        this.backdropTarget.classList.remove('opacity-100');
        // Burger → hamburger
        this.burgerTarget.setAttribute('aria-expanded', 'false');
        this.line1Target.classList.remove('rotate-45', 'translate-y-2');
        this.line2Target.classList.remove('opacity-0', 'scale-x-0');
        this.line3Target.classList.remove('-rotate-45', '-translate-y-2');
        // Débloquer le scroll
        document.body.classList.remove('overflow-hidden');
    }

    // ── lg = 1024px, identique au breakpoint Tailwind ──
    _isLg() {
        return window.innerWidth >= 1024;
    }

    _onResizeHandler() {
        this._updateThreshold();
        // Fermer le menu si on passe en lg
        if (this._isLg()) this.closeMenu();
        // Recalcule l'état visuel quand on change de taille
        this._state = null;
        this._applyState(this._getState(), false);
    }

    _updateThreshold() {
        const hero = document.querySelector('[data-navbar-hero-section]');
        this._threshold = hero
            ? hero.getBoundingClientRect().bottom + window.scrollY
            : window.innerHeight;
    }

    _getState() {
        if (!this._isLg())                        return 'mobile';
        if (window.scrollY < 10)                  return 'top';
        if (window.scrollY <= this._threshold)    return 'in-hero';
        return 'past';
    }

    _handleScroll() {
        const state = this._getState();
        if (state === this._state) return;
        this._state = state;
        this._applyState(state, true);
    }

    _applyState(state, animate) {
        const el = this.element;

        if (state === 'mobile') {
            // Pas d'animation — navbar visible, fond vert
            el.style.transform = '';
            el.classList.add('bg-secondary', 'shadow-lg');
            el.classList.remove('bg-transparent');

        } else if (state === 'top') {
            el.style.transform = '';
            el.classList.remove('bg-secondary', 'shadow-lg');
            el.classList.add('bg-transparent');

        } else if (state === 'in-hero') {
            el.style.transform = 'translateY(-100%)';
            el.classList.remove('bg-secondary', 'shadow-lg');
            el.classList.add('bg-transparent');

        } else if (state === 'past') {
            if (animate) {
                el.style.transition = 'none';
                el.style.transform  = 'translateY(-100%)';
                el.getBoundingClientRect();
                el.style.transition = '';
            }
            el.style.transform = '';
            el.classList.add('bg-secondary', 'shadow-lg');
            el.classList.remove('bg-transparent');
        }
    }
}
