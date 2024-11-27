</div>
<!--.container-fluid-->
</div>
<!--.page-content-->
<!-- JS start -->
<script src="/inc/js/lib/jquery/jquery-3.2.1.min.js"></script>
<script src="/inc/js/lib/popper/popper.min.js"></script>
<script src="/inc/js/lib/tether/tether.min.js"></script>
<script src="/inc/js/lib/bootstrap/bootstrap.min.js"></script>
<script src="/inc/js/plugins.js"></script>
<script src="/inc/js/jc.js"></script>
<script src="/inc/js/gherx.js"></script>

<script type="text/javascript" src="/inc/js/lib/jqueryui/jquery-ui.min.js"></script>
<script type="text/javascript" src="/inc/js/lib/lobipanel/lobipanel.min.js"></script>
<script type="text/javascript" src="/inc/js/lib/match-height/jquery.matchHeight.min.js"></script>




<script src="/inc/js/lib/bootstrap-sweetalert/sweetalert.min.js"></script>

<script type="text/javascript" src="/inc/js/lib/moment/moment-with-locales.min.js"></script>
<script type="text/javascript" src="/inc/js/lib/flatpickr/flatpickr.min.js"></script>
<script src="/inc/js/lib/clockpicker/bootstrap-clockpicker.min.js"></script>
<script src="/inc/js/lib/clockpicker/bootstrap-clockpicker-init.js"></script>
<script src="/inc/js/lib/daterangepicker/daterangepicker.js"></script>

<script src="/inc/js/lib/jquery-tag-editor/jquery.caret.min.js"></script>
<script src="/inc/js/lib/jquery-tag-editor/jquery.tag-editor.min.js"></script>
<script type="text/javascript" src="/inc/js/lib/bootstrap-select/bootstrap-select.min.js"></script>
<script type="text/javascript" src="/inc/js/lib/select2/select2.full.min.js"></script>

<script src="/inc/js/lib/input-mask/jquery.mask.min.js"></script>

<script src="/inc/js/lib/prism/prism.js"></script>


<script src="/inc/js/app.js"></script>
<!-- JS end -->



<script type="text/javascript">

jQuery('document').ready(function(){

    //init datepicker
    $('.flatpickr').flatpickr();


    //select2
    $(".select2-photo").not('.manual').select2({
			templateSelection: select2Photos,
			templateResult: select2Photos
		});
    function select2Photos (state) {
		if (!state.id) { return state.text; }
		var $state = $(
			'<span class="user-item"><img src="' + state.element.getAttribute('data-photo') + '"/>' + state.text + '</span>'
		);
		return $state;
    }
    
    // date input mask
    $('.phone-with-code-area-mask-input').mask('(00) 0000-0000', {placeholder: "(__) ____-____"});


    //success message 
    jQuery('.p_success_msg').fadeOut(9000);

    //Short Term Rental tweak
    jQuery('#holiday_rental').change(function(){
        var thisVal = jQuery(this).val();
        if(thisVal == '0'){
            jQuery('.current_vacant').show();
        }else{
            jQuery('.current_vacant').hide();
            jQuery('.current_vacant').hide();
            jQuery('.new_tenancy').hide();
        }
    })

    //currently vacant
    jQuery('#prop_vacant').change(function(){
        var thisVal = jQuery(this).val();
        if(thisVal == '1'){
            jQuery('.vacant_from_to').show();
            jQuery('.new_tenancy').hide();
        }else{
            jQuery('.vacant_from_to').hide();
            jQuery('.new_tenancy').show();
        }
    })

    //new tenancy dropdown tweak
    jQuery('#is_new_tent').change(function(){
        var thisVal = jQuery(this).val();
        if(thisVal == '1'){
            jQuery('.new_tenant_start').show();
        }else{
            jQuery('.new_tenant_start').hide();
        }
    })

});


</script>


