var Apple = {};
        Apple.UA = navigator.userAgent;
		Apple.Device = false;
		Apple.dev_w = window.screen.width;
		//alert(Apple.dev_w);	
		if(navigator.userAgent.match(/Android/i)) {
			if(Apple.dev_w > 640){
				var scale=0.56;
				//alert("大分辨率");
				var text = '<meta name="viewport" content="width=device-width, initial-scale=' + scale + ', maximum-scale=' + scale +', minimum-scale=' + scale + ', user-scalable=yes" />'
				document.write(text);
			}
			else{
			var scale=0.5;
			var text = '<meta name="viewport" content="width=device-width, initial-scale=' + scale + ', maximum-scale=' + scale +', minimum-scale=' + scale + ', user-scalable=yes" />'
			document.write(text);
			}	
 			}
			
		else{	 
		
		if(Apple.dev_w > 320){
			var scale=0.58;
			var text = '<meta name="viewport" content="width=device-width, initial-scale=' + scale + ', maximum-scale=' + scale +', minimum-scale=' + scale + ', user-scalable=yes" />'
			document.write(text);
			}
		else{
			var scale=0.5;
			var text = '<meta name="viewport" content="width=device-width, initial-scale=' + scale + ', maximum-scale=' + scale +', minimum-scale=' + scale + ', user-scalable=yes" />'
			document.write(text);
			}	
		}