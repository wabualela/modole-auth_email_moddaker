const input = document.querySelector("#id_phone1");
window.intlTelInput(input, {
  loadUtils: () => import("https://cdn.jsdelivr.net/npm/intl-tel-input@25.3.0/build/js/utils.js"),
});