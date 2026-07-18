import { fetchAPI } from "../../shared/api.js";

window.document.addEventListener('DOMContentLoaded', init);

function init() {
    console.log("[DEBUG]: confirm-account loaded.");
    handleConfirmAccount();
}

async function handleConfirmAccount() {
    let spinner = window.document.getElementById('spinner');
    let confirmMsg = window.document.getElementById('confirm-msg');
    const params = new URLSearchParams(window.location.search);
    const allParams = Object.fromEntries(params);

    console.log("[DEBUG_PARAMS]: ",params);
    console.log("[DEBUG__ALL_PARAMS]: ",allParams);

    if(!allParams.confirmation){
        spinner.remove();
        confirmMsg.innerText = "No se encontró el token de confirmación.";
        return;
    }
    const response  = await fetchAPI(`/auth/confirm?confirmation=${allParams.confirmation}`,null,'GET');

    if(!response.ok && !response.status){
        spinner.remove();
        confirmMsg.innerText = response.error;
        return;
    }

    if(response){
        spinner.remove();
        confirmMsg.innerText = response.data.msg;
        if(response.ok){
        setTimeout(() => {
            window.location.replace("/Auth/Login/login.html");
        }, 4000);
    }
        return;
    }
}