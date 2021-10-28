function collapse(div) {
	if(document.getElementById(div).style.display == 'none') {
		document.getElementById(div).style.display = 'block';
	}
	else{
		document.getElementById(div).style.display = 'none';
	}
}