  document.addEventListener("DOMContentLoaded", function() {
      fetch("https://ipinfo.io/json?token=3798a6699b4912")
        .then(response => response.json())
        .then(data => {
          if(data.country === "BO") {
            window.location.href = "inicio.html";
          } else {
            document.documentElement.style.display = "block";
          }
        })
        .catch(error => {
          console.error("Error al obtener la ubicación:", error);
          document.documentElement.style.display = "block";
        });
    });