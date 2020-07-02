/**
 * Created by zhangwenjia on 15-8-19.
 */
var mapp = mapp || {};
mapp.createmobile = {
	initCreatePage: function(type) {
		var _this = this;

		var stepPoints = [{
				text: '创建游戏',
				lineText: ''
			}, {
				text: '签署合同',
				lineText: ''
			},

			{
				text: '新游测试',
				lineText: ''
			}, {
				text: '上线运营',
				lineText: ''
			}
		];
		var currentStep = 1;

		if (type && type == 'ceshi') {
			stepPoints = [{
					text: '创建游戏',
					lineText: ''
				}, {
					text: '签署合同',
					lineText: ''
				},

				{
					text: '新游测试',
					lineText: ''
				}, {
					text: '上线运营',
					lineText: ''
				}
			];
			currentStep = 3;
		}

		$('.stepnav').stepnav({
			points: stepPoints,
			currentStep: currentStep
		});

		$('#begintime').datepicker({
			minDate: 1,
			onSelect: function(date) {
				$(this).blur();
				$('#p_oltime').val(date)
			}
		});
		//初始化分类
		var cate = 2; //软件的类型，2为游戏
		Util.getTag('/createmobile/tagapi', cate);

		//初始化资费类型
		this.initMenu('/createmobile/getFreeDic', cate);


		this.fillData();

		//初始化表单验证
		validObj = $('.validform').Validform({
			btnSubmit: "#submitform",
			tiptype: 3,
			tipmsg: {
				r: "&nbsp;"
			},
			showAllError: true,
			datatype: {
				time: function() {
					var bt = $('#begintime').val();
					if (bt == '') {
						return '请设置开始时间';
					}
				},
				uploadshot: function(gets, obj) {
					if (parseInt(obj.val()) >= 4) {
						var shot = [];
						var tmpW = 0,
							tmpH = 0;
						for (var i = 1; i <= 5; i++) {
							tmpW = parseInt($('input[name=app_shot' + i + '_w]').val()) || 0;
							tmpH = parseInt($('input[name=app_shot' + i + '_h]').val()) || 0;
							if (tmpH && tmpW) {
								break;
							}
						}

						if (!tmpH || !tmpW) {
							return '请重新上传截图';
						}

						for (var i = 1; i <= 5; i++) {
							shot[i] = {};
							shot[i].w = parseInt($('input[name=app_shot' + i + '_w]').val()) || 0;
							shot[i].h = parseInt($('input[name=app_shot' + i + '_h]').val()) || 0;
							if (shot[i].w != 0 && shot[i].h != 0) {
								if (shot[i].w != tmpW || shot[i].h != tmpH) {
									return '截图尺寸要统一';
								}
							}
						}

						return true;
					} else {
						return '请上传4-5张截图';
					}
				},
				cate: function(gets, obj) {
					return obj.val() != 0;
				},
				tel: function(a, b) {
					return (/^((0(([1,2]\d)|([3-9]\d{2})))|400)-?\d{7,8}$/.test(a));
				},
				num100: function(gets, obj) {
					return /^[0-9]\d*$/.test(gets) && gets >= 0 && gets <= 100;
				},
				//带运营字段的名称
				appname: function(gets, obj) {
					var tip = obj.nextAll('.Validform_checktip');
					tip.removeClass('Validform_right Validform_wrong');
					if (gets) {
						var ext = $('input[name="name_ext"]').val();
						if (ext == '' || _this.getByteStrLen(ext) <= 10) {
							if (_this.getByteStrLen(gets) > 20) {
								tip.addClass('Validform_wrong').html('应用名称长度不超过20个字符');
								return false;
							} else {
								tip.addClass('Validform_right').html('');
								return true;
							}
						} else {
							tip.addClass('Validform_wrong').html('运营字段不超过10个字符');
							return '运营字段不超过10个字符';
						}
					} else {
						tip.addClass('Validform_wrong').html('应用名称不能为空');
						return '应用名称不能为空';
					}
				},
				//运营字段
				appnameExt: function(gets, obj) {
					var appname = $('#name').val();

					if (_this.getByteStrLen(appname) + _this.getByteStrLen(gets) > 30) {
						return false;
					}

					return true;

					/*var tip = obj.nextAll('.Validform_checktip');
					 tip.removeClass('Validform_right Validform_wrong');
					 if(appname==''){
					 tip.addClass('Validform_wrong').html('应用名称不能为空');
					 return '应用名称不能为空';
					 }
					 else if(_this.getByteStrLen(appname)>20){
					 tip.addClass('Validform_wrong').html('应用名称长度不超过20个字符');
					 return '应用名称长度不超过20个字符';
					 }
					 else{
					 if(gets==''){
					 tip.addClass('Validform_right').html('');
					 return true;
					 }
					 else{
					 if(_this.getByteStrLen(gets)>10){
					 tip.addClass('Validform_wrong').html('运营字段不超过10个字符');
					 return '运营字段不超过10个字符';
					 }
					 else{
					 tip.addClass('Validform_right').html('');
					 return true;
					 }
					 }
					 }*/
				}
			/*	yunying: function(gets, obj) {
					if (gets == '') {
						return true
					}
					var rightOpreateLength = _this.countOperate();
					if (rightOpreateLength) {
						return '运营字段不能超过' + rightOpreateLength + '个字符';
					}
					var r1 = /^[\u4E00-\u9FA5\uf900-\ufa2d\a-\z\A-\Z0-9]{0,30}$/;
					if (!r1.test(gets)) {
						return '不含标点';
					}
					var value = $('#now-name').val();

					if (gets == value) {
						return '不能与应用名称重复';
					}
				}*/
			},
			beforeSubmit: function() {
				//检测控量数据
				var onlineVer = parseInt("<!--{$app['onlineVersionCode']}-->");
				var version_code = parseInt($('input[name=version_code]').val());
				if ($('input[name=is_contr_vol]:checked').val() == 'y') {
					if ("<!--{$app['onlineVersionCode']}-->" == "-1") {
						util.popError('第一次上线不能申请控量');
						return false;
					}

					if (onlineVer > version_code) {
						util.popError("当前包版本(" + version_code + ")小于线上版本(" + onlineVer + ")");
						return false;
					}
					if (onlineVer == version_code && "<!--{$app['onlineIsContrVol']}-->" != 'y') {
						util.popError("当前包版本(" + version_code + ")已上线，不能申请控量");
						return false;
					}
					var startDate = $('#start_time').val();
					var endDate = $('#end_time').val();
					if (startDate > endDate) {
						util.popError('控量开始时间不可大于结束时间');
						return false;
					}
					var contr_level = $('input[name=contr_level]').val();
					contr_level = parseInt(contr_level);
					if (contr_level < 0 || contr_level > 100) {
						util.popError('控量等级有误');
						return false;
					}
					if (onlineVer == version_code && contr_level < "<!--{$app['contr_level']}-->") {
						util.popError('控量等级只能增加，不能减少');
						return false
					}
				}

				//图片检测结果
				if ($('.Validform_warming').length > 0) {
					var continueSubmit = true;
					$.popbox({
						content: '检测到当前应用图标包含白边，很可能会导致上线审核不通过，请再次确认是否继续提交。',
						btns: [{
								type: 'blue',
								text: '继续提交',
								click: function() {
									//提交表单
									validObj.unignore('select[name="tag1"], select[name="tag2"]');
									var pass = validObj.check(false, 'select[name="tag1"], select[name="tag2"]');
									if (pass) {
										_this.submit(type);
									}
								}
							}, {
								type: 'gray',
								text: '取消提交',
								click: function() {
									continueSubmit = false;
								}
							}

						]
					});
					setTimeout(function() {
						$('.Validform_warming').html('检测到图标包含白边，可能无法通过上线审核，请参考示例进行优化。')
					}, 10);
					return false;
				} else {
					//提交表单
					validObj.unignore('select[name="tag1"], select[name="tag2"]');
					var pass = validObj.check(false, 'select[name="tag1"], select[name="tag2"]');
					if (pass) {
						_this.submit();
					}
				}



				return false;
			}
		});
		validObj.tipmsg.r = "&nbsp;";
		validObj.ignore('select[name="tag1"], select[name="tag2"]');
		if ($('#is_contr_vol_n').is(':checked')) {
			validObj.ignore('#contr_level');
		} else if ($('#is_contr_vol_y').is(':checked')) {
			$('#contr_time').show();
			mapp.createmobile.initDatePicker();
			validObj.unignore('#contr_level');
		}

		//初始化输入统计
		this.initLimitInput();

		this.disableSelect();

        //隐私初始化
        if(sensitive_permission!=''){
            var lihtmlup='';
            var permissionvalup='';
            for(var i in sensitive_permission){
                lihtmlup+='<li><i>'+ sensitive_permission[i]+'</i></li>';
                permissionvalup+=i+',';
            };
            permissionvalup = permissionvalup.substring(0,permissionvalup.length-1);
            $('input[name="sensitive_permission"]').val(permissionvalup);
            $('.sensitive').html(lihtmlup);
        }
	},
	countOperate: function() {
		var $maxLength = 30,
			connectorLength = this.getByteStrLen($('.connector').val()),
			nameLength = this.getByteStrLen($('#name').val()),
			rightOpreateLength = $maxLength - nameLength - connectorLength,
			nowOprateLength = this.getByteStrLen($('#name_ext').val());
		if (nowOprateLength > rightOpreateLength) {
			return rightOpreateLength;
		}
	},
	appNameTips: function(label) {
		$('#now-name').val(label);
		var nameLength = this.getByteStrLen(label);
		var wrongDom = $('.yunying .Validform_checktip');
		var appendField = $('.append-field');
		//应用名称不能超过30字符
		if (nameLength > 30) {
			wrongDom.addClass('Validform_wrong').text('应用名称不能大于30个字符');
		}
		//应用名称>28已经不能再填运营字段和连字符了。
		if (nameLength > 28) {
			appendField.addClass('field-hide')
		} else {
			appendField.addClass('field-show');
		}
		//如果应用名称改变对运营字段的长度重新判断
		var rightOpreateLength = this.countOperate();
		if (rightOpreateLength) {
			wrongDom.addClass('Validform_wrong').text('运营字段不能大于' + rightOpreateLength + '个字符');
		}
	},
	//获取字节数
	getByteStrLen: function(str) {
		str = str || "";
		return str.replace(/([^\x00-\xff])/g, "$1 ").length;
	},
	//实时统计字数
	initLimitInput: function() {
		$('#brief').limitTextarea({
			maxNumber: 1500
		});
		$('#apk_desc').limitTextarea({
			maxNumber: 400
		});
		$('#desc_desc').limitTextarea({
			maxNumber: 400,
			isByte: true
		});
        $('#sensitive_permission_exp').limitTextarea({
            maxNumber: 400,
            isByte: true
        });
	},
	//页面更新时填充数据
	fillData: function() {

		//显示应用名称
		var appname = $('#name');
		if (appname.val()) {
			appname.prop('readonly', true).addClass('disabledbg');
		}

		//显示apk
		var appinfopanel = $('.uploadpanel .appinfopanel');
		if (isUploadApk) {
			$('#uploadedApkInfo').val('apkinfo');
			appinfopanel.show();
		}

		//显示版权声明
		var cpimg = $('#uploadcopyrightpanel .uploadedcp');
		var cpimgsrc = cpimg.attr('src');
		if (cpimgsrc) {
			if (cpimgsrc.indexOf('zip') >= 0 || cpimgsrc.indexOf('rar') >= 0) {
				cpimg.attr('src', '/resource/img/yasuo.png');
			}
			$('#uploadedCopyrightInfo').val('copyrightinfo');
			$('#uploadcopyrightpanel .imgcontainer').show();
		}

		//显示logo
		var logocontainer = $('#uploadlogopanel');
		if (logocontainer.find('.uploaded').attr('src').indexOf('defaultimg') == -1) {
			$('#uploadedLogoInfo').val('logoinfo');
			logocontainer.find('.delpicbtn').show();
			logocontainer[0].getElementsByTagName('object')[0].style.display = 'none'; //操作object不能用jQuery，否则在IE9下会报错
		}

		//显示截图
		$('#uploadshotspanel .uploaded').each(function(index, element) {
			var src = $(element).attr('src') || '';
			if (src.indexOf('defaultimg') == -1) {
				var num = parseInt($('#uploadedShotsInfo').val()) || 0;
				$('#uploadedShotsInfo').val(num + 1);
				var shotcontainer = $(element).closest('.uploadshot');
				shotcontainer.find('.delpicbtn').show();
				shotcontainer[0].getElementsByTagName('object')[0].style.display = 'none';
				//如果没有获取到宽高，则用js获取
				var width = shotcontainer.find('.w');
				var height = shotcontainer.find('.h');
				if (width.val() == '' || height.val() == '') {
					util.getImgSize(src, function(img) {
						width.val(img.w);
						height.val(img.h);
					});
				}
			}
		});
	},
	initMenu: function(url, level1_val) {
		type = "danji";
		$.ajax({
			url: url,
			data: {
				level1: level1_val,
				_appType: type || ''
			},
			async: false,
			success: function(data, textstatus) {
				var freeDic = eval("(" + data.split('=')[1] + ")");
				var options = "<option value=''>请选择</option>";
				for (val in freeDic) {
					selected = val == 2 ? 'selected' : '';
					var option = '<option ' + selected + ' value=' + val + '>' + freeDic[val] + '</option>';
					options += option;
				}
				$('#for_free').empty().append(options);
			}
		});
	},
	initDatePicker: function() {
		mapp.util.loadScript(['/resource/module/daterangepicker/daterangepicker.js'], function() {
			new mapp.pickerDateRange('start_end_time', {
				calendars: 2,
				isSingleDay: !1,
				stopToday: !1,
				isTodayValid: !0,
				singleCompare: !1,
				shortOpr: !0,
				isToday: "dateRangeToday",
				showTime: !0,
				success: function(config) {
					if (config.startDate) {
						$('#start_end_time').val(config.startDate + ' ~ ' + config.endDate);
					}
				}
			});

		});
	},
	initListeners: function() {
		var _this = this;
		$(document.body).delegates({
            /*限制词*/
            '.restrict_onewords' : {
                'blur' : function(){
                    Util.sensitiveOneWords("restrict_onewords");
                }
            },
            '.restrict_brief' : {
                'blur' : function(){
                    Util.sensitiveOneWords("restrict_brief");
                }
            },
            '.restrict_desc' : {
                'blur' : function(){
                    Util.sensitiveOneWords("restrict_desc");
                }
            },
			'.delpicbtn': function() {
				var _this = $(this);
				$.popbox({
					width: '400px',
					content: '<div style="text-align:center;margin-top:20px;font-size:16px;">确定删除该图片吗？</div>',
					onOk: function() {
						var container = _this.closest('.uploadimg');
						container.removeClass('datafilled').addClass('unfilled');
						container.find('.uploaded').attr('src', '/resource/img/createmobile/defaultimg.png');
						container[0].getElementsByTagName('object')[0].style.display = ''; //jQuery操作object在IE下会报错，此处必须用原生的
						_this.hide();
						if (container.hasClass('uploadlogo')) {
							delete uploadedLogoInfo;
							$('#uploadedLogoInfo').val('');
							$('#uploadlogopanel .Validform_checktip').removeClass('Validform_right Validform_warming Validform_wrong').html('');
						} else {
							var num = parseInt($('#uploadedShotsInfo').val());
							$('#uploadedShotsInfo').val(num - 1);
							_this.closest('.uploadimg').find('input[type="hidden"]').val('');
						}
					}
				});
			},
			'#settimepub': {
				'change': function() {
					if ($(this).is(':checked')) {
						$('.pubtimepanel').show();
					}
				}
			},
			'#checkpub': {
				'change': function() {
					if ($(this).is(':checked')) {
						$('.pubtimepanel').hide();
					}
				}
			},
			'input[name="is_contr_vol"]': {
				'change': function() {
					if ($(this).val() == 'y') {
						$('#contr_time').show();

						mapp.createmobile.initDatePicker();
						validObj.unignore('#contr_level');
					} else {
						$('#contr_time').hide();
						validObj.ignore('#contr_level');
					}
				}
			},
			//触发验证分类1
			'select[name="tag1"]': {
				'change': function() {
					if (initCateDataChange) return;
					if ($(this).next('select').length == 0) {
						validObj.unignore('select[name="tag1"]');
						var pass = validObj.check(false, 'select[name="tag1"]');
						validObj.ignore('select[name="tag1"]');
						if (pass) {
							$('.catetag').find('.Validform_checktip').removeClass('Validform_wrong').addClass('Validform_right').html('&nbsp;');
						} else {
							$('.catetag').find('.Validform_checktip').removeClass('Validform_right').addClass('Validform_wrong').html($(this).attr('errormsg'));
						}
					} else {
						validObj.check(false, 'select[name="tag2"]');
					}

					validObj.ignore('select[name="tag2"]');

				}
			},
			//触发直接验证
			'select[name="tag2"]': {
				'change': function() {
					validObj.unignore('select[name="tag2"]');
					validObj.check(false, 'select[name="tag2"]');
					validObj.ignore('select[name="tag2"]');
				}
			},
			//修改应用名称
			'#modifyname': function() {
				// $.popbox({
				// 	width : '500px',
				// 	title : '申请修改应用主名称',
				// 	contentSelector : '#modifynamecontent',
				// 	btns : [
				// 		{
				// 			type : 'blue',
				// 			text : '申请修改',
				// 			click : ''
				// 		},
				// 		{
				// 			type : 'gray',
				// 			text : '取消',
				// 			click : ''
				// 		}

				// 	],
				// 	onOk : function(){
				// 		window.open('/mod/mprotocol/manage?appid='+appid+'&pageItem=createmobile');
				// 	}
				// });
				$('#name').prop('readonly', false).removeClass('disabledbg');
			},
			'#submitform': {
				'mousedown': function() {
					validObj.unignore('select[name="tag1"], select[name="tag2"]');
					validObj.check(false, 'select[name="tag1"], select[name="tag2"]');
				}
			}
		});

		//已选择的连接符
		this.initConnector();
		$('.connector').change(function() {
			_this.appNameTips($('#name').val());
		});
	},
	//向序列化表单数据中追加数据
	appendData: function(str, obj) {
		for (i in obj) {
			str += '&' + i + '=' + obj[i];
		}
		return str;
	},
	//更新应用时，分类选择失效
	disableSelect: function() {
		if (appid != '' && canUpCate == 'n') {
			setTimeout(function() {
				$('select[name="tag1"], select[name="tag2"], #for_cate').prop('disabled', true).addClass('disabledbg');
				$('.catelimit').show();
			}, 200);
		}
	},
	//分类选择生效
	enableSelect: function() {
		if (appid != '' && canUpCate == 'n') {
			$('select[name="tag1"], select[name="tag2"], #for_cate').prop('disabled', false).removeClass('disabledbg');
		}
	},
	uploadSeo: function(data) {
		$.ajax({
			url: '/mod/createmobile/seo',
			type: 'get',
			success: function(jsonData) {
				//errno=0 在白名单显示运营字段； =-1 不显示
				if (jsonData.errno == 0) {
					$('#now-name').val(data);
					//白名单传包之后，展示运营字段
					$('.old-name').hide().next().show();

				} else {
					$('.new-name').hide();
					$('#name_ext').val('');
				}
			}
		}, true);
	},
	initConnector: function() {
		$('.connector').val(name_link);
	},
	submit: function() {

		this.enableSelect();
		var d = $('.validform').serialize();
		this.disableSelect();

		d = this.appendData(d, {
			id: appid,
			cate_level1_id: 2,
			//应用标签
			common_tag: Util.selectedDefaultTag.join(','),
			common_other: Util.selectedCustomTag.join(','),
			//应用定位
			feature_tag: Util.selectedDefaultFeature.join('|'),
			feature_other: Util.selectedCustomFeature.join('|')
		});


		util.ajax({
			url: '/mod2/createmobile/submit',
			type: 'POST',
			data: d,
			showLoading: true,
			showLoadingMask: true,
			success: function(data) {
				userUnload = false;
				location.href = data.url;
			}
		}, true);

	}
}