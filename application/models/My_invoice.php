<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * @file      : my_invoice
 * @author    : JMuchiri
 * @Copyright 2017 A&M Digital Technologies
 * https://amdtllc.com
 */
class My_invoice extends CI_Model
{

    public $invoice_db = 'invoices';

    function __construct()
    {
        //dbs
        $this->invoice_db = 'invoices';
        $this->invoice_items_db = 'invoice_items';
        $this->payments_db = 'accnt_payments';
        $this->bank_db = 'accnt_pay_bank';
        $this->bank_card_db = 'accnt_pay_cards';
        $this->pay_method_db = 'accnt_pay_methods';
    }

    /**
     * Return single invoice or invoice column
     *
     * @param        $id
     * @param bool   $items
     *
     * @return mixed
     */
    function get($id, $items = false)
    {

        if ($items == true)
            return $this->all($id);

        return $this->db->where('id', $id)->get('invoices')->row();;
    }

    /**
     * Get all invoices with their items
     *
     * @param string $id
     * @param string $childID
     *
     * @return mixed
     */
    function all($id = '', $childID = '')
    {

        $this->db->select('invoices.*,invoice_items.id as item_id,invoice_items.item_name,invoice_items.description,invoice_items.price,invoice_items.qty,invoice_items.discount');
        $this->db->from('invoices');
        $this->db->join('invoice_items', 'invoice_items.invoice_id=invoices.id', 'left');

        if ($id > 0)
            $this->db->where('invoices.id', $id);

        if ($childID > 0)
            $this->db->where('invoices.child_id', $childID);

        $result = $this->db->get()->result();
        return $result;
    }

    function childInvoices($id)
    {
        $invoices = $this->db->where('child_id', $id)->get('invoices')->result();

        foreach ($invoices as $invoice) {

            $invoice->amount = 0;
            $invoice->totalPaid = 0;
            $invoice->totalDue = 0;

            $invoice->items = $this->db->where('invoice_id', $invoice->id)->get('invoice_items')->result();
            $invoice->payments = $this->db->where('invoice_id', $invoice->id)->get('invoice_payments')->result();

            foreach ($invoice->items as $item) {
                $invoice->amount = $invoice->amount + ($item->price * $item->qty);
            }
            foreach ($invoice->payments as $payment) {
                $invoice->totalPaid = $invoice->totalPaid + $payment->amount;
            }

            $invoice->totalDue = $invoice->amount - $invoice->totalPaid;
        }
        return $invoices;
    }

    /**
     * @param $cid
     *
     * @return mixed
     */
    function payments($cid = null, $invoice = null)
    {
        if ($cid !== null)
            $this->db->where('invoices.child_id', $cid);
        if ($invoice !== null)
            $this->db->where('invoice_id', $invoice);
        $this->db->select('invoice_payments.*,invoices.child_id,invoices.date_due,invoices.invoice_status');
        $this->db->from('invoice_payments');
        $this->db->join('invoices', 'invoices.id=invoice_payments.invoice_id');
        return $this->db->get();
    }

    /**
     * @return array
     */
    function getInvoices()
    {
        $data = array();
        $query = $this->db->get($this->invoice_db);
        foreach ($query->result() as $row) {
            $data[] = $row;
        }
        return $data;
    }

    function getInvoiceItems($invoice_id)
    {
        $data = array();
        $this->db->where('invoice_id', $invoice_id);
        $query = $this->db->get($this->invoice_items_db);
        foreach ($query->result() as $row) {
            $data[] = $row;
        }
        return $data;
    }

    /**
     * @param $status
     *
     * @return string
     */
    function status($status)
    {
        switch ($status) {
            case "paid":
                $color = "success";
                break;
            case "due":
                $color = "warning";
                break;
            case "draft":
                $color = "default";
                break;
            case "cancelled":
                $color = "info";
                break;
            default:
                $color = 'info';
                break;
        }
        return '<span class="label label-' . $color . '">' . lang($status) . '</span>';
    }

