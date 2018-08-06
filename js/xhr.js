function sendXHR(url, params, callback, method){
    method = (typeof method === 'undefined') ? 'POST' : method;
    url += (url.match(/\?/) == null ? "?" : "&") + (new Date()).getTime(); // Dodanie znacznika czasu, omijanie cache'u

    var xhr = new XMLHttpRequest();
    xhr.open(method, url, true);
    xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    xhr.onreadystatechange = function (aEvt) {
        if (xhr.readyState == 4) {
            callback(xhr.responseText);
        }
    };
    xhr.send(params);
}