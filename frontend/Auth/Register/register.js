import { fetchAPI } from "../../shared/api.js"
import { toast } from "../../shared/toast.js";

window.document.addEventListener('DOMContentLoaded', init);

const validations = {
    NOT_NULL: (value, msg, options = null) => {
        return /^\s*$/i.test(value) ? msg : null;
    },
    ONLY_LETTERS: (value, msg, options = null) => {
        return /^[A-Za-záéíóúÁÉÍÓÚÑñ\s]*$/i.test(value) ? null : msg;
    },
    ONLY_NUMBERS: (value, msg, options = null) => {
        return /^\d{1,}/.test(value) ? msg : null;
    },
    IS_EMAIL: (value, msg, options = null) => {
        return /^[a-z0-9\.\_\+\-]+@[a-z0-9]+(\.[a-z]{2,}){1,}$/i.test(value) ? null : msg;
    },
    MIN_LENGTH: (value, msg, options = null) => {
        let validOptions = options ? options : { min: 1 };
        return value.length >= validOptions.min ? null : msg;
    }
}

let userForm = {
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
let spinner = window.document.getElementById('spinner');


function init() {
    console.log("[DEBUG]: INICIO REGISTER SCRIPT")
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
            return;
        }

        if (!response.ok && response.status) {
            toast(5000, 'warning', response.data.msg);
            return;
        }

        toast(5000, 'success', response.data.msg);
        form.reset();
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

function validateField(fieldName, fieldObj) {
    let value = fieldObj.element.value;
    let errors = [];
    fieldObj.validations.forEach((validation, i) => {
        let error = null;
        if (fieldObj.options) {
            error = validation(value, fieldObj.msgs[i], fieldObj.options[i])
        } else {
            error = validation(value, fieldObj.msgs[i])
        }

        if (error !== null) {
            errors = [...errors, error];
        }
    })
    renderMsgField(errors, fieldObj.element);
}

function renderMsgField(errors, element) {
    let nodes = [...element.closest(".form__campo").getElementsByClassName('form__campo--error')];
    if (nodes.length > 0) {
        nodes.forEach((node) => {
            node.remove();
        })
    }

    if (errors.length > 0) {
        errors.forEach((_, i) => {
            element.classList.add('error');
            let errorHTML = window.document.createElement("span");
            errorHTML.classList.add('form__campo--error')
            errorHTML.innerText = errors[i];
            element.closest(".form__campo").appendChild(errorHTML);
        })
    } else {
        element.classList.remove('error');
    }
}

function isEquals(field, secondField, msg) {
    if (field.value !== secondField.value) {
        renderMsgField([msg], secondField);
    }
}