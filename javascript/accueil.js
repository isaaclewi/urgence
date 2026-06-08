let slideIndex = 0;
showSlides();

function showSlides() {
    let i;
    let slides = document.getElementsByClassName("mySlides");
    for (i = 0; i < slides.length; i++) {
        slides[i].style.display = "none";
    }
    slideIndex++;
    if (slideIndex > slides.length) { slideIndex = 1 }
    slides[slideIndex - 1].style.display = "block";
    setTimeout(showSlides, 5000); // Change image every 2 seconds
};

let button1 = document.querySelector("#Button1");
let button2 = document.querySelector("#Button2");
let button3 = document.querySelector("#Button3");
let button4 = document.querySelector("#Button4");
let button5 = document.querySelector("#Button5");
let button6 = document.querySelector("#Button6");
let button7 = document.querySelector("#Button7");
let cadre1 = document.querySelector(".cadre1");
let cadre = document.querySelector(".cadre2");
let cadre2 = document.querySelector(".cadre3");
let logo = document.querySelector(".cercle4");
let cadre3 = document.querySelector(".cadre4");
let cadre4 = document.querySelector("#cadre5");


function apparaitreClick() {
    cadre.style.display = "block"; // Affiche l'élément
    cadre2.style.display = "none"; // Affiche l'élément
    logo.style.display = "none";// Affiche l'élément
    cadre3.style.display = "none";
    cadre4.style.display = "none";
};
function disparaitreClick() {
    cadre.style.display = "none"; // Affiche l'élément
    cadre2.style.display = "none"; // Affiche l'élément
    logo.style.display = "none";// Affiche l'élément
    cadre3.style.display = "none";
    cadre4.style.display = "none";
};
function Apparaitre() {
    cadre2.style.display = "flex";
    logo.style.display = "block";// Affiche l'élément
    cadre.style.display = "none"; // Affiche l'élément
    cadre3.style.display = "none";
    cadre4.style.display = "none";
};
function apparaitreClick2() {
    cadre3.style.display = "flex";
    cadre.style.display = "none"; // Affiche l'élément
    cadre2.style.display = "none"; // Affiche l'élément
    logo.style.display = "none";// Affiche l'élément
    cadre4.style.display = "none";

};
function appuis() {
    cadre4.style.display = "block";
    cadre3.style.display = "none";
    cadre.style.display = "none"; // Affiche l'élément
    cadre2.style.display = "none"; // Affiche l'élément
    logo.style.display = "none";// Affiche l'élément
};

function RedirectionInscription() {
    window.location.href = routeFormulaire;
};
function RedirectionConnection() {
    window.location.href = routeLogin;
};
function RedirectionReglement() {
    window.location.href = routeRegle;
};
function RedirectionAvis() {
    window.location.href = routeAvis;
};

window.addEventListener('load', () => {
    const loader = document.getElementById('loader');
    if (loader) {
        loader.style.display = 'none';
    }
});
