var toast_container;
var next_id = 0;

function initToasts(){
    toast_container = document.getElementById("toast-container");
}

function showToast(text){
    var toast = document.createElement("div");
    toast.id = "toast"+next_id;
    toast.innerHTML = text;
    toast_container.appendChild(toast);
    next_id++;
    return toast.id;
}

function showToastPermanent(text){
    var raw = false;
    if(text instanceof Node) raw = true;
    
    var toast = document.createElement("div");
    toast.id = "toast"+next_id;
    toast.classList.add("shown");
    if(!raw) toast.innerHTML = text;
    else toast.appendChild(text);
    toast_container.appendChild(toast);
    next_id++;
    return toast.id;
}

function hideToastPermanent(id){
    var toast = document.getElementById(id);
    toast.classList.remove("shown");
    toast.classList.add("hidden");
}

function showDeleteToast(qid){
    var node = document.createElement("div");
    node.appendChild(document.createTextNode("Czy na pewno chcecz usunąć to pytanie?"));

    var buttons_node = document.createElement("div");
    buttons_node.classList.add("toast-buttons");

    var button1 = document.createElement("a");
    button1.classList.add("button");
    button1.classList.add("flat");
    button1.classList.add("red");
    button1.innerText = "Tak";
    button1.href = "delete_question?question_id=" + qid;
    buttons_node.appendChild(button1);

    var button2 = document.createElement("button");
    button2.classList.add("flat");
    button2.innerText = "Nie";
    buttons_node.appendChild(button2);

    node.appendChild(buttons_node);

    var toast_id = showToastPermanent(node);

    button2.addEventListener("click", function(){
        hideToastPermanent(toast_id);
    });
}

function showUserNameChangeToast(uid, fname, lname, name_id){
    var node = document.createElement("div");
    node.appendChild(document.createTextNode("Podaj nowe imię i nazwisko dla tego użytkownika:"));

    var form_node = document.createElement("form");
    form_node.target = "#";

    var fname_input = document.createElement("input");
    fname_input.type = "text";
    fname_input.value = fname;
    form_node.appendChild(fname_input);

    var lname_input = document.createElement("input");
    lname_input.type = "text";
    lname_input.value = lname;
    form_node.appendChild(lname_input);

    var buttons_node = document.createElement("div");
    buttons_node.classList.add("toast-buttons");

    var button1 = document.createElement("button");
    button1.classList.add("flat");
    button1.classList.add("colored");
    button1.type = "submit";
    button1.innerText = "Zapisz";
    buttons_node.appendChild(button1);

    var button2 = document.createElement("button");
    button2.classList.add("flat");
    button2.type = "button";
    button2.innerText = "Anuluj";
    buttons_node.appendChild(button2);

    form_node.appendChild(buttons_node);
    node.appendChild(form_node);

    var toast_id = showToastPermanent(node);

    form_node.addEventListener("submit", function(e){
        e.preventDefault();
        hideToastPermanent(toast_id);
        
        var session_key = getCookie("SESSION");
        var post_data = "uid="+uid+"&first_name="+fname_input.value+"&last_name="+lname_input.value+"&session="+session_key;
        
        sendXHR("api/change_user_name", post_data, function (response){
            var json_response = JSON.parse(response);
            if(json_response.result == "success"){
                showToast("<i class='fa fa-check green'></i> Imię i nazwisko tego użytkownika zostały zmienione.");
                document.getElementById(name_id).innerHTML = fname_input.value+" "+lname_input.value;
            }else{
                showToast("<i class='fa fa-times red'></i> Błąd: "+json_response.description);
            }
        });

        return false;
    });

    button2.addEventListener("click", function(){
        hideToastPermanent(toast_id);
    });
}

function showDeleteUserToast(uid){
    var node = document.createElement("div");
    node.appendChild(document.createTextNode("Czy na pewno chcecz usunąć tego użytkownika?"));
    
    var i_node = document.createElement("i");
    i_node.classList.add("secondary");
    i_node.classList.add("block");
    i_node.innerHTML = "(Tę operację można cofnąć)";
    node.appendChild(i_node);

    var buttons_node = document.createElement("div");
    buttons_node.classList.add("toast-buttons");

    var button1 = document.createElement("button");
    button1.classList.add("flat");
    button1.classList.add("red");
    button1.innerText = "Tak";
    buttons_node.appendChild(button1);

    var button2 = document.createElement("button");
    button2.classList.add("flat");
    button2.innerText = "Nie";
    buttons_node.appendChild(button2);

    node.appendChild(buttons_node);

    var toast_id = showToastPermanent(node);

    button1.addEventListener("click", function(){
        hideToastPermanent(toast_id);

        var session_key = getCookie("SESSION");
        var post_data = "uid="+uid+"&session="+session_key;
        
        sendXHR("api/delete_user", post_data, function (response){
            var json_response = JSON.parse(response);
            if(json_response.result == "success"){
                showToast("<i class='fa fa-check green'></i> Usunięto użytkownika.");
            }else{
                showToast("<i class='fa fa-times red'></i> Błąd: "+json_response.description);
            }
        });
    });

    button2.addEventListener("click", function(){
        hideToastPermanent(toast_id);
    });
}