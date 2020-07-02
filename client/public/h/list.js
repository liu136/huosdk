(function() {
     /**
     * 游戏下拉
     */
	   function initGame() {
                 $("#gameinput").bsSuggest('init', {
                 url: "/Public/ajaxgame",
                 idField: "appid",
                 keyField: "gamename"
             }).on('onDataRequestSuccess', function (e, result) {
               console.log('onDataRequestSuccess: ', result);
             }).on('onSetSelectValue', function (e, keyword, data) {
            	 /*alert(data.appid);*/
				 $("#appid").attr("value",data.appid);
               console.log('onSetSelectValue: ', keyword, data);
             }).on('onUnsetSelectValue', function () {
				 $("#appid").attr("value",0);
               console.log('onUnsetSelectValue');
            });
           }
           //按钮方法事件监听
           $('#methodTest button').on('click', function() {
             var method = $(this).text();
             var $i;

             if (method === 'init') {
                 initGame();
             } else {
                 $i = $('#gameinput').bsSuggest(method);
                 console.log($i);
                 if (!$i) {
                    alert('未初始化或已销毁');
                 }
             }

             if (method === 'version') {
                alert($i);
             }
           });
         initGame();
         
		/**
         * 渠道下拉
        */
         function initClient() {
	             $("#clientinput").bsSuggest('init', {
	             url: "/Public/ajaxclient",
	             idField: "id",
	             keyField: "nickname"
	         }).on('onDataRequestSuccess', function (e, result) {
	           console.log('onDataRequestSuccess: ', result);
	         }).on('onSetSelectValue', function (e, keyword, data) {
				 $("#cid").attr("value",data.id);
	           console.log('onSetSelectValue: ', keyword, data);
	         }).on('onUnsetSelectValue', function () {
				 $("#cid").attr("value",0);
	           console.log('onUnsetSelectValue');
	        });
	       }
           //按钮方法事件监听
           $('#methodTest button').on('click', function() {
             var method = $(this).text();
             var $i;

             if (method === 'init') {
                 initClient();
             } else {
                 $i = $('#clientinput').bsSuggest(method);
                 console.log($i);
                 if (!$i) {
                    alert('未初始化或已销毁');
                 }
             }

             if (method === 'version') {
                alert($i);
             }
           });

		   initClient();
		   
		   
		   /**
	         * 渠道名称
	        */
			 function initAgent() {
	                 $("#agentinput").bsSuggest('init', {
	                 url: "/Public/ajaxagentlist",
	                 idField: "id",
	                 keyField: "user_nicename"
	             }).on('onDataRequestSuccess', function (e, result) {
	               console.log('onDataRequestSuccess: ', result);
	             }).on('onSetSelectValue', function (e, keyword, data) {
					 $("#agentid").attr("value",data.id);
	               console.log('onSetSelectValue: ', keyword, data);
	             }).on('onUnsetSelectValue', function () {
					 $("#agentid").attr("value",0);
	               console.log('onUnsetSelectValue');
	            });
	           }
	           //按钮方法事件监听
	           $('#methodTest button').on('click', function() {
	             var method = $(this).text();
	             var $i;

	             if (method === 'init') {
	            	 initAgent();
	             } else {
	                 $i = $('#agentinput').bsSuggest(method);
	                 console.log($i);
	                 if (!$i) {
	                    alert('未初始化或已销毁');
	                 }
	             }

	             if (method === 'version') {
	                alert($i);
	             }
	           });

	           initAgent();

}());
