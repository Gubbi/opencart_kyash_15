function preparePullShops(url) {
    var postcode = document.getElementById("postcode").value;
    pullNearByShops(postcode, url);
}
var old_postcode = "";
var errorMessage = "Due to some unexpected errors, this is not available at the moment. We are working on fixing it.";
function pullNearByShops(postcode, url) {
    if (postcode.length == 0) {
        alert("Enter your post code to retrieve the shops");
    }
    else {
        if (old_postcode != postcode) {
            $("#see_nearby_shops_container").show();
            var loader = '<img src="catalog/view/theme/default/template/payment/kyash/image/loading.gif" alt="" />';
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
                }
            });
        }
    }
}