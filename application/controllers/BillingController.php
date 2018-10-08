<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class BillingController extends CI_Controller
{
    function __construct()
    {
        parent::__construct();
        setRedirect();
        auth(true);
        //local variables
        $this->module = 'child/billing/';
        $this->invoice_db = 'invoices';
        $this->payments_db = 'accnt_payments';
        $this->load->model('My_child', 'child');
        $this->load->model('My_invoice', 'invoice');
        $this->title = lang('child').'-'.lang('invoice');
    }

    function index()
    {
        $child_id = $this->uri->segment(2);
        $child = $this->child->first($child_id);
        $invoices = $this->invoice->childInvoices($child_id);
        page($this->module.'invoices', compact('child','invoices'));
    }
}