

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

function loadMorePosts() {
    let page = 1;
    let isLoading = false;
    if (isLoading) return;
    isLoading = true;
    document.getElementById("loader").style.display = "block";

    // Simuler une récupération d'actualités
    setTimeout(() => {
        for (let i = 0; i < 4; i++) {
            const post = document.createElement("div");
            post.className = "post";

            const isImage = Math.random() > 0.5;
            let media;

            if (isImage) {
                media = `<img class="post-media" src="images/news${(i % 3) + 1}.jpg" alt="Image actualité">`;
            } else {
                media = `<video class="post-media" controls src="videos/clip${(i % 2) + 1}.mp4"></video>`;
            }

            post.innerHTML = `
                ${media}
                <div class="post-content">
                    <p class="post-text">
                        Ceci est une publication ${isImage ? "image" : "vidéo"} d'exemple n°${i + 1 + (page - 1) * 4}.
                        Vous pouvez la remplacer par des données réelles.
                    </p>
                </div>
            `;

            document.getElementById("feed").appendChild(post);
        }

        page++;
        isLoading = false;
        document.getElementById("loader").style.display = "none";
    }, 1000);
}

// Charger les premiers posts
window.addEventListener("DOMContentLoaded", () => {
    loadMorePosts();
});

// Charger plus de posts lors du scroll vers le bas
window.addEventListener("scroll", () => {
    const { scrollTop, scrollHeight, clientHeight } = document.documentElement;
    if (scrollTop + clientHeight >= scrollHeight - 100) {
        loadMorePosts();
    }
});


