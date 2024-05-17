// Seleccionar el formulario y todos los inputs dentro de él
const formulario = document.getElementById("formulario");
const inputs = document.querySelectorAll("#formulario input");

// Expresiones regulares para validar campos
const expresiones = {
  documento: /^\d{7,11}$/, // Expresión para validar el documento de identidad
};

// Estado de los campos, inicialmente todos en falso
const campos = {
  documento: false, // Estado del campo documento
};

// Función para validar el formulario
const validarFormulario = (e) => {
  switch (e.target.name) {
    case "documento":
      validarCampo(expresiones.documento, e.target, "documento"); // Validar el campo documento
      break;
  }
};

// Función para validar un campo específico
const validarCampo = (expresion, input, campo) => {
  if (expresion.test(input.value)) { // Si la expresión regular coincide con el valor del input
    // Marcar el grupo como correcto
    document.getElementById(`grupo__${campo}`).classList.remove("formulario__grupo-incorrecto");
    document.getElementById(`grupo__${campo}`).classList.add("formulario__grupo-correcto");
    campos[campo] = true; // Actualizar el estado del campo
  } else {
    // Marcar el grupo como incorrecto
    document.getElementById(`grupo__${campo}`).classList.add("formulario__grupo-incorrecto");
    document.getElementById(`grupo__${campo}`).classList.remove("formulario__grupo-correcto");
    campos[campo] = false; // Actualizar el estado del campo
  }
};

// Añadir listeners para los eventos keyup y blur a cada input
inputs.forEach((input) => {
  input.addEventListener("keyup", validarFormulario);
  input.addEventListener("blur", validarFormulario);
});

// Listener para el evento submit del formulario
formulario.addEventListener("submit", (e) => {
  e.preventDefault(); // Evitar el comportamiento predeterminado de envío del formulario

  // Verificar si todos los campos son válidos
  if (Object.values(campos).every((campo) => campo)) {
    const formData = new FormData(formulario); // Crear un objeto FormData con los datos del formulario

    // Enviar los datos del formulario mediante una solicitud fetch
    fetch("validar_login.php", {
      method: "POST",
      body: formData,
    })
      .then((response) => response.json()) // Convertir la respuesta a JSON
      .then((data) => {
        // Manejar la respuesta del servidor
        if (data.success) {
          alert("¡Inicio de sesión exitoso!"); // Mostrar mensaje de éxito
          // Redirigir al usuario según el rol devuelto por el servidor
          switch (data.rol) {
            case 1:
              window.location.href = "views/admin/index.php";
              break;
            case 2:
              window.location.href = "views/usuario/index.php";
              break;
            case 4:
              window.location.href = "views/instructor/index.php";
              break;
            default:
              alert("Su usuario está bloqueado"); // Mensaje de usuario bloqueado
              break;
          }
        } else {
          alert(data.message); // Mostrar mensaje de error
        }
      })
      .catch((error) => {
        console.error("Error:", error); // Mostrar error en la consola
        alert("Hubo un error al procesar tu solicitud. Por favor, inténtalo de nuevo más tarde."); // Mostrar mensaje de error al usuario
      });
  } else {
    console.log("Formulario inválido. Por favor, revisa los campos."); // Mensaje de consola para formulario inválido
  }
});
