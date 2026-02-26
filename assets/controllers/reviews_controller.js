import { Controller } from '@hotwired/stimulus';

/**
 * Carousel simple basé sur scroll-snap.
 * Les boutons prev/next font défiler d'une carte à la fois.
 */
export default class extends Controller {
    static targets = ['track', 'prev', 'next'];

    connect() {
        this._update();
        this.trackTarget.addEventListener('scroll', () => this._update(), { passive: true });
    }

    prev() {
        const card = this.trackTarget.querySelector('[data-reviews-card]');
        const w = card ? card.offsetWidth + this._gap() : 320;
        this.trackTarget.scrollBy({ left: -w, behavior: 'smooth' });
    }

    next() {
        const card = this.trackTarget.querySelector('[data-reviews-card]');
        const w = card ? card.offsetWidth + this._gap() : 320;
        this.trackTarget.scrollBy({ left: w, behavior: 'smooth' });
    }

    _gap() {
        return parseInt(getComputedStyle(this.trackTarget).columnGap) || 24;
    }

    _update() {
        const t   = this.trackTarget;
        const max = t.scrollWidth - t.clientWidth;
        if (this.hasPrevTarget) this.prevTarget.disabled = t.scrollLeft <= 1;
        if (this.hasNextTarget) this.nextTarget.disabled = t.scrollLeft >= max - 1;
    }
}
