// ==============================
// EnviroManage Service Worker
// Production PWA (Stable + Auto Update)
// ==============================

const CACHE_NAME = "enviromanage-static-v9";

// ==============================
// FILES TO CACHE (SAFE ONLY)
// DO NOT cache PHP pages
// ==============================
const STATIC_ASSETS = [
  "./",
  "index.html",
  "offline.html",

  "offline-router.js",
  "app.js",
  "style.css",
  "manifest.json",

  "assets/logo.png",
  "assets/logo-192.png",
  "assets/logo-512.png"
];


// ==============================
// INSTALL EVENT
// ==============================
self.addEventListener("install", event => {
  console.log("[SW] Installing new service worker...");

  event.waitUntil(
    caches.open(CACHE_NAME).then(cache => {
      return cache.addAll(STATIC_ASSETS);
    })
  );

  // Force immediate activation
  self.skipWaiting();
});


// ==============================
// ACTIVATE EVENT (AUTO CLEAN OLD CACHE)
// ==============================
self.addEventListener("activate", event => {
  console.log("[SW] Activating new service worker...");

  event.waitUntil(
    caches.keys().then(keys => {
      return Promise.all(
        keys.map(key => {
          if (key !== CACHE_NAME) {
            console.log("[SW] Deleting old cache:", key);
            return caches.delete(key);
          }
        })
      );
    })
  );

  // Take control immediately
  return self.clients.claim();
});


// ==============================
// FETCH STRATEGY
// ==============================
self.addEventListener("fetch", event => {

  if (event.request.method !== "GET") return;

  const url = new URL(event.request.url);

  // ==============================
  // 1. NEVER CACHE PHP (IMPORTANT)
  // ==============================
  if (url.pathname.endsWith(".php")) {
    event.respondWith(fetch(event.request));
    return;
  }

  // ==============================
  // 2. API REQUESTS = NETWORK ONLY
  // ==============================
  if (url.pathname.includes("/api/")) {
    event.respondWith(fetch(event.request));
    return;
  }

  // ==============================
  // 3. PAGE NAVIGATION (MAIN ROUTING)
  // ==============================
  if (event.request.mode === "navigate") {

    event.respondWith(
      fetch(event.request)
        .then(response => {

          // Cache fresh version when online
          const clone = response.clone();

          caches.open(CACHE_NAME).then(cache => {
            cache.put(event.request, clone);
          });

          return response;
        })
        .catch(() => {

          // Offline fallback
          return caches.match(event.request)
            .then(cached => {

              return cached || caches.match("offline.html");

            });

        })
    );

    return;
  }

  // ==============================
  // 4. STATIC FILES (CACHE FIRST)
  // ==============================
  event.respondWith(
    caches.match(event.request).then(cached => {

      return cached || fetch(event.request).then(networkResponse => {

        // Save new version
        const clone = networkResponse.clone();

        caches.open(CACHE_NAME).then(cache => {
          cache.put(event.request, clone);
        });

        return networkResponse;
      });

    })
  );

});


// ==============================
// PUSH NOTIFICATIONS
// ==============================
self.addEventListener("push", event => {

  const data = event.data ? event.data.json() : {
    title: "EnviroManage",
    body: "New notification received."
  };

  event.waitUntil(
    self.registration.showNotification(data.title, {
      body: data.body,
      icon: "assets/logo-192.png",
      badge: "assets/logo-192.png",
      vibrate: [200, 100, 200],
      data: { url: "login.php" }
    })
  );

});


// ==============================
// NOTIFICATION CLICK
// ==============================
self.addEventListener("notificationclick", event => {

  event.notification.close();

  event.waitUntil(
    clients.matchAll({ type: "window", includeUncontrolled: true })
      .then(clientList => {

        for (const client of clientList) {
          if (client.url.includes(location.origin)) {
            return client.focus();
          }
        }

        return clients.openWindow("login.php");

      })
  );

});