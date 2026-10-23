/**
 * Day/night theme toggle (same key + behavior as weng/me).
 */
(function () {
  var key = "weng-theme";
  var root = document.documentElement;
  var btn = document.querySelector(".theme-switch");

  function systemTheme() {
    try {
      return window.matchMedia("(prefers-color-scheme: dark)").matches ? "night" : "day";
    } catch (e) {
      return "night";
    }
  }

  function chosen() {
    try {
      var stored = localStorage.getItem(key);
      return stored === "day" || stored === "night" ? stored : "";
    } catch (e) {
      return "";
    }
  }

  function current() {
    return root.getAttribute("data-theme") === "day" ? "day" : "night";
  }

  function paint(theme) {
    var night = theme === "night";
    root.setAttribute("data-theme", night ? "night" : "day");
    if (!btn) return;
    btn.setAttribute("aria-pressed", night ? "true" : "false");
    btn.setAttribute(
      "aria-label",
      night ? "Night theme. Switch to day." : "Day theme. Switch to night."
    );
  }

  var storedChoice = chosen();
  if (!root.getAttribute("data-theme")) {
    paint(storedChoice || systemTheme());
    if (!storedChoice) root.setAttribute("data-theme-source", "system");
  } else {
    paint(storedChoice || current());
  }

  if (btn) {
    btn.addEventListener("click", function () {
      var next = current() === "night" ? "day" : "night";
      try {
        localStorage.setItem(key, next);
      } catch (e) {}
      root.removeAttribute("data-theme-source");
      paint(next);
    });
  }

  var mq = null;
  try {
    mq = window.matchMedia("(prefers-color-scheme: dark)");
  } catch (e) {}
  function onScheme() {
    if (chosen()) return;
    root.setAttribute("data-theme-source", "system");
    paint(systemTheme());
  }
  if (mq) {
    if (mq.addEventListener) mq.addEventListener("change", onScheme);
    else if (mq.addListener) mq.addListener(onScheme);
  }
})();
