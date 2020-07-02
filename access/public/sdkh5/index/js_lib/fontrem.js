adaptation(1024);
//适配
function adaptation(size) {
	if (document.documentElement.clientWidth > size) {
		// document.documentElement.style.fontSize = size / 7.5 + 'px';
		document.documentElement.style.fontSize = size / 20.5 + 'px';
	} else {
		document.documentElement.style.fontSize = document.documentElement.clientWidth / 7.5 + 'px';
	}
}
window.onresize = function() {
	adaptation(1024);
}