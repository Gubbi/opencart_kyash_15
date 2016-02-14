$(document).ready(function () {

    $("#kyash").parent().css({"vertical-align": "top", "padding-top": "9px"});
    $("input[name='payment_method']").on("click", function () {
        if ($(this).val() != "kyash") {
            $("#see_nearby_shops_container").hide();
            $("#kyash_payment_instructions").hide();
        }
        else {
            $("#see_nearby_shops_container").show();
            $("#kyash_payment_instructions").show();
        }
    });

});
