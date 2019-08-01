


function validate(form) {

		fail = validateUsername(form.username.value)
		fail += validatePassword(form.password.value)
		fail += validateEmail(form.email.value)

		if (fail == "") return true;
		else { alert(fail); return false; }
}



function validateUsername(field){
	if (/[ *]/.test(field) || field == "") return "No Username was entered.\n"
	else if (field.length < 8)
		return "Usernames must be at least 8 characters.\n"
	else if (/[^a-zA-Z0-9_-]/.test(field))
		return "Only a-z, A-Z, 0-9, - and _ allowed in Usernames.\n"

	return ""
}



function validatePassword(field){

	
	if (field == "") return "No Password was entered.\n"
	else if (field.length < 6)
		return "Passwords must be at least 6 characters.\n"
	else if (field.length > 12) {return ""}
	else if (!/[a-z]/.test(field) || ! /[A-Z]/.test(field) ||!/[0-9]/.test(field))
		return "Passwords require one each of a-z, A-Z and 0-9.\n"
	return ""
}


function validateEmail(field){
	if (field == "") return "No Email was entered.\n"

	else if (!/^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/.test(field))	
		return "The Email address is invalid.\n"
	return ""
}









