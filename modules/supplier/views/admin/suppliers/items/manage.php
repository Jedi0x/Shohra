<div role="tabpanel" class="tab-pane" id="product_services">
    <?php if (has_permission('items', '', 'create') || has_permission('items', '', 'edit')) { ?>
        <a href="<?php echo admin_url('supplier/manage_services/'.$client->userid) ?>" class="btn btn-info mbot30"><?php echo _l('supplier_new_service'); ?></a>
    <?php } ?>


    <table class="table dt-table invoice-items"  data-order-type="desc">
      <thead>
        <tr>
          <th class="th-invoice-items-description"><?php echo _l('invoice_items_list_description'); ?></th>
          <th class="th-invoice-items-long_description"><?php echo _l('invoice_item_long_description'); ?></th>
          <th class="th-invoice-items-rate"><?php echo _l('invoice_items_list_rate'); ?></th>
          <th class="th-invoice-items-tax_1"><?php echo _l('publish_status'); ?></th>
          <th class="th-invoice-items-tax_2"><?php echo _l('featured_status'); ?></th>
       

      </tr>
  </thead>
  <tbody>
    <?php 


    foreach($items as $item){


          $publish_status = '<div class="onoffswitch">
                <input type="checkbox"  data-switch-url="' . admin_url() . 'supplier/change_publish_status" name="onoffswitch" class="onoffswitch-checkbox" id="c_' . $item['itemid'] . '" data-id="' . $item['itemid'] . '"' . ($item['is_publish'] == 1 ? ' checked': '') . '>
                <label class="onoffswitch-label" for="c_' . $item['itemid'] . '"></label>
            </div>';


            $featured_status = '<div class="onoffswitch">
                <input type="checkbox"  data-switch-url="' . admin_url() . 'supplier/change_featured_status" name="onoffswitch" class="onoffswitch-checkbox" id="c_' . $item['itemid'] . '" data-id="' . $item['itemid'] . '"' . ($item['is_featured'] == 1 ? ' checked': '') . '>
                <label class="onoffswitch-label" for="c_' . $item['itemid'] . '"></label>
            </div>';
   
        ?>
        <tr>

          <td>
           <a href="#"><?php echo $item['description'];?></a>
           <div class="row-options"><a href="javascript:;" class="view-service" data-id = '<?=$item['itemid']?>'>View </a> | <a href="<?=admin_url('supplier/manage_services/'.$client->userid).'/'. $item['itemid']?>">Edit </a> | <a href="<?php echo admin_url();?>supplier/delete_item/<?php echo $item['itemid']?>/<?php echo $client->userid?>" class="text-danger _delete">Delete </a></div>
       </td>
       <td><?php echo $item['long_description'];?></td>
       <td><?php echo $item['rate'];?></td>
       <td><?php echo $publish_status; ?></td>
       <td><?php echo $featured_status;?></td>
       
   </tr>
<?php } ?>
</tbody>
</table>
</div>
<?php $CI->load->view(MODULE_SUPPLIER . '/admin/suppliers/items/items'); ?>
<div class="checkbox checkbox-primary no-mtop checkbox-inline task-add-edit-public" style=" display:none;">
   <input type="checkbox" id="is_supplier" name="is_supplier" checked>
   <label for="is_supplier"><?= _l('is_supplier') ?></label>
</div>

<div class="modal fade" id="service_view_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog" role="document">
  <div id="service-modal-content">

  </div>
</div>
</div>
