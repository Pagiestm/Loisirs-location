import { Controller } from '@hotwired/stimulus';
import lucide from '@iconify-json/lucide/icons.json';

const DEFAULT_LIMIT = 48;

function normalizeIconName(value) {
    return (value || '')
        .trim()
        .toLowerCase()
        .replace(/^lucide:/, '');
}

function buildSvg(iconName, size = 18) {
    const icon = lucide.icons[iconName];

    if (!icon) {
        return '';
    }

    return `
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="${size}" height="${size}" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
            ${icon.body}
        </svg>
    `;
}

export default class extends Controller {
    connect() {
        this.iconNames = Object.keys(lucide.icons).sort();
        this.boundHandleInput = this.handleInput.bind(this);
        this.boundHandleFocus = this.handleFocus.bind(this);
        this.boundHandleOutsideClick = this.handleOutsideClick.bind(this);
        this.boundHandleThemeChange = this.handleThemeChange.bind(this);

        this.createPanel();
        this.applyTheme();
        this.renderSuggestions();
        this.updatePreview();

        // If the input has a data-default attribute, use it as initial value
        if (!this.element.value && this.element.dataset.default) {
            this.element.value = this.element.dataset.default;
            this.updatePreview();
        }
        this.element.addEventListener('input', this.boundHandleInput);
        this.element.addEventListener('focus', this.boundHandleFocus);
        document.addEventListener('click', this.boundHandleOutsideClick);
        document.addEventListener('ea.theme.changed', this.boundHandleThemeChange);
    }

    disconnect() {
        this.element.removeEventListener('input', this.boundHandleInput);
        this.element.removeEventListener('focus', this.boundHandleFocus);
        document.removeEventListener('click', this.boundHandleOutsideClick);
        document.removeEventListener('ea.theme.changed', this.boundHandleThemeChange);

        if (this.panelElement) {
            this.panelElement.remove();
        }
    }

    createPanel() {
        this.panelElement = document.createElement('div');
        this.panelElement.className = 'lucide-icon-picker mt-2 rounded-lg border shadow-sm overflow-hidden';
        this.panelElement.innerHTML = `
            <div class="flex items-center gap-3 px-3 py-2 border-b" data-header>
                <span class="flex h-8 w-8 items-center justify-center rounded-md bg-primary/10 text-primary" data-preview></span>
                <div class="min-w-0">
                    <p class="text-xs font-medium truncate" data-current-label>Icône à choisir</p>
                </div>
            </div>
            <div class="max-h-64 overflow-auto p-2" data-body>
                <div class="grid grid-cols-5 gap-1 sm:grid-cols-6 md:grid-cols-8" data-results></div>
            </div>
        `;

        this.element.insertAdjacentElement('afterend', this.panelElement);
        this.resultsElement = this.panelElement.querySelector('[data-results]');
        this.previewElement = this.panelElement.querySelector('[data-preview]');
        this.currentLabelElement = this.panelElement.querySelector('[data-current-label]');
        this.headerElement = this.panelElement.querySelector('[data-header]');
        this.bodyElement = this.panelElement.querySelector('[data-body]');
    }

    handleInput() {
        this.renderSuggestions();
        this.updatePreview();
        this.showPanel();
    }

    handleFocus() {
        this.renderSuggestions();
        this.showPanel();
    }

    handleOutsideClick(event) {
        if (!this.panelElement.contains(event.target) && event.target !== this.element) {
            this.hidePanel();
        }
    }

    handleThemeChange() {
        this.applyTheme();
        this.renderSuggestions();
        this.updatePreview();
    }

    isDarkMode() {
        return document.body.classList.contains('ea-dark-scheme');
    }

    applyTheme() {
        const isDark = this.isDarkMode();
        const background = isDark ? '#111827' : '#ffffff';
        const surface = isDark ? '#0f172a' : '#f8fafc';
        const border = isDark ? '#334155' : '#e5e7eb';
        const text = isDark ? '#f3f4f6' : '#1f2937';
        const muted = isDark ? '#cbd5e1' : '#6b7280';
        const tileBg = isDark ? '#0b1220' : '#ffffff';
        const tileBorder = isDark ? '#334155' : '#e5e7eb';
        const tileColor = isDark ? '#f8fafc' : '#334155';
        const tileHoverBg = isDark ? '#1e293b' : '#eff6ff';

        this.panelElement.style.backgroundColor = background;
        this.panelElement.style.color = text;
        this.panelElement.style.borderColor = border;

        this.headerElement.style.backgroundColor = surface;
        this.headerElement.style.borderColor = border;
        this.currentLabelElement.style.color = text;
        this.bodyElement.style.backgroundColor = background;

        this.tileStyles = {
            tileBg,
            tileBorder,
            tileColor,
            tileHoverBg,
        };

        if (this.previewElement) {
            this.previewElement.style.color = isDark ? '#f8fafc' : '#7c3aed';
        }
    }

    getQuery() {
        return normalizeIconName(this.element.value);
    }

    renderSuggestions() {
        const query = this.getQuery();
        const matches = this.iconNames
            .filter((iconName) => iconName.includes(query))
            .slice(0, DEFAULT_LIMIT);

        if (!matches.length) {
            this.resultsElement.innerHTML = `
                <div class="col-span-full rounded-md border border-dashed border-gray-200 px-3 py-4 text-center text-sm text-gray-500">
                    Aucune icône ne correspond à cette recherche.
                </div>
            `;
            return;
        }

        this.resultsElement.innerHTML = matches.map((iconName) => `
            <button
                type="button"
                class="group flex aspect-square w-full items-center justify-center rounded-lg border transition focus:outline-none p-1"
                style="background:${this.tileStyles.tileBg}; border-color:${this.tileStyles.tileBorder}; color:${this.tileStyles.tileColor};"
                data-icon-name="${iconName}"
                title="${iconName}"
                aria-label="Choisir l'icône ${iconName}"
            >
                <span class="flex h-7 w-7 items-center justify-center rounded-md transition group-hover:bg-white" style="background:${this.isDarkMode() ? '#1f2937' : '#f8fafc'}; color:${this.tileStyles.tileColor};">
                    ${buildSvg(iconName, 16)}
                </span>
                <span class="sr-only">${iconName}</span>
            </button>
        `).join('');

        this.resultsElement.querySelectorAll('button[data-icon-name]').forEach((button) => {
            button.addEventListener('click', () => this.selectIcon(button.dataset.iconName));
        });
    }

    selectIcon(iconName) {
        this.element.value = iconName;
        this.element.dispatchEvent(new Event('input', { bubbles: true }));
        this.element.dispatchEvent(new Event('change', { bubbles: true }));
        this.updatePreview();
        this.hidePanel();
        this.element.focus();
    }

    updatePreview() {
        const iconName = normalizeIconName(this.element.value);

        if (lucide.icons[iconName]) {
            this.previewElement.innerHTML = buildSvg(iconName, 20);
            this.currentLabelElement.textContent = 'Icône sélectionnée';
            this.currentLabelElement.style.color = this.isDarkMode() ? '#f3f4f6' : '#1f2937';
            return;
        }

        this.previewElement.innerHTML = '';
        this.currentLabelElement.textContent = 'Icône à choisir';
        this.currentLabelElement.style.color = this.isDarkMode() ? '#cbd5e1' : '#6b7280';
    }

    showPanel() {
        this.panelElement.classList.remove('hidden');
    }

    hidePanel() {
        this.panelElement.classList.add('hidden');
    }
}