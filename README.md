# opencart_kyash_15
OpenCart 1.5 Integration Kit for the [Kyash Payment Gateway](http://www.kyash.com). This extension is vQmod based, hence your core files are not directly modified.


## Installation
1. Install the latest vQmod for OpenCart, as explained [here](https://github.com/vqmod/vqmod/wiki/Installing-vQmod-on-OpenCart).
2. Upload the contents of [opencart_kyash_15.zip](https://github.com/Gubbi/opencart_kyash_15/releases/download/v1.1/opencart_kyash_15-1.1.zip) file as it is to OpenCart.
3. In OpenCart Admin dashboard go to `Extensions`->`Payments`. You should see *Kyash* as one of the options there.
4. Click *Install*.


## Configuration
1. Go to `Extensions`->`Payments` in your OpenCart Admin dashboard.
2. Click *Edit*.
3. Enter the credentials listed on your Kyash Account Settings. There are two types of credentials you can enter:
   * To test the system, use the *Developer* credentials. 
   * To make the system live and accept your customer payments use the *Production* credentials.
4. Copy the *Callback URL* listed in OpenCart Settings and set it in your Kyash Account settings.


## Testing the Integration.
1. Place an order in your OpenCart store.
2. Pick *Kyash - Pay at a nearby shop* as the payment option.
3. Note down the *KyashCode* generated for this order.
4. In a live system, the customer will take this KyashCode to a nearby shop and make the payment using cash.
5. But since we are testing, Login to your Kyash Account.
6. Enter the KyashCode in the search box.
7. You should see a ```Mark as Paid``` button there.
8. Clicking this should change the order status from *Pending* to *Processing* in your OpenCart order details page.

## Paid and Expired KyashCodes are not being marked as such in Opencart.
Once you have successfully installed Kyash extension, if your orders are not being marked as paid after payment is done, then follow the below steps.

* Configure the Kyash Extension using your Kyash *Development API Credentials*.
* Create a test order with Kyash as the payment option.
* Note down the KyashCode returned.
* Login to your Kyash account and search for the KyashCode.
* Mark it as Paid.
* Check if the order status changes from "pending" to "processing" in opencart.
* If the status has not changed, then make the following entry in your .htaccess file just after the ```RewriteEngine On``` entry.
```
RewriteCond %{HTTP:Authorization} .+
RewriteRule .* - [E=HTTP_AUTHORIZATION:%{HTTP:Authorization}]
```
* Create another test order and mark it as paid to see if the issue is now fixed.

## Support
Contact developers@kyash.com for any issues you might be facing with this Kyash extension or call +91 8050114225.
