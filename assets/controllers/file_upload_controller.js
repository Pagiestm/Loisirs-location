import { Controller } from "@hotwired/stimulus";
import { PDFDocument, rgb, degrees } from "pdf-lib";

export default class extends Controller {
    static targets = ["input", "dropzone", "preview"];
    static values = {
        watermark: {
            type: String,
            default: "false",
        },
    };

    connect() {
        this.files = [];
        this.setupEvents();
    }

    setupEvents() {
        this.dropzoneTarget.addEventListener("dragover", (e) => {
            e.preventDefault();
            this.dropzoneTarget.classList.add(
                "border-emerald-500",
                "bg-emerald-50",
            );
        });

        this.dropzoneTarget.addEventListener("dragleave", () => {
            this.dropzoneTarget.classList.remove(
                "border-emerald-500",
                "bg-emerald-50",
            );
        });

        this.dropzoneTarget.addEventListener("drop", (e) => {
            e.preventDefault();
            this.handleFiles(e.dataTransfer.files);
        });

        this.inputTarget.addEventListener("change", (e) => {
            this.handleFiles(e.target.files);
        });
    }

    handleFiles(fileList) {
        if (!fileList || fileList.length === 0) return;

        this.files = Array.from(fileList);

        this.renderPreview();
        if (this.watermarkValue === "true") {
            this.applyWatermark();
        }
    }

    renderPreview() {
        this.previewTarget.innerHTML = "";

        this.files.forEach((file) => {
            const el = document.createElement("div");
            el.className = "mt-1";

            if (file.type.startsWith("image/")) {
                const img = document.createElement("img");
                img.className = "mt-2 max-h-32 rounded border";
                img.src = URL.createObjectURL(file);
                el.appendChild(img);
            } else {
                el.textContent = `📄 ${file.name}`;
            }

            this.previewTarget.appendChild(el);
        });
    }

    async applyWatermark() {
        const watermarked = await Promise.all(
            this.files.map((file) => this.processFile(file)),
        );

        const dataTransfer = new DataTransfer();

        watermarked.forEach((file) => {
            dataTransfer.items.add(file);
        });

        this.inputTarget.files = dataTransfer.files;

        console.log("Files ready for Symfony:", this.inputTarget.files);
    }

    async processFile(file) {
        if (!this.watermarkValue) return file;

        if (file.type.startsWith("image/")) {
            return await this.watermarkImage(file);
        }

        if (file.type === "application/pdf") {
            return await this.watermarkPDF(file);
        }

        return file;
    }

    async watermarkImage(file) {
        return new Promise((resolve) => {
            const img = new Image();
            const url = URL.createObjectURL(file);

            img.onload = () => {
                const canvas = document.createElement("canvas");
                const ctx = canvas.getContext("2d");

                canvas.width = img.width;
                canvas.height = img.height;

                // 1. image de base
                ctx.drawImage(img, 0, 0);

                // 2. watermark répétitif (intrusif)
                ctx.font = "bold 40px Arial";
                ctx.fillStyle = "rgba(255, 0, 0, 0.18)";
                ctx.textAlign = "center";
                ctx.textBaseline = "middle";

                const text = "Loisirs Location";

                const stepX = 250;
                const stepY = 180;

                for (let x = -img.width; x < img.width * 2; x += stepX) {
                    for (let y = -img.height; y < img.height * 2; y += stepY) {
                        ctx.save();
                        ctx.translate(x, y);
                        ctx.rotate(-Math.PI / 6); // diagonal
                        ctx.fillText(text, 0, 0);
                        ctx.restore();
                    }
                }

                canvas.toBlob((blob) => {
                    resolve(new File([blob], file.name, { type: file.type }));
                }, file.type);

                URL.revokeObjectURL(url);
            };

            img.src = url;
        });
    }

    async watermarkPDF(file) {
        const arrayBuffer = await file.arrayBuffer();
        const pdfDoc = await PDFDocument.load(arrayBuffer);

        const pages = pdfDoc.getPages();

        const text = "Loisirs Location";

        pages.forEach((page) => {
            const { width, height } = page.getSize();

            // 🔁 répétition en grille
            const stepX = 200;
            const stepY = 120;

            for (let x = -width; x < width * 2; x += stepX) {
                for (let y = -height; y < height * 2; y += stepY) {
                    page.drawText(text, {
                        x,
                        y,
                        size: 35,
                        color: rgb(0.75, 0, 0),
                        opacity: 0.12,
                        rotate: degrees(-35),
                    });
                }
            }
        });

        const pdfBytes = await pdfDoc.save();

        return new File([pdfBytes], `watermarked_${file.name}`, {
            type: "application/pdf",
        });
    }
}
