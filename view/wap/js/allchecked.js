<script type="text/javascript">
$("#checkbox").click(function(){ 
if($(this).attr("checked")){ 
$(".footing li input").attr("checked","checked"); 
}else{ 
$(".footing li input").attr("checked","") 
} 
}) 

$(".footing input").not("#checkbox").click(function(){ 
$(".footing input").not("#checkbox").each(function(){ 
if($(".footing input[type='checkbox']:checked").not("#checkbox").length==$(".footing li").not("#footing").length){ 
$("#checkbox").attr("checked","checked"); 
}else{ 
$("#checkbox").attr("checked",""); 
} 
}) 

})</script> 
