const formulario = document.getElementById("formulario");
const inputs = document.querySelectorAll(
  "#formulario input, #formulario select"
);

const expresiones = {
  documento: /^\d{7,11}$/, // Solo números entre 7 y 11 dígitos
  contrasena: /^.{8,12}$/, // Entre 8 y 12 caracteres
  nombre: /^[a-zA-ZÀ-ÿ\s]{10,40}$/, // Solo letras y espacios
  email: /^[a-zA-Z0-9_.+-]+@[a-zA-Z0-9-]+\.[a-zA-Z.]{2,}$/, // Correo electrónico válido
  ficha: /^\d+$/ // Solo números
};

const campos = {
  documento: false,
  contrasena: false,
  nombre: false,
  email: false,
  ficha: false, // Agregar ficha a los campos
};

const validarFormulario = (e) => {
  switch (e.target.name) {
    case "documento":
      validarCampo(expresiones.documento, e.target, "documento");
      break;
    case "contrasena":
      validarCampo(expresiones.contrasena, e.target, "contrasena");
      break;
    case "nombre":
      validarCampo(expresiones.nombre, e.target, "nombre");
      break;
    case "email":
      validarCampo(expresiones.email, e.target, "email");
      break;
    case "ficha": // Agregar ficha al switch
      validarCampo(expresiones.ficha, e.target, "ficha");
      break;
  }
};

const validarCampo = (expresion, input, campo) => {
  if (expresion.test(input.value)) {
    document.getElementById(`grupo__${campo}`).classList.remove("formulario__grupo-incorrecto");
    document.getElementById(`grupo__${campo}`).classList.add("formulario__grupo-correcto");
    campos[campo] = true;
  } else {
    document.getElementById(`grupo__${campo}`).classList.add("formulario__grupo-incorrecto");
    document.getElementById(`grupo__${campo}`).classList.remove("formulario__grupo-correcto");
    campos[campo] = false;
  }
};

const limitarEmail = (e) => {
  const email = e.target.value;
  const domainPattern = /\.(com|edu\.co)$/; // Ajusta el patrón de dominios según los requerimientos

  // Encuentra la posición del dominio
  const domainMatch = email.match(domainPattern);
  if (domainMatch && email.length > domainMatch.index + domainMatch[0].length) {
    e.target.value = email.slice(0, domainMatch.index + domainMatch[0].length);
  }
};

inputs.forEach((input) => {
  input.addEventListener("keyup", validarFormulario);
  input.addEventListener("blur", validarFormulario);
  if (input.name === "email") {
    input.addEventListener("input", limitarEmail);
  }
});

formulario.addEventListener("submit", (e) => {
  e.preventDefault();

  if (Object.values(campos).every((campo) => campo)) {
    const formData = new FormData(formulario);

    fetch("guardar_datos.php", {
      method: "POST",
      body: formData,
    })
      .then((response) => {
        // Verifica el tipo de contenido de la respuesta
        if (response.headers.get("Content-Type").includes("application/json")) {
          return response.json();
        } else {
          throw new Error("La respuesta del servidor no es JSON");
        }
      })
      .then((data) => {
        if (data.status === "error") {
          console.error("Error en el servidor:", data.message);
          alert(data.message);
        } else {
          console.log(data.message);
          alert("¡Registro exitoso!");
          formulario.reset();
          window.location.href = "lista_instructores.php"; // Redireccionar a login.php
        }
      })
      .catch((error) => {
        console.error("Error:", error);
        console.error("Detalles del error:", error.message);
        alert(
          "Hubo un error al procesar tu solicitud. Por favor, inténtalo de nuevo más tarde."
        );
      });
  } else {
    console.log("Formulario inválido. Por favor, revisa los campos.");
  }
});
