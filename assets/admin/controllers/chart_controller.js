import { Controller } from "@hotwired/stimulus";

export default class extends Controller {
    connect() {
        this.chart = null;
        this.element.addEventListener("chartjs:connect", this._onConnect);
        document.addEventListener("ea.theme.changed", this.themeChangedHandler);
    }

    disconnect() {
        document.removeEventListener(
            "ea.theme.changed",
            this.themeChangedHandler,
        );
        this.element.removeEventListener("chartjs:connect", this._onConnect);
    }

    darkMode(chart) {
        // Configuration des couleurs pour le thème sombre
        if (chart.options.plugins?.legend?.labels) {
            chart.options.plugins.legend.labels.color = "#e5e7eb";
        }
        if (chart.options.plugins?.title) {
            chart.options.plugins.title.color = "#e5e7eb";
        }
        if (chart.options.scales) {
            Object.keys(chart.options.scales).forEach((scaleId) => {
                const scale = chart.options.scales[scaleId];
                if (scale.ticks) scale.ticks.color = "#e5e7eb";
                if (scale.grid) scale.grid.color = "rgba(255,255,255,0.1)";
            });
        }
        chart.update();
    }

    lightMode(chart) {
        // Configuration des couleurs pour le thème clair
        if (chart.options.plugins?.legend?.labels) {
            chart.options.plugins.legend.labels.color = "#111827";
        }
        if (chart.options.plugins?.title) {
            chart.options.plugins.title.color = "#111827";
        }
        if (chart.options.scales) {
            Object.keys(chart.options.scales).forEach((scaleId) => {
                const scale = chart.options.scales[scaleId];
                if (scale.ticks) scale.ticks.color = "#111827";
                if (scale.grid) scale.grid.color = "rgba(0,0,0,0.1)";
            });
        }
        chart.update();
    }

    themeChangedHandler = (event) => {
        const dark = event.detail.dark;

        if (dark) {
            this.darkMode(this.chart);
        } else {
            this.lightMode(this.chart);
        }
    };

    _onConnect = (event) => {
        this.chart = event.detail.chart;
        const isDark =
            document.querySelector("body").dataset.bsTheme === "dark";
        if (isDark) {
            this.darkMode(this.chart);
        } else {
            this.lightMode(this.chart);
        }
    };
}
