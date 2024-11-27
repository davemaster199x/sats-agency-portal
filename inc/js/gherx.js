$(document).ready(function(){

    
        //success message 
        jQuery('#notie-alert-outer').delay('4000').animate({top:-99999},5000)



        //init datepicker
        $('.flatpickr').flatpickr({
            dateFormat: "d/m/Y"
        });

         //mobile and phone custom masking ang error validation for default fields (not event fields)
         phone_mobile_mask(); //init phone and mobile mas for default fields (not event fields)
         mobile_validation(); //init mobile validation
         phone_validation(); //init phone validation




	
		

});



function select2Photos (state) {
    if (!state.id) { return state.text; }
    var $state = $(
        '<span class="user-item"><img src="' + state.element.getAttribute('data-photo') + '"/>' + state.text + '</span>'
    );
    return $state;
}




