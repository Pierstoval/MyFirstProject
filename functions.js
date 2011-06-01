function pagination(page) {
	document.forms['astropix'].elements['page'].value = page;
	document.forms['astropix'].submit();
}