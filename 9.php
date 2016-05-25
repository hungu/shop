<!DOCTYPE html><html><head><title>代码在线DIY、演示</title><meta charset="UTF-8"><script type="text/javascript">
window.onerror = function(msg, url, line){ alert("第 " + line + " 行脚本代码出错:\n" + msg); };
</script><script type="text/javascript" src="http://apps.bdimg.com/libs/jquery/1.11.1/jquery.min.js"></script>
<link rel="stylesheet" type="text/css" href="http://apps.bdimg.com/libs/jqueryui/1.10.4/css/jquery-ui.min.css" />
<script type="text/javascript" src="http://apps.bdimg.com/libs/jqueryui/1.10.4/jquery-ui.min.js"></script><style type="text/css">
#sayHi {
	color: blue;
}
</style></head><body><label for="language">请输入指定的语言：</label>

<input id="language" name="language" type="text">

<input id="lang_id" name="lang_id" type="hidden" >
<script type="text/javascript">
$("#language").autocomplete({
	// 静态的数据源
    source: [
		{ label: "你好(Chinese)", value: 1, sayHi: "你好" },
		{ label: "Hello(English)", value: 2, sayHi: "Hello" },
		{ label: "Hola(Spanish)", value: 3, sayHi: "Hola" },
		{ label: "Привет(Russian)", value: 4, sayHi: "Привет" },
		{ label: "Bonjour(French)", value: 5, sayHi: "Bonjour" },
		{ label: "こんにちは(Japanese)", value: 6, sayHi: "こんにちは" },	
	],
	select: function(event, ui){
		// 这里的this指向当前输入框的DOM元素
		// event参数是事件对象
		// ui对象只有一个item属性，对应数据源中被选中的对象
		
		$(this).value = ui.item.label;
		$("#lang_id").val( ui.item.value );
		$("#language").val( ui.item.sayHi );
		
		// 必须阻止默认行为，因为autocomplete默认会把ui.item.value设为输入框的value值
		event.preventDefault();		
	}
});
</script></body></html>