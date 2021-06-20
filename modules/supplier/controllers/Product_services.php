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
    $this->load->model('services/subscription_products_model', 'spm');
    $this->load->model('services/invoice_products_model', 'ipm');
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
    $last = $this->uri->total_segments();
    $record_num = $this->uri->segment($last);
    $userid = get_client_user_id() ? get_client_user_id() : $record_num;
    $this->db->select('*');
    $this->db->from(db_prefix() . 'invoice_products');
    $this->db->where('client_id',$userid);
    $this->db->order_by('id', 'asc');
    $data['offers'] =  $this->db->get()->result_array();
    $data['currencies'] = $this->currencies_model->get();

    $data['base_currency'] = $this->currencies_model->get_base_currency();

    $data['title'] = _l('invoice_items');

        /**
         * Pass data to the view
         */
        $this->data(['currencies' => $data['currencies'],'base_currency'=>$data['base_currency'],'taxes'=>$data['taxes'],'items_groups'=>$data['items_groups'],'items'=>$data['items'], 'offers' => $data['offers'] ]);

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

    public function manage($id = false)
    {
        
        $data['title'] = _l('add_new', _l('Offer'));
        $data['taxes']      = $this->taxes_model->get();
        $data['currencies'] = $this->currencies_model->get();


        $this->data(['currencies' => $data['currencies'],'taxes'=>$data['taxes']]);

        if ($id) {
            $data['product'] = $this->ipm->get($id);

            $this->data['product'] =  $data['product'];

        }



        /**
         * Set page title
         */
        $this->title($data['title']);

        /**
         * The view name
         */
        $this->view('client/add');
        /**
         * Render the layout/view
         */
        $this->layout();
    }

    public function add()
    {
        $long_description = html_purify($this->input->post('long_description', false));
        $long_description = remove_emojis($long_description);
        $long_description = nl2br($long_description);

        $tax = implode(',', (array) $this->input->post('tax'));

        $iframe = html_entity_decode($this->input->post('video'));
        $height = 190;
        $width = 255;


        $iframe = preg_replace('/height="(.*?)"/i', 'height="' . $height .'"', $iframe);
        $iframe = preg_replace('/width="(.*?)"/i', 'width="' . $width .'"', $iframe);
            
        $data = [
            'name'                  => $this->input->post('name'),
            'description'           => nl2br($this->input->post('description')),
            'long_description'      => $long_description,
            'price'                 => $this->input->post('price'),
            'group'                 => $this->input->post('group'),
            'tax_1'                 => $tax,
            'currency'              => $this->input->post('currency'),
            'is_recurring'          => $this->input->post('is_recurring'),
            'interval'              => $this->input->post('interval'),
            'interval_type'         => $this->input->post('interval_type'),
            'created_from'          => get_staff_user_id(),
            'video'                 => $iframe,
            'is_publish'            => 0,
            'slug'                  => product_slug($this->input->post('name')),
            'video_time'            => $this->input->post('video_time'),
            'video_number'          => $this->input->post('video_number')
        ];


        $data['client_id'] = $this->input->post('clientid');
        $data['customer_group'] = null;


        // Junaid code here

        if (!is_dir('uploads/products')) {
            mkdir('./uploads/products/', 0777, TRUE);
        }

        $attachments = array();
        if(isset($_FILES['attachments']) && !empty($_FILES['attachments'])){
            $files = $_FILES['attachments'];
            $config = array(
                'upload_path'   => PRODUCT_IMAGE_UPLOAD,
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


            $update = $this->ipm->update($this->input->post('id'), $data);
            if ($update) {

                if(!empty($attachments)){
                    foreach ($attachments as $k => $attachment) {
                        $attachments_data = array('product_id' => $this->input->post('id'), 'attachment' => $attachment );
                        $this->db->insert(db_prefix().'invoice_product_attachments', $attachments_data);
                    }

                }

                set_alert('success', _l('updated_successfully'));
            }
        } else if ($this->input->post()) {

            $insert_id = $this->ipm->create($data);
            if ($insert_id) {

                if(!empty($attachments)){
                    foreach ($attachments as $k => $attachment) {
                        $attachments_data = array('product_id' => $insert_id, 'attachment' => $attachment );
                        $this->db->insert(db_prefix().'invoice_product_attachments', $attachments_data);
                    }

                }
                set_alert('success', _l('added_successfully'));
            }
        }
        redirect(admin_url('supplier/product_services'));
        
    }

    public function delete($id)
    {
        $resp = $this->ipm->delete($id);
        set_alert('success', 'successful');
        redirect($this->agent->referrer());
    }

    public function delete_attachment($attachment='')
    {
        $this->db->where('attachment', $attachment);
        $this->db->delete(db_prefix() . 'invoice_product_attachments');
        unlink(PRODUCT_IMAGE_UPLOAD.$attachment);
     
        if ($this->db->affected_rows() > 0) {
            set_alert('success', _l('attachment_deleted'));
            redirect($_SERVER['HTTP_REFERER']);
        }
        
        
    }




}