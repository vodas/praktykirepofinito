SiteContainer = {
    triggerFileInputClick: function() {
        $(".form-control.file-caption.kv-fileinput-caption").click(function(){
            $("#user-image").trigger("click");
        });
    },

    serializeObject: function(form) {
        var o = {};
        var a = $(form).serializeArray();
        $.each(a, function() {
            if (o[this.name] !== undefined) {
                if (!o[this.name].push) {
                    o[this.name] = [o[this.name]];
                }
                o[this.name].push(this.value || '');
            } else {
                o[this.name] = this.value || '';
            }
        });
        return o;
    },

    addToMenu: function(warehouse_id, event) {
        $.ajax({
            url: 'add-to-menu',
            type: 'POST',
            data: warehouse_id,
            success: function(data) {
                if(data == true) {
                } else {
                }
            },
            error: function(error) {
                alert(error);
            }
        });
    },
    getNameOfProductByIdAjax: function(id) {
        $.ajax({
            url: '/OrderHandler/give-name-of-product-by-id',
            type: 'POST',
            contentType: false,
            processData: false,
            cache: false,
            async: false,
            data: id,
            success: function (data) {
                //success
                if(data == true) {
                    alert('updated');
                } else {
                    console.log(data);
                    alert(data);
                }
            },
            error: function (error) {
                alert("An error" + error);
            }
        });
    },
    //getCookie from SC(shipping cart) cookie schema
    getCookie: function(cname) {
        var name = "[SC]" + cname + "=";
        var ca = document.cookie.split(';');
        for(var i=0; i<ca.length; i++) {
            var c = ca[i];
            while (c.charAt(0)==' ') c = c.substring(1);
            if (c.indexOf(name) == 0) return c.substring(name.length,c.length);
        }
        return "";
    },

    getNameFromCookieById: function(id) {
        var cookie = SiteContainer.getCookie(id);
        return (cookie == '') ? false : cookie.split(":")[0];
    },

    getAmountFromCookieById: function(id) {
        var cookie = SiteContainer.getCookie(id);
        return (cookie == '') ? false : cookie.split(":")[1];
    },
    getPriceFromCookieById: function(id) {
        var cookie = SiteContainer.getCookie(id);
        return (cookie == '') ? false : cookie.split(":")[2];
    },
    buttonToCoockie: function(elem) {
        var priceHtml = $("tr#"+ $(elem).attr("id") + " td.price").text();
        priceHtml = priceHtml.split(" ")[0];
        var item = $("tr#"+ $(elem).attr("id") + " td.name").text() + ":"+ 1 + ":"+ priceHtml;
        var amount = SiteContainer.getAmountFromCookieById($(elem).attr("id"));
        if(amount != false) {
            var name = SiteContainer.getNameFromCookieById($(elem).attr("id"));
            var price = SiteContainer.getPriceFromCookieById($(elem).attr("id"));
            item = name + ":"+ (parseInt(amount) + 1) + ":" + price;
        }
        document.cookie = "[SC]" + $(elem).attr("id") + " = " + item + '; path=/';

        var amountOfAll = 1;
        if(SiteContainer.getCookie("amountOfAll") != '') {
            amountOfAll = parseInt(SiteContainer.getCookie("amountOfAll"))+1;
        }
        document.cookie = "[SC]amountOfAll = " + amountOfAll + '; path=/';
    },
    initCookieToShippingCart: function() {
        var name = "[SC]=";
        var totalPrice = 0;
        var price = 0;
        var ca = document.cookie.split(';');
        for(var i=0; i<ca.length; i++) {
            if(ca[i].indexOf('[SC]') != -1) {
                var cookie = ca[i].split('[SC]')[1];
                var key = cookie.split('=')[0];
                //console.log(key);
                if(key != 'amountOfAll') {
                    price = Number(SiteContainer.getPriceFromCookieById(key)) * Number(SiteContainer.getAmountFromCookieById(key));
                    price = Math.round(price * 100) / 100;
                    totalPrice += Number(SiteContainer.getPriceFromCookieById(key)) * Number(SiteContainer.getAmountFromCookieById(key));
                    console.log(totalPrice);
                    $('ul.cd-cart-items').append('<li id = '+ key +'> <span class="cd-qty">'+ SiteContainer.getAmountFromCookieById(key) + 'x</span> '+ SiteContainer.getNameFromCookieById(key) +' <div class="cd-price">'+ price +'</div> <a href="#0" class="cd-item-remove cd-img-replace">Remove</a> </li>');
                }
            }
        }
        totalPrice = Math.round(totalPrice * 100) / 100;
        $(".totalPrice").replaceWith('<p class = "totalPrice">Total <span>'+ totalPrice +' zl</span></p>');
    },

    cookieToShippingCart: function(elem) {
        var divs = $("ul.cd-cart-items li");
        var id = $(elem).attr("id");
        var price = 0;
        var totalPrice = 0;

        if(SiteContainer.getCookie(id) != '') {
            for(var i = 0; i < divs.length; i++) {
                if($(divs[i]).attr('id') == id) {
                    price = Number(SiteContainer.getPriceFromCookieById(id));
                    price = Math.round(price * 100) / 100;
                    totalPrice = $(".totalPrice").text();
                    totalPrice = totalPrice.split(" ")[1];
                    totalPrice = Number(totalPrice) + Number(price);
                    //console.log($(divs[i]).text());
                    $(divs[i]).replaceWith('<li id = '+ id +'><span class="cd-qty">'+ SiteContainer.getAmountFromCookieById(id) + 'x</span> '+ SiteContainer.getNameFromCookieById(id) +' <div class="cd-price">' + price * SiteContainer.getAmountFromCookieById(id) + '</div> <a href="#0" class="cd-item-remove cd-img-replace">Remove</a></li>');
                    totalPrice = Math.round(totalPrice * 100) / 100;
                    $(".totalPrice").replaceWith('<p class = "totalPrice">Total <span>'+ totalPrice +' zl</span></p>');
                    return true;
                }
            }
        }

        if(elem != null) {
            totalPrice = $(".totalPrice").text();
            totalPrice = totalPrice.split(" ")[1];
            var alreadyToPay = $("tr#"+ $(elem).attr("id") + " td.price").text();
            alreadyToPay = alreadyToPay.split(" zl")[0];
            totalPrice = Number(totalPrice) + Number(alreadyToPay);
            totalPrice = Math.round(totalPrice * 100) / 100;
            $(".totalPrice").replaceWith('<p class = "totalPrice">Total <span>'+ totalPrice +' zl</span></p>');
            $('ul.cd-cart-items').append('<li id = '+ $(elem).attr("id") +'> <span class="cd-qty">1x</span> '+ $("tr#"+ $(elem).attr("id") + " td.name").text() +' <div class="cd-price">'+$("tr#"+ $(elem).attr("id") + " td.price").text()+'</div> <a id="'+ $(elem).attr("id") +'" class="cd-item-remove cd-img-replace">Remove</a> </li>');
            return true;
        }
        return false;
    },
    submitAjaxForm: function(form, event) {
        event.preventDefault();

        var serializedObject = SiteContainer.serializeObject(form);
        //console.log(serializedObject);

        var formData = new FormData($('form')[0]);
        formData.append('data', serializedObject);
        if(document.getElementById("user-image") != null) {
            formData.append('image', document.getElementById("user-image").files[0]);
        }

        $.ajax({
            url: $(form).attr('action'),
            type: 'POST',
            contentType: false,
            processData: false,
            cache: false,
            async: false,
            data: formData,
            success: function (data) {
                //success
                if(data == true) {
                    alert('updated');
                } else {
                    console.log(data);
                    alert(data);
                }
            },
            error: function (error) {
                alert("An error" + error);
            }
        });
    }
}

$(window).load(function() {
    SiteContainer.initCookieToShippingCart();
    SiteContainer.triggerFileInputClick();
    $( ".cd-img-replace").css('backgroundColor', "green");
    $('.btn-success').click(function(){
        SiteContainer.buttonToCoockie(this);
        SiteContainer.cookieToShippingCart(this);

        $( ".cd-img-replace").finish().animate({
            backgroundColor: "#00CC00"
        }, 300 , function(){
            $( ".cd-img-replace" ).animate({
                backgroundColor: "green"
            }, 300 );
        });
        //ajax add to menu
        SiteContainer.addToMenu($(this).attr('id'));
    });

    $('#user-zipcode').mask("00-000", {placeholder: "__-___"});
    $("input[name = 'RestaurantCRUD[zip_code]']").mask("00-000", {placeholder: "__-___"});
    $('#restaurant-zip_code').mask("00-000", {placeholder: "__-___"});
    $("input[name = 'UserCRUD[zipcode]']").mask("00-000", {placeholder: "__-___"});
});