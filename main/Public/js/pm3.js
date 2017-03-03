$(function(){

	// 点击按钮同意后添加
	$(".admit").on("click",function(){
		$(this).parent().after("已添加").remove();
	});

	// 点击按钮下拉菜单同意并添加
	$(".accept").on("click",function(){
		$(this).parent().parent().after("已添加").remove();
	});

	// 点击按钮下拉菜单拒绝并删除
	$(".refuse").on("click",function(){
		$(this).parents(".personnel-audit").remove();
	});

	
})