var Apple = {};
Apple.UA = navigator.userAgent;
Apple.Device = false;
Apple.dev_w = window.screen.width;
var scale=Apple.dev_w/640;
var text = '<meta name="viewport" content="width=device-width, initial-scale=' + scale + ', maximum-scale=' + scale +', minimum-scale=' + scale + ', user-scalable=yes" />'
document.write(text);