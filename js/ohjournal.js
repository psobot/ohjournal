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
		var target_offset = $($(this).attr("href")).offset();
		var target_top = target_offset.top;
		$('html, body').animate({scrollTop:target_top}, 750, "easeInOutQuad");
	});

	$(".slider").slider({
		max: 100,
		min: -100,
		stop: function(event, ui) {
			$.post(
				"rate",
				{
					rating: ui.value,
					date: $(this).parent().parent().attr('id').split("_")[1]
				},
				function(data) {
					if(data == "false"){
						alert("Something didn't work. Sorry.");	//fix later, tired
					}
				}
			);
		},
		slide: function(event, ui) {
			$(this).siblings("span.percent").html(ui.value);
		}
	});
	$(".slider").each(function(i,e){
		$(e).slider( "value", $(e).attr("value") );
	});

	$(".bar").click(function(e){
		e.preventDefault();
		$(this).siblings(".body").slideToggle();
		$(this).parent().toggleClass("active");
		changeHash($(this).parent().attr("id"));
	});
	$("#showall").click(function(e){
		e.preventDefault();
		$(".body").toggle();
		$(".entry").toggleClass("active");
	});
	$(".up").click(function(e){
		e.preventDefault();
		$(this).siblings(".body").slideToggle();
		$(this).parent().toggleClass("active");
		$(this).fadeToggle();
	});
	$("#passwordHolder").focus(function(){
		$(this).remove();
		$("#password").show().focus();
	});
	if(window.location.hash != ""){
		var target_offset = $(window.location.hash).offset();
		var target_top = target_offset.top;
		$('html, body').animate({scrollTop:target_top}, 750, "easeInOutQuad");
		$(window.location.hash + " .bar").click();
	}
});

