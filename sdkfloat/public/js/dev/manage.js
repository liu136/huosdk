var mapp = mapp || {};
mapp.createmanage = {

    initPage : function(){


       var onlineStatus = ['已上线','审核中','审核未通过','已上线','待发布'],
            offlineStatus = ['未提交审核','审核中','审核未通过','','待发布','已下线'],
            onlineStatusColor = ['green','green','orange','green','green'],
            offlineStatusColor = ['gray','green','orange','green','green','orange'],
            xinzhengStatus = ['未申请','申请中','申请成功','申请失败'],
            xinzhengAction = ['立即申请','','查看详情','查看详情'],
            isOnline = $('#isonline').val();
        var status = $('.status').text();
        $('.status').text( isOnline ? onlineStatus[status-1] : offlineStatus[status-1]);
        $('.status').addClass('status-'+(isOnline? onlineStatusColor[status-1] : offlineStatusColor[status-1] || 'gray' ))
        $('.status').text() == '' ? $('.status').hide() : $('.status').show();
        var breadcrumbint =parseInt(breadcrumb);
        this.initStepNav(breadcrumbint);

        switch(shenhestatus){
            case 'DEVING':$('.shenhestatus').text('未提交审核');break;
            case 'VERIFY_ING':$('.shenhestatus').text('审核中');break;
            case 'VERIFY_FAIL':$('.shenhestatus').text('审核未通过');break;
            case 'VERIFY_SUCC':$('.shenhestatus').text('通过审核');break;
            default :break;
        }




    },
    initStepNav : function(step){


       var  stepPoints = [
            {
                text : '创建游戏',
                lineText : ''
            },
            {
                text : '签署合同',
                lineText : ''
            },

            {
                text : '新游测试',
                lineText : ''
            },
            {
                text : '上线运营',
                lineText : ''
            }
        ];

        $('.stepnav').stepnav({
            points : stepPoints,
            currentStep : step || 1
        });
    },




    initListeners : function(){
        var _this = this;
        $(document.body).delegates({

            //申请开通专区
            '.zoneapply':function(){
                util.ajax({
                    url : '/mod/comtest/zoneApply?appid='+appid,
                    success : function(data){
                        location.href = '/mod/comtest/createInfo?appid='+appid;
                    }
                });
            },
            '.disabled':function(){
                return false;
            },


            '.xinzheng-action':function(){

                    $.ajax({
                        url : '/mod/mobileapp/apply?appid='+appid,
                        type : 'get',
                        dataType : 'json',
                        success : function(data){
                            var res = ['<p style="text-align:center;line-height:30px;font-size:16px;font-weight:bold">操作失败</p><p style="text-align:center;line-height:30px">服务器通信失败，请稍后重试。</p>','<p style="text-align:center;line-height:30px;font-size:16px;font-weight:bold">操作成功</p><p style="text-align:center;line-height:30px">正在为您安排推广资源，请等候通知。</p>']
                            $.popbox({
                                'content' : res[++data['errno']],
                                'width' : 500,
                                'btns' : [
                                    {
                                        type : 'blue',
                                        text : '确定'
                                    }
                                ]
                            });
                            data['errno']== 1 && ($this.hide(),$('.xinzheng').text('申请中'));
                        },
                        error : function(data){
                            $.popbox({
                                'content' : '<p style="text-align:center;line-height:30px;font-size:16px;font-weight:bold">操作失败</p><p style="text-align:center;line-height:30px">服务器通信失败，请稍后重试。</p>',
                                'width' : 500,
                                'btns' : [
                                    {
                                        type : 'blue',
                                        text : '确定'
                                    }
                                ]
                            });
                        }
                    })

            },
            '.service-end':function(){
                util.ajax({
                    url : '/mod2/createmobile/endcomtest?appid='+appid,
                    success : function(data){
                        if(data=='rz'){
                            $.popbox({
                                width : '400px',
                                content : '<div style="text-align:center;margin-top:20px;font-size:16px;">请先提交软件的著作权文件<p style="text-align:center;margin-top:14px;font-size:14px;color:#666;">根据相关规定，游戏类应用必须提供版权证明。</p></div>',
                                btns : [
                                    {
                                        type : 'blue',
                                        text : '立即填写'

                                    },
                                    {
                                        type : 'gray',
                                        text : '取消',
                                        click:function(){
                                            $('.pb_container').hide();
                                        }
                                    }
                                ],
                                onOk: function(){
                                    $('.pb_container').hide();
                                    window.open('/mod/mprotocol/create?appid='+appid, '_blank');

                                },
                                onClose:function(){
                                    $('.pb_container').hide();
                                }
                            });

                        }
                        else if(data=='skip_test'){
                            $.popbox({
                                width : '400px',
                                content : '<div style="text-align:center;margin-top:20px;font-size:16px;">确认要跳过测试，以基础评级发布？<p style="text-align:center;margin-top:14px;font-size:14px;color:#666;">未进行测试将无法获得游戏测试数据，无法获得游戏评级。</p></div>',
                                btns : [
                                    {
                                        type : 'blue',
                                        text : '结束测试'

                                    },
                                    {
                                        type : 'gray',
                                        text : '取消',
                                        click:function(){
                                            $('.pb_container').hide();
                                        }
                                    }
                                ],
                                onOk: function(){
                                    $('.pb_container').hide();
                                    window.open('/mod2/createmobile/app?id='+appid+'&op=normal', '_blank');

                                },
                                onClose:function(){
                                    $('.pb_container').hide();
                                }
                            });
                        }
                        else if(data=='end_test'){
                            $.popbox({
                                width : '400px',
                                content : '<div style="text-align:center;margin-top:20px;font-size:16px;">确认要结束测试，以当前游戏评级上线？<p style="text-align:center;margin-top:14px;font-size:14px;color:#666;">正式上线前可不限次数测试，获得最满意的最佳评级。</p></div>',
                                btns : [
                                    {
                                        type : 'blue',
                                        text : '结束测试'

                                    },
                                    {
                                        type : 'gray',
                                        text : '取消',
                                        click:function(){
                                            $('.pb_container').hide();
                                        }
                                    }
                                ],
                                onOk: function(){
                                    $('.pb_container').hide();
                                    window.open('/mod2/createmobile/app?id='+appid+'&op=normal', '_blank');

                                },
                                onClose:function(){
                                    $('.pb_container').hide();
                                }
                            });
                        }

                    }
                });



            },

             '.btn-cancel' : function(){
                 var _this = $(this);
                 $.popbox({
                     'content' : '<p style="text-align:center;line-height:30px">确定撤销审核吗？</p><p style="text-align:center;line-height:30px">  撤销审核后还可以编辑再次提交。</p>',
                     'width' : 500,
                     btns : [
                         {
                             type : 'blue',
                             text : '确定',
                             click : function(){
                                 $.ajax({
                                     url : "/mod/mobile/replay?id="+appid,
                                     success : function(data){
                                        location.href="/mod2/mobileapp/index?appid="+appid;
                                     }
                                 })

                             }
                         },
                         {
                             type : 'gray',
                             text : '取消'

                         }

                     ]
                 });
            }


        });
    }


}