    /**
     * @return bool
     */
    function createInvoice($id)
    {
        $UNLIMITED = "Unlimited";
        $admin_role = 1;
        $data = array(
            'child_id' => $id,
            'date_due' => $this->input->post('date_due'),
            'invoice_terms' => $this->input->post('invoice_terms'),
            'invoice_status' => "due", //default = due
            'user_id' => $this->user->uid(),
            'created_at' => date_stamp()
        );
        $daycare_id = $this->session->userdata('daycare_id');
        $plans = $this->session->userdata('plans');
        $plan_invoices_events = $plans['invoices']; //plan invoices count

        $invoices = $this->db
            ->select("in.*")
            ->from("invoices as in")
            ->join("children as cd", "cd.id = in.child_id")
            ->where("daycare_id", $daycare_id)
            ->get()->result_array();
        $invoices_count = count($invoices);

        if ($invoices_count < $plan_invoices_events || $plan_invoices_events == $UNLIMITED) {
            if (!$this->db->insert('invoices', $data))
                return false;
            $invoice_id = $this->db->insert_id();
            $item_name = $this->input->post('item_name');
            $data2 = array(
                'invoice_id' => $invoice_id,
                'item_name' => $item_name,
                'description' => $this->input->post('description'),
                'price' => $this->input->post('price'),
                'qty' => $this->input->post('qty')
            );
            if ($this->db->insert('invoice_items', $data2)) {
                $last_id = $this->db->insert_id();
                logEvent($user_id = NULL, "Added Invoice {$item_name} for child {$this->child->child($id)->first_name}", $care_id = NULL);
                $this->parent->notifyParents($id, lang('new_invoice_subject'), sprintf(lang('new_invoice_message'), $this->child->first($id)->first_name));
                return $invoice_id;
            }
            return false;
        } else {
            $error = "error";
            return $error;
        }
    }

    /**
     * @param $invoice_id
     *
     * @return bool
     */
    function makePayment($invoice_id)
    {
        $amount = $this->input->post('amount');
        $data = array(
            'invoice_id' => $invoice_id,
            'amount' => $amount,
            'date_paid' => $this->input->post('date_paid'),
            'method' => $this->input->post('method'),
            'remarks' => $this->input->post('remarks'),
            'user_id' => $this->user->uid(),
            'created_at' => date_stamp()
        );
        if ($this->db->insert('invoice_payments', $data)) {
            $last_id = $this->db->insert_id();
            logEvent($user_id = NULL, "Added manual payment of amount {$amount} for invoice", $care_id = NULL);
            $invoice = $this->get($invoice_id);
            $child = $this->child->first($invoice->child_id);
            $this->parent->notifyParents($child->id, lang('manual_payment_subject'), sprintf(lang('manual_payment'), $amount, $child->first_name));
            return true;
        } else {
            return false;
        }
    }

    /**
     * @param int $method
     *
     * @return array|bool
     */
    function paymentMethods($method = 0)
    {
        if ($method == 0 || $method == "") {
            $data = array();
            foreach ($this->db->get('payment_methods')->result() as $row) {
                $data[] = $row;
            }
            return $data;
        } else {
            $this->db->where('id', $method);
            foreach ($this->db->get('payment_methods')->result() as $row) {
                return $row->title;
            }
        }
        return false;
    }

    /**
     * @param $invoice_id
     * @param $item
     *
     * @return string
     */
    function paypal($invoice_id, $item)
    {
        //todo remove this and references moved to daycare library
        $url = 'https://www.paypal.com/cgi-bin/webscr?cmd=_xclick';
        $business = session('company_paypal_email');
        $lc = "US";
        $item_name = $item;
        $item_number = 'DayCare_' . $invoice_id;
        $amount = $this->amountDue($invoice_id);
        $currency_code = "USD";
        $button_subtype = "services";
        $no_note = 0;
        $cn = "Add special remarks";
        $no_shipping = 2;
        $undefined_quantity = 1;
        $tax_rate = 0;
        $link = $url . '&business=' . $business . '&lc=' . $lc . '&item_name=' .
            $item_name . '&item_number=' .
            $item_number . '&amount=' . $amount . '&currency_code=' . $currency_code . '&button_subtype=' .
            $button_subtype . '&no_note=' . $no_note . '&cn=' . $cn . '&no_shipping=' . $no_shipping . '&undefined_quantity=' .
            $undefined_quantity . '&tax_rate=' . $tax_rate;
        return $link;
    }

    /**
     * @param $invoice_id
     *
     * @return string
     */
    function amountDue($invoice_id)
    {
        $due = $this->subTotal($invoice_id) - $this->amountPaid($invoice_id);
        if ($due < 0) {
            $this->updateStatus($invoice_id, "paid"); //mark as paid
        }
        $due = str_replace(',', '', $due);
        $due = str_replace(' ', '', $due);
        $due = str_replace(session('company_currency_symbol'), '', $due);

        return number_format($due, 2);
    }

    /**
     * @param $invoice_id
     *
     * @return string
     */
    function subTotal($invoice_id)
    {
        $this->db->where('invoice_id', $invoice_id);
        $query = $this->db->get('invoice_items');
        $totalPrice = 0;
        if ($query->num_rows() > 0) {
            foreach ($query->result() as $row) {
                $totalPrice = ($row->price * $row->qty) + $totalPrice;
            }
            return $totalPrice;
        } else {
            return 0.00;
        }
    }

