<?php echo $header; ?><?php echo $column_left; ?>
<div id="content">
  <div class="page-header">
    <div class="container-fluid">
      <div class="pull-right">
        <button type="submit" form="form-special" data-toggle="tooltip" title="<?php echo $button_save; ?>" class="btn btn-primary"><i class="fa fa-save"></i></button>
        <a href="<?php echo $cancel; ?>" data-toggle="tooltip" title="<?php echo $button_cancel; ?>" class="btn btn-default"><i class="fa fa-reply"></i></a></div>
      <h1><?php echo $heading_title; ?></h1>
      <ul class="breadcrumb">
        <?php foreach ($breadcrumbs as $breadcrumb) { ?>
        <li><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a></li>
        <?php } ?>
      </ul>
    </div>
  </div>
  <div class="container-fluid">
    <?php if ($error_warning) { ?>
    <div class="alert alert-danger"><i class="fa fa-exclamation-circle"></i> <?php echo $error_warning; ?>
      <button type="button" class="close" data-dismiss="alert">&times;</button>
    </div>
    <?php } ?>
    <div class="panel panel-default">
      <div class="panel-heading">
        <h3 class="panel-title"><i class="fa fa-pencil"></i> <?php echo $text_edit; ?></h3>
      </div>
      <div class="panel-body">
        <form action="<?php echo $action; ?>" method="post" enctype="multipart/form-data" id="form-special" class="form-horizontal">
          <div class="form-group required">
            <label class="col-sm-2 control-label" for="input-name"><?php echo $entry_access_token; ?></label>
            <div class="col-sm-10">
              <input type="text" name="dreamkas_access_token" value="<?php echo $access_token; ?>" placeholder="<?php echo $entry_access_token; ?>" id="input-name" class="form-control" />
              <?php if ($error_access_token) { ?>
              <div class="text-danger"><?php echo $error_access_token; ?></div>
              <?php } ?>
            </div>
          </div>
          <div class="form-group required">
            <label class="col-sm-2 control-label" for="input-limit"><?php echo $entry_device_id; ?></label>
            <div class="col-sm-10">
              <input type="text" name="dreamkas_device_id" value="<?php echo $device_id; ?>" placeholder="<?php echo $entry_device_id; ?>" id="input-device_id" class="form-control" />
            </div>
          </div>
          <div class="form-group required">
            <label class="col-sm-2 control-label" for="input-width"><?php echo $entry_tax_mode; ?></label>
            <div class="col-sm-10">
              <select name="dreamkas_tax_mode" id="input-tax_mode" class="form-control">
                <option value="DEFAULT" <?php if ($tax_mode=='DEFAULT') echo "selected=\"selected\"";?>><?php echo $text_tax_default; ?></option>
                <option value="SIMPLE" <?php if ($tax_mode=='SIMPLE') echo "selected=\"selected\"";?>><?php echo $text_tax_simple; ?></option>
                <option value="SIMPLE_WO" <?php if ($tax_mode=='SIMPLE_WO') echo "selected=\"selected\"";?>><?php echo $text_tax_simple_wo; ?></option>
                <option value="ENVD" <?php if ($tax_mode=='ENVD') echo "selected=\"selected\"";?>><?php echo $text_tax_envd; ?></option>
                <option value="AGRICULT" <?php if ($tax_mode=='AGRICULT') echo "selected=\"selected\"";?>><?php echo $text_tax_agricult; ?></option>
                <option value="PATENT" <?php if ($tax_mode=='PATENT') echo "selected=\"selected\"";?>><?php echo $text_tax_patent; ?></option>
              </select>
            </div>
          </div>
          <div class="form-group required">
            <label class="col-sm-2 control-label" for="input-width"><?php echo $entry_tax_type; ?></label>
            <div class="col-sm-10">
              <select name="dreamkas_tax_type" id="input-tax_type" class="form-control">
                <option value="NDS_NO_TAX" <?php if ($tax_type=='NDS_NO_TAX') echo "selected=\"selected\"";?>><?php echo $text_tax_nds_no_tax; ?></option>
                <option value="NDS_0" <?php if ($tax_type=='NDS_0') echo "selected=\"selected\"";?>><?php echo $text_tax_nds_0; ?></option>
                <option value="NDS_10" <?php if ($tax_type=='NDS_10') echo "selected=\"selected\"";?>><?php echo $text_tax_nds_10; ?></option>
                <option value="NDS_18" <?php if ($tax_type=='NDS_18') echo "selected=\"selected\"";?>><?php echo $text_tax_nds_18; ?></option>
                <option value="NDS_10_CALCULATED" <?php if ($tax_type=='NDS_10_CALCULATED') echo "selected=\"selected\"";?>><?php echo $text_tax_nds_10_calculated; ?></option>
                <option value="NDS_18_CALCULATED" <?php if ($tax_type=='NDS_18_CALCULATED') echo "selected=\"selected\"";?>><?php echo $text_tax_nds_18_calculated; ?></option>
              </select>
            </div>
          </div>

          <div class="form-group required">
            <label class="col-sm-2 control-label" for="input-paid_order"><?php echo $entry_paid_order; ?></label>
            <div class="col-sm-10">
              <select name="dreamkas_paid_order" id="input-paid_order" class="form-control">
                <?php $arr = get_defined_vars();
                    foreach ($arr["order_statuses"] as $value)
                    { ?>
                            <option value=<?php echo $value['order_status_id']; ?>
                            <?php if ($paid_order==$value['order_status_id'])
                            echo "selected=\"selected\"";?>
                            ><?php echo $value['name']; ?></option>
                    <?php } ?>
                </select>
            </div>
          </div>

            <div class="form-group">
                <label class="col-sm-4 control-label" for="input-process-status"><?php echo $entry_payments_ids;?></label>
                <div class="col-sm-8">
                    <div class="well well-sm" style="height: 150px; overflow: auto;">
                        <?php foreach ($extensions as $paymenttype) { ?>
                        <div class="checkbox">
                            <label>
                                <?php if (in_array($paymenttype['code'], $payments_ids)) { ?>
                                <input type="checkbox" name="dreamkas_payments_ids[]" value="<?php echo $paymenttype['code']; ?>" checked="checked" />
                                <?php echo $paymenttype['name']; ?>
                                <?php } else { ?>
                                <input type="checkbox" name="dreamkas_payments_ids[]" value="<?php echo $paymenttype['code']; ?>" />
                                <?php echo $paymenttype['name']; ?>
                                <?php } ?>
                            </label>
                        </div>
                        <?php } ?>
                    </div>
                </div>
            </div>


          <div class="form-group">
            <label class="col-sm-2 control-label" for="input-status"><?php echo $entry_status; ?></label>
            <div class="col-sm-10">
              <select name="dreamkas_status" id="input-status" class="form-control">
                <?php if ($status) { ?>
                <option value="1" selected="selected"><?php echo $text_enabled; ?></option>
                <option value="0"><?php echo $text_disabled; ?></option>
                <?php } else { ?>
                <option value="1"><?php echo $text_enabled; ?></option>
                <option value="0" selected="selected"><?php echo $text_disabled; ?></option>
                <?php } ?>
              </select>
            </div>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
<?php echo $footer; ?>
