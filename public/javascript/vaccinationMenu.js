let logo1 = document.querySelector("#logo1");
let logo1c= document.querySelector("#logo1-c");
let logo2 = document.querySelector("#logo2");
let logo3 = document.querySelector("#logo3");
let logo4 = document.querySelector("#logo4");
let logo5 = document.querySelector("#logo5");
let logo5c = document.querySelector("#logo5-c");
let logo6 = document.querySelector("#logo6");
let logoc = document.querySelector("#logoc");

let Actuailtes = document.querySelector("#Actuailtes");
let Actuailte = document.querySelector("#actualite");
let Urgence = document.querySelector("#Urgence");
let deconnecter = document.querySelector("#deconnecter");
let deconnecte = document.querySelector("#deconnecte");

logo1.addEventListener('click', ()=> {
    window.location.href = logo1_c;
});

logo1c.addEventListener('click', () => {
    window.location.href = logo1c_c;
});

logo2.addEventListener('click', () => {
    window.location.href =logo2_c;
});

logo3.addEventListener('click', () => {
    window.location.href = logo3_c;
});

logo4.addEventListener('click', () => {
    window.location.href = logo4_c;
});

logo5.addEventListener('click', () => {
    window.location.href = logo5_c;
});

logo5c.addEventListener('click', () => {
    window.location.href = logo5c_c;
});

logo6.addEventListener('click', () => {
    window.location.href = logo6_c;
});


Actuailtes.addEventListener('click', () => {
    window.location.href = Actuailtes_c;
});
Actuailte.addEventListener('click', () => {
    window.location.href = Actuailte_c;
});

Urgence.addEventListener('click', () => {
    window.location.href = Urgence_c;
});

deconnecter.addEventListener('click', () => {
    if (confirm("Êtes-vous sûr de vouloir vous déconnecter ?")) {
        window.location.href = deconnecter_c;
    }
});

deconnecte.addEventListener('click', () => {
    if (confirm("Êtes-vous sûr de vouloir vous déconnecter ?")) {
        window.location.href = deconnecte_c;
    }
});