let currentTimeOut = null;

export function toast(ms, type, msg) {
    let body = window.document.getElementsByTagName('body');
    let msgToRender = msg ?? "Hola, soy un mensaje para dar retroalimentacion al cliente";
    let toastDuration = ms ?? 3000;
    let toast = window.document.createElement('div');
    let toastParaph = window.document.createElement('p');
    toastParaph.innerText = msgToRender;

    toast.classList.add('toast')
    toast.classList.add('toast--info');
    toast.id = "toast"
    toast.appendChild(toastParaph);

    if (type === 'error') {
        toast.classList.add('toast--error')
    }

    if (type === 'success') {
        toast.classList.add('toast--success')
    }

    if (type === 'warning') {
        toast.classList.add('toast--warning');
    }

    if (body[0].getElementsByClassName('toast')) {
        [...body[0].getElementsByClassName('toast')].forEach(node => {
            node.remove();
        })
    }

    console.log(currentTimeOut);
    if (currentTimeOut) {
        clearTimeout(currentTimeOut);
    }

    body[0].appendChild(toast);
    currentTimeOut = setTimeout(() => {
        body[0].removeChild(toast);
    }, toastDuration);
}