
accessid = ''
accesskey = ''
host = ''
policyBase64 = ''
signature = ''
filename = ''
key = ''
expire = 0
g_object_name = ''
gettype = ''
now = timestamp = Date.parse(new Date()) / 1000; 

function send_request()
{
    var xmlhttp = null;
    if (window.XMLHttpRequest)
    {
        xmlhttp=new XMLHttpRequest();
    }
    else if (window.ActiveXObject)
    {
        xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
    }
  
    if (xmlhttp!=null)
    {	
		var sigweb = document.getElementById('sigweb').value;
      
        phpUrl = sigweb;
        xmlhttp.open( "GET", phpUrl, false );
        xmlhttp.send( null );
        return xmlhttp.responseText
    }
    else
    {
        alert("Your browser does not support XMLHTTP.");
    }
};

//获取文件类型
function get_suffix(oldfilename) {
    pos = oldfilename.lastIndexOf('.')
    suffix = ''
    if (pos != -1) {
        suffix = oldfilename.substring(pos)
    }
    return suffix;
}

//新文件名拼写
function calculate_object_name(oldfilename)
{
    suffix = get_suffix(oldfilename)
    g_object_name = key + filename + suffix
	filename = filename + suffix
    return '';
}

function get_signature()
{
    //可以判断当前expire是否超过了当前时间,如果超过了当前时间,就重新取一下.3s 做为缓冲
    now = timestamp = Date.parse(new Date()) / 1000; 
    console.log('get_signature ...');
    console.log('expire:' + expire.toString());
    console.log('now:', + now.toString())
    if (expire < now + 3)
    {
        console.log('get new sign')
        body = send_request()
        var obj = eval ("(" + body + ")");
        host = obj['host']
        policyBase64 = obj['policy']
        accessid = obj['accessid']
        signature = obj['signature']
        expire = parseInt(obj['expire'])
        key = obj['dir']
		filename = obj['filename']	
		//gettype = obj['gettype']
        return true;
    }
    return false;
};

function set_upload_param(up, oldfilename, ret)
{
    if (ret == false)
    {
        ret = get_signature()
    }
    g_object_name = key;
    if (oldfilename != '') {
        calculate_object_name(oldfilename)
    }
        new_multipart_params = {
            'key' : g_object_name,
            'policy': policyBase64,
            'OSSAccessKeyId': accessid, 
            'success_action_status' : '200', //让服务端返回200,不然，默认会返回204
            'signature': signature,
        };

        up.setOption({
            'url': host,
            'multipart_params': new_multipart_params
        });

        console.log('reset uploader')
        uploader.start();
}

var uploader = new plupload.Uploader({
	runtimes : 'html5,flash,silverlight,html4',
	browse_button : 'selectfiles', 
	container: document.getElementById('container'),
	flash_swf_url : 'lib/plupload-2.1.2/js/Moxie.swf',
	silverlight_xap_url : 'lib/plupload-2.1.2/js/Moxie.xap',

    url : 'http://oss.aliyuncs.com',

	init: {
		PostInit: function() {
			document.getElementById('ossfile').innerHTML = '';
			document.getElementById('postfiles').onclick = function() {
            set_upload_param(uploader, '', false);
            uploader.start();
            return false;
			};
		},

		FilesAdded: function(up, files) {
			plupload.each(files, function(file) {
				document.getElementById('ossfile').innerHTML = '';
				document.getElementById('ossfile').innerHTML += '<div id="' + file.id + '">' + file.name + ' (' + plupload.formatSize(file.size) + ')<b></b>'
				+'<div class="progress"><div class="progress-bar" style="width: 0%"></div></div>'
				+'</div>';
			});
		},

		BeforeUpload: function(up, file) {
            set_upload_param(up, file.name, true);
        },

		UploadProgress: function(up, file) {
			var d = document.getElementById(file.id);
			d.getElementsByTagName('b')[0].innerHTML = '<span>' + file.percent + "%</span>";
            
            var prog = d.getElementsByTagName('div')[0];
			var progBar = prog.getElementsByTagName('div')[0]
			progBar.style.width= 2*file.percent+'px';
			progBar.setAttribute('aria-valuenow', file.percent);
		},

		FileUploaded: function(up, file, info) {
            console.log('uploaded')
            console.log(info.status)
            //set_upload_param(up);

            if (info.status >= 200 || info.status < 200)
            {

				var ajaxweb = document.getElementById('ajaxweb').value;
				var id = document.getElementById('appid').value;
                
				$.ajax({
					url : ajaxweb,
					type : 'POST',
					data:{appid:id},
					success : function(data) {
						//var result = eval('('+data+')');
						if (data.success){
							document.getElementById(file.id).getElementsByTagName('b')[0].innerHTML = '上传成功';
							//if(gettype == 'material'){
							//	document.getElementById('filename').value = filename;
							//}else{
							  alert(data.msg);
                              window.location.reload();//刷新当前页面.
							//}
							//self.location=document.referrer;
						} else {
							alert(data.msg);
						}
					}
				},'json');
				
            }
            else
            {
                document.getElementById(file.id).getElementsByTagName('b')[0].innerHTML = info.response;
            } 
		},

		Error: function(up, err) {
            //set_upload_param(up);
			document.getElementById('console').appendChild(document.createTextNode("\nError xml:" + err.response));
		}
	}
});

uploader.init();
