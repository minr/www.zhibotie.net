(function(a){a.fn.mailAutoComplete=function(p){var f={boxClass:"mailListBox",listClass:"mailListDefault",focusClass:"mailListFocus",markCalss:"mailListHlignt",zIndex:1,autoClass:true,mailArr:["vip.qq.com","qq.com","gmail.com","126.com","163.com","hotmail.com","yahoo.com","yahoo.com.cn","live.com","sohu.com","sina.com","vip.sina.com.cn"],textHint:false,hintText:"",focusColor:"#333",blurColor:"#999"};var d=a.extend({},f,p||{});if(d.autoClass&&a("#mailListAppendCss").size()===0){a('<style id="mailListAppendCss" type="text/css">.mailListBox{border:1px solid #369; background:#fff; font:12px/20px Arial;}.mailListDefault{padding:0 5px;cursor:pointer;white-space:nowrap;}.mailListFocus{padding:0 5px;cursor:pointer;white-space:nowrap;background:#369;color:white;}.mailListHlignt{color:red;}.mailListFocus .mailListHlignt{color:#fff;}</style>').appendTo(a("head"))}var e=d.boxClass,m=d.listClass,b=d.focusClass,l=d.markCalss;var k=d.zIndex,h=mailArr=d.mailArr,g=d.textHint,n=d.hintText,c=d.focusColor,j=d.blurColor;a.createHtml=function(t,q,s){var r="";if(a.isArray(q)){a.each(q,function(u,w){if(u===s){r+='<div class="mailHover '+b+'" id="mailList_'+u+'"><span class="'+l+'">'+t+"</span>@"+q[u]+"</div>"}else{r+='<div class="mailHover '+m+'" id="mailList_'+u+'"><span class="'+l+'">'+t+"</span>@"+q[u]+"</div>"}})}return r};var i=-1,o;a(this).each(function(){var y=a(this),t=a(".justForJs").size();if(t>0){return}var r=y.outerWidth(),u=y.outerHeight();y.wrap('<span style="display:inline-block;position:relative;"></span>').before('<div id="mailListBox_'+t+'" class="justForJs '+e+'" style="min-width:'+r+"px;_width:"+r+"px;position:absolute;left:-6000px;top:"+u+"px;z-index:"+k+';"></div>');var q=a("#mailListBox_"+t),s;y.focus(function(){a(this).css("color",c).parent().css("z-index",k);if(g&&n){var w=a.trim(a(this).val());if(w===n){a(this).val("")}}a(this).keyup(function(z){o=v=a.trim(a(this).val());if(/@/.test(v)){o=v.replace(/@.*/,"")}if(v.length>0){if(z.keyCode===38){if(i<=0){i=h.length}i--}else{if(z.keyCode===40){if(i>=h.length-1){i=-1}i++}else{if(z.keyCode===13){if(i>-1&&i<h.length){a(this).val(a("#mailList_"+i).text())}}else{if(/@/.test(v)){i=-1;var x=v.replace(/.*@/,"");h=a.map(mailArr,function(B){var A=new RegExp(x);if(A.test(B)){return B}})}else{h=mailArr}}}}q.html(a.createHtml(o,h,i)).css("left",0);if(z.keyCode===13){if(i>-1&&i<h.length){q.css("left","-6000px")}}}else{q.css("left","-6000px")}}).blur(function(){if(g&&n){var x=a.trim(a(this).val());if(x===""){a(this).val(n)}}a(this).css("color",j).unbind("keyup").parent().css("z-index",0);q.css("left","-6000px")});a(".mailHover").live("mouseover",function(){i=Number(a(this).attr("id").split("_")[1]);s=a("#mailList_"+i).text();q.children("."+b).removeClass(b).addClass(m);a(this).addClass(b).removeClass(m)})});q.bind("mousedown",function(){y.val(s)})})}})(jQuery);

$(document).ready(function(e) {
    $("#email").mailAutoComplete({
		boxClass: "out_box", //外部box样式
		listClass: "list_box", //默认的列表样式
		focusClass: "focus_box", //列表选样式中
		markCalss: "mark_box", //高亮样式
		autoClass: false,
		textHint: true, //提示文字自动隐藏
		hintText: "请输入邮箱地址"
	});
});