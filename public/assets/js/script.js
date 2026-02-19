// console.log("js ")
let isCaptured = false;




function showForm(formId) {
    document.querySelectorAll(".form-box").forEach(form => form.classList.remove("active"));
    document.getElementById(formId).classList.add("active")
}


// script.js
function showMail() {
    document.getElementById("login-form").querySelector("form").style.display = "none";
    document.getElementById("forgot-form").style.display = "block";
}


function sendEmail() {
    const username = document.getElementById('user-username').value;

    if (!username){
        alert("Pleas enter your username");
        return;
    }
    fetch("../src/forgot_password.php", { 
        method: "POST",
        headers: {
            "Content-Type": "application/x-www-form-urlencoded"
        },
        body: "username=" + encodeURIComponent(username)
    })
    .then(response => response.text())
    .then(data => {
        alert(data);
    });

}

function openModal() {
    document.getElementById("editModal").style.display = "block";
    document.getElementById("overlay").style.display = "block";
    document.getElementById("mainContent").classList.add("blur"); // blur only the main content
    document.getElementById("gallery").classList.add("blur"); // blur only the main content
}

function closeModal() {
    document.getElementById("editModal").style.display = "none";
    document.getElementById("overlay").style.display = "none";
    document.getElementById("mainContent").classList.remove("blur");
    document.getElementById("gallery").classList.remove("blur");
}


function autoHideFlashMessage() {
    const flash = document.getElementById("flash-message");

    if (!flash) return;

    setTimeout(() => {
        flash.style.transition = "opacity 0.5s ease";
        flash.style.opacity = "0";

        setTimeout(() => {
            flash.remove();
        }, 500);

    }, 3000);
}
document.addEventListener("DOMContentLoaded", function () {
    autoHideFlashMessage();
});

function toggleCommentsSection(imageId) {
    const section = document.getElementById("comments-section-" + imageId);
    if (!section) return;

    section.style.display = (section.style.display === "none") ? "block" : "none";
}


document.addEventListener("DOMContentLoaded", () => {

    const fileInput = document.getElementById("file-input");
    const uploadBtn = document.getElementById("upload-btn");
    const canvasContainer = document.getElementById("canvas-container");
    const canvas = document.getElementById("canvas");
    if (!canvas) return;
    const ctx = canvas.getContext("2d");
    const saveBtn = document.getElementById("save-btn");
    const webcamVideo = document.getElementById("webcam");
    const captureBtn = document.getElementById("capture-btn");
    const form = document.getElementById("save-form");
    const imageInput = document.getElementById("imageDataInput");

    let baseImage = new Image();
    let stickers = [];

    // =============================
    // REDRAW CANVAS
    // =============================
    function redrawCanvas() {
        ctx.clearRect(0, 0, canvas.width, canvas.height);

        if (baseImage.src) {
            ctx.drawImage(baseImage, 0, 0, canvas.width, canvas.height);
        }

        stickers.forEach(sticker => {
            ctx.drawImage(
                sticker.img,
                sticker.x,
                sticker.y,
                sticker.width,
                sticker.height
            );
        });
    }

    // =============================
    // RESIZE FUNCTION
    // =============================
    function resizeCanvas(img) {
        const MAX = 600;
        let width = img.width;
        let height = img.height;

        if (width > height && width > MAX) {
            height *= MAX / width;
            width = MAX;
        } else if (height > width && height > MAX) {
            width *= MAX / height;
            height = MAX;
        }

        canvas.width = width;
        canvas.height = height;
    }

    // =============================
    // UPLOAD IMAGE
    // =============================
    uploadBtn.addEventListener("click", () => fileInput.click());

    fileInput.addEventListener("change", (e) => {
        const file = e.target.files[0];
        if (!file) return;

        const reader = new FileReader();
        reader.onload = function(event) {
            baseImage = new Image();
            baseImage.src = event.target.result;

            baseImage.onload = () => {
                resizeCanvas(baseImage);
                stickers = [];
                redrawCanvas();
                canvasContainer.style.display = "block";
                form.style.display = "block";
            };
        };
        reader.readAsDataURL(file);
    });

    // =============================
    // START WEBCAM
    // =============================
    function startWebcam() {
        // Feature detection: ensure getUserMedia is available
        if (!navigator.mediaDevices || !navigator.mediaDevices.getUserMedia) {
            console.warn("getUserMedia is not supported in this browser.");
            return;
        }

        if (!webcamVideo) {
            console.warn("No #webcam element found in the DOM.");
            return;
        }

        navigator.mediaDevices.getUserMedia({ video: true })
            .then(stream => {
                try {
                    webcamVideo.srcObject = stream;
                    // play may return a promise; swallow any play errors
                    webcamVideo.play().catch(() => {});

                    if (captureBtn) {
                        captureBtn.style.display = "inline-block";
                        captureBtn.disabled = false; // IMPORTANT
                    }
                } catch (err) {
                    console.error("Error while setting up webcam stream:", err);
                }
            })
            .catch(err => console.error("Webcam error:", err));
    }

    startWebcam();

    // =============================
    // CAPTURE
    // =============================
    if (captureBtn){
        captureBtn.addEventListener("click", () => {
            if (!webcamVideo.videoWidth) return;

            canvas.width = webcamVideo.videoWidth;
            canvas.height = webcamVideo.videoHeight;

            ctx.drawImage(webcamVideo, 0, 0, canvas.width, canvas.height);

            baseImage = new Image();
            baseImage.src = canvas.toDataURL("image/png");

            baseImage.onload = () => {
                stickers = [];
                redrawCanvas();
            };

            canvasContainer.style.display = "block";
            form.style.display = "block";
        });
    }

    // =============================
    // ADD STICKERS (MULTIPLE)
    // =============================
    const stickerElements = document.querySelectorAll("#sticker-gallery img");

    stickerElements.forEach(sticker => {
        sticker.addEventListener("click", () => {
    
            const img = new Image();
            img.src = sticker.src || sticker.dataset.src;
    
            img.onload = () => {
    
                const stickerWidth = canvas.width * 0.3;
                const stickerHeight = img.height * (stickerWidth / img.width);
    
                // ✅ Random position inside canvas bounds
                const x = Math.random() * (canvas.width - stickerWidth);
                const y = Math.random() * (canvas.height - stickerHeight);
    
                // ✅ Add sticker to array (IMPORTANT)
                stickers.push({
                    img: img,
                    x: x,
                    y: y,
                    width: stickerWidth,
                    height: stickerHeight
                });
    
                redrawCanvas();
    
                captureBtn.disabled = false;
                saveBtn.disabled = false;
            };
        });
    });
    

    // =============================
    // SAVE BUTTON
    // =============================
    saveBtn.addEventListener("click", () => {
        const imageData = canvas.toDataURL("image/png");
        console.log("Send this to PHP:", imageData);
    });

    // =============================
    // FORM SUBMIT
    // =============================
    form.addEventListener("submit", () => {
        imageInput.value = canvas.toDataURL("image/png");
    });

});



function showSaveForm() {
    const form = document.getElementById("save-form");
    form.style.display = "block"; // show form
}
