import { validations, validateField, isEquals } from "../../shared/form-validations.js";
import { fetchAPI } from "../../shared/api.js"
import { toast } from "../../shared/toast.js";

window.document.addEventListener('DOMContentLoaded', init);

let userForm = null;
let spinner = null;


function init() {
    console.log("[DEBUG]: INICIO REGISTER SCRIPT");
    spinner = window.document.getElementById('spinner');
    userForm = {
        userName: {
            element: window.document.getElementById('userName'),
            validations: [validations.NOT_NULL, validations.ONLY_LETTERS],
            msgs: ["El nombre no puede ir vacío", "El nombre solo puede contener letras"]
        },
        lastName: {
            element: window.document.getElementById('lastName'),
            validations: [validations.NOT_NULL, validations.ONLY_LETTERS],
            msgs: ["Este campo no puede ir vacío", "El apellido solo puede contener letras"]
        },
        email: {
            element: window.document.getElementById('email'),
            validations: [validations.NOT_NULL, validations.IS_EMAIL],
            msgs: ["Este campo no puede ir vacío", "Esto no parece un email"]
        },
        rawPassword: {
            element: window.document.getElementById('rawPassword'),
            validations: [validations.NOT_NULL, validations.MIN_LENGTH],
            msgs: ["Este campo no puede ir vacío", "La contraseña debe tener al menos 8 caracteres"],
            options: [null, { min: 8 }]
        },
        rawPasswordRepeat: {
            element: window.document.getElementById('rawPasswordRepeat'),
            validations: [validations.NOT_NULL],
            msgs: ["Este campo no puede ir vacío"]
        }
    }
    handleSubmit();
    spinner.classList.add('hidden');
}


function handleSubmit() {
    let form = window.document.getElementById('form');
    let btnSubmit = window.document.getElementById('register-btn');
    let hasErrors = false;
    btnSubmit.addEventListener('click', async (ev) => {
        ev.preventDefault();
        validateForm();
        if ([...form.getElementsByClassName("error")].length > 0) {
            hasErrors = true;
            return;
        }

        const dataToSend = {
            userName: userForm.userName.element.value,
            lastName: userForm.lastName.element.value,
            email: userForm.email.element.value,
            rawPassword: userForm.rawPassword.element.value
        }
        console.log("[DEBUG_DATA]: Data to send... " + JSON.stringify(dataToSend));
        spinner.classList.remove('hidden');
        form.classList.add('hidden');

        const response = await fetchAPI('/auth/register', dataToSend, 'POST');
        console.log(response);
        if (!response.ok && !response.status) {
            toast(3000, 'error', response.error);
        }

        if (!response.ok && response.status) {
            toast(5000, 'warning', response.data.msg);
        }

        if (response.ok && response.status) {
            toast(5000, 'success', response.data.msg);
            form.reset();
        }

        spinner.classList.add('hidden');
        form.classList.remove('hidden');

    })
}


function validateForm() {
    Object.entries(userForm).forEach(([key, obj]) => {
        validateField(key, obj);
    });
    isEquals(userForm.rawPassword.element, userForm.rawPasswordRepeat.element, "Las contraseñas deben ser iguales");
}
