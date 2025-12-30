const CACHE_NAME = "engcalc-v1";
const urlsToCache = [
  "/",
  "/assets/css/theme.css",
  "/assets/css/header.css",
  "/assets/css/footer.css",
  "/assets/css/home.css",
  "/manifest.json",
  "/offline.html",
];

self.addEventListener("install", (event) => {
  event.waitUntil(
    caches.open(CACHE_NAME).then((cache) => cache.addAll(urlsToCache))
  );
});

self.addEventListener("fetch", (event) => {
  event.respondWith(
    caches.match(event.request).then((response) => {
      return response || fetch(event.request);
    })
  );
});
