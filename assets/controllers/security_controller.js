import { Controller } from "@hotwired/stimulus";
import { getComponent } from "@symfony/ux-live-component";

/**
 * Controller pour gérer le composant Security avec lazy loading
 */
export default class extends Controller {
    static outlets = ["modal"];

    connect() {
        this.parentModal = this.element.closest("[data-controller='modal']");

        this.boundHandleRegistrationSuccess =
            this.handleRegistrationSuccess.bind(this);
        this.boundHandleModalOpened = this.handleModalOpened.bind(this);
        this.boundHandleModalClosed = this.handleModalClosed.bind(this);

        document.addEventListener(
            "registration:success",
            this.boundHandleRegistrationSuccess,
        );

        // Écouter l'événement d'ouverture du modal
        this.parentModal.addEventListener(
            "modal:opened",
            this.boundHandleModalOpened,
        );

        // Écouter l'événement de fermeture du modal
        this.parentModal.addEventListener(
            "modal:closed",
            this.boundHandleModalClosed,
        );

        // Focus sur le premier input au chargement initial
        this.focusFirstInput();
    }

    disconnect() {
        document.removeEventListener(
            "registration:success",
            this.boundHandleRegistrationSuccess,
        );
        this.parentModal.removeEventListener(
            "modal:opened",
            this.boundHandleModalOpened,
        );
        this.parentModal.removeEventListener(
            "modal:closed",
            this.boundHandleModalClosed,
        );
    }

    /**
     * Bascule entre login et register
     * Méthode publique appelable via outlet
     */
    async toggleMode(modalConfig = {}) {
        try {
            // Afficher le loader pendant le chargement
            this.showLoader();

            // Récupérer le composant LiveComponent
            const component = await getComponent(this.element);

            // Configurer le modal si des paramètres sont fournis
            if (this.hasModalOutlet && Object.keys(modalConfig).length > 0) {
                this.modalOutlet.edit(modalConfig);
            }

            // Déclencher l'action toggleMode
            await component.action("toggleMode");

            // Cacher le loader une fois terminé
            this.hideLoader();

            // Focus sur le premier input après le changement de mode
            this.focusFirstInput();
        } catch (error) {
            console.error("Erreur lors du toggle du mode:", error);
            this.hideLoader();
        }
    }

    /**
     * Affiche le skeleton loader
     */
    showLoader() {
        const content = this.element.querySelector(
            ".space-y-6:not(.animate-pulse)",
        );
        const loader = this.element.querySelector(".animate-pulse");

        if (content) content.style.display = "none";
        if (loader) loader.style.display = "block";
    }

    /**
     * Cache le skeleton loader
     */
    hideLoader() {
        const content = this.element.querySelector(
            ".space-y-6:not(.animate-pulse)",
        );
        const loader = this.element.querySelector(".animate-pulse");

        if (loader) loader.style.display = "none";
        if (content) content.style.display = "block";
    }

    /**
     * Met le focus sur le premier input du formulaire visible
     */
    focusFirstInput() {
        // Petit délai pour s'assurer que le DOM est mis à jour
        setTimeout(() => {
            // Rechercher tous les inputs visibles et focusables
            const inputs = this.element.querySelectorAll(
                'input[type="email"]:not([disabled]):not([readonly]), input[type="text"]:not([disabled]):not([readonly])',
            );

            // Trouver le premier input vraiment visible
            for (const input of inputs) {
                if (input.offsetParent !== null) {
                    input.focus();
                    break;
                }
            }
        }, 100);
    }

    /**
     * Gère l'événement d'ouverture du modal
     */
    handleModalOpened() {
        this.focusFirstInput();
    }

    /**
     * Gère l'événement de fermeture du modal
     */
    handleModalClosed() {
        const url = new URL(window.location);
        if (url.searchParams.has("open_login")) {
            url.searchParams.delete("open_login");
            window.history.replaceState({}, "", url);
        }
    }

    /**
     * Bascule vers le mode Register
     */
    async toRegister() {
        await this.toggleMode({ size: "lg", title: "Inscription" });
    }

    /**
     * Bascule vers le mode Login
     */
    async toLogin() {
        await this.toggleMode({ size: "sm" });
    }

    async handleRegistrationSuccess() {
        setTimeout(async () => {
            await this.toLogin();
            // Récupérer le composant LiveComponent
            const component = await getComponent(this.element);
            // Déclencher l'action handleRegistrationSuccess
            await component.action("handleRegistrationSuccess");
        }, 400);
    }
}
