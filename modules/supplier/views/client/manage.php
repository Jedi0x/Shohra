<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>

<div id="wrapper">
  <div class="content">
    <div class="row">
      <div class="col-md-12">
        <div class="panel_s">
          <div class="panel-body">
           
             <a href="#" data-toggle="modal" data-table=".table-invoice-items" data-target="#items_bulk_actions" class="hide bulk-actions-btn table-btn"><?php echo _l('bulk_actions'); ?></a>
             <div class="modal fade bulk_actions" id="items_bulk_actions" tabindex="-1" role="dialog">
              <div class="modal-dialog" role="document">
               <div class="modal-content">
                <div class="modal-header">
                 <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                 <h4 class="modal-title"><?php echo _l('bulk_actions'); ?></h4>
               </div>
               <div class="modal-body">
                 
                   <div class="checkbox checkbox-danger">
                    <input type="checkbox" name="mass_delete" id="mass_delete">
                    <label for="mass_delete"><?php echo _l('mass_delete'); ?></label>
                  </div>
                  <!-- <hr class="mass_delete_separator" /> -->
                
              </div>
              <div class="modal-footer">
               <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo _l('close'); ?></button>
               <a href="#" class="btn btn-info" onclick="items_bulk_action(this); return false;"><?php echo _l('confirm'); ?></a>
             </div>
           </div>
           <!-- /.modal-content -->
         </div>
         <!-- /.modal-dialog -->
       </div>
       <!-- /.modal -->
    
     <?php hooks()->do_action('before_items_page_content'); ?>
 
       <div class="_buttons">
        <a href="<?php echo site_url('supplier/product_services/manage_service') ?>" class="btn btn-info pull-left"><?php echo _l('supplier_new_service'); ?></a>
      </div>
      <div class="clearfix"></div>
      <hr class="hr-panel-heading" />
   
    <?php
    $table_data = [];

   
      $table_data[] = '<span class="hide"> - </span><div class="checkbox mass_select_all_wrap"><input type="checkbox" id="mass_select_all" data-to-table="invoice-items"><label></label></div>';
    
  ?>
  <table class="table dt-table invoice-items" data-order-col="3" data-order-type="desc">
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
    	<?php foreach($items as $item){


        if ($item['is_publish'] == 1) {
          $publish = '<span class="badge bg-success">'._l('published').'</span>';
        }
        else{
          $publish = '<span class="badge bg-warning">'._l('not_publish').'</span>';
        }

        if ($item['is_featured'] == 1) {
          $featured = '<span class="badge bg-success">'._l('featured').'</span>';
        }
        else{
          $featured = '<span class="badge bg-warning">'._l('not_featured').'</span>';
        }


        ?>
   <tr>
     
      <td>
         <a href="#"><?php echo $item['description'];?></a>
         <div class="row-options"><a href="javascript:;" class="view-service" data-id="<?php echo $item['itemid']?>">View </a> | <a href="<?php echo site_url('supplier/product_services/manage_service/'.$item['itemid']) ?>">Edit </a> | <a href="<?php echo site_url();?>supplier/product_services/delete/<?php echo $item['itemid']?>" class="text-danger _delete">Delete </a></div>
      </td>
      <td><?php echo $item['long_description'];?></td>
      <td><?php echo $item['rate'];?></td>
      <td><?php echo $publish;?></td>
      <td><?php echo $featured;?></td>
      
   </tr>
<?php } ?>
</tbody>
</table>
  </div>
</div>
</div>
</div>
</div>
</div>
<?php $this->load->view('client/item'); ?>

<div class="modal fade" id="service_view_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog" role="document">
  <div id="service-modal-content">

  </div>
</div>
</div>


<script type="text/javascript">
  // Junaid code her
    $('body').on('click', '.view-service', function() {
        var service_id = $(this).data('id');
        $.ajax({
            method : 'POST',
            url: "<?=site_url('supplier/product_services/service_view')?>", 
            data: {service_id: service_id},
            success: function(result){
                $("#service-modal-content").html(result);
                $("#service_view_modal").modal('show');

            }
        });
    });
</script>


