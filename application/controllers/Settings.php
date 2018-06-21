<?php if(!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * @file      : settings.php
 * @author    : JMuchiri
 * @Copyright 2017 A&M Digital Technologies
 */
class Settings extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        setRedirect();
        allow('admin,manager');
        //variables
        $this->module = 'admin/';
        $this->title = lang('settings');
    }

    function index()
    {
        $payMethods = $this->db->get('payment_methods')->result();
        page($this->module.'index', compact('payMethods'));
    }

    /**
     * update settings
     */
    function update()
    {
        allow('admin');
        foreach ($_POST as $field => $value) {
            $this->form_validation->set_rules($field, lang($field), 'xss_clean|trim');
        }
        if($this->form_validation->run() == true) {
            $error = 0;
            foreach ($_POST as $field => $value) {
                if(!update_option($field, $value, true)) {
                    $error++;
                }
            }
            flash('success', lang('Settings have been updated'));
        } else {
            validation_errors();
            flash('error');
            echo 'error';
            exit;
        }
        echo 'success';
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
        $upload_path = './assets/uploads/content/';
        if(!file_exists($upload_path)) {
            mkdir($upload_path, 755, true);
        }
        $filename= $_FILES["logo"]["name"];
        $file_ext = pathinfo($filename,PATHINFO_EXTENSION);
        $config = array(
            'upload_path' => $upload_path,
            'allowed_types' => 'png|jpg|jpeg',
            'max_size' => '2048',
            'max_width' => '500',
            'max_height' => '112',
            'encrypt_name' => false,
            'file_name'=>'logo.'.$file_ext,
            'overwrite' => true
        );
        $this->load->library('upload', $config);
        if(!$this->upload->do_upload('logo')) {
            $errors['errors'] = $this->upload->display_errors();
            flash('danger', lang('request_error').implode('', $errors));
        } else {
            $upload_data = $this->upload->data();
            $data = array('upload_data' => $upload_data);
            if($data) {
                update_option('logo', 'logo.png', true);
                flash('success', lang('Settings have been updated'));
            } else {
                flash('danger', lang('request_error'));
            }
        }
        redirect('settings/#logo');

    }

    /**
     * upload invoice logo
     */
    function upload_invoice_logo()
    {
        allow('admin');
        $upload_path = './assets/uploads/content/';
        if(!file_exists($upload_path)) {
            mkdir($upload_path, 755, true);
        }
        $filename= $_FILES["invoice_logo"]["name"];
        $file_ext = pathinfo($filename,PATHINFO_EXTENSION);
        $config = array(
            'upload_path' => $upload_path,
            'allowed_types' => 'png|jpg|jpeg',
            'max_size' => '2048',
            'max_width' => '500',
            'max_height' => '112',
            'encrypt_name' => false,
            'file_name' => 'invoice_logo.'.$file_ext,
            'overwrite' => true
        );
        $this->load->library('upload', $config);
        if(!$this->upload->do_upload('invoice_logo')) {
            $errors['errors'] = $this->upload->display_errors();
            flash('danger', lang('request_error').implode('', $errors));
        } else {
            $upload_data = $this->upload->data();
            $data = array('upload_data' => $upload_data);
            if($data) {
                update_option('invoice_logo', 'invoice_logo.png', true);
                flash('success', lang('Settings have been updated'));
            } else {
                flash('danger', lang('request_error'));
            }
        }
        redirect('settings/#logo');
    }

    function paymentMethods()
    {
        allow('admin');
        $this->form_validation->set_rules('title', lang('payment_method'), 'required|trim|xss_clean');

        if($this->form_validation->run() == TRUE) {
            $this->db->insert('payment_methods', array(
                'title' => $this->input->post('title')
            ));
            flash('success', lang('Settings have been updated'));
        } else {
            flash('error');
            validation_errors();
        }
        redirect('settings/#paymentMethods');
    }

    function deletePaymentMethod($id)
    {
        allow('admin');
        $this->db->delete('payment_methods', array('id' => $id));
        flash('success', lang('Settings have been updated'));
        redirect('settings/#paymentMethods');
    }
}