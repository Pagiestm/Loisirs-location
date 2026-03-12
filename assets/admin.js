import { startStimulusApp } from "@symfony/stimulus-bridge";

// Registers Stimulus controllers from controllers.json and in the controllers/ directory
export const app = startStimulusApp(
    require.context(
        "@symfony/stimulus-bridge/lazy-controller-loader!./admin/controllers",
        true,
        /\.[jt]sx?$/,
    ),
);

// Event listener for dark mode
const observer = new MutationObserver((mutations) => {
    mutations.forEach((mutation) => {
        if (
            mutation.attributeName === "class" ||
            mutation.attributeName === "data-ea-dark-scheme-is-enabled"
        ) {
            const isDark = document.body.classList.contains("ea-dark-scheme");
            document.dispatchEvent(
                new CustomEvent("ea.theme.changed", {
                    detail: { dark: isDark },
                }),
            );
        }
    });
});

observer.observe(document.body, { attributes: true });
