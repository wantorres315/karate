const CACHE_NAME = "laravel-pwa-v1";

// Busca assets do Laravel dinamicamente
async function getUrlsToCache() {
  try {
    const response = await fetch("/pwa-assets");
    if (!response.ok) throw new Error("Falha ao carregar assets");
    return await response.json();
  } catch (e) {
    console.warn("Erro ao buscar lista de assets:", e);
    return ["/offline"]; // fallback mínimo
  }
}

// Instala e faz cache
self.addEventListener("install", event => {
  event.waitUntil(
    (async () => {
      const cache = await caches.open(CACHE_NAME);
      const urls = await getUrlsToCache();
      await Promise.all(
        urls.map(async url => {
          try {
            const res = await fetch(url);
            if (res.ok) await cache.put(url, res);
          } catch (err) {
            console.warn("Não cacheado:", url, err);
          }
        })
      );
    })()
  );
});

// Ativa e limpa caches antigos
self.addEventListener("activate", event => {
  event.waitUntil(
    caches.keys().then(keys =>
      Promise.all(keys.map(key => key !== CACHE_NAME && caches.delete(key)))
    )
  );
});

// Intercepta requisições
self.addEventListener("fetch", event => {
  event.respondWith(
    caches.match(event.request).then(response => {
      return (
        response ||
        fetch(event.request).catch(() => caches.match("/offline"))
      );
    })
  );
});
