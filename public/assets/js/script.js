import config from "./config.js";


var swiper = new Swiper(".mySwiper", {
    slidesPerView: 1,
    spaceBetween: 30,
    allowTouchMove: false,
    speed: 400,
    effect: "fade",
    navigation: {
        nextEl: ".button-next",
        prevEl: ".button-prev",
    },
    pagination: {
        el: ".slider-pagination",
        type: "fraction",
    },
});

var swiper = new Swiper(".mySwiper2", {
    slidesPerView: 1,
    spaceBetween: 30,
    speed: 500,
    effect: "fade",
    pagination: {
        el: ".swiper-pagination",
        clickable: true,
    },
});

const $lgContainer = document.querySelector(".mySwiper");

const lg = lightGallery($lgContainer, {
    showZoomInOutIcons: true,
    actualSize: false,
    selector: ".swiper-slide .lightgallery  a",
    speed: 300,
    controls: true,
    loop: false,
});

const $lgContainer2 = document.querySelector(".gallery-2");

const lg2 = lightGallery($lgContainer2, {
    showZoomInOutIcons: true,
    actualSize: false,
    selector: ".swiper-slide .lightgallery  a",
    speed: 300,
    controls: true,
    loop: true,
    counter: false,
});

const $lgContainer3 = document.querySelector(".gallery-3");

const lg3 = lightGallery($lgContainer3, {
    showZoomInOutIcons: true,
    actualSize: false,
    selector: ".swiper-slide .lightgallery  a",
    speed: 300,
    controls: true,
    loop: false,
});

// like btn
const likeBtnParent = document.querySelectorAll(
    ".slide__btns-footer .swiper-slide"
);
const btnSwiper = document.querySelector(".btn__swiper");
let ruLangs = document.querySelectorAll(".ru-lang");
let enLangs = document.querySelectorAll(".en-lang");

function ruLanguage() {
    ruLangs.forEach((lang) => {
        lang.style.display = "inline-block";
        lang.classList.add("active");
    });

    enLangs.forEach((lang) => {
        lang.style.display = "none";
        lang.classList.remove("active");
    });
}

function enLanguage() {
    ruLangs.forEach((lang) => {
        lang.style.display = "none";
    });

    enLangs.forEach((lang) => {
        lang.style.display = "inline-block";
    });
}

enLanguage();

likeBtnParent.forEach((item) => {
    const likeBtn = item.querySelector(".like__btn");
    const advertId = likeBtn.getAttribute('data-advert-id');
    const apiKey = document.querySelector('meta[name="api-key"]').getAttribute('content');

    likeBtn.addEventListener("click", (e) => {
        const isLiked = likeBtn.classList.contains("active");
        if (!isLiked) {
            likeBtn.textContent = "Liked";
        } else if (likeBtn.textContent === "Liked") {
            likeBtn.textContent = "Like";
        }
        btnSwiper.classList.toggle("active");
        likeBtn.classList.toggle("active");


        fetch(`${config.apiUrl}/selection/advert/${advertId}/like`, {
            method: 'PATCH',
            headers: {
                'Content-Type': 'application/json',
                'x-api-key': apiKey
            },
            body: JSON.stringify({like: !isLiked})
        })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Failed to update like status');
                }
                return response.json();
            })
            .then(data => {
                console.log('Like status updated successfully:', data);
            })
            .catch(error => {
                console.error('Error updating like status:', error);
                // Возвращаем кнопку в исходное состояние в случае ошибки
                likeBtn.textContent = isLiked ? "Liked" : "Like";
                likeBtn.classList.toggle("active");
                btnSwiper.classList.toggle("active");
            });

    });


});

// select language
document.querySelectorAll(".option__value").forEach((option) => {
    option.addEventListener("click", () => {
        const selectedBox = document.querySelector(".eng");
        const valueChildEng = document.querySelector(".option__value .eng");
        selectedBox.innerHTML = option.innerHTML;
        valueChildEng.style.display = "none";
        console.log(option.innerHTML);
        const lang = option.querySelector("p").textContent.toLowerCase();
        if (lang == "en") {
            enLanguage();
            console.log("salom");
        } else {
            ruLanguage();
        }
    });
});
