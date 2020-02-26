jQuery(document).ready(function($){


   /* $('#price').select2({
        minimumResultsForSearch: -1

    });*/

    $("#pa_price").prepend("<option disabled></option>").val('');
    $('#pa_price').select2({
        minimumResultsForSearch: -1,
        placeholder: "Select an amount",
    });
    $('#pa_price, input.qty').change(function(){
        var val = $('#pa_price').val();
        var qty = $('input.qty').val();
        $('p.price').text('£'+val*qty+'.00');
    });
    $('#delivery-options').select2({
        minimumResultsForSearch: -1
    });
    $('.designs-slide input.radio-designs').change(function () {

        if ($(this).prop('checked')) {
            console.log( $('#price'+$(this).attr('data-id')));
            $('#price').html($('#price'+$(this).attr('data-id')).html()).removeClass('disable-select');
            $('#price').select2({
                minimumResultsForSearch: -1

            });
            $('.designs-slide input.radio-designs').parent('label').removeClass('checked')
            $(this).parent('label').addClass('checked');
        }
    }).change();
    $('#price, #quantity').change(function(){
        $q = $('#quantity').val();
        $p = $('#price').val();
        $('p.total').text('£'+$p*$q+'.00');
    });
    var $pid,
        $qty = $('#quantity').val(),
        $design,
        $price;

    $('.designs-slide input.radio-designs, #price, #quantity').change(function(){
        $pid = $('a#home-buy').attr('value');
        $qty = $('#quantity').val();
        $design = $('.designs-slide label.checked input').val();
        $price = $('#price').val();
       var $site_url = $('a#home-buy').attr('href');
        $('a#home-buy').attr('href', "/basket/?add-to-cart="+$pid+"&quantity="+$qty+"&attribute_pa_design="+$design+"&attribute_pa_price="+$price);

    });

    $('a#home-buy').on('click', function (e) {
     e.preventDefault();
     var check = true;
        if($price === '' || $price === undefined || $price === null) {
            e.preventDefault();
            $('.note').append('<div>Please select a price</div>');
            tweenCall();
            check = false;
        }

        if($design === '' || $design === undefined || $design === null) {
            e.preventDefault();
            $('.note').append('<div>Please select a Design</div>');
            tweenCall();

            check = false;
        }

        if(check) {
            let path = window.location.origin + $('a#home-buy').attr('href');
            $('a#home-buy').addClass('disable-btn');
            $('.note').append('<div class="three col">\n' +
                '                <div class="cutom-loader" id="loader-2">\n' +
                '                    <span></span>\n' +
                '                    <span></span>\n' +
                '                    <span></span>\n' +
                '                </div>\n' +
                '            </div>');
            TweenMax.to('.note', 1, {
                opacity: 1,
                yPercent: -50,
                ease: Power4.easeInOut
            });


            var xmlhttp = new XMLHttpRequest();
            xmlhttp.onreadystatechange=function() {
                if (xmlhttp.readyState==3) {
                    TweenMax.to('.note', 0.3, {
                        opacity: 0,
                        yPercent: 0,
                        ease: Power4.easeInOut
                    });
                }
                if (xmlhttp.readyState==4 && xmlhttp.status==200) {
                    var response = xmlhttp.responseText; //if you need to do something with the returned value

                    $('.note').empty().append('<div>'+$design+' Has been added to you basket</div>');
                    $('a#home-buy').removeClass('disable-btn').text('ADD MORE TO BASKET');
                    $('.view-basket').css('display', 'inline-block');
                    $('.empty-basket').hide();
                    $('.full-basket').show();
                    if($('.basket-qt').length){

                        $('.basket-qt').show().text(parseInt($('.basket-qt').text(), 0) + parseInt($qty, 0));
                    }else {
                        $('.basket-qt').show().text($qty);
                    }

                    $('.toggleNav').attr('href',window.location.href+'/basket').removeClass('toggleNav');


                    tweenCall();
                    return false;
                }else {

                }
            }

            xmlhttp.open("GET",path,true);
            xmlhttp.send();
        }




    });
    function tweenCall() {
        TweenMax.to('.note', 1, {
            opacity: 1,
            yPercent: -50,
            ease: Power4.easeInOut
        });
        setTimeout(function(){
            TweenMax.to('.note', 1, {
                opacity: 0,
                yPercent: 0,
                ease: Power4.easeInOut
            });

        }, 2000);
        setTimeout(function(){$('.note').empty();}, 2500);
    }



    $('.spinner').each(function() {
        var spinner = jQuery(this),
          input = spinner.find('input[type="number"]'),
          btnUp = spinner.find('img.spinner-helper.up'),
          btnDown = spinner.find('img.spinner-helper.down'),
          min = input.attr('min'),
          max = input.attr('max');

        btnUp.click(function() {
          var oldValue = parseFloat(input.val());
          if (oldValue >= max) {
            var newVal = oldValue;
          } else {
            var newVal = oldValue + 1;
          }
          input.val(newVal);
          input.trigger("change");
        });

        btnDown.click(function() {
          var oldValue = parseFloat(input.val());
          if (oldValue <= min) {
            var newVal = oldValue;
          } else {
            var newVal = oldValue - 1;
          }
          input.val(newVal);
          input.trigger("change");
        });
    });
    $('#page_number').change(function(){
        var val = $('#page_number').val();
        var url = $('#page_number').attr('url');
        window.location.href = url+val;
    });
    if($('body').hasClass('archive')) {

            $('ul.products').infiniteScroll({
                // options
                path: '.next-link',
                append: 'li.product',
                history: false,
                button: '.load-more',
                scrollThreshold: false,
            });

    }
      if(!$('.next-link').length){
          $('.load-more').hide();
      }
      $('.toggleNav').click(function(){
        $('.top-nav .cart-dropdown').toggle();
      });


      // postcode tbb 3


    $('#billing_country_field').setupPostcodeLookup({

        // Set your API key
        api_key: 'ak_joofemj95fDSar6ihJol1DGJTYgFp',
        // api_key: 'iddqd',
        // Pass in CSS selectors pointing to your input fields to pipe the results
        output_fields: {
            line_1: '#first_line',
            line_2: '#second_line',
            line_3: '#third_line',
            post_town: '#post_town',
            postcode: '#postcode'
        },
        onAddressSelected: function($arg) {

            $("#billing_address_1").val($arg.line_1);
            $("#billing_address_2").val($arg.line_2);
            $("#billing_postcode").val($arg.postcode);
            $("#billing_state").val($arg.county);
            $("#billing_city").val($arg.post_town);
        }

    });

    $('#shipping_country_field').setupPostcodeLookup({

        // Set your API key
        api_key: 'ak_joofemj95fDSar6ihJol1DGJTYgFp',
        // api_key: 'iddqd',
        // Pass in CSS selectors pointing to your input fields to pipe the results
        output_fields: {
            line_1: '#first_line',
            line_2: '#second_line',
            line_3: '#third_line',
            post_town: '#post_town',
            postcode: '#postcode'
        },
        onAddressSelected: function($arg) {

            $("#shipping_address_1").val($arg.line_1);
            $("#shipping_address_2").val($arg.line_2);
            $("#shipping_postcode").val($arg.postcode);
            $("#shipping_state").val($arg.county);
            $("#shipping_city").val($arg.post_town);
        }

    });

    var marginTop = $('.main').outerHeight() + $('.top-nav').outerHeight() + 20;
    $('#menu').css({
        'top': marginTop
    })


    $("#billing_country").on('change', function(){
        if($(this).val() !== 'GB'){
            $("#billing_country_field").children("input, button").hide();
            $('#billing_postcode_field').css({
                'visibility': 'visible',
                'opacity': '1',
                'height': 'auto'
            });
        }else{
            $("#billing_country_field").children("input, button").show();
        }


    });

    $("#shipping_country").on('change', function(){

        if($(this).val() !== 'GB'){
            $("#shipping_country_field").children("input, button").hide();
            $('#shipping_postcode_field').css({
                'visibility': 'visible',
                'opacity': '1',
                'height': 'auto'
            })
        }else{
            $("#shipping_country_field").children("input, button").show();
        }

    });

    var $gform = $('#gform_1');

    if( $gform.length > 0 ) {

      $gform.find('input').each(function(i,v){

        $(v).attr('autocomplete', 'no');

      });

    }



    $('.gift_card_form').on('submit', function (e) {
        e.preventDefault();

        check = false
        var filter = $(this);
 
        jQuery.ajax({
            //url: filter.attr('action'),
            url: "/wp-content/themes/ws/ajax-post-to-pdf.php/",
            type: filter.attr('method'),
            data: {
                //  action: 'productcatfilter',
                code: filter.data('code') ,
                product_id: filter.data('product_id'),
                image: filter.data('image'),
                product_name: filter.data('product_name'),
                price: filter.data('price')
            },
            beforeSend: function (xhr) {
                console.log('before');
            },
            success: function (output) {
                console.log('succ');
                console.log(output);
                if(output){
                    window.open(output, '_blank');
                    /*
                    var link = document.createElement("a");
                    link.download = name;
                    link.href = output;
                    document.body.appendChild(link);
                    link.click();
                    document.body.removeChild(link);
                    delete link; */
                    setTimeout(function(){
                        ajaxDelete(output)
                     }, 3000);

                }
                return;

            },
            error: function (jqxhr, status, exception) {
                console.log(status);
                console.log(jqxhr);
            }
        });

        function ajaxDelete(output) {
            jQuery.ajax({
                url: "/wp-content/themes/ws/ajax-post-delete-pdf.php",
                type: filter.attr('method'),
                data: {
                    filename: output ,
                },
                success: function (data) {
                    console.log('delete');
                    console.log(data);
                    return;
                },
                error: function (jqxhr, status, exception) {
                    console.log(status);
                }
            });
        }

    })




    /*
     * giftee checkout edit / save cookie
     */
    var cookie =[];
    if (getCookie("giftee") !== "") {
        cookie = JSON.parse(getCookie("giftee"))
        if(cookie.length > 1) {
            for (var i = 1; i < cookie.length; i++) {


            }
        }

        cookie.forEach( function ( cok) {
            Object.values(cok).map(function ( c) {


            })
        })
    }

    /*
    * back from basket
    */
    var variation = getParameterByName('variation');
    var variation_price = getParameterByName('attribute_pa_price');
    var quantity = getParameterByName('qty');
    if(variation_price) {

        $('#pa_price').val(variation_price ).trigger('change');
    }else{
        if($("#pa_price option[value=20]").length > 0){
            $('#pa_price').val(20).trigger('change');
        }
    }
    var qty_variation = 0;
    if(variation && variation !==''){
        if (cookie.length > 0) {
            cookie.map(function (c, i) {

                if( c[variation] && c[variation].length > 0 ){
                    $('.single_update_cart_button').show()
                    console.log(c[variation]);
                    c[variation].forEach(function ( _var, i) {
                        if(i == 0){
                            $('#mainGifteeWrapper .giftee-email').val(_var.email)
                            $('#mainGifteeWrapper .giftee-msg').val(_var.msg)
                        }else{

                            $('.repeater-wrapper').append('<div class="giftee-wrapper">' +
                                '<h4>eGift #<span class="giftee-numb">'+(i+1)+'</span></h4>' +
                                '<label for="">To</label><br/>'+
                                '<input type="email" name="giftee-email2" class="giftee-email" autocomplete="on" value="'+_var.email+'" />' +
                                ' <label for="">Message</label><br/>'+
                                '<textarea name="giftee-msg2" class="giftee-msg"  id="" cols="30" rows="10" >'+_var.msg+'</textarea>'+
                                '</div>');  
                        }
                        qty_variation = i+1;
                        console.log(_var);
                       
                    })

                }
            })
        }
    }
    if(quantity && quantity > 0){
        $('.qty').val(quantity)
        setTimeout(function () {
            $('.qty').trigger('change');
        }, 200)

    }



    $(document).on('click','.giftee-edit', function (e) {
        $(this).find('.g-edit').toggle()
        $(this).find('.g-save').toggle()
        $(this).siblings('.giftee-email').toggle().siblings('.giftee-inp-wrapper').toggle();
        $(this).siblings('').toggle();
    })


    let arr = [], em, id;
    $(document).on('click','.g-save', function (e) {
        $(this).parent('.giftee-edit').siblings('.giftee-inp-wrapper').each(function(){
            id = $(this).find('.giftee-inp-email').data('id')
            em = $(this).find('.giftee-inp-email').val()
            let msg = $(this).find('.giftee-inp-msg').val()
            arr.push({email: em, msg: msg});
            if(em != '') {
                if (cookie.length > 0) {
                    cookie.map(function (c, i) {

                        if( c[id] ){
                            console.log(i,1);
                            cookie.splice(i,1)
                        }
                    })
                }

            }
            let all = {
                [id]: arr
            }
            cookie.push(all);
            setCookie("giftee", JSON.stringify(cookie), 30);
            console.log(cookie);
            location.reload();

        })


    })


    $(document).on('click','.remove', function (e) {
        e.preventDefault();
        let id = $(this).data('var')
        if (cookie.length > 0) {
            cookie.map(function (c, i) {

                if( c[id] ){
                    console.log(i,1);
                    cookie.splice(i,1)
                }
            })
        }

        setCookie("giftee", JSON.stringify(cookie), 30);
        console.log(cookie);

    })

    /*
     * add to cart giftee
     */
    $('.qty').on('change', function () {

        if( $(this).val() < $('.giftee-wrapper').length ) {
            while ($(this).val() < $('.giftee-wrapper').length) {
                $('.giftee-wrapper').last().remove()
            }

        }else{
            for (var i = $('.giftee-wrapper').length; i < $(this).val(); i++) {
                $('.repeater-wrapper').append('<div class="giftee-wrapper">' +
                    '<h4>eGift #<span class="giftee-numb">'+(i+1)+'</span></h4>' +
                    '<label for="">To</label><br/>'+
                    '<input type="email" name="giftee-email2" class="giftee-email" autocomplete="on" placeholder="Recipient email" />' +
                    ' <label for="">Message</label><br/>'+
                    '<textarea name="giftee-msg2" class="giftee-msg"  id="" cols="30" rows="10"  placeholder="Recipient message"></textarea>'+
                    '</div>');

            }



        }
    })
    $(document).on('click','.single_add_to_cart_button', function (e) {
        add_update_cookie()
    })
    $(document).on('click','.single_update_cart_button', function (e) {
        e.preventDefault();
        add_update_cookie();
        var $pid =  $("#singleID").val(),
            $qty =  $('.qty').val(),
            $price = $('#pa_price').val();
        var basket = "/basket/?add-to-cart="+$pid+"&quantity="+$qty+"&attribute_pa_price="+$price;
        $.ajax({
            type: "POST",
            url: '/wp-admin/admin-ajax.php',
            data: {action : 'remove_item_from_cart','product_id' : $pid, 'quantity': $qty},
            beforeSend: function(){
                $('.single_update_cart_button').attr('disabled', true)
            },
            success: function (res) {
                console.log(res);
                $('.single_update_cart_button').attr('disabled', false)
                    window.location.href = $('.single_update_cart_button').data('url')

            }
        });

    })

    function add_update_cookie(){

        var cookie =[];
        if (getCookie("giftee") !== "") {
            cookie = JSON.parse(getCookie("giftee"))
            console.log(cookie);
        }

        let arr = [],  em;
        if(!$(this).hasClass('disabled')){

            $('.giftee-wrapper').each(function (i) {
                em = escape($(this).find('.giftee-email').val())
                //let msg = encodeURIComponent( $(this).find('.giftee-msg').val().replace(/["']/g, ""))
                let msg = $(this).find('.giftee-msg').val()
                  msg =  msg.replace(new RegExp('\n','g'), ' ')
                  msg = encodeURIComponent( msg.replace(/[`~@#$%^&*()_|+\-='",<>\{\}\[\]\\\/]/gi, ''))
                console.log(msg);
                if(em != '') {
                    arr.push({email: em, msg: msg});
                }
            })
        }
        let id = $('.variation_id').val()
        let check = true

        if (cookie.length > 0) {
            cookie.map(function (c, i) {
                if( c[id] ){
                    cookie.splice(i,1)
                }
            })
        }

        let all = {
            [id]: arr
        }
        cookie.push(all);
        setCookie("giftee", JSON.stringify(cookie), 30);
    }



    function getParameterByName(name, url) {
        if (!url) url = window.location.href;
        name = name.replace(/[\[\]]/g, '\\$&');
        var regex = new RegExp('[?&]' + name + '(=([^&#]*)|&|#|$)'),
            results = regex.exec(url);
        if (!results) return null;
        if (!results[2]) return '';
        return decodeURIComponent(results[2].replace(/\+/g, ' '));
    }


    /*
     * cookie
     */


    function setCookie(cname,cvalue,exdays) {
        var d = new Date();
        d.setTime(d.getTime() + (exdays*24*60*60*1000));
        var expires = "expires=" + d.toGMTString();
        document.cookie = cname + "=" + cvalue + ";" + expires + ";path=/";
    }

    function getCookie(cname) {
        var name = cname + "=";
        var decodedCookie = decodeURIComponent(document.cookie);
        var ca = decodedCookie.split(';');
        for(var i = 0; i < ca.length; i++) {
            var c = ca[i];
            while (c.charAt(0) == ' ') {
                c = c.substring(1);
            }
            if (c.indexOf(name) == 0) {
                return c.substring(name.length, c.length);
            }
        }
        return "";
    }

    //sameHeight('.blocks-3-slider li');
    function sameHeight($class) {
        var maxHeightName = 0;
        $($class).each(function(){
            var currheightName = $(this).height();

            if (currheightName > maxHeightName) {
                maxHeightName = currheightName;
            }
        });
        $($class).each(function(){
            $(this).css({'height' : maxHeightName});
        });

    }

});
