<?php if(!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * @file      : settings.php
 * @author    : JMuchiri
 * @Copyright 2017 A&M Digital Technologies
 */
class Settings extends CI_Controller
{

    public
    function __construct()
    {
        parent::__construct();
        setRedirect();
        allow([
            'admin',
            'manager',
        ]);

        disable_debug();
        //variables
        $this->module = 'admin/';
        $this->title = lang('settings');
    }

    function index()
    {
        $daycare_id = $this->session->userdata('owner_daycare_id');
        if(session('company_demo_mode') == 1) {
            allow('super');
        }

        $this->load->model('My_backup', 'backup');

        $payMethods = $this->db->get('payment_methods')->result();
        $settings = $this->db
            ->select('d.*,ds.*,add.*,d.daycare_id as daycare_unquie_id,ds.id as setting_id')
            ->where('d.id', $this->session->userdata('daycare_id'))
            ->from('daycare as d')
            ->join('daycare_settings as ds', 'ds.daycare_id=d.id')
            ->join('address as add', 'add.id=d.address_id')
            ->get()->row();

        $settings->email = $this->session->userdata('email');
        // $settings = $this->db->get('options')->result_array();

        $event_logs = $this->db->get_where('event_log', [
            'daycare_id' => $this->session->userdata('daycare_id'),
        ])->result();

        // $option=array();
        // foreach($settings as $key=>$val){
        //      $option[$val['option_name']]=$val['option_value'];
        // }

        dashboard_page($this->module.'settings', compact('payMethods', 'settings', 'event_logs'), $daycare_id);
    }

    function logs()
    {
        $this->load->model("My_settings");
        $fetch_data = $this->My_settings->make_datatables();
        $output = [
            "draw"            => 1,
            "recordsTotal"    => $this->My_settings->get_all_data(),
            "recordsFiltered" => $this->My_settings->get_filtered_data(),
        ];
        $data = [];
        foreach ($fetch_data as $row) {
            // $sub_array = array();
            // $sub_array[] = $row->id;
            // $sub_array[] = $row->user_id;
            // $sub_array[] = $row->daycare_id;
            // $sub_array[] = $row->event;
            // $sub_array[] = $row->date;
            // $data[] = $sub_array;

            $output['data'][] = [
                'id'         => $row->id,
                'user_id'    => $row->user_id,
                'daycare_id' => $row->daycare_id,
                'event'      => $row->event,
                'date'       => $row->date,
            ];
        }

        echo $output;
    }

    /**
     * update settings
     */
    function update()
    {
        allow('admin');
        if(empty($_POST)) redirectBack('No data passed','error');

        foreach ($_POST as $field => $value) {
            $this->form_validation->set_rules($field, lang($field), 'xss_clean|trim');
        }

        if($this->form_validation->run() == TRUE) {
            // $error = 0;            
            // foreach ($_POST as $field => $value) {
            //     if($value == "") {
            //         empty_option($field);
            //     } else {
            //         if(!update_option($field, $value, true)) {
            //             $error++;
            //         }
            //     }
            //     return $field;
            // }
            if(array_key_exists('name', $_POST)) {
                $address_data = [
                    'phone'          => $_POST['phone'],
                    'fax'            => $_POST['fax'],
                    'address_line_1' => $_POST['address_line_1'],
                    'address_line_2' => $_POST['address_line_2'],
                    'city'           => $_POST['city'],
                    'state'          => $_POST['state'],
                    'zip_code'       => $_POST['zip_code'],
                    'country'        => $_POST['country'],
                ];
                $this->db->where('id', $_POST['address_id'])->update('address', $address_data);
                $setting_data = [
                    'timezone'    => $_POST['timezone'],
                    'date_format' => $_POST['date_format'],
                    'start_time'  => $_POST['start_time'],
                    'end_time'    => $_POST['end_time'],
                ];
                $this->db->where('daycare_id', session('daycare_id'))->update('daycare_settings', $setting_data);

                $daycare_data = [
                    'name'                    => $_POST['name'],
                    'slogan'                  => $_POST['slogan'],
                    'facility_id'             => $_POST['facility_id'],
                    'employee_tax_identifier' => $_POST['employee_tax_identifier'],
                    'daycare_id'              => $_POST['daycare_unquie_id'],
                ];
                $this->db->where('id', $_POST['id'])->update('daycare', $daycare_data);
                redirectBack('Settings updated successfully.','success','');
            }
            elseif(array_key_exists('stripe', $_POST)) {
                if(empty($_POST['stripe_toggle'])) {
                    $toggle = 0;
                }
                else {
                    $toggle = 1;
                }
                $setting_data = [
                    'stripe_pk_live' => $_POST['stripe_pk_live'],
                    'stripe_sk_live' => $_POST['stripe_sk_live'],
                    'stripe_pk_test' => $_POST['stripe_pk_test'],
                    'stripe_sk_test' => $_POST['stripe_sk_test'],
                    'stripe_enabled' => $_POST['stripe_enabled'],
                    'stripe_toggle'  => $toggle,
                ];
                $this->db->where('daycare_id', session('daycare_id'))->update('daycare_settings', $setting_data);
                redirectBack('Billing Settings updated successfully.','success','billing');
            }
            elseif(array_key_exists('invoice_terms', $_POST)) {
                $this->db->where('daycare_id', session('daycare_id'))->update('daycare_settings', ['invoice_terms' => $_POST['invoice_terms']]);
                redirectBack('Invoice terms settings updated successfully.','success','billing');
            }
            elseif(array_key_exists('tawkto_embed_url', $_POST)) {
                $this->db->where('daycare_id', session('daycare_id'))->update('daycare_settings', ['tawkto_embed_url' => $_POST['tawkto_embed_url']]);
                redirectBack('Twakto Integration updated','success','integrations');
            }
        }
        else {
            validation_errors();
            flash('error');
            echo 'error';
            exit;
        }

        // reload_company();
        // echo 'success';
    }

    /*
     * purge payments for a child
     */
    function purge_payments()
    {
        page($this->module.'purge_payments');
    }

    /*
     * purge all charges from child record
     */
    function purge_charges()
    {
        page($this->module.'purge_charges');
    }

    /*
     * completely delete a child and associated data
     */
    function purge_child()
    {
        page($this->module.'purge_child');
    }

    /**
     * upload logo
     */
    function upload_logo()
    {
        allow('admin');
        $upload_path = './assets/uploads/daycare_logo/';
        if(!file_exists($upload_path)) {
            mkdir($upload_path, 0777, TRUE);
            chmod($upload_path, 0777);
        }
        $filename = $_FILES["logo"]["name"];
        $file_ext = pathinfo($filename, PATHINFO_EXTENSION);
        $config = [
            'upload_path'   => $upload_path,
            'allowed_types' => 'png|jpg|jpeg|png|svg',
            'max_size'      => '2048',
            'max_width'     => '500',
            'max_height'    => '112',
            'encrypt_name'  => FALSE,
            'file_name'     => $_POST['daycare_unquie_id'].'.'.$file_ext,
            'overwrite'     => TRUE,
        ];
        @unlink($upload_path.$config['file_name']);
        $this->load->library('upload', $config);
        if(!$this->upload->do_upload('logo')) {
            $errors['errors'] = $this->upload->display_errors();
            flash('danger', implode('', $errors));
        }
        else {
            $upload_data = $this->upload->data();
            $data = ['upload_data' => $upload_data];
            if($data) {
                $this->db->where('id', $_POST['daycare_id'])->update('daycare', ['logo' => $data['upload_data']['file_name']]);
                $this->session->set_userdata('company_logo', $data['upload_data']['file_name']);
                flash('success', lang('Logo has been updated successfully.'));
            }
            else {
                flash('danger', lang('request_error'));
            }
        }
        redirectPrev('', 'logo');
    }

    /**
     * upload invoice logo
     */
    function upload_invoice_logo()
    {
        allow('admin');
        $upload_path = './assets/uploads/invoice_logo/';
        if(!file_exists($upload_path)) {
            mkdir($upload_path, 0777, TRUE);
            chmod($upload_path, 0777);
        }
        $filename = $_FILES["invoice_logo"]["name"];
        $file_ext = pathinfo($filename, PATHINFO_EXTENSION);
        $config = [
            'upload_path'   => $upload_path,
            'allowed_types' => 'png|jpg|jpeg|svg',
            'max_size'      => '2048',
            'max_width'     => '500',
            'max_height'    => '112',
            'encrypt_name'  => FALSE,
            'file_name'     => 'invoice_logo_'.$_POST['daycare_unquie_id'].'.'.$file_ext,
            'overwrite'     => TRUE,
        ];
        $this->load->library('upload', $config);
        if(!$this->upload->do_upload('invoice_logo')) {
            $errors['errors'] = $this->upload->display_errors();
            flash('danger', lang('request_error').implode('', $errors));
        }
        else {
            $upload_data = $this->upload->data();
            $data = ['upload_data' => $upload_data];
            if($data) {
                $this->db->where('id', $_POST['settings_id'])->update('daycare_settings', ['invoice_logo' => $data['upload_data']['file_name']]);
                flash('success', lang('Invoice logo has been updated successfully.'));
            }
            else {
                flash('danger', lang('request_error'));
            }
        }
        redirectPrev('', 'logo');
    }

    function paymentMethods()
    {
        allow('admin');
        $this->form_validation->set_rules('title', lang('payment_method'), 'required|trim|xss_clean');

        if($this->form_validation->run() == TRUE) {
            $this->db->insert('payment_methods', [
                'title' => $this->input->post('title'),
            ]);
            flash('success', lang('Payment Methods has been added successfully.'));
        }
        else {
            flash('error');
            validation_errors();
        }
        redirectPrev('', 'billing');
    }

    function deletePaymentMethod($id)
    {
        allow('admin');
        $this->db->delete('payment_methods', ['id' => $id]);
        flash('success', lang('Settings have been updated'));
        redirectPrev('', 'billing');
    }
}
