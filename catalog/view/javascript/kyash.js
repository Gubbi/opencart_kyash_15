var pincodePlaceHolder = "Enter Pincode";
$(document).ready(function () {

    $("#kyash").parent().css({"vertical-align": "top", "padding-top": "9px"});

    $("input[name='payment_method']").on("click", function () {
        if ($(this).val() != "kyash") {
            $("#kyash_postcode_payment_sub").hide();
            $("#see_nearby_shops_container").hide();
            $("#kyash_open").show();
        }
    });

    $("#kyash_postcode").on("focus", function () {
        if ($(this).val() === pincodePlaceHolder) {
            $(this).val("");
        }
    });

    $("#kyash_postcode").on("blur", function () {
        if ($(this).val().length == 0) {
            $(this).val(pincodePlaceHolder);
        }
    });

});

function openShops(url, loader) {
    $("#kyash_postcode_payment_sub").show();
    $("#see_nearby_shops_container").hide();
    selectKyash();
    $("#kyash_open").hide();
    pullNearByShops(url, loader);
}

function selectKyash() {
    $("input[value='kyash']").prop("checked", true);
}

var old_postcode = "";
var errorMessage = "Due to some unexpected errors, this is not available at the moment. We are working on fixing it.";

function closeShops() {
    $("#see_nearby_shops_container").hide();
    $("#kyash_close").hide();
}

function pullNearByShops(url, loader) {
    loader = "<img src='catalog/view/theme/default/template/payment/kyash/image/loading.gif' alt='Processing...' />";
    closeShops();
    postcode = $("#kyash_postcode").val();
    if (postcode.length == 0 || postcode === pincodePlaceHolder) {
        alert("Enter your post code to retrieve the shops");
    }
    else {
        if (old_postcode === postcode) {
            $("#see_nearby_shops_container").show();
            $("#kyash_close").show();
        }
        else {
            $("#see_nearby_shops_container").show();
            $("#see_nearby_shops_container").html(loader);
            $.ajax({
                url: url + "&postcode=" + postcode,
                success: function (output, textStatus, xhr) {
                    if (xhr.status == 400 || xhr.status == 200) {
                        $("#see_nearby_shops_container").html(output);
                    }
                    else {
                        $("#see_nearby_shops_container").html(errorMessage);
                    }
                    old_postcode = postcode;
                    $("#kyash_close").show();
                }
            });
        }
    }
}