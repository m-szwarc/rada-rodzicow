function onBodyLoad(){
	addFileInputListeners();
	initToasts();
}

function checkInputDateSupport(date_control_id, time_control_id, infobox_id){
    var date_control = document.getElementById(date_control_id);
    var time_control = document.getElementById(time_control_id);
    if(date_control.type == "date" && time_control.type == "time") return;

    document.getElementById(infobox_id).style.display = "inline";
}

function addFileInputListeners(){
	var inputs = document.querySelectorAll('.inputfile');
	inputs.forEach(function(input)
	{
		var label = input.nextElementSibling;
		var labelVal = label.innerHTML;

		input.addEventListener('change', function(e)
		{
			var fileName = '';
			if(this.files && this.files.length > 1)
				fileName = (this.getAttribute('data-multiple-caption') || '').replace('{count}', this.files.length);
			else
				fileName = e.target.value.split('\\').pop();

			if(fileName)
				label.querySelector('span').innerHTML = fileName;
			else
				label.innerHTML = labelVal;
		});
	});
}

function getCookie(name){
	var value = "; " + document.cookie;
	var parts = value.split("; " + name + "=");
	if (parts.length == 2) return parts.pop().split(";").shift();
  }

function sendVote(uid, qid, aid){
	var session_key = getCookie("SESSION");
	var post_data = "uid="+uid+"&qid="+qid+"&aid="+aid+"&session="+session_key;

	sendXHR("api/vote", post_data, function (response){
		var json_response = JSON.parse(response);
		if(json_response.result == "success"){
			showToast("<i class='fa fa-check green'></i> Twój głos został zapisany.");
			buttons = document.querySelectorAll(".vote-button[data-qid='"+qid+"']");
			buttons.forEach(function(button){
				button.disabled = true;
				if(button.dataset.aid != aid) button.classList.remove("colored");
				button.removeEventListener('click', sendVote);
			});

			var total_votes = document.querySelector(".total-votes[data-qid='"+qid+"']");
			if(total_votes !== null) total_votes.innerText = parseInt(total_votes.innerText) + 1;
			var answer_votes = document.querySelector(".answer-votes[data-qid='"+qid+"'][data-aid='"+aid+"']");
			if(answer_votes !== null) answer_votes.innerText = parseInt(answer_votes.innerText) + 1;

			var new_vote = document.querySelector(".new-vote[data-qid='"+qid+"'][data-aid='"+aid+"']");
			var user_full_name = document.getElementById("user-full-name");
			if(new_vote !== null && user_full_name !== null){
				new_vote.innerText = new_vote.innerText.replace("%name%", user_full_name.innerText);
				new_vote.style.display = "inline";
			}
		}else{
			showToast("<i class='fa fa-times red'></i> Błąd: "+json_response.description);
		}
	});
}

function useLimitChanged(checkbox){
	document.getElementById("limit1").disabled = !checkbox.checked;
	document.getElementById("limit2").disabled = !checkbox.checked;
}

function addAnswer(){
	var answer_container = document.getElementById("answer-container");
	var new_answer = document.createElement("div");
	var ans_id = document.getElementById("next-aid");
	var answer_id = parseInt(ans_id.value);

	new_answer.classList.add("answer-box");
	new_answer.innerHTML  = "<input type='checkbox' name='use_answer[n" + answer_id + "]' value='n" + answer_id + "' id='use-answern" + answer_id + "' checked />";
	new_answer.innerHTML += "<label for='use-answern" + answer_id + "'></label>";
	new_answer.innerHTML += "<input type='text' name='value_answer[n" + answer_id + "]' value='' />";

	ans_id.value = answer_id+1;

	answer_container.appendChild(new_answer);
}

function disableFile(id){
	var file_box = document.getElementById("file-"+id);
	file_box.classList.toggle("disabled");
	if(file_box.classList.contains("disabled")) document.getElementById("use_file_"+id).value = 0;
	else document.getElementById("use_file_"+id).value = 1;
}

function changePassword(oldpass_id, newpass_id, pwtpass_id){
	var oldpass = document.getElementById(oldpass_id).value;
	var newpass = document.getElementById(newpass_id).value;
	var pwtpass = document.getElementById(pwtpass_id).value;

	if(newpass.length < 8){
		showToast("<i class='fa fa-times red'></i> Nowe hasło jest za krótkie");
		return;
	}

	if(!checkPasswordChars(newpass)){
		showToast("<i class='fa fa-times red'></i> Nowe hasło nie posiada wymaganych znaków");
		return;
	}

	if(newpass != pwtpass){
		showToast("<i class='fa fa-times red'></i> Pole \"Powtórz hasło\" zawiera inne hasło niż pole \"Nowe hasło\"");
		return;
	}

	var session_key = getCookie("SESSION");
	var post_data = "old="+oldpass+"&new="+newpass+"&session="+session_key;

	sendXHR("api/change_password", post_data, function (response){
		var json_response = JSON.parse(response);
		if(json_response.result == "success"){
			showToast("<i class='fa fa-check green'></i> Twoje hasło zostało zmienione.");
		}else{
			showToast("<i class='fa fa-times red'></i> Błąd: "+json_response.description);
		}
	});
}

