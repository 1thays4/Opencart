<?php echo $header; ?>
<div id="content">
  <div class="breadcrumb">
    <?php
    foreach ($breadcrumbs as $breadcrumb) {
        if ($breadcrumb['href']) {
            ?>
            <?php echo $breadcrumb['separator']; ?><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a>
            <?php
        } else {
            ?>
            <?php echo $breadcrumb['separator']; ?><?php echo $breadcrumb['text']; ?>
            <?php
        }
    }
    ?>
  </div>
  <?php if ($error_warning) { ?>
  <div class="warning"><?php echo $error_warning; ?></div>
  <?php } ?>
  <?php if ($success) { ?>
  <div class="success"><?php echo sprintf($success, $success_param); ?></div>
  <?php } ?>
  <div class="box">
    <div class="heading">
      <h1><img src="view/image/setting.png" alt="" /> <?php echo $heading_title; ?></h1>
      <div class="buttons"><a onclick="$('#form').submit();" class="button"><?php echo $button_save; ?></a><a href="<?php echo $cancel; ?>" class="button"><?php echo $button_cancel; ?></a></div>
    </div>
    <div class="content">
      <form action="<?php echo $action; ?>" method="post" enctype="multipart/form-data" id="form">
        <table class="form">
          <tr>
            <td><?php echo $entry_email; ?></td>
            <td><input type="text" name="squidfacil_email" value="<?php echo $squidfacil_email; ?>" /></td>
          </tr>
          <tr>
            <td><?php echo $entry_token; ?></td>
            <td><input type="text" name="squidfacil_token" value="<?php echo $squidfacil_token; ?>"/></td>
          </tr>
        </table>
      </form>
    </div>
  </div>
</div>
<?php echo $footer; ?> 