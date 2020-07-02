function H5GNSDK(opts) {
    var options = {
     
      /**
       * 用户登陆成功回调，参数“Object data”：
       */
    //  onLogin: opts['onLogin'] || function (data) {  alert("未实现登录回调的方法");},
  
 
      onPayComplete: opts['onPayComplete'] || function (data) { },
    };
  
    var isReady = false;
  
    var isEnterGame = false;
  
    var debug = function (title, msg) {
      if (!options.debug) return;
      alert(title);
      console.log(title);
      console.log(msg);
    };
  
    var postMessage = function (event, data) {
      debug('发送postMessage:[' + event + ']', {
        event: event,
        data: data
      });
  
      window.parent.postMessage({
        event: event,
        data: data
      }, '*');
    };
  
 
  
  /**
  *   orderinfo 订单数据
    *       product_name     商品名字
    *       product_id       商品id
    *       product_desc     商品描述
    *       exchange_rate    虚拟币兑换比例（例如100，表示1元购买100虚拟币） 默认为0。
    *       ext    CP自定义扩展字段 透传信息 默认为””
    *       currency_name    虚拟币名称（如金币、元宝）默认为””
    *       product_count    商品数量(除非游戏需要支持一次购买多份商品),默认为1
    *       product_price    价格(元),留两位小数
    *      cp_order_id    虚拟币兑换比例（例如100，表示1元购买100虚拟币） 默认为0。
  *   app_id  游戏id
  *   timestamp  客户端时间戳
  *   roleinfo  角色数据
    *   party_name  工会、帮派名称，如果没有，请填空字符串””
    *   role_vip  玩家vip等级，如果没有，请填0。
    *   role_balence  玩家游戏中游戏币余额，留两位小数;如果没有账户余额，请填0。
    *   rolelevel_ctime  玩家创建角色的时间 时间戳(11位的整数，单位秒)，默认0
    *   rolelevel_mtime  玩家角色等级变化时间 时间戳(11位的整数，单位秒)，默认0
    *   server_name  游戏服务器名称
    *   role_name  玩家角色名称
    *   server_id  游戏服务器id
    *   role_id  玩家角色id
    *   role_level  玩家角色等级，如果没有，请填0。
  *   sign  接口 sign
  */
    this.pay = function(payData) {
      if (!isReady) return;
  
      fee = parseFloat(payData.orderinfo.product_price);
      if (isNaN(fee) || fee <= 0) {
        alert("支付金额异常：" + fee);
      }
 
      postMessage('fastPay', payData);
    };
 
  
 

    this.logout =function(){
        postMessage('logout', {});
    };
  
    this.ready = function () {
      if (!isReady) {
        isReady = true;
        postMessage('ready', {});
      }
    };
 
    /**
     * 接收来着sdk的postMessage
     * e = {event: "<event>", data: <data>}
     */
    var receive = function (e) {
      var data = e.data;
  
      debug('接收到postMessage', e);
  
      switch (data.event) {
        case 'onPayComplete':
          options.onPayComplete(data.data);
          break;
        case 'onLogin':
          options.onLogin(data.data);
          break;
        default:
          debug('unknowEvent', {
            event: data.event,
            data: data.data
          });
      }
    };

    window.addEventListener('message', receive, false);
  }