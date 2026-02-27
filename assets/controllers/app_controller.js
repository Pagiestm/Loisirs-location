import { Controller } from "@hotwired/stimulus";

/**
 * Contrôleur principal de l'application
 * Gère les interactions globales et la communication entre composants
 */
export default class extends Controller {
    static outlets = ["modal"];

    connect() {
        console.log("App controller connected");

        // Écouter l'événement de reload après connexion
        window.addEventListener("window:reload", () => {
            window.location.reload();
        });
    }

    /**
     * Ouvre une modale spécifique
     * @param {Event} event
     */
    openModal(event) {
        event.preventDefault();
        const { id } = event.params;

        if (id && this.hasModalOutlet) {
            // Trouver la modale avec l'ID correspondant
            const modal = this.modalOutlets.find(
                (outlet) => outlet.element.id === id,
            );

            if (modal) {
                modal.open();
            }
        }
    }

    /**
     * Ferme toutes les modales
     * @param {Event} event
     */
    closeModals(event) {
        if (event) {
            event.preventDefault();
        }

        // Fermer toutes les modales via outlets
        if (this.hasModalOutlet) {
            this.modalOutlets.forEach((modal) => modal.close());
        }
    }

    /**
     * Dispatch d'un événement personnalisé
     * @param {Event} event
     */
    dispatch(event) {
        const eventName = event.params.event;
        const detail = event.params.detail || {};

        if (eventName) {
            document.dispatchEvent(new CustomEvent(eventName, { detail }));
        }
    }
}
