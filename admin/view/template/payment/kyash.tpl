<?php echo $header; ?>
<div id="content">
  <div class="breadcrumb">
    <?php foreach ($breadcrumbs as $breadcrumb) { ?>
    <?php echo $breadcrumb['separator']; ?><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a>
    <?php } ?>
  </div>
  <?php if ($error_warning) { ?>
  <div class="warning"><?php echo $error_warning; ?></div>
  <?php } ?>
  <div class="box">
    <div class="heading">
      <h1><img src="view/image/payment.png" alt="" /> <?php echo $heading_title; ?></h1>
      <div class="buttons"><a onclick="$('#form').submit();" class="button"><?php echo $button_save; ?></a><a href="<?php echo $cancel; ?>" class="button"><?php echo $button_cancel; ?></a></div>
    </div>
    <div class="content">
      <form action="<?php echo $action; ?>" method="post" enctype="multipart/form-data" id="form">
        <table class="form">
          <tr>
            <td><span class="required">*</span> <?php echo $entry_public_api_id; ?></td>
            <td><input type="text" name="kyash_public_api_id" value="<?php echo $public_api_id; ?>" />
              <?php if ($error_public_api_id) { ?>
              <span class="error"><?php echo $error_public_api_id; ?></span>
              <?php } ?></td>
          </tr>
          <tr>
            <td><span class="required">*</span> <?php echo $entry_api_secrets; ?></td>
            <td><input type="password" name="kyash_api_secrets" value="<?php echo $api_secrets; ?>" />
              <?php if ($error_api_secrets) { ?>
              <span class="error"><?php echo $error_api_secrets; ?></span>
              <?php } ?></td>
          </tr>
          <tr>
            <td><span class="required">*</span> <?php echo $entry_callback_secret; ?></td>
            <td><input type="password" name="kyash_callback_secret" value="<?php echo $callback_secret; ?>" />
              <?php if ($error_callback_secret) { ?>
              <span class="error"><?php echo $error_callback_secret; ?></span>
              <?php } ?></td>
          </tr>
          <tr>
            <td><span class="required">*</span> HMAC Secret Key</td>
            <td><input type="password" name="kyash_hmac_secret" value="<?php echo $hmac_secret; ?>" />
              <?php if ($error_hmac_secret) { ?>
              <span class="error"><?php echo $error_hmac_secret; ?></span>
              <?php } ?></td>
          </tr>
          <tr>
            <td><?php echo $entry_callback_url; ?></td>
            <td><strong><?php echo HTTP_CATALOG.'index.php?route=payment/kyash/handler'?></strong></td>
          </tr>
          <tr>
            <td><?php echo $entry_instructions; ?></td>
            <td><textarea cols="80" rows="8" name="kyash_instructions"><?php echo $instructions; ?></textarea></td>
          </tr>
          <tr>
            <td><?php echo $entry_total; ?></td>
            <td><input type="text" name="kyash_total" value="<?php echo $kyash_total; ?>" /></td>
          </tr>          
          <tr>
            <td><?php echo $entry_geo_zone; ?></td>
            <td><select name="kyash_geo_zone_id">
                <option value="0"><?php echo $text_all_zones; ?></option>
                <?php foreach ($geo_zones as $geo_zone) { ?>
                <?php if ($geo_zone['geo_zone_id'] == $kyash_geo_zone_id) { ?>
                <option value="<?php echo $geo_zone['geo_zone_id']; ?>" selected="selected"><?php echo $geo_zone['name']; ?></option>
                <?php } else { ?>
                <option value="<?php echo $geo_zone['geo_zone_id']; ?>"><?php echo $geo_zone['name']; ?></option>
                <?php } ?>
                <?php } ?>
              </select></td>
          </tr>
          <tr>
            <td><?php echo $entry_status; ?></td>
            <td><select name="kyash_status">
                <?php if ($kyash_status) { ?>
                <option value="1" selected="selected"><?php echo $text_enabled; ?></option>
                <option value="0"><?php echo $text_disabled; ?></option>
                <?php } else { ?>
                <option value="1"><?php echo $text_enabled; ?></option>
                <option value="0" selected="selected"><?php echo $text_disabled; ?></option>
                <?php } ?>
              </select></td>
          </tr>
          <tr>
            <td><?php echo $entry_sort_order; ?></td>
            <td><input type="text" name="kyash_sort_order" value="<?php echo $kyash_sort_order; ?>" size="1" /></td>
          </tr>
        </table>
      </form>
    </div>
  </div>
</div>
<?php echo $footer; ?> 