(function () {
  function boot() {
    if (typeof mermaid === "undefined") return;
    var night = document.documentElement.getAttribute("data-theme") !== "day";
    mermaid.initialize({
      startOnLoad: true,
      theme: night ? "dark" : "default",
      securityLevel: "strict"
    });
  }
  if (document.readyState === "loading") {
    document.addEventListener("DOMContentLoaded", boot);
  } else {
    boot();
  }
})();
