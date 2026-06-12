import { Controller } from "@hotwired/stimulus";
import tinymce from "tinymce";

// Thème & skin
import contentUiSkinCss from "tinymce/skins/ui/oxide/skin.css?inline";
import contentCss from "tinymce/skins/content/default/content.css?inline";
import contentUiCss from "tinymce/skins/ui/oxide/content.css?inline";
import "tinymce/icons/default";
import "tinymce/themes/silver";
import "tinymce/models/dom";

// Plugins
import "tinymce/plugins/advlist";
import "tinymce/plugins/autolink";
import "tinymce/plugins/lists";
import "tinymce/plugins/link";
import "tinymce/plugins/image";
import "tinymce/plugins/charmap";
import "tinymce/plugins/preview";
import "tinymce/plugins/anchor";
import "tinymce/plugins/searchreplace";
import "tinymce/plugins/visualblocks";
import "tinymce/plugins/code";
import "tinymce/plugins/fullscreen";
import "tinymce/plugins/insertdatetime";
import "tinymce/plugins/media";
import "tinymce/plugins/table";
import "tinymce/plugins/help";
import "tinymce/plugins/wordcount";

export default class extends Controller {
    static targets = ["textarea", "loader", "status", "saveButton"];
    static values = { content: String };

    connect() {
        // Petit délai pour laisser le DOM se stabiliser
        setTimeout(() => this._initTinyMCE(), 0);
        // Écoute l'événement de sauvegarde réussie émis par le LiveComponent
        window.addEventListener("page-editor:saved", () => this._onSaved());
    }

    disconnect() {
        if (this._editor) {
            this._editor.destroy();
            this._editor = null;
        }
    }

    _initTinyMCE() {
        if (typeof tinymce === "undefined") {
            console.error("TinyMCE non chargé.");
            return;
        }

        tinymce.init({
            target: this.textareaTarget,
            license_key: "gpl", // ← obligatoire avec npm
            skin: false,
            content_css: false,
            language: "fr_FR",
            language_url: "/tinymce/langs/fr_FR.js",
            content_style: contentCss + "\n" + contentUiCss,
            plugins: [
                "advlist",
                "autolink",
                "lists",
                "link",
                "image",
                "charmap",
                "anchor",
                "searchreplace",
                "visualblocks",
                "code",
                "fullscreen",
                "insertdatetime",
                "media",
                "table",
                "wordcount",
            ],
            toolbar:
                "undo redo | blocks | bold italic underline | " +
                "alignleft aligncenter alignright alignjustify | " +
                "bullist numlist outdent indent | link image media | " +
                "removeformat code fullscreen saveContent",
            height: "calc(100vh - 56px)",
            menubar: true,
            menu: {
                file: { title: "Fichier", items: "newdocument previewPage" },
                edit: {
                    title: "Édition",
                    items: "undo redo | cut copy paste pastetext | selectall",
                },
                view: {
                    title: "Affichage",
                    items: "code visualblocks fullscreen",
                },
                insert: {
                    title: "Insertion",
                    items: "link image media | charmap anchor",
                },
                format: {
                    title: "Format",
                    items: "bold italic underline | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent",
                },
                tools: { title: "Outils", items: "searchreplace" },
            },
            branding: false,
            resize: false,
            setup: (editor) => {
                this._editor = editor;

                editor.on("input change", () => {
                    this._syncToLive(editor.getContent());
                    this._setStatus("Modifications non enregistrées…");
                });

                editor.on("init", () => {
                    setTimeout(() => {
                        editor.execCommand("mceFullScreen");
                    }, 0);
                });

                editor.ui.registry.addMenuItem("previewPage", {
                    text: "Aperçu",
                    icon: "preview",
                    onAction: () => {
                        let url = new URL(location.href);
                        url.searchParams.set("edit", "0");

                        const previewWindow = window.open(
                            url.toString(),
                            "_blank",
                        );
                    },
                });

                editor.ui.registry.addButton("saveContent", {
                    icon: "save",
                    tooltip: "Enregistrer",
                    onAction: () => {
                        if (this.saveButtonTarget) {
                            this.saveButtonTarget.click();
                            this.createSuccessNotification();
                        } else {
                            console.error("Bouton de sauvegarde non trouvé.");
                            this.createErrorNotification();
                        }
                    },
                });
            },
            images_upload_handler: async (blobInfo) => {
                const formData = new FormData();
                formData.append("file", blobInfo.blob(), blobInfo.filename());

                const response = await fetch("/upload/image", {
                    method: "POST",
                    body: formData,
                });

                const data = await response.json();

                return data.location; // <-- IMPORTANT
            },
            automatic_uploads: true,
            images_reuse_filename: false,
            paste_data_images: false,
        });
    }

    /** Pousse le contenu HTML dans le LiveProp `content` via le modèle Live */
    _syncToLive(html) {
        // Mise à jour de la textarea pour que LiveComponent récupère la valeur
        this.textareaTarget.value = html;
        this.textareaTarget.dispatchEvent(
            new Event("input", { bubbles: true }),
        );
    }

    /** Déclenche l'action Live saveContent */
    save() {
        if (this._editor) {
            this._syncToLive(this._editor.getContent());
        }

        this._setLoading(true);
    }

    _onSaved() {
        this._setLoading(false);
        this._setStatus("✓ Enregistré");
        setTimeout(() => this._setStatus(""), 3000);
    }

    _setLoading(state) {
        this.loaderTarget.classList.toggle("hidden", !state);
    }

    _setStatus(msg) {
        if (this.hasStatusTarget) this.statusTarget.textContent = msg;
    }

    createSuccessNotification = () => {
        this._editor.notificationManager.open({
            text: "Contenu enregistré avec succès !",
            type: "success",
            timeout: 2000,
        });
    };

    createErrorNotification = () => {
        this._editor.notificationManager.open({
            text: "Une erreur est survenue lors de l'enregistrement du contenu.",
            type: "error",
            timeout: 2000,
        });
    };
}
