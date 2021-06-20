<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>

<div id="wrapper">
  <div class="content">
    <div class="row">
      <div class="col-md-12">
        <div class="panel_s">
          <div class="panel-body">



           <?php hooks()->do_action('before_items_page_content'); ?>

           <div class="_buttons">
            <a href="<?php echo base_url('supplier/product_services/manage') ?>" class="btn btn-info pull-left" ><?php echo _l('new_offer'); ?></a>
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
                <th class="th-invoice-items-description"><?php echo _l('#'); ?></th>
                <th class="th-invoice-items-description"><?php echo _l('name'); ?></th>
                <th class="th-invoice-items-tax_1"><?php echo _l('publish'); ?></th>
                <th class="th-invoice-items-long_description"><?php echo _l('price'); ?></th>
                <th class="th-invoice-items-rate"><?php echo _l('description'); ?></th>
              </tr>
            </thead>
            <tbody>
             <?php foreach($offers as $key => $offer){ ?>
               <tr>
                 <?php 

                 $options = '<div class="row-options">';
                 if (has_permission('invoices', '', 'edit')) {
                  $options .= '<a href="' . base_url('supplier/product_services/manage/'. $offer['id']) . '">' . _l('edit') . '</a>';
                }
                if (has_permission('invoices', '', 'delete')) {
                  $options .= ' | <a class=" text-danger" href="' . base_url('supplier/product_services/delete/' . $offer['id']) . '">' . _l('delete') . '</a>';
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
    </div>
  </div>
</div>
</div>
</div>



