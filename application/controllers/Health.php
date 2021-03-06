<?php if(!defined('BASEPATH')) exit('No direct script access allowed');

class Health extends CI_Controller
{

    function __construct()
    {
        parent::__construct();
        //redirect session
        setRedirect();
        auth(TRUE);
        $this->load->model('My_child', 'child');
        $this->load->model('My_health', 'health');
        $this->load->model('My_food', 'food');
        $this->load->model('My_meds', 'meds');

        $this->module = 'child/health/';
        $this->title = lang('child').'-'.lang('health');
    }

    function index($id)
    {        
        $daycare_id = $this->session->userdata('owner_daycare_id');
        if(!authorizedToChild(user_id(), $id)) {
            flash('error', lang('You do not have permission to view this child\'s profile'));
            redirectPrev();
        }

        $child = $this->db->where('id', $id)->get('children')->row();

        $child->meds = $this->db
            ->select('cm.*,mp.name,mp.photo')
            ->where('cm.child_id', $child->id)
            ->from('child_meds as cm')
            ->join('med_photos as mp', 'mp.id=cm.photo_id','left')
            ->order_by('cm.id', 'DESC')
            ->get()->result();

        if(empty($child)) {
            flash('error', lang('request_error'));
            redirect($daycare_id.'/dashboard');
        }

        page($this->module.'health', compact('child','daycare_id'));
    }

    /*
     * add allergy
     * @return void
     */
    function addAllergy()
    {
        allow(['admin', 'manager', 'staff']);

        $this->form_validation->set_rules('allergy', 'Allergy Name', 'required|trim|xss_clean');

        if($this->form_validation->run() == TRUE) {

            if($this->health->addAllergy()) {

                flash('success', lang('request_success'));

            } else {

                flash('warning', lang('no_change_to_db'));

            }

        } else {

            flash('danger', lang('request_error'));

        }

        redirectPrev(NULL, 'allergies');
    }

    /*
     * delete allergy
     */
    function deleteAllergy($id)
    {
        allow(['admin', 'manager', 'staff']);
        $allergy_detail = $this->db->get_where('child_allergy',array(
            'id' => $id
        ));
        $allergies = $allergy_detail->row();        
        $this->db->where('id', $id);
        $this->db->delete('child_allergy');
        if($this->db->affected_rows() > 0) {
            logEvent($user_id=NULL,"Deleted allergy {$allergies->allergy} for child {$this->child->child($allergies->child_id)->first_name}",$care_id = NULL);
            flash('success', lang('request_success'));
        } else {
            flash('danger', lang('request_error'));
        }
        redirectPrev(NULL, 'allergies');
    }

    /**
     * add contact
     */
    function addContact()
    {
        $this->form_validation->set_rules('name', lang('name'), 'required|trim|xss_clean');
        $this->form_validation->set_rules('phone', lang('phone'), 'required|trim|xss_clean');
        $this->form_validation->set_rules('relation', lang('relation'), 'required|trim|xss_clean');
        $this->form_validation->set_rules('address', lang('address'), 'trim|xss_clean');
        if($this->form_validation->run() == TRUE) {
            if($this->health->addContact()) {
                flash('success', lang('request_success'));
            } else {
                flash('danger', lang('request_error'));
            }
        } else {
            flash('danger');
            validation_errors();
        }
        redirectPrev(NULL, 'emergency_contacts');
    }

    /**
     * @param $id
     */
    function deleteContact($id)
    {
        allow(['admin', 'manager', 'staff']);
        $contact_details = $this->db->get_where('child_contacts',array(
            'id' => $id
        ));
        $contacts = $contact_details->row();       
        if($this->db->where('id', $id)->delete('child_contacts')) {
            logEvent($user_id = NULL,"Deleted child contact {$contacts->contact_name} for child {$this->child->child($contacts->child_id)->first_name}",$care_id = NULL);
            flash('success', lang('request_success'));
        } else {
            flash('danger', lang('request_danger'));
        }
        redirectPrev(NULL, 'contacts');
    }


    /**
     * add contact
     */
    function addProvider()
    {
        $this->form_validation->set_rules('name', lang('name'), 'required|trim|xss_clean');
        $this->form_validation->set_rules('phone', lang('phone'), 'required|trim|xss_clean');
        $this->form_validation->set_rules('type_role', lang('type_role'), 'required|trim|xss_clean');
        $this->form_validation->set_rules('address', lang('address'), 'trim|xss_clean');
        $this->form_validation->set_rules('notes', lang('address'), 'trim|xss_clean');
        if($this->form_validation->run() == TRUE) {
            if($this->health->addProvider()) {
                flash('success', lang('request_success'));
            } else {
                flash('danger', lang('request_error'));
            }
        } else {
            flash('danger');
            validation_errors();
        }
        redirectPrev(NULL, 'providers');
    }

    /**
     * @param $id
     */
    function deleteProvider($id)
    {
        allow(['admin', 'manager', 'staff']);
        $providers_detail = $this->db->get_where('child_providers',array(
            'id' => $id
        ));
        $providers = $providers_detail->row();       
        if($this->db->where('id', $id)->delete('child_providers')) {
            logEvent($user_id = NULL,"Deleted provider {$providers->provider_name} for child {$this->child->child($providers->child_id)->first_name}",$care_id = NULL);
            flash('success', lang('request_success'));
        } else {
            flash('danger', lang('request_danger'));
        }
        redirectPrev(NULL, 'providers');
    }


    /*
     * add allergy
     * @return void
     */
    function addProblem()
    {
        allow(['admin', 'manager', 'staff']);

        $this->form_validation->set_rules('name', lang('problem'), 'required|trim|xss_clean');
        $this->form_validation->set_rules('first_event', lang('problem'), 'required|trim|xss_clean');
        $this->form_validation->set_rules('last_event', lang('problem'), 'trim|xss_clean');
        $this->form_validation->set_rules('notes', lang('problem'), 'trim|xss_clean');
        if($this->form_validation->run() == TRUE) {
            if($this->health->addProblem()) {
                flash('success', lang('request_success'));
            } else {
                flash('warning', lang('request_error'));
            }
        } else {
            flash('danger', lang('request_error'));
        }

        redirectPrev(NULL, 'problem-list');
    }

    /**
     * @param $id
     */
    function deleteProblem($id)
    {
        allow(['admin', 'manager', 'staff']);
        $problem_details = $this->db->get_where('child_problems',array(
            'id' => $id
        ));
        $problems = $problem_details->row();       
        if($this->db->where('id', $id)->delete('child_problems')) {
            logEvent($user_id = NULL,"Deleted child problem {$problems->name} for child {$this->child->child($problems->child_id)->first_name}",$care_id = NULL);
            flash('success', lang('request_success'));
        } else {
            flash('danger', lang('request_danger'));
        }
        redirectPrev(NULL, 'problem-list');
    }
}