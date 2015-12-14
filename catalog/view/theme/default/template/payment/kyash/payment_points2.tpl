<?php if(count($payments) == 0):?>
<div class="notice">No shops available</div>
<?php else:?>
<iframe src="<?php echo $payments['widget'] ?>" id="kyash_contents" height="400px" frameborder="0" style="border: none; width: 100%;"></iframe>
<?php endif;?>