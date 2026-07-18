import { validations } from "../../shared/form-validations.js";

window.document.addEventListener('DOMContentLoaded', init);

let loginForm = null;
let spinner = null;

function init() {
    console.log("[DEBUG]: login loaded");
    spinner = window.document.getElementById('spinner');
    loginForm = {
        email: {
            element: window.document.getElementById('email'),
            validations: [validations.NOT_NULL, validations.IS_EMAIL],
            msgs: ["El email no puede ir vacío.", "Esto no parece un email."]
        },
        rawPassword: {
            element: window.document.getElementById('rawPassword'),
            validations: [validations.NOT_NULL],
            msgs: ["La contraseña no puede ir vacía."]
        }
    }
    handleSubmit();
}

function handleSubmit() {

}