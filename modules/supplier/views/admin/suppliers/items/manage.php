 
<div role="tabpanel" class="tab-pane" id="product_services">
            <?php if (has_permission('items', '', 'create') || has_permission('items', '', 'edit')) { ?>
            <a href="<?php echo admin_url('services/products/offer/invoice/'.$client->userid) ?>" class="btn btn-info mbot30" ><?php echo _l('new_offer'); ?></a>
            <?php } ?>
        
           
              <table class="table dt-table invoice-items"  data-order-type="desc">
                  <thead>
                    <tr>
                        <th class="th-invoice-items-description"><?php echo _l('#'); ?></th>
                        <th class="th-invoice-items-description"><?php echo _l('name'); ?></th>
                        <th class="th-invoice-items-tax_1"><?php echo _l('publish'); ?></th>
                        <th class="th-invoice-items-long_description"><?php echo _l('price'); ?></th>
                        <th class="th-invoice-items-rate"><?php echo _l('description'); ?></th>
                    </tr>
                  </thead>
                <tbody>
                    <?php 
                    foreach($offers as $key => $offer){
                        ?>
               <tr>
                 <?php 

                 $options = '<div class="row-options">';
                 if (has_permission('invoices', '', 'edit')) {
                    $options .= '<a href="' . admin_url('services/products/offer/invoice/'.$offer['client_id'].'/'  . $offer['id']) . '">' . _l('edit') . '</a>';
                }
                if (has_permission('invoices', '', 'delete')) {
                    $options .= ' | <a class=" text-danger" href="' . admin_url('services/products/delete/invoice/' . $offer['id']) . '">' . _l('delete') . '</a>';
                }
                $options .= '</div>';

                if ($offer['is_publish'] == 1) {
                    $publish = '<span class="badge bg-success">'._l('published').'</span>';
                }
                else{
                    $publish = '<span class="badge bg-warning">'._l('not_publish').'</span>';
                }

                 ?>
                  <td><?php echo $key+1;?></td>
                  <td><?php echo $offer['name'].$options;?></td>
                  <td><?php echo $publish;?></td>
                  <td><?php echo app_format_money($offer['price'], get_base_currency());?></td>
                  <td><?php echo $offer['description'];?></td>
                  
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
          