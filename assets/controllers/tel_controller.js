import { Controller } from "@hotwired/stimulus";

export default class extends Controller {
    static targets = ["hidden", "input"];

    connect() {
        // Initialiser les cases avec la valeur du champ caché
        if (this.hiddenTarget.value) {
            const digits = this.hiddenTarget.value.replace(/\D/g, "");
            for (
                let i = 0;
                i < digits.length && i < this.inputTargets.length;
                i++
            ) {
                this.inputTargets[i].value = digits[i];
            }
        }
    }

    onInput(event) {
        const input = event.target;
        const index = this.inputTargets.indexOf(input);

        // Supprimer tout caractère non numérique
        input.value = input.value.replace(/\D/g, "");

        // Passer à la case suivante si on a écrit un chiffre
        if (input.value.length === 1 && index < this.inputTargets.length - 1) {
            this.inputTargets[index + 1].focus();
        }

        this.updateHidden();
    }

    onKeyDown(event) {
        const input = event.target;
        const index = this.inputTargets.indexOf(input);

        if (event.key === "Backspace") {
            if (input.value !== "") {
                // La case n'est pas vide : le navigateur va effacer le contenu.
                // On reste sur cette case.
            } else if (index > 0) {
                // La case est vide : on recule et on vide la précédente
                const prevInput = this.inputTargets[index - 1];
                prevInput.focus();
                prevInput.value = "";
                this.updateHidden();
                event.preventDefault(); // Évite que le navigateur supprime autre chose par erreur
            }
        }
    }

    onPaste(event) {
        event.preventDefault();
        // Récupérer le contenu collé et ne garder que les chiffres
        const pasteData = (event.clipboardData || window.clipboardData)
            .getData("text")
            .replace(/\D/g, "");

        if (pasteData) {
            let pIndex = 0;
            const startIndex = this.inputTargets.indexOf(event.target);

            // Remplir les cases à partir de là où on a collé
            for (
                let i = startIndex;
                i < this.inputTargets.length && pIndex < pasteData.length;
                i++
            ) {
                this.inputTargets[i].value = pasteData[pIndex];
                pIndex++;
            }

            // Placer le focus sur la dernière case remplie
            const lastFilled = Math.min(
                startIndex + pIndex,
                this.inputTargets.length - 1,
            );
            if (lastFilled < this.inputTargets.length) {
                this.inputTargets[lastFilled].focus();
            }

            this.updateHidden();
        }
    }

    updateHidden() {
        // Concaténer toutes les valeurs des cases dans le champ caché
        this.hiddenTarget.value = this.inputTargets
            .map((i) => i.value)
            .join("");
    }
}
