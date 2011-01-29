$(document).ready(function(){
	$(".scroll").click(function(event){
		event.preventDefault();
		var full_url = this.href;
		var parts = full_url.split("#");
		var trgt = parts[1];
		var target_offset = $("#"+trgt).offset();
		var target_top = target_offset.top;
		$('html, body').animate({scrollTop:target_top}, 500);
	});

	$(".bar").click(function(e){
		e.preventDefault();
		$(this).siblings(".body").slideToggle();
		$(this).parent().toggleClass("active");
		$(this).siblings(".up").fadeToggle();
		$(this).siblings(".delete").fadeToggle();
		$(this).siblings(".down").fadeOut();
	});
	$(".up").click(function(e){
		e.preventDefault();
		$(this).siblings(".body").slideToggle();
		$(this).parent().toggleClass("active");
		$(this).fadeToggle();
		$(this).siblings(".delete").fadeToggle();
	});
	$(".bar").hover(function(){
		if(!$(this).siblings(".body").is(":visible"))$(this).siblings(".down").show();
	}, function(){
		if(!$(this).siblings(".body").is(":visible"))$(this).siblings(".down").hide();
	});
});

