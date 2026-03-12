import { Controller } from "@hotwired/stimulus";

export default class extends Controller {
    static targets = ["input", "iconShow", "iconHide", "text"];
    static values = {
        isVisible: { type: Boolean, default: false },
    };

    connect() {
        this.updateUI();
    }

    isVisibleValueChanged() {
        this.updateUI();
    }

    toggle(event) {
        event.preventDefault();
        this.isVisibleValue = !this.isVisibleValue;
    }

    updateUI() {
        this.inputTarget.type = this.isVisibleValue ? "text" : "password";

        this.iconShowTarget.classList.toggle("hidden", this.isVisibleValue);
        this.iconHideTarget.classList.toggle("hidden", !this.isVisibleValue);

        this.textTarget.textContent = this.isVisibleValue
            ? "Cacher"
            : "Montrer";
    }
}
