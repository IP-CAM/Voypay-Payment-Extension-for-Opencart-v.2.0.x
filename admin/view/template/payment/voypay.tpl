<?php echo $header; ?><?php echo $column_left; ?>
<div id="content">
    <div class="page-header">
        <div class="container-fluid">
            <h1><?php echo $heading_title; ?></h1>
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
                <h3 class="panel-title"><i class="fa fa-pencil"></i> <?php echo $heading_title; ?></h3>
            </div>

            <div class="panel-body">
                <form action="<?php echo $action; ?>" method="post" enctype="multipart/form-data" id="form" class="form-horizontal">

                    <div class="form-group required">
                        <label class="col-sm-2 control-label" for="input-merchant"><?php echo $entry_mer_no; ?></label>
                        <div class="col-sm-10">
                            <input size="10" type="text" name="voypay_mer_no" value="<?php echo empty($voypay_mer_no)?'':$voypay_mer_no; ?>" class="form-control" />
                            <?php if ($error_mer_no) { ?>
                            <span class="error"><?php echo $error_mer_no; ?></span>
                            <?php } ?>
                        </div>
                    </div>
                    <div class="form-group required">
                        <label class="col-sm-2 control-label" for="input-md5key"><?php echo $entry_sign;?></label>
                        <div class="col-sm-10">
                            <input size="20" type="text" name="voypay_sign" value="<?php echo empty($voypay_sign)?'':$voypay_sign; ?>" class="form-control" />
                            <?php if ($error_sign) { ?>
                            <span class="error"><?php echo $error_sign; ?></span>
                            <?php } ?>
                        </div>
                    </div>
                    <div class="form-group required">

                        <label class="col-sm-2 control-label" for="input-status"><?php echo $entry_status; ?></label>
                        <div class="col-sm-10"><select name="voypay_status" class="form-control">
                                <?php if ($voypay_status) { ?>
                                <option value="1" selected="selected"><?php echo $text_enabled; ?></option>
                                <option value="0"><?php echo $text_disabled; ?></option>
                                <?php } else { ?>
                                <option value="1"><?php echo $text_enabled; ?></option>
                                <option value="0" selected="selected"><?php echo $text_disabled; ?></option>
                                <?php } ?>
                            </select>
                        </div>
                    </div>
                    <div class="form-group required">

                        <label class="col-sm-2 control-label" for="input-status">mode</label>
                        <div class="col-sm-10"><select name="voypay_mode" class="form-control">
                                <?php if (voypay_mode == 'live') { ?>
                                <option value="live"  selected="selected">live</option>
                                <option value="sandbox">sandbox</option>
                                <?php } else { ?>
                                <option value="live">live</option>
                                <option value="sandbox" selected="selected">sandbox</option>
                                <?php } ?>
                            </select>
                        </div>
                    </div>
                    <div class="form-group required">
                        <label class="col-sm-2 control-label" for="input-geo-zone">
                            <?php echo $entry_geo_zone; ?></label>
                        <div class="col-sm-10">
                            <select name="voypay_geo_zone_id" class="form-control">
                                <option value="0"><?php echo $text_all_zones; ?></option>
                                <?php foreach ($geo_zones as $geo_zone) { ?>
                                <?php if ($geo_zone['geo_zone_id'] == $new_geo_zone_id) { ?>
                                <option value="<?php echo $geo_zone['geo_zone_id']; ?>" selected="selected"><?php echo $geo_zone['name']; ?></option>
                                <?php } else { ?>
                                <option value="<?php echo $geo_zone['geo_zone_id']; ?>"><?php echo $geo_zone['name']; ?></option>
                                <?php } ?>
                                <?php } ?>
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-2 control-label" for="input-sort-order"><?php echo $entry_sort_order; ?></label>
                        <div class="col-sm-10"><input type="text" name="voypay_sort_order" value="<?php echo $voypay_sort_order; ?>" size="1" class="form-control" />
                        </div>
                    </div>
                    <!--        <div class="form-group">
                               <label class="col-sm-2 control-label" for="input-sort-order"><?php echo $entry_transactionurl; ?></label>
                               <div class="col-sm-10">
                                   <input type="text" name="voypay_transactionurl" value="<?php echo empty($voypay_transactionurl)?'./voypay.php' : $voypay_transactionurl; ?>" style="width:600px;">
                               </div>
                           </div> -->

                    <div class="form-group">
                        <!--  <div class="form-group">
                            <label class="col-sm-2 control-label" for="input-order-status"><?php echo $entry_order_status; ?></label>
                            <div class="col-sm-10"><select name="voypay_order_status_id" class="form-control">
                                    <?php foreach ($order_statuses as $order_status) { ?>
                                    <?php if ($order_status['order_status_id'] == $voypay_order_status_id) { ?>
                                    <option value="<?php echo $order_status['order_status_id']; ?>" selected="selected"><?php echo $order_status['name']; ?></option>
                                    <?php } else { ?>
                                    <option value="<?php echo $order_status['order_status_id']; ?>"><?php echo $order_status['name']; ?></option>
                                    <?php } ?>
                                    <?php } ?>
                                </select></div>
                        </div> -->

                        <div class="form-group">
                            <label class="col-sm-2 control-label" for="input-neworder_success_order_status"><?php echo $entry_voypay_success_order_status; ?></label>
                            <div class="col-sm-10"><select name="voypay_new_success_order_status_id" class="form-control">
                                    <?php foreach ($order_statuses as $order_status) { ?>
                                    <?php if ($order_status['order_status_id'] == $voypay_new_success_order_status_id) { ?>
                                    <option value="<?php echo $order_status['order_status_id']; ?>" selected="selected"><?php echo $order_status['name']; ?></option>
                                    <?php } else { ?>
                                    <option value="<?php echo $order_status['order_status_id']; ?>"><?php echo $order_status['name']; ?></option>
                                    <?php } ?>
                                    <?php } ?>
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-2 control-label" for="input-neworder_fail_order_status"><?php echo $entry_voypay_fail_order_status; ?></label>
                            <div class="col-sm-10"><select name="voypay_new_fail_order_status_id" class="form-control">
                                    <?php foreach ($order_statuses as $order_status) { ?>
                                    <?php if ($order_status['order_status_id'] == $voypay_new_fail_order_status_id) { ?>
                                    <option value="<?php echo $order_status['order_status_id']; ?>" selected="selected"><?php echo $order_status['name']; ?></option>
                                    <?php } else { ?>
                                    <option value="<?php echo $order_status['order_status_id']; ?>"><?php echo $order_status['name']; ?></option>
                                    <?php } ?>
                                    <?php } ?>
                                </select></div>
                        </div>
                        <div class="form-group">
                            <div class="pull-right">
                                <button type="submit" form="form" data-toggle="tooltip" title="<?php echo $button_save; ?>" class="btn btn-primary"><i class="fa fa-save"></i></button>
                                <a href="<?php echo $cancel; ?>" data-toggle="tooltip" title="<?php echo $button_cancel; ?>" class="btn btn-default"><i class="fa fa-reply"></i></a>
                            </div>
                        </div>

                </form>
            </div>
        </div>
    </div>
</div>
<?php echo $footer; ?>