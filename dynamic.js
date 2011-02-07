jQuery.easing['jswing'] = jQuery.easing['swing'];
jQuery.extend( jQuery.easing,{ def: 'easeInOutSine',
	easeInOutQuad: function (x, t, b, c, d) { if ((t/=d/2) < 1) return c/2*t*t + b;	return -c/2 * ((--t)*(t-2) - 1) + b; },
	easeInOutSine: function (x, t, b, c, d) { return -c/2 * (Math.cos(Math.PI*t/d) - 1) + b; }});
function changeHash(hash){
	$("#"+hash).attr("id", hash+"_");
	window.location.hash = hash;
	$("#"+hash+"_").attr("id", hash);
}
$(document).ready(function(){
	$(".scroll").click(function(event){
		var full_url = this.href;
		var parts = full_url.split("#");
		var trgt = parts[1];
		var target_offset = $("#"+trgt).offset();
		var target_top = target_offset.top;
		$('html, body').animate({scrollTop:target_top}, 750, "easeInOutQuad");
	});

	$(".bar").click(function(e){
		e.preventDefault();
		$(this).siblings(".body").slideToggle();
		$(this).parent().toggleClass("active");
		$(this).siblings(".up").fadeToggle();
		$(this).siblings(".down").fadeOut();
		changeHash($(this).parent().attr("id"));
	});
	$("#showall").click(function(e){
		e.preventDefault();
		$(".body").slideToggle();
		$(".entry").toggleClass("active");
		$(".up").fadeToggle();
		$(".down").fadeOut();
	});
	$(".up").click(function(e){
		e.preventDefault();
		$(this).siblings(".body").slideToggle();
		$(this).parent().toggleClass("active");
		$(this).fadeToggle();
	});
	$(".bar").hover(function(){
		if(!$(this).siblings(".body").is(":visible"))$(this).siblings(".down").show();
	}, function(){
		if(!$(this).siblings(".body").is(":visible"))$(this).siblings(".down").hide();
	});
	$("#passwordHolder").focus(function(){
		$(this).remove();
		$("#password").show().focus();
	});
});

