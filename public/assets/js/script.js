console.log("js ")





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
}

function closeModal() {
    document.getElementById("editModal").style.display = "none";
    document.getElementById("overlay").style.display = "none";
    document.getElementById("mainContent").classList.remove("blur");
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


// Grab DOM elements
const fileInput = document.getElementById("file-input");
const uploadBtn = document.getElementById("upload-btn");
const canvasContainer = document.getElementById("canvas-container");
const canvas = document.getElementById("canvas");
const ctx = canvas.getContext("2d");
const stickersPanel = document.getElementById("stickers-panel");
const saveBtn = document.getElementById("save-btn");
const saveSection = document.getElementById("save-section");

let baseImage = new Image();
let currentSticker = null;

// Upload button triggers file input
uploadBtn.addEventListener("click", () => fileInput.click());

// When user selects image
fileInput.addEventListener("change", (e) => {
    const file = e.target.files[0];
    if (!file) return;

    const reader = new FileReader();
    reader.onload = function(event) {
        baseImage.src = event.target.result;
    };
    reader.readAsDataURL(file);
});

// Draw image on canvas when loaded
baseImage.onload = function() {
    canvas.width = baseImage.width;
    canvas.height = baseImage.height;
    ctx.drawImage(baseImage, 0, 0);

    // Show canvas, stickers, save button
    canvasContainer.style.display = "block";
    stickersPanel.style.display = "block";
    saveSection.style.display = "block";
};

// Handle sticker clicks
document.querySelectorAll(".sticker-btn").forEach(btn => {
    btn.addEventListener("click", () => {
        const stickerSrc = btn.getAttribute("data-src");
        currentSticker = new Image();
        currentSticker.src = stickerSrc;

        currentSticker.onload = () => {
            // Clear canvas
            ctx.clearRect(0, 0, canvas.width, canvas.height);

            // Draw base image
            ctx.drawImage(baseImage, 0, 0);

            // Draw sticker in fixed position (center)
            const x = canvas.width / 2 - currentSticker.width / 2;
            const y = canvas.height / 2 - currentSticker.height / 2;
            ctx.drawImage(currentSticker, x, y);
        };
    });
});

// Save button (example: sends base64 to backend)
saveBtn.addEventListener("click", () => {
    const imageData = canvas.toDataURL("image/png");
    console.log("Send this to PHP:", imageData);

    // Example: You can use fetch() to send to PHP
});