    /**
     * @param $invoice_id
     *
     * @return string
     */
    function amountPaid($invoice_id)
    {
        $this->db->where('invoice_id', $invoice_id);
        $this->db->select_sum('amount');
        $query = $this->db->get('invoice_payments');
        if ($query->num_rows() > 0) {
            $row = $query->row();
            return $row->amount;
        } else {
            return 0.00;
        }
    }

    /**
     * @param $invoice_id
     * @param $status
     *
     * @return bool
     */
    function updateStatus($invoice_id, $status)
    {
        $data = array(
            'invoice_status' => $status
        );
        $this->db->where('id', $invoice_id);
        if ($this->db->update('invoices', $data)) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * @return string
     */
    function getTotalDue()
    {
        $invoices = $this->db->where('invoice_status', "due")->get('invoices');
        $total = 0;
        if ($invoices->num_rows() > 0) {
            foreach ($invoices->result() as $inv) {
                $due = $this->amountDue($inv->id);
                $total = (float) $total + (float) $due;
            }
        }

        return $total;
    }

    function stamp($status)
    {
        return '<img style="width:200px" src="' . base_url() . 'assets/img/content/' . $status . '_stamp.png" class="stamp"/>';
    }

    /**
     * @param $invoice_id
     *
     * @return array
     */
    function getInvoice($invoice_id)
    {
        $data = array();
        $this->db->where('id', $invoice_id);
        $query = $this->db->get('invoices');
        foreach ($query->result() as $row) {
            $data[] = $row;
        }
        return $data;
    }

    /**
     * @param $email
     * @param $token
     *
     * @return bool|\Stripe\ApiResource
     */
    function createStripeCustomer($email, $token)
    {
        $error = null;
        try {
            $customer = \Stripe\Customer::create(array(
                'email' => $email,
                'source' => $token,
                'currency' => session('comapny_currency_abbreviation')
            ));
            return $customer;
        } catch (\Stripe\Error\Card $e) {
            $error = $e->getMessage();
        } catch (\Stripe\Error\InvalidRequest $e) {
            $error = $e->getMessage();
        } catch (\Stripe\Error\Authentication $e) {
            $error = $e->getMessage();
        } catch (\Stripe\Error\ApiConnection $e) {
            $error = $e->getMessage();
        } catch (\Stripe\Error\Base $e) {
            $error = $e->getMessage();
        } catch (Exception $e) {
            $error = $e->getMessage();
        }


        if ($error !== null) {
            flash('error', $error);
            redirectPrev();
        };
        return false;
    }

    function money($amount)
    {
        return $amount;
    }

    /**
     * @param $token
     * @param $data
     *
     * @return bool|\Stripe\ApiResource
     */
    function createStripeCharge($token, $data)
    {
        $error = null;

        try {
            //charge a credit or a debit card
            $charge = \Stripe\Charge::create([
                'source' => $token,
                'amount' => $data['amount'],
                'currency' => session('company_currency_abbreviation'),
                'description' => $data['description'],
                'metadata' => array(
                    'item_id' => $data['invoice_id']
                )
            ]);
            return $charge;
        } catch (\Stripe\Error\Card $e) {
            $error = $e->getMessage();
        } catch (\Stripe\Error\InvalidRequest $e) {
            $error = $e->getMessage();
        } catch (\Stripe\Error\Authentication $e) {
            $error = $e->getMessage();
        } catch (\Stripe\Error\ApiConnection $e) {
            $error = $e->getMessage();
        } catch (\Stripe\Error\Base $e) {
            $error = $e->getMessage();
        } catch (Exception $e) {
            $error = $e->getMessage();
        }

        if ($error !== null) {
            flash('error', $error);
            redirectPrev();
        };
        return false;
    }

    /**
     * @param $data
     *
     * @return bool|\Stripe\ApiResource
     */
    function createStripeSubscription($data)
    {
        $error = null;

        try {
            $charge = \Stripe\Charge::create(array(
                'customer' => $data['stripe_id'],
                'amount' => $data['amount'],
                'currency' => session('customer_currency_abbreviation'),
                'description' => $data['description'],
                'metadata' => array(
                    'item_id' => $data['invoice_id']
                )
            ));
            return $charge;
        } catch (\Stripe\Error\Card $e) {
            $error = $e->getMessage();
        } catch (\Stripe\Error\InvalidRequest $e) {
            $error = $e->getMessage();
        } catch (\Stripe\Error\Authentication $e) {
            $error = $e->getMessage();
        } catch (\Stripe\Error\ApiConnection $e) {
            $error = $e->getMessage();
        } catch (\Stripe\Error\Base $e) {
            $error = $e->getMessage();
        } catch (Exception $e) {
            $error = $e->getMessage();
        }

        if ($error !== null) {
            flash('error', $error);
            redirectPrev();
        };
        return false;
    }
}