<script type="text/javascript">

    // google map autocomplete
    var placeSearch, autocomplete;

    // test
    var componentForm2 = {
        route: {
            'type': 'long_name',
            'field': 'address_2'
        },
        administrative_area_level_1: {
            'type': 'short_name',
            'field': 'state'
        },
        postal_code: {
            'type': 'short_name',
            'field': 'postcode'
        }
    };

    function initAutocomplete() {
        // Create the autocomplete object, restricting the search to geographical
        // location types.

        var options = {
            types: ['geocode'],
            componentRestrictions: {
                country: 'au'
            }
        };

        var input = document.getElementById('fullAdd');

        autocomplete = new google.maps.places.Autocomplete(input, options);

        // When the user selects an address from the dropdown, populate the address
        // fields in the form.
        autocomplete.addListener('place_changed', fillInAddress);

    }


    // [START region_fillform]
    function fillInAddress() {
        // Get the place details from the autocomplete object.
        var place = autocomplete.getPlace();

        // test
        for (var i = 0; i < place.address_components.length; i++) {
            var addressType = place.address_components[i].types[0];
            if (componentForm2[addressType]) {
                var val = place.address_components[i][componentForm2[addressType].type];
                document.getElementById(componentForm2[addressType].field).value = val;
            }
        }

        // street name
        var ac = jQuery("#fullAdd").val();
        var ac2 = ac.split(" ");
        var street_number = ac2[0];
        //	console.log(street_number);
        jQuery("#address_1").val(street_number);

        // suburb
        jQuery("#address_3").val(place.vicinity);

        // duplicate property check script here
       
    }
</script>

<script type="text/javascript">

    var swalErrorConfig = {
        title: "Error!",
        text: "Fields cannot be empty"
    }
    var swalSuccessConfig = {
        title: "Good job!",
        text: "Add function is still in progress",
        type: "success",
        confirmButtonClass: "btn-success",
        confirmButtonText: "Success"
    }


    var currentTab = 0; // Current tab is set to be the first tab (0)
    showTab(currentTab); // Display the crurrent tab

    function showTab(n) {
        // This function will display the specified tab of the form...
        var x = document.getElementsByClassName("ptabs");
        x[n].style.display = "block";
        //... and fix the Previous/Next buttons:
        if (n == 0) {
            // document.getElementById("prevBtn").style.display = "none";
            document.getElementById("prevBtn").removeAttribute('onclick');
        } else {
            // document.getElementById("prevBtn").style.display = "block";
            document.getElementById("prevBtn").setAttribute('onclick', 'nextPrev(-1)');
            //remove add property button
            document.getElementById("btnAddProperty").style.display = 'none';
           
        }
        if (n == (x.length - 1)) {
            //end of step code here
            document.getElementById("nextBtn").style.display = "none";
          //  document.getElementById("btn_add_property").setAttribute('style','block');

            //document.getElementById("nextBtn").innerHTML = "ADD PROPERTY→";
            document.getElementById("btnAddProperty").style.display = 'block';
            
        } else {
            document.getElementById("nextBtn").innerHTML = "NEXT→";

            // display next buttons
             document.getElementById("nextBtn").setAttribute('style','display:block');
             
        }
        //... and run a function that will display the correct step indicator:
        fixStepIndicator(n)
    }

    function nextPrev(n) {
        // This function will figure out which tab to display
        var x = document.getElementsByClassName("ptabs");
        // Exit the function if any field in the current tab is invalid:
       
       // if (n == 1 && !validateForm()) return false;
       
        // Hide the current tab:
        x[currentTab].style.display = "none";
        // Increase or decrease the current tab by 1:
        currentTab = currentTab + n;
        // if you have reached the end of the form...
        if (currentTab >= x.length) {
            // ... the form gets submitted:
            //form submit code here

            swal(swalSuccessConfig);
            return false;
        }
        // Otherwise, display the correct tab:
        showTab(currentTab);
    }

    function validateForm() {
        // This function deals with validation of the form fields
        var x, y, i, o, valid = true;
        x = document.getElementsByClassName("ptabs");
        y = x[currentTab].getElementsByTagName("input");
        o = x[currentTab].getElementsByTagName("select");
        // A loop that checks every input field in the current tab:
        for (i = 0; i < y.length; i++) {
            // If a field is empty...
            if (y[i].value == "") {
                // add an "invalid" class to the field:
                y[i].className += " invalid";
                swal(swalErrorConfig);
                // and set the current valid status to false
                valid = false;
            }
        }
        // If the valid status is true, mark the step as finished and valid:
        if (valid) {
            document.getElementsByClassName("step")[currentTab].className += " finish";
        }
        return valid; // return the valid status
    }

    function fixStepIndicator(n) {
        // This function removes the "active" class of all steps...
        var i, x = document.getElementsByClassName("step");
        for (i = 0; i < x.length; i++) {
            x[i].className = x[i].className.replace(" active", "");
        }
        //... and adds the "active" class on the current step:
        x[n].className += " active";
    }
</script>





<script src="https://maps.googleapis.com/maps/api/js?key=<?= config_item('gmap_api_key'); ?>&signed_in=true&libraries=places&callback=initAutocomplete" async defer></script>
</body>
</html>