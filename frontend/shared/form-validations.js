export const validations = {
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

export function isEquals(field, secondField, msg) {
    if (field.value !== secondField.value) {
        renderMsgField([msg], secondField);
    }
}

export function validateField(fieldName, fieldObj) {
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