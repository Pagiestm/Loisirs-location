import { Controller } from "@hotwired/stimulus";

/*
 * Controller pour gérer le context menu
 */
export default class extends Controller {
    static targets = ["trigger", "content", "container", "input"];

    static values = {
        open: { type: Boolean, default: false },
    };

    connect() {
        this.boundHandleClickOutside = this.handleClickOutside.bind(this);
        this.boundHandleEscape = this.handleEscape.bind(this);
        this.boundHandleBlur = this.handleBlur.bind(this);
        this.boundHandleMouseDown = this.handleMouseDown.bind(this);
        this.boundHandleMouseUp = this.handleMouseUp.bind(this);

        this.isMouseDownInside = false;

        // Écouter le blur sur le container
        this.containerTarget.addEventListener("blur", this.boundHandleBlur);
    }

    disconnect() {
        this.removeEventListeners();
        this.containerTarget.removeEventListener("blur", this.boundHandleBlur);
    }

    open(event) {
        event.preventDefault();
        event.stopPropagation();

        this.openValue = true;
        this.positionContentAtCursor(event);
        this.showContent();

        // Focus le container
        this.containerTarget.focus();

        // Focus l'input si disponible
        if (this.hasInputTarget) {
            setTimeout(() => {
                this.inputTarget.focus();
            }, 150);
        }

        // Ajouter les event listeners pour fermer
        requestAnimationFrame(() => {
            document.addEventListener("mousedown", this.boundHandleMouseDown);
            document.addEventListener("mouseup", this.boundHandleMouseUp);
            document.addEventListener("keydown", this.boundHandleEscape);
        });
    }

    preventFocus(event) {
        // Empêcher le focus par click gauche seulement si le menu est fermé
        if (event.button === 0 && !this.openValue) {
            event.preventDefault();
        }
    }

    close() {
        this.openValue = false;
        this.hideContent();
        this.removeEventListeners();
    }

    showContent() {
        // Retirer pointer-events-none et ajouter les animations
        this.contentTarget.classList.remove("pointer-events-none");
        this.contentTarget.classList.remove("opacity-0", "scale-95");
        this.contentTarget.classList.add("opacity-100", "scale-100");
    }

    hideContent() {
        // Ajouter les animations de sortie
        this.contentTarget.classList.remove("opacity-100", "scale-100");
        this.contentTarget.classList.add("opacity-0", "scale-95");

        // Désactiver les interactions après l'animation
        setTimeout(() => {
            this.contentTarget.classList.add("pointer-events-none");
        }, 100);
    }

    handleClickOutside(event) {
        // Fermer si on clique en dehors du menu
        if (!this.contentTarget.contains(event.target)) {
            this.close();
        }
    }

    handleMouseDown(event) {
        // Tracker si le mousedown est à l'intérieur du context menu
        this.isMouseDownInside =
            this.contentTarget.contains(event.target) ||
            this.containerTarget.contains(event.target);
    }

    handleMouseUp(event) {
        // Fermer seulement si le mousedown ET le mouseup sont en dehors
        if (
            !this.isMouseDownInside &&
            !this.contentTarget.contains(event.target) &&
            !this.containerTarget.contains(event.target)
        ) {
            this.close();
        }
    }

    handleEscape(event) {
        if (event.key === "Escape") {
            this.close();
        }
    }

    handleBlur(event) {
        // Vérifier si le nouveau focus est en dehors du container et du content
        if (
            !this.containerTarget.contains(event.relatedTarget) &&
            !this.contentTarget.contains(event.relatedTarget)
        ) {
            this.close();
        }
    }

    removeEventListeners() {
        document.removeEventListener("click", this.boundHandleClickOutside);
        document.removeEventListener("mousedown", this.boundHandleMouseDown);
        document.removeEventListener("mouseup", this.boundHandleMouseUp);
        document.removeEventListener("keydown", this.boundHandleEscape);
    }

    positionContentAtCursor(event) {
        const content = this.contentTarget;
        const contentRect = content.getBoundingClientRect();

        // Position du curseur
        let top = event.clientY;
        let left = event.clientX;

        // Ajuster si le menu sort de l'écran
        const viewportWidth = window.innerWidth;
        const viewportHeight = window.innerHeight;

        // Ajustement horizontal
        if (left + contentRect.width > viewportWidth) {
            left = viewportWidth - contentRect.width - 8;
        }
        if (left < 0) {
            left = 8;
        }

        // Ajustement vertical
        if (top + contentRect.height > viewportHeight) {
            top = viewportHeight - contentRect.height - 8;
        }
        if (top < 0) {
            top = 8;
        }

        content.style.top = `${top}px`;
        content.style.left = `${left}px`;
    }
}
