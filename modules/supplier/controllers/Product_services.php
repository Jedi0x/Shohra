<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Product_services extends ClientsController
{
     public function __construct()
    {
        parent::__construct();
        $this->load->model('taxes_model');
        $this->load->model('invoice_items_model');
        $this->load->model('currencies_model');
        $this->load->model('items_model');

        // Junaid code here
        $this->load->model('service_model');
    }

    public function index()
    {
        if (!has_contact_permission('items')) {
            set_alert('warning', _l('access_denied'));
            redirect(site_url());
        }

        $data['taxes']        = $this->taxes_model->get();
        $data['items_groups'] = $this->invoice_items_model->get_groups();
        $data['items'] = $this->items_model->get();

      
        $data['currencies'] = $this->currencies_model->get();

        $data['base_currency'] = $this->currencies_model->get_base_currency();

        $data['title'] = _l('invoice_items');

        /**
         * Pass data to the view
         */
        $this->data(['currencies' => $data['currencies'],'base_currency'=>$data['base_currency'],'taxes'=>$data['taxes'],'items_groups'=>$data['items_groups'],'items'=>$data['items'] ]);

        /**
         * Set page title
         */
        $this->title($data['title']);

        /**
         * The view name
         */
        $this->view('client/manage');
        /**
         * Render the layout/view
         */
        $this->layout();
    }

    public function items_create($id = '')
    {
        if (!has_contact_permission('items')) {
            set_alert('warning', _l('access_denied'));
            redirect(site_url());
        }
        $userid = $this->input->post('userid');
        $id = $this->input->post('itemid');
        if ($this->input->post()) {

            if ($id == '') {
               if (!has_contact_permission('items')) {
                    set_alert('warning', _l('access_denied'));
                    redirect(site_url());
                }
                $id = $this->invoice_items_model->add($this->input->post());
                if ($id) {
                    set_alert('success', _l('added_successfully', _l('items')));
                    // redirect(admin_url('mindmap'));
                    redirect(site_url('supplier/product_services/'));
                }
            } else {
                if (!has_contact_permission('items')) {
                    set_alert('warning', _l('access_denied'));
                    redirect(site_url());
                }
                $success = $this->invoice_items_model->edit($this->input->post(), $id);
                if ($success) {
                    set_alert('success', _l('updated_successfully', _l('items')));
                    redirect(site_url('supplier/product_services/'));
                }
                //redirect(admin_url('mindmap/mindmap_create/' . $id));
            }
        }
        
      }

      public function edit($id = ''){
        if (!has_contact_permission('items')) {
            set_alert('warning', _l('access_denied'));
            redirect(site_url());
        }
         if ($this->input->is_ajax_request()) {
            $item                     = $this->invoice_items_model->get($id);
            $item->long_description   = nl2br($item->long_description);
            $item->custom_fields_html = render_custom_fields('items', $id, [], ['items_pr' => true]);
            $item->custom_fields      = [];

            $cf = get_custom_fields('items');

            foreach ($cf as $custom_field) {
                $val = get_custom_field_value($id, $custom_field['id'], 'items_pr');
                if ($custom_field['type'] == 'textarea') {
                    $val = clear_textarea_breaks($val);
                }
                $custom_field['value'] = $val;
                $item->custom_fields[] = $custom_field;
            }

            echo json_encode($item);
        }
      }


       /* Delete items */
    public function delete($id)
    {
        if (!has_contact_permission('items')) {
            set_alert('warning', _l('access_denied'));
            redirect(site_url());
        }
        if (!$id) {
            redirect(site_url('supplier/product_services/'));
        }
        $response = $this->invoice_items_model->delete($id);
        if (is_array($response) && isset($response['referenced'])) {
            set_alert('warning', _l('is_referenced', _l('invoice_item_lowercase')));
        } elseif ($response == true) {
            set_alert('success', _l('deleted', _l('service')));
        } else {
            set_alert('warning', _l('problem_deleting', _l('invoice_item_lowercase')));
        }
       
        redirect(site_url('supplier/product_services/'));
    }


    // Junaid code here

    public function manage_service($service = false)
    {

        $data['taxes']        = $this->taxes_model->get();
        $data['currencies'] = $this->currencies_model->get();
        $data['base_currency'] = $this->currencies_model->get_base_currency();
        $data['header'] = 'supplier_new_service';

        if($service){
            $this->data['header'] = 'supplier_edit_service';
            $this->data['product'] = $this->service_model->get($service);
        }

        if($this->input->post()){

            $iframe = NULL;
            
            if(!empty($this->input->post('offer_embed_link'))){
                $iframe = html_entity_decode($this->input->post('offer_embed_link'));
                $height = 190;
                $width = 255;
                $iframe = preg_replace('/height="(.*?)"/i', 'height="' . $height .'"', $iframe);
                $iframe = preg_replace('/width="(.*?)"/i', 'width="' . $width .'"', $iframe);
            }
            

            $long_description = html_purify($this->input->post('long_description', false));
            $long_description = remove_emojis($long_description);
            $long_description = nl2br($long_description);


            $service = [
                'rel_type'              => 'supplier_offer',
                'description'           => nl2br($this->input->post('name')),
                'long_description'      => $long_description,
                'rate'                  => $this->input->post('price'),
                'tax'                   => NULL,
                'tax2'                  => NULL,
                'userid'                => get_client_user_id(),
                'unit'                  => 1,
                'offer_embed_link'      => $iframe,
                'is_publish'            => 0,
                'is_featured'           => 0,
                'offer_time'            => $this->input->post('offer_time'),
                'offer_video_number'    => $this->input->post('offer_video_number')
            ];

            if (!is_dir('uploads/services')) {
                mkdir('./uploads/services/', 0777, TRUE);
            }

            $attachments = array();
            if(isset($_FILES['attachments']) && !empty($_FILES['attachments'])){
                $files = $_FILES['attachments'];
                $config = array(
                    'upload_path'   => SERVICE_IMAGE_UPLOAD,
                    'allowed_types' => '*',
                    'max_size' => '1000000000',
                    'encrypt_name' => TRUE,

                );

                $this->load->library('upload', $config);
                foreach ($files['name'] as $key => $image) {

                    $_FILES['images[]']['name']= $files['name'][$key];
                    $_FILES['images[]']['type']= $files['type'][$key];
                    $_FILES['images[]']['tmp_name']= $files['tmp_name'][$key];
                    $_FILES['images[]']['error']= $files['error'][$key];
                    $_FILES['images[]']['size']= $files['size'][$key];

                    if(!empty($image)){
                        $this->upload->initialize($config);
                        if ($this->upload->do_upload('images[]')) {
                            $result = $this->upload->data();
                            $attachments[] = $result['file_name'];
                        } 
                    }
                }
            }

            if ($this->input->post('id')) {


                $update = $this->service_model->update($this->input->post('id'), $service);
                if ($update) {

                    if(!empty($attachments)){
                        foreach ($attachments as $k => $attachment) {
                            $attachments_data = array('item_id' => $this->input->post('id'), 'attachment' => $attachment );
                            $this->db->insert(db_prefix().'item_attachments', $attachments_data);
                        }

                    }
                    
                    set_alert('success', _l('updated_successfully', _l('service')));
                }
            } else if ($this->input->post()) {

                $service['slug'] = product_slug($this->input->post('name'));
                $insert_id = $this->service_model->create($service);
                if ($insert_id) {

                    if(!empty($attachments)){
                        foreach ($attachments as $k => $attachment) {
                            $attachments_data = array('item_id' => $insert_id, 'attachment' => $attachment );
                            $this->db->insert(db_prefix().'item_attachments', $attachments_data);
                        }

                    }
                    set_alert('success', _l('added_successfully', _l('service')));
                }
            }

            redirect(admin_url('supplier/product_services/'));




        }

        $data['title'] = 'Services';

        $this->data(['currencies' => $data['currencies'],'base_currency'=>$data['base_currency'],'taxes'=>$data['taxes'],'header'=>$data['header']]);

        /**
         * Set page title
         */
        $this->title($data['title']);

        /**
         * The view name
         */
        $this->view('client/manage_service');
        /**
         * Render the layout/view
         */
        $this->layout();
        
    }


    public function delete_service_attachment($attachment='')
    {
        $this->db->where('attachment', $attachment);
        $this->db->delete(db_prefix() . 'item_attachments');
        unlink(PRODUCT_IMAGE_UPLOAD.$attachment);
     
        if ($this->db->affected_rows() > 0) {
            set_alert('success', _l('attachment_deleted'));
            redirect($_SERVER['HTTP_REFERER']);
        }
        
        
    }


    public function service_view()
    {
        $service = $_POST['service_id'];
        $data['product'] = $this->service_model->get($service);
        echo $this->load->view('admin/suppliers/view_service', $data, true);
    }


    public function orders()
    {
        
        $data['orders'] = $this->service_model->get_orders();
        $data['title']    = _l('orders');
        $this->data($data);
        $this->view('client/orders');
        $this->layout();
    }

   
}