function changeEmail(email_field_id, email_display_id){
	var newemail = document.getElementById(email_field_id).value;
	var session_key = getCookie("SESSION");
	var post_data = "new="+newemail+"&session="+session_key;

	sendXHR("api/change_email", post_data, function (response){
		var json_response = JSON.parse(response);
		if(json_response.result == "success"){
			showToast("<i class='fa fa-check green'></i> Twój adres e-mail został zmieniony.");
			document.getElementById(email_display_id).innerHTML = newemail;
		}else{
			showToast("<i class='fa fa-times red'></i> Błąd: "+json_response.description);
		}
	});
}

function checkPasswordChars(pass){
	if(!pass.match(/[a-ząćęłńóśźż]/)) return false;
	if(!pass.match(/[A-ZĄĆĘŁŃÓŚŹŻ]/)) return false;
	if(!pass.match(/[0-9`~!@#$%^&*()\-_=+\[\]{};:<,>.\/?|\\\'\"]/)) return false;
	return true;
}

function changePasswordForUser(user_id, newpass_id, pwtpass_id){
	var newpass = document.getElementById(newpass_id).value;
	var pwtpass = document.getElementById(pwtpass_id).value;

	if(newpass.length < 8){
		showToast("<i class='fa fa-times red'></i> Nowe hasło jest za krótkie");
		return;
	}

	if(!checkPasswordChars(newpass)){
		showToast("<i class='fa fa-times red'></i> Nowe hasło nie posiada wymaganych znaków");
		return;
	}

	if(newpass != pwtpass){
		showToast("<i class='fa fa-times red'></i> Pole \"Powtórz hasło\" zawiera inne hasło niż pole \"Nowe hasło\"");
		return;
	}

	var session_key = getCookie("SESSION");
	var post_data = "uid="+user_id+"&new="+newpass+"&session="+session_key;

	sendXHR("api/change_password_for_user", post_data, function (response){
		var json_response = JSON.parse(response);
		if(json_response.result == "success"){
			showToast("<i class='fa fa-check green'></i> Hasło tego użytkownika zostało zmienione.");
		}else{
			showToast("<i class='fa fa-times red'></i> Błąd: "+json_response.description);
		}
	});
}

function changeEmailForUser(user_id, email_field_id, email_display_id){
	var newemail = document.getElementById(email_field_id).value;
	var session_key = getCookie("SESSION");
	var post_data = "uid="+user_id+"&new="+newemail+"&session="+session_key;

	sendXHR("api/change_email_for_user", post_data, function (response){
		var json_response = JSON.parse(response);
		if(json_response.result == "success"){
			showToast("<i class='fa fa-check green'></i> Adres e-mail tego użytkownika został zmieniony.");
			document.getElementById(email_display_id).innerHTML = newemail;
		}else{
			showToast("<i class='fa fa-times red'></i> Błąd: "+json_response.description);
		}
	});
}

function changeUserName(user_id, curr_first_name, curr_last_name, name_id){
	showUserNameChangeToast(user_id, curr_first_name, curr_last_name, name_id);
}

function updateSeasonData(uid, value){
	var input = document.getElementById("season-data"+uid);
	input.value = parseInt(input.value) ^ (1<<(value-1));
}

function saveSeasonData(uid){
	var session_key = getCookie("SESSION");
	var post_data = "uid="+uid+"&season="+document.getElementById("season-data"+uid).value+"&session="+session_key;

	sendXHR("api/change_season_for_user", post_data, function (response){
		var json_response = JSON.parse(response);
		if(json_response.result == "success"){
			showToast("<i class='fa fa-check green'></i> Kadencje tego użytkownika zostały ustawione.");
		}else{
			showToast("<i class='fa fa-times red'></i> Błąd: "+json_response.description);
		}
	});
}

function restoreUser(uid){
	var session_key = getCookie("SESSION");
	var post_data = "uid="+uid+"&session="+session_key;

	sendXHR("api/restore_user", post_data, function (response){
		var json_response = JSON.parse(response);
		if(json_response.result == "success"){
			showToast("<i class='fa fa-check green'></i> Użytkownik został przywrócony.");
			document.getElementById("user-row-"+uid).style.display = "none";
		}else{
			showToast("<i class='fa fa-times red'></i> Błąd: "+json_response.description);
		}
	});
}

function deleteUser(uid){
	showDeleteUserToast(uid);
}

function showQuestions(sender, target_id){
	sender.style.display = "none";
	document.getElementById(target_id).classList.remove("hidden");
}

function updatePrivData(value){
	var input = document.getElementById("priv-data");
	input.value = parseInt(input.value) ^ (1<<value);
}

function savePrivData(uid){
	var session_key = getCookie("SESSION");
	var post_data = "uid="+uid+"&priv="+document.getElementById("priv-data").value+"&session="+session_key;

	sendXHR("api/change_priv_for_user", post_data, function (response){
		var json_response = JSON.parse(response);
		if(json_response.result == "success"){
			showToast("<i class='fa fa-check green'></i> Uprawnienia tego użytkownika zostały ustawione.");
		}else{
			showToast("<i class='fa fa-times red'></i> Błąd: "+json_response.description);
		}
	});
}

function toggleVisibility(id){
	document.getElementById(id).classList.toggle("hidden");
}

function toggleNavbar(){
	document.getElementById('nav-content').classList.toggle('shown');
	document.getElementById('nav-right-edge').classList.toggle('shown');
}

function showFullResults(){
	document.getElementById('show-full-results').style.display = 'none';
	var names = document.querySelectorAll('span.full-results');
	names.forEach((elem) => {
		elem.style.display = 'inline';
	});
}