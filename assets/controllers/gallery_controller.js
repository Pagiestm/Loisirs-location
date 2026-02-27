import { Controller } from '@hotwired/stimulus'

export default class extends Controller {
    static targets = ['main']

    connect () {
        // Marquer la première vignette comme active au chargement
        const first = this.element.querySelector('[data-action*="gallery#select"]')
        if (first) {
            first.classList.remove('opacity-50')
            first.classList.add('opacity-100')
        }
    }

    select (event) {
        const src = event.params.src
        const btn = event.currentTarget

        // Fondu sur l'image principale
        this.mainTarget.style.opacity = '0'
        setTimeout(() => {
            this.mainTarget.src = src
            this.mainTarget.style.opacity = '1'
        }, 150)

        // Toutes les vignettes → inactives
        this.element.querySelectorAll('[data-action*="gallery#select"]').forEach(b => {
            b.classList.remove('opacity-100')
            b.classList.add('opacity-50')
        })

        // Vignette cliquée → active
        btn.classList.remove('opacity-50')
        btn.classList.add('opacity-100')
    }
}
