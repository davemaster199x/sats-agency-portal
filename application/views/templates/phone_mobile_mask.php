
<script type="text/javascript">

                    // phone and mobile input mask
                    function phone_mobile_mask(){

                        <?php
                        if($this->session->country_id==1): //AU
                        ?>
                           // $('.phone-with-code-area-mask-input').mask('00 0000 0000', {placeholder: "__ ____ ____"});
                            $('.phone-with-code-area-mask-input').mask('00 0000 0000', {
                                placeholder: "Phone",
                            });

                           // $('.tenant_mobile').mask('0000 000 000', {placeholder: "____ ___ ___"});
                            $('.tenant_mobile').mask('0000 000 000', {
                                placeholder: "Mobile",
                            });

                        <?php
                        else: //NZ
                        ?>

                            //$('.phone-with-code-area-mask-input').mask('00 0000 000', {placeholder: "__ ____ ___"});
                            $('.phone-with-code-area-mask-input').mask('00 000 0000', {
                                placeholder: "Phone",
                            });

                            //$('.tenant_mobile').mask('0000 000 0000', {placeholder: "____ ___ ____"});
                            $('.tenant_mobile').mask('####00000000', {
                                placeholder: "Mobile",
                            });

                        <?php
                        endif;
                        ?>

                    }

                    function mobile_validation(){
                        $('.tenant_mobile').focusout(function(){
                            var phoneNumber = $(this).val().replace(/ /g, '');
                            <?php
                            if($this->session->country_id==1):
                            ?>
                                var requiredLength = 10;
                                if(phoneNumber.length < requiredLength && phoneNumber.length != 0){
                                    $(this).after('<div class="form-tooltip-error" data-error-list="">Format must be 0412 222 222</div>');
                                    $(this).parents('.form-group').addClass('error');
                                }else if(phoneNumber.length >= requiredLength){
                                    $(this).next('.form-tooltip-error').remove();
                                    $(this).parents('.form-group').removeClass('error');
                                }else if(phoneNumber.length == 0){
                                    $(this).next('.form-tooltip-error').remove();
                                    $(this).parents('.form-group').removeClass('error');
                                }
                            <?php
                            else:
                            ?>
                                var requiredLength = 8;
                                if(phoneNumber.length < requiredLength && phoneNumber.length != 0){
                                    $(this).after('<div class="form-tooltip-error" data-error-list="">Format must be between 8-12 digits</div>');
                                    $(this).parents('.form-group').addClass('error');
                                }else if(phoneNumber.length >= requiredLength && phoneNumber.length <= 12){
                                    $(this).next('.form-tooltip-error').remove();
                                    $(this).parents('.form-group').removeClass('error');
                                }else if(phoneNumber.length == 0){
                                    $(this).next('.form-tooltip-error').remove();
                                    $(this).parents('.form-group').removeClass('error');
                                }
                            <?php
                            endif;
                            ?>
                        });


                    }

                    function phone_validation(){
                        $('.phone-with-code-area-mask-input').focusout(function(){
                            var phoneNumber = $(this).val().replace(/ /g, '');
                            <?php
                            if($this->session->country_id==1):
                            ?>
                                var requiredLength = 10;
                                if(phoneNumber.length < requiredLength && phoneNumber.length !=0 ){
                                    $(this).after('<div class="form-tooltip-error" data-error-list="">Format must be 02 2222 2222</div>');
                                    $(this).parents('.form-group').addClass('error');
                                }else if(phoneNumber.length >= requiredLength){
                                    $(this).next('.form-tooltip-error').remove();
                                    $(this).parents('.form-group').removeClass('error');
                                }else if(phoneNumber.length == 0){
                                    $(this).next('.form-tooltip-error').remove();
                                    $(this).parents('.form-group').removeClass('error');
                                }

                            <?php
                            else:
                            ?>
                                var requiredLength = 9;
                                if(phoneNumber.length < requiredLength && phoneNumber.length !=0 ){
                                    $(this).after('<div class="form-tooltip-error" data-error-list="">Format must be 02 222 2222</div>');
                                    $(this).parents('.form-group').addClass('error');
                                }else if(phoneNumber.length >= requiredLength){
                                    $(this).next('.form-tooltip-error').remove();
                                    $(this).parents('.form-group').removeClass('error');
                                }else if(phoneNumber.length == 0){
                                    $(this).next('.form-tooltip-error').remove();
                                    $(this).parents('.form-group').removeClass('error');
                                }

                            <?php
                            endif;
                            ?>
                        });
                    }



</script>