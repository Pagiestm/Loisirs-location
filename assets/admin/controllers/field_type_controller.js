import { Controller } from '@hotwired/stimulus';

/*
 * This is an example Stimulus controller!
 *
 * Any element with a data-controller="field-type" attribute will cause
 * this controller to be executed. The name "field-type" comes from the filename:
 * field_type_controller.js -> "field-type"
 */
export default class extends Controller {
    static targets = ['type', 'choicesContainer'];

    connect() {
        this.toggleChoices();
    }

    toggleChoices() {
        const type = this.typeTarget.value;
        const needsChoices = ['select', 'checkbox', 'radio'].includes(type);
        
        if (needsChoices) {
            this.choicesContainerTarget.classList.remove('d-none');
        } else {
            this.choicesContainerTarget.classList.add('d-none');
        }
    }
}
