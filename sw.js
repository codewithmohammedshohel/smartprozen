// Service Worker for SmartProzen PWA - Network First Strategy
const CACHE_NAME = 'smartprozen-cache-v12'; // Bump version to force update
const urlsToCache = [
  '/',
  '/index.php',
  '/css/style.css',
  '/css/modern-components.css',
  '/js/main.js',
  '/images/logo.png'
];

// Install event - cache assets
self.addEventListener('install', event => {
  event.waitUntil(
    caches.open(CACHE_NAME)
      .then(cache => {
        console.log('Opened cache');
        return cache.addAll(urlsToCache);
      })
  );
});

// Activate event - clean up old caches
self.addEventListener('activate', event => {
  const cacheWhitelist = [CACHE_NAME];
  event.waitUntil(
    caches.keys().then(cacheNames => {
      return Promise.all(
        cacheNames.map(cacheName => {
          if (cacheWhitelist.indexOf(cacheName) === -1) {
            return caches.delete(cacheName);
          }
        })
      );
    })
  );
});

// Fetch event - Network First, then Cache
self.addEventListener('fetch', event => {
    event.respondWith(
        fetch(event.request)
            .then(response => {
                // Check if we received a valid response
                if (response && response.status === 200) {
                    const responseToCache = response.clone();
                    caches.open(CACHE_NAME)
                        .then(cache => {
                            // Cache only GET requests and non-API calls
                            if (event.request.method === 'GET' && !event.request.url.includes('/api/')) {
                                cache.put(event.request, responseToCache);
                            }
                        });
                }
                return response;
            })
            .catch(() => {
                // Network request failed, try to get it from the cache.
                return caches.match(event.request);
            })
    );
});