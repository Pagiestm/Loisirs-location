import { Controller } from "@hotwired/stimulus";

/*
 * Controller pour gérer les modales
 */
export default class extends Controller {
    static targets = ["backdrop", "panel", "header", "title"];

    static values = {
        id: String,
        initiallyOpen: { type: Boolean, default: false },
    };

    connect() {
        // Mode Déclencheur (bouton seul) ou Mode Modale
        if (this.hasBackdropTarget && this.hasPanelTarget) {
            // Initialiser les styles
            this.backdropTarget.style.opacity = "0";
            this.panelTarget.style.transform = "scale(0.95)";
            this.panelTarget.style.opacity = "0";
        }

        // Variable pour suivre où le mousedown se produit
        this.mouseDownTarget = null;

        // Lier les méthodes pour pouvoir les supprimer plus tard
        this.boundHandleOpen = this.handleOpen.bind(this);
        this.boundHandleClose = this.handleClose.bind(this);
        this.boundHandleKeydown = this.handleKeydown.bind(this);

        // Écouter les événements personnalisés pour ouvrir/fermer
        document.addEventListener("modal:open", this.boundHandleOpen);
        document.addEventListener("modal:close", this.boundHandleClose);
        document.addEventListener("keydown", this.boundHandleKeydown);

        // Ouvrir la modale si initiallyOpen est vrai
        if (this.initiallyOpenValue) {
            this.open();
        }
    }

    disconnect() {
        document.removeEventListener("modal:open", this.boundHandleOpen);
        document.removeEventListener("modal:close", this.boundHandleClose);
        document.removeEventListener("keydown", this.boundHandleKeydown);
    }

    triggerOpen(event) {
        if (event) event.preventDefault();
        document.dispatchEvent(
            new CustomEvent("modal:open", {
                detail: { modalId: this.idValue },
            }),
        );
    }

    triggerClose(event) {
        if (event) event.preventDefault();
        document.dispatchEvent(
            new CustomEvent("modal:close", {
                detail: { modalId: this.idValue },
            }),
        );
    }

    handleOpen(event) {
        if (event.detail?.modalId === this.element.id) {
            this.open();
        }
    }

    handleClose(event) {
        if (
            !event.detail?.modalId ||
            event.detail?.modalId === this.element.id
        ) {
            this.close();
        }
    }

    editSize(size) {
        this.panelTarget.classList.remove(
            "max-w-sm",
            "max-w-md",
            "max-w-lg",
            "max-w-xl",
        );
        switch (size) {
            case "sm":
                this.panelTarget.classList.add("max-w-sm");
                break;
            case "md":
                this.panelTarget.classList.add("max-w-md");
                break;
            case "lg":
                this.panelTarget.classList.add("max-w-lg");
                break;
            case "xl":
                this.panelTarget.classList.add("max-w-xl");
                break;
            default:
                this.panelTarget.classList.add("max-w-md");
        }
    }

    editTitle(title) {
        if (title) {
            this.titleTarget.textContent = title;
            this.headerTarget.classList.remove("hidden");
            this.headerTarget.classList.add("flex");
        } else {
            this.headerTarget.classList.add("hidden");
            this.headerTarget.classList.remove("flex");
        }
    }

    edit({ title = null, size = null }) {
        this.editTitle(title);

        if (size !== null) {
            this.editSize(size);
        }
    }

    open() {
        if (!this.hasBackdropTarget || !this.hasPanelTarget) return;

        this.element.classList.remove("hidden");
        document.body.style.overflow = "hidden";

        // Animation d'entrée
        requestAnimationFrame(() => {
            this.backdropTarget.style.opacity = "1";
            this.panelTarget.style.transform = "scale(1)";
            this.panelTarget.style.opacity = "1";

            // Dispatcher un événement pour signaler que le modal est ouvert
            // Cela permet aux contrôleurs enfants de réagir (ex: focus sur input)
            setTimeout(() => {
                this.element.dispatchEvent(
                    new CustomEvent("modal:opened", {
                        bubbles: true,
                        detail: { modalId: this.element.id },
                    }),
                );
            }, 100);
        });
    }

    close() {
        if (!this.hasBackdropTarget || !this.hasPanelTarget) return;

        // Animation de sortie
        this.backdropTarget.style.opacity = "0";
        this.panelTarget.style.transform = "scale(0.95)";
        this.panelTarget.style.opacity = "0";

        setTimeout(() => {
            this.element.classList.add("hidden");
            document.body.style.overflow = "";
        }, 300);

        // Dispatcher un événement pour signaler que le modal est fermé
        // Cela permet aux contrôleurs enfants de réagir (ex: focus sur input)
        setTimeout(() => {
            this.element.dispatchEvent(
                new CustomEvent("modal:closed", {
                    bubbles: true,
                    detail: { modalId: this.element.id },
                }),
            );
        }, 100);
    }

    // Enregistrer où le mousedown se produit
    handleMouseDown(event) {
        this.mouseDownTarget = event.target;
    }

    // Ne fermer que si mousedown et mouseup se produisent sur le backdrop/container
    handleBackdropClick(event) {
        // Vérifier que le mousedown s'est produit sur le même élément (backdrop/container)
        // et pas sur le panel ou ses enfants
        if (
            this.mouseDownTarget === event.target &&
            (event.target === this.backdropTarget ||
                event.target.hasAttribute("data-modal-container"))
        ) {
            this.close();
        }
        this.mouseDownTarget = null;
    }

    // Empêcher la fermeture quand on clique sur le panel
    preventClose(event) {
        event.stopPropagation();
    }

    // Fermer avec la touche Échap
    handleKeydown(event) {
        if (
            event.key === "Escape" &&
            !this.element.classList.contains("hidden")
        ) {
            this.close();
        }
    }
}
