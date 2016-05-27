/**
 * Created by Administrator on 2016/3/18.
 */
    //删除弹窗
$(function() {
    $(".btn").click(function () {		
		$(".alert").slideDown();
		$(".theme-popover-mask").show();
    });
    $(".theme-popover-mask").click(function () {
        $(".alert").slideUp();
        $(".theme-popover-mask").hide();
    });
});
$(function(){
	$(".delete").click(function(){
		if($(".footing ul").find('input[id*=checkbox]').attr('checked')==true){
				$(".spxz").remove();
				$(".li5").hide();
				$(".li6").hide();
				$(".alert").slideUp();
				$(".theme-popover-mask").hide();
		}else{
			$(".spxz ul").each(function(){
				if($(this).find('input[class*=sub_box]').attr('checked')==true){
					$(this).remove();
					$(".alert").slideUp();
					$(".theme-popover-mask").hide();
				}
			})
		}
		setTotal(); 
	})
});
$(function(){
	$(".none").click(function(){
		  $(".alert").slideUp();
	        $(".theme-popover-mask").hide();
	})
});