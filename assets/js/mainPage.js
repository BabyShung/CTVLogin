$(document).ready(function(){

	var herizon = {
		'#step1 a.next'	    : '-100%'
	}
	
	var verizon = {
		'#step1 a.down'	    : '-100%'
	}
	
	var b = $('body'),
		bottomBtn = $('a.bottomBtn'),
		rightBtn = $('a.rightBtn');
	
	// Adding a click event listener to
	// every element in the object:
	
	$.each(herizon,function(key,val){
		$(key).click(function(){
			
			return false;
		});
	});
	
	$.each(verizon,function(key,val){
		$(key).click(function(){
			bottomBtn.fadeIn();
			b.animate({top:val});
			return false;
		});
	});
 
    bottomBtn.click(function(){
		bottomBtn.fadeOut();
		b.animate({top:'0%'});
	});
	
	rightBtn.click(function(){
		rightBtn.fadeOut();
		b.animate({marginLeft:0});
	});
 

	/**********************
	
		Caching the tabs into a variable
		
	***********************/

	var the_mods = $('#step1 a.next');
	the_mods.click(function(e){
		if($('#login-register').length){
			slideRight();
			return false;
		}
		
		var element = $(this);//the link
		
			$.get(element.attr('href'),function(msg){
						
				$('#step2').html(msg);
				element.data('cache',msg);// save the cache
		
				//after having the html, then add css file and load js file
				$('head').append('<link rel="stylesheet" href="assets/css/loginRegister_part.css" type="text/css" />');
				getScriptCcd('assets/js/loginRegister.js', slideRight );
				
			});
		
		
		e.preventDefault();
	})


});

function getScriptCcd(url, callback)
{
    jQuery.ajax({
            type: "GET",
            url: url,
            success: callback,
            dataType: "script",
            cache: false
    });
};

function slideRight() {
		$('a.rightBtn').fadeIn();
		$('body').animate({marginLeft:'-100%'});
		//$('body').animate({marginLeft:'-100%',top:'-100%'});
}