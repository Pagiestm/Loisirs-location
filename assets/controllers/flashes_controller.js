import { Controller } from "@hotwired/stimulus";

/*
 * Contrôleur unique pour gérer tous les flashes
 * - Affiche le premier flash avec auto-suppression après 3s
 * - Empile les autres flashes en dessous
 * - Pause sur hover
 * - Suppression manuelle possible
 */
export default class extends Controller {
    static targets = ["flash", "progress"];

    timeout = null;
    isPaused = false;
    remainingTime = 3000;
    startTime = null;

    connect() {
        this.updateStack();

        // Activer le premier flash
        if (this.flashTargets.length > 0) {
            this.activateFirstFlash();
        }
    }

    updateStack() {
        this.flashTargets.forEach((flash, index) => {
            const offset = index * 12; // 12px de décalage vertical par flash
            const scale = 1 - index * 0.05; // Réduction de 5% par flash

            // Récupérer la hauteur du premier élément
            const firstFlashHeight = this.flashTargets[0]?.offsetHeight || 0;

            if (index === 0) {
                // Premier flash : pleinement visible avec hauteur automatique
                flash.style.transform = `translateY(0) scale(1)`;
                flash.style.zIndex = 1000 - index;
                flash.style.opacity = "1";
                flash.style.height = "auto";
                flash.querySelector("#flash-content").style.visibility =
                    "visible";
                flash.classList.remove("pointer-events-none");
                flash.classList.add("pointer-events-auto");
            } else {
                // Flashes suivants : empilés en dessous avec hauteur fixe
                flash.style.transform = `translateY(${offset}px) scale(${scale})`;
                flash.style.zIndex = 1000 - index;
                flash.style.opacity = "0.6";
                flash.querySelector("#flash-content").style.visibility =
                    "hidden";
                flash.classList.remove("pointer-events-auto");
                flash.classList.add("pointer-events-none");
                flash.style.height = `${firstFlashHeight}px`;
                flash.classList.add("pointer-events-none");
            }
        });
    }

    activateFirstFlash() {
        if (this.flashTargets.length === 0) return;

        // Nettoyer complètement l'ancien timer
        if (this.timeout) {
            clearTimeout(this.timeout);
            this.timeout = null;
        }

        // Réinitialiser les états
        this.remainingTime = 3000;
        this.isPaused = false;
        this.startTime = null;

        const firstFlash = this.flashTargets[0];
        const progressBar = firstFlash.querySelector("[data-progress]");

        // Réinitialiser la barre de progression
        if (progressBar) {
            progressBar.style.transitionDuration = "0ms";
            progressBar.style.width = "100%";

            // Démarrer la barre de progression
            setTimeout(() => {
                progressBar.style.transitionDuration = "3000ms";
                progressBar.style.width = "0%";
            }, 50);
        }

        // Démarrer le timer d'auto-suppression
        this.startTime = Date.now();
        this.startAutoCloseTimer();
    }

    startAutoCloseTimer() {
        // S'assurer qu'il n'y a pas de timer en cours
        if (this.timeout) {
            clearTimeout(this.timeout);
            this.timeout = null;
        }

        this.timeout = setTimeout(() => {
            this.closeFirstFlash();
        }, this.remainingTime);
    }

    pauseAutoClose(event) {
        if (this.flashTargets[0] !== event.currentTarget) return;

        this.isPaused = true;

        if (this.timeout) {
            clearTimeout(this.timeout);
            // Calculer le temps restant
            const elapsed = Date.now() - this.startTime;
            this.remainingTime = Math.max(0, this.remainingTime - elapsed);
        }

        // Mettre en pause la barre de progression
        const progressBar =
            event.currentTarget.querySelector("[data-progress]");
        if (progressBar) {
            const computedStyle = window.getComputedStyle(progressBar);
            const currentWidth = computedStyle.width;
            progressBar.style.transitionDuration = "0ms";
            progressBar.style.width = currentWidth;
        }
    }

    resumeAutoClose(event) {
        if (this.flashTargets[0] !== event.currentTarget) return;

        this.isPaused = false;

        // Reprendre la barre de progression
        const progressBar =
            event.currentTarget.querySelector("[data-progress]");
        if (progressBar) {
            progressBar.style.transitionDuration = `${this.remainingTime}ms`;
            progressBar.style.width = "0%";
        }

        // Reprendre le timer
        this.startTime = Date.now();
        this.startAutoCloseTimer();
    }

    close(event) {
        const flash = event.currentTarget.closest(
            '[data-flashes-target="flash"]',
        );

        // Annuler le timer en cours
        if (this.timeout) {
            clearTimeout(this.timeout);
            this.timeout = null;
        }

        // Animation de sortie
        flash.style.transform = "translateX(100%)";
        flash.style.opacity = "0";

        // Supprimer après animation
        setTimeout(() => {
            flash.remove();

            // Mettre à jour la pile
            this.updateStack();

            // Activer le nouveau premier flash (qui va réinitialiser tous les états)
            if (this.flashTargets.length > 0) {
                this.activateFirstFlash();
            }
        }, 300);
    }

    closeFirstFlash() {
        if (this.flashTargets.length === 0) return;

        const firstFlash = this.flashTargets[0];

        // Annuler le timer en cours
        if (this.timeout) {
            clearTimeout(this.timeout);
            this.timeout = null;
        }

        // Animation de sortie
        firstFlash.style.transform = "translateX(100%)";
        firstFlash.style.opacity = "0";

        // Supprimer après animation
        setTimeout(() => {
            firstFlash.remove();

            // Mettre à jour la pile
            this.updateStack();

            // Activer le nouveau premier flash (qui va réinitialiser tous les états)
            if (this.flashTargets.length > 0) {
                this.activateFirstFlash();
            }
        }, 300);
    }

    disconnect() {
        if (this.timeout) {
            clearTimeout(this.timeout);
        }
    }
}
