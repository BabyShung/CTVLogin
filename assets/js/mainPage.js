$(document).ready(function(){
	
	/* An object with element selectors and margin values */
	
	
	
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
			rightBtn.fadeIn();
			b.animate({marginLeft:val});
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
 
 
	// An additional click handler for the finish button:
	
	$('#step2 a.finish').click(function(){
		
		
	});
	
	

});
