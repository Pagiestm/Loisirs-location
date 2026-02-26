import { Controller } from '@hotwired/stimulus';

export default class extends Controller {
    toggle({ currentTarget }) {
        const item = currentTarget.closest('.faq-item');
        item.classList.toggle('faq-open');
    }
}
