jQuery(function($){
    /*
    **adding the uploaded images in the order panel
    */
    var items_panel = $('#woocommerce-order-items')
    	post_id = $('#post_ID').val();

    if(items_panel.length != 0){
        items = items_panel.find('tr.item');

        $.each(items, function(i, el){
	            wrapper = $('<div/>', {class: 'wc-extension-data'}),
            	target = $(el).find('td.thumb'),
            	uploaded_img_src = $(el).find('.meta_items tr:first-child > td:nth-child(2) > input').val()
            	uploaded_img = $('<img />', {
            		src: uploaded_img_src,
            		width: 100,
            		height: 100,
            		title: 'Uploaded Photo',
            		alt: 'Uploaded Photo'
            	}),
            	dload_btn = $('<button/>', {
            		class: 'button download-uploaded-img',
            		text: 'Download'
            	}).on('click', function(e){
            		e.preventDefault();
            		get_uploaded_image($(el), uploaded_img_src);
            	});


            wrapper.append(uploaded_img)
            	.append(dload_btn)
            	.appendTo(target);
        });

		var get_uploaded_image = function(el, url){
			var data = {
                action  : 'cu_pr_get_uploaded_img',
                order   : post_id,
                item_id : el.data('order_item_id'),
                url		: url
			};

			$.post(ajaxurl, data, function(ret){
				window.location.href = ret.url;
				//console.log(ret.url);
			}, 'json');
		};
    }

    var btn_wrapper = $('<p/>', {
            class: 'buttons'
        }),
        btn = $('<button/>', {
            id:'notify_for_image_upload', 
            class: 'button button-primary',
            type: 'button',
            text: 'Send Email'
        }).on('click', function(e){
            e.preventDefault();
           
            if($(this).hasClass('inactive')){
                $('#acf-email_subject').slideDown();
                $('#acf-email_content').slideDown();
                $(this).removeClass('inactive').addClass('active');
                $(this).parent().prev().hide();
            }else{
                send_email();
                $('#acf-email_subject').slideUp();
                $('#acf-email_content').slideUp();
                $(this).parent().after('<p>Email Sent</p>');
            }
        });

    $('<div class="field"></div>').html(btn_wrapper.append(btn)).insertAfter('#acf-email_content.field_type-wysiwyg');

    var send_email = function(){
        var data = {
                action  : 'cu_pr_send_upload_image_email',
                order   : post_id,
                content : tinyMCE.get('wysiwyg-acf-field-email_content').getContent(),
                subject : $('#acf-field-email_subject').val()
            };


            $.post(ajaxurl, data, function(ret){
                if(ret.status == 'true'){

                }
            }, 'json');
    };

    //invoice html email
    $('input#cpm_send_invoice_email').on('click', function (e) {
        e.preventDefault();
        var data = {
                'action' : 'cpm_send_invoice_email',
                'order_id' : $('input#post_ID').val()
            },
            $this = $(this);

        PostForm.showSpinner($this.parent());

        jQuery.post(ajaxurl, data, function(ret){
           if(ret.status == true){
                $this.siblings('.spinner').delay(1300).remove();
                $this.parent().append(ret.msg);
            }else{
                $this.parent().append('Error!');
            }
        }, 'json');
    });
});