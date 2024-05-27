const formulario = document.getElementById("formulario");
const inputs = document.querySelectorAll("#formulario input, #formulario select");

const expresiones = {
    documento: /^\d{6,11}$/, // Solo números entre 6 y 11 dígitos
    contrasena: /^.{8,12}$/, // Entre 8 y 12 caracteres
    nombre: /^[a-zA-ZÀ-ÿ\s]{10,40}$/, // Solo letras y espacios
    email: /^[a-zA-Z0-9_.+-]+@[a-zA-Z0-9-]+\.[a-zA-Z.]{2,}$/ // Correo electrónico válido
};

const campos = {
    documento: false,
    contrasena: false,
    nombre: false,
    email: false
};

const validarFormulario = (e) => {
    const campo = e.target.name;
    const valor = e.target.value;

    if (expresiones[campo]) {
        if (expresiones[campo].test(valor)) {
            document.getElementById(`grupo__${campo}`).classList.remove("formulario__grupo-incorrecto");
            document.getElementById(`grupo__${campo}`).classList.add("formulario__grupo-correcto");
            campos[campo] = true;
        } else {
            document.getElementById(`grupo__${campo}`).classList.add("formulario__grupo-incorrecto");
            document.getElementById(`grupo__${campo}`).classList.remove("formulario__grupo-correcto");
            campos[campo] = false;
        }
    }

    if (campo === "email") {
        validarEmailCompleto(valor);
    }
};

const validarEmailCompleto = (email) => {
    if (!expresiones.email.test(email)) {
        document.getElementById(`grupo__email`).classList.add("formulario__grupo-incorrecto");
        document.getElementById(`grupo__email`).classList.remove("formulario__grupo-correcto");
        campos["email"] = false;
    }
};

inputs.forEach((input) => {
    input.addEventListener("keyup", validarFormulario);
    input.addEventListener("blur", validarFormulario);
});

formulario.addEventListener("submit", (e) => {
    e.preventDefault();

    if (Object.values(campos).every((campo) => campo)) {
        const formData = new FormData(formulario);

        fetch("guardar_datos_ad.php", {
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
                window.location.href = "lista_admin.php"; // Redireccionar a login.php
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
