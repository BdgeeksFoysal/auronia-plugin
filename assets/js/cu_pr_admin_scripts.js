jQuery(function($){ //make sure DOM is loaded and pass $ for use
    //if the user is editing a custom product - hide the title input
    if($('#post_type').val() === 'cu_pr'){
        //$('#title').attr('disabled','disabled');
        //$('#post-body-content').hide();
    }

    $('select[name="cu_pr_order_status"]').chosen();

    var item_select = $("select#cu_pr_item_id"),
        order_select= $("select#cu_pr_order_id"),
        item_uid    = $('input[name="cu_pr_item_uid"]'),
        qty_msg     = $('.cu-pr-qty-msg');

    order_select.chosen().change(function(){
        var data = {
            action : 'cu_pr_order_selected',
            order : $(this).val()
        };
        jQuery.post(ajaxurl, data, function(ret){
            if(ret.count > 1){
                item_select.siblings('.select-notify').html('The order has more than one product, chose one product from below.');
                item_select.html('<option>Select One product.</option>');
            }else{
                item_select.siblings('.select-notify').html('One Product Selected.');
                item_select.html('');
            }

            $.each(ret.items, function(i, obj){
                item_select.append('<option value="'+ i +'">'+ obj.name +'</option>');
            });

            item_select.parent('.item-select').slideDown(function(){
                item_select.chosen();
                item_select.trigger("liszt:updated");
                item_uid.trigger('new_item');
            });
        }, 'json');
    });

    item_select.chosen().change(function(){
        item_uid.trigger('new_item');
    });

    item_uid.on('new_item', function(){
        var $this = $(this),
            data = {
                action : 'cu_pr_item_selected',
                item   : item_select.val(),
                order  : order_select.val()
            };
        if(parseInt(data.item) > 0){
            jQuery.post(ajaxurl, data, function(ret){
                qty_msg.html(ret.msg);
                $this.val(ret.item_uid);
            }, 'json');
        }else{
             qty_msg.html('');
             $this.val('');
        }
    });

    //notifying customer action
    $('#cu_pr_notify_customer').click(function(e){
        e.preventDefault();
        var $this = $(this),
            data = {
                action : 'cu_pr_notify_customer',
                post_id   : $('#post_ID').val(),
                order  : order_select.val()
            };

        PostForm.showSpinner($this.parent());
        
        jQuery.post(ajaxurl, data, function(ret){
           if(ret.status == 'true'){
                $this.siblings('.spinner').delay(1300).remove();
                $this.parent().append('<span class="msg">Email Sent!</span>');
            }
        }, 'json');
    });

    $('#submit_privacy_tpls').click(function(e){
        e.preventDefault();

        PostForm.init('cu_pr_tpl_update', 'form[name="privacy_tpl_form"]');
    });

    $('#submit_conditions_tpls').click(function(e){
    	e.preventDefault();

        PostForm.init('cu_pr_tpl_update', 'form[name="conditions_tpl_form"]');
    });

    $('#submit_customer_email_tpls').click(function(e){
        e.preventDefault();

        PostForm.init('cu_pr_tpl_update', 'form[name="customer_email_tpl_form"]');
    });

    $('#submit_admin_email_tpls').click(function(e){
        e.preventDefault();

        PostForm.init('cu_pr_tpl_update', 'form[name="admin_email_tpl_form"]');
    });


    var dl_photo_email_box = $('#acf_6279'),
        dl_photo_notify_button = $('<input >', { 
            id : "cu_pr_notify_customer", 
            class : "button button-primary button-large",
            value : "Notify Customer" ,
            type : "submit",
            style: 'margin-top: 10px; cursor: pointer'
        }),
        dl_photo_secret_code_button = $('<input >', { 
            id : "generate_secret_code", 
            class : "button button-primary button-large",
            value : "Generate Secret Code" ,
            type : "submit",
            style: 'margin-top: 10px; cursor: pointer'
        });

    if(dl_photo_email_box.length > 0){
        dl_photo_email_box
            .find('#acf-customer_email')
            .append(dl_photo_notify_button);

        dl_photo_notify_button.on('click', function (e) {
            e.preventDefault();
            var email = $(this).siblings('input[type="email"]').val();

            if(email.length > 0){
                var $this = $(this),
                    data = {
                        action : 'downloadable_photo_notify_customer',
                        email  : email
                    };

                PostForm.showSpinner($this.parent());
                
                jQuery.post(ajaxurl, data, function(ret){
                   if(ret.status == 'true'){
                        $this.siblings('.spinner').delay(1300).remove();
                        $this.parent().append('<span class="msg">Email Sent!</span>');
                    }
                }, 'json');
            }
        });

        /*
        dl_photo_email_box
            .find('#acf-secret_code')
            .append(dl_photo_secret_code_button);

        dl_photo_secret_code_button.on('click', function (e) {
            e.preventDefault();
            var code_field = $(this).siblings('input[type="text"]');

            if(code_field.val().length == 0){
                var $this = $(this),
                    data = {
                        action      : 'cpm_generate_secret_code',
                        code_for    : 'downloadable_photo'
                    };

                PostForm.showSpinner($this.parent());
                
                jQuery.post(ajaxurl, data, function(ret){
                    $this.siblings('.spinner').delay(1300).remove();

                    if(ret.status == 'true'){
                        code_field.removeClass('error required');
                        code_field.val(ret.codes[0]);
                    }else{
                        code_field.parent().addClass('error required');
                        code_field.after('<div>'+ ret.msg +'</div>');
                    }
                }, 'json');
            }
        });
        */
    }

    //adds export, quick generate option ad the top
    if(typenow == 'cpm_secret_code' && adminpage == 'edit-php'){
        var $code_type_selector = '<select name="code_for">';

        $.each(CPM_Secret_Code_Types, function(value, label){
            $code_type_selector += '<option value="'+ value +'">'+ label +'</option>';
        });

        $code_type_selector += '</select>';

        var $generate_button = $('<p class="search-box" style="margin-left:10px">'
            +'<input type="text" id="" placeholder="Number of codes" name="total">'
            +$code_type_selector
            +'<input type="submit" name="" id="quick_generate_cpm_secret_code" class="button" value="Generate Secret Code">'
            +'</p>');

        $('#posts-filter').prepend($generate_button);

        $generate_button.on('click', '#quick_generate_cpm_secret_code', function (e) {
            e.preventDefault();
            var total = $(this).siblings('input[name="total"]').val(),
                code_for = $(this).siblings('select[name="code_for"]').find('option:selected').val();
                
            if(total.length > 0 && code_for.length > 0){
                var $this = $(this),
                    data = {
                        action : 'cpm_generate_secret_code',
                        total  : total,
                        code_for : code_for
                    };

                PostForm.showSpinner($this.parent());
                
                jQuery.post(ajaxurl, data, function(ret){
                   if(ret.status == 'true'){
                        $this.siblings('.spinner').delay(1300).remove();

                        window.reload();
                    }else{
                        $this.parent().append(ret.msg);
                    }
                }, 'json');
            }
        });
    }
});

PostForm = {
    init: function(action, form){
        tinyMCE.triggerSave();
        var data = {
            action: action,
            form_data: jQuery(form).serialize()
        };

        PostForm.showSpinner(jQuery(form).find('p.submit'));

        jQuery.post(ajaxurl, data, function(ret) {
            if(ret.status == 'true'){
                jQuery(form).find('p.submit .spinner').delay(1300).remove();
                jQuery(form).find('p.submit').append('<span class="msg">Saved Successfully!</span>');
            }
        }, 'json');
    },
    showSpinner: function(container){
        container.append(
            '<span class="spinner" style="float:left;"></span>'
        );
        container.find('.msg').remove();
        container.find('.spinner').show();
    }
};