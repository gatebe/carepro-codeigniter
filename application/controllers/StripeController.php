<?php

use Stripe\Charge;
use Stripe\Error\Base;
use Stripe\Stripe;

defined('BASEPATH') or exit('No direct script access allowed');

class StripeController extends CI_Controller
{

    /**
     * Get All Data from this method.
     *
     * @return Response
     */
    public
    function __construct()
    {
        parent::__construct();
        $this->load->library("session");
        $this->load->helper('url');
    }

    /**
     * Get All Data from this method.
     *
     * @return Response
     */
    public
    function index()
    {
        $this->load->view('front/registration/subscribe');
    }

    /**
     * Get All Data from this method.
     *
     * @return Response
     */
    public
    function stripePost($activation_code = NULL)
    {
        $query = $this->db->get_where('users', [
            'activation_code' => $activation_code,
        ]);
        $user_data = $query->result_array()[0];
        $user_name = $user_data['name'];
        $to = $user_data['email'];

        $selected_plan = $user_data['selected_plan'];
        //get subscription detail
        $get_plan_details = $this->db->get_where('subscription_plans', [
            'id' => $selected_plan,
        ]);
        $plans = $get_plan_details->result_array()[0];
        $price = $plans['price'];
        $plan = $plans['plan'];

        try {
            //get stripe secret key
            Stripe::setApiKey($this->config->item('stripe_secret'));
            //stripe make payment
            $charge = Charge::create([
                "amount"      => $price * 100,
                "currency"    => "usd",
                "source"      => $this->input->post('stripeToken'),
                "description" => "Payment of $ ".$price." for ".$plan." subscription plan completed.",
            ]);
        }
        catch (Base $e) {
            flash('danger', $e->getMessage());
            redirectPrev();
        }

        $this->load->config('email');
        $this->load->library('email');

        $data = [
            'user_name'          => $user_name,
            'price'              => $price,
            'plan'               => $plan,
            'activation_code'    => $activation_code,
            'registered_success' => '',
            'payment_success'    => '',
        ];
        $this->email->set_mailtype('html');
        $from = $this->config->item('smtp_user');
        $this->email->from($from, 'Daycare');
        $this->email->to($to);
        $this->email->subject('Daycare payment');

        $body = $this->load->view('custom_email/payment_success_email', $data, TRUE);
        $this->email->message($body);        //Send mail on successful payment
        if($this->email->send()) {
            $this->change_owner_status($to, $activation_code, $plan);
        }
        else {
            $logs = "[".date('m/d/Y h:i:s A', time())."]"."\n\r";
            $logs .= $this->email->print_debugger('message');
            $logs .= "\n\r";
            file_put_contents('./application/logs/log_'.date("j.n.Y").'.log', $logs, FILE_APPEND); //log error if any unable to send email.
           flash('error', "Unable to send verification email. Please try again.");
        }
    }

    //change user status to subscribed if payment completed.
    public
    function change_owner_status($to, $activation_code, $plan)
    {
        $get_status = $this->db->get('user_status');
        $result = $get_status->result_array();
        $owner_status = $result[2]['id'];
        $data = [
            'owner_status' => $owner_status,
        ];
        $this->db->where('email', $to);
        $this->db->update('users', $data);

        $query = $this->db->get_where('users', [
            'email' => $to,
        ]);
        $check_status = $query->row_array();
        $user_status = $check_status['owner_status'];
        if($user_status === "3") {
            flash("success", "Payment completed successfully. Thank you for subscription.");
            redirect('daycare/'.$activation_code);
        }
    }
}
