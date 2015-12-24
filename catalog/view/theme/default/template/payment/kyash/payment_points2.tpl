<?php if(count($payments) == 0):?>
<div class="notice">No shops available</div>
<?php else:?>
<div xmlns:kyash="http://www.w3.org/1999/xhtml">
    <kyash:code merchant_id="<?php echo $payments['id'] ?>" postal_code="<?php echo $payments['postal_code']?>"
                kyash_code="<?php echo $payments['kyash_code']?>"></kyash:code>
    <script type="text/javascript" src="<?php echo $payments['widget']; ?>"></script>
</div>
<?php endif;?>
