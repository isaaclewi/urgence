/* CongoAssist — service worker (v4)
 * Règle de sécurité : on ne met JAMAIS en cache une page HTML ni une réponse de l'application
 * (tableaux de bord, bilans de santé, messagerie, flux temps réel…). Seuls les fichiers
 * statiques (CSS, JS, images, polices, icônes) sont conservés pour accélérer l'affichage.
 * Hors ligne, on affiche uniquement l'accueil public mis en cache à l'installation. */
const CACHE_NAME = "congoassist-v4";
const OFFLINE_URL = "/";
const STATIC_ASSETS = ["/", "/manifest.json", "/manifest-services.json", "/icons/icon-192.png", "/icons/icon-512.png"];
const STATIC_EXT = /\.(?:css|js|png|jpe?g|gif|webp|svg|ico|woff2?|ttf)$/i;

self.addEventListener("install", (event) => {
  event.waitUntil(
    caches.open(CACHE_NAME).then((cache) =>
      Promise.allSettled(STATIC_ASSETS.map((url) => cache.add(url)))
    )
  );
  self.skipWaiting();
});

self.addEventListener("activate", (event) => {
  event.waitUntil(
    caches.keys().then((keys) => Promise.all(keys.filter((k) => k !== CACHE_NAME).map((k) => caches.delete(k))))
  );
  self.clients.claim();
});

self.addEventListener("fetch", (event) => {
  const req = event.request;
  if (req.method !== "GET") return;

  const url = new URL(req.url);
  if (url.origin !== self.location.origin) return;

  // Flux temps réel (SSE) et appels de rafraîchissement : jamais interceptés
  if (url.pathname.endsWith("/stream") || url.pathname.endsWith("/messages") || url.pathname.startsWith("/api/")) return;

  // Navigation (pages HTML) : toujours le réseau ; hors ligne => accueil public uniquement
  if (req.mode === "navigate") {
    event.respondWith(fetch(req).catch(() => caches.match(OFFLINE_URL)));
    return;
  }

  // Fichiers statiques : réseau d'abord, copie en cache en secours
  if (STATIC_EXT.test(url.pathname) || url.pathname.startsWith("/icons/") || url.pathname.startsWith("/medias/")) {
    event.respondWith(
      fetch(req)
        .then((res) => {
          const type = res.headers.get("content-type") || "";
          if (res.status === 200 && !type.includes("text/html")) {
            const copie = res.clone();
            caches.open(CACHE_NAME).then((cache) => cache.put(req, copie));
          }
          return res;
        })
        .catch(() => caches.match(req))
    );
  }
  // Tout le reste : comportement navigateur normal (aucune mise en cache)
});
