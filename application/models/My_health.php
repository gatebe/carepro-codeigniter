<?php if(!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Class: my_health
 * User: John Muchiri
 * Email: jgmuchiri@gmail.com
 * Date: 11/29/2014
 *
 * https://amdtllc.com
 * Copyright 2014 All Rights Reserved
 */
class my_health extends CI_Model
{

    /**
     * @return bool
     */
    function addAllergy()
    {
        $allergy = $this->input->post('allergy');
        $child_id = $this->input->post('child_id');
        $data = [
            'child_id' => $this->input->post('child_id'),
            'allergy' => $allergy,
            'reaction' => $this->input->post('reaction'),
            'notes' => $this->input->post('notes'),
            'created_at' => date_stamp(),
            'user_id' => $this->user->uid(),
        ];

        $this->db->insert('child_allergy', $data);
        $last_id = $this->db->insert_id();
        if($this->db->affected_rows() > 0) {
            //log event
            logEvent($id = NULL,"Added allergy {$allergy} for child {$this->child->child($child_id)->first_name}",$care_id = NULL);
            //notify parent
            $this->parent->notifyParents($data['child_id'], lang('new_allergy_subject'), lang('new_allergy_message'));
            return TRUE;
        }
        return FALSE;

    }

    /**
     * @return bool
     */
    function addProblem()
    {
        $name = $this->input->post('name');
        $child_id = $this->input->post('child_id');
        $data = [
            'child_id' => $child_id,
            'name' => $name,
            'notes' => $this->input->post('notes'),
            'first_event' => $this->input->post('first_event'),
            'last_event' => $this->input->post('last_event'),
            'created_at' => date_stamp(),
            'user_id' => $this->user->uid(),
        ];
        $this->db->insert('child_problems', $data);
        if($this->db->affected_rows() > 0) {
            $last_id = $this->db->insert_id();
            //log event
            logEvent($id = NULL,"Added problem {$name} for child {$this->child->child($child_id)->first_name}",$care_id = NULL);
            //notify parent
            $this->parent->notifyParents($data['child_id'], lang('new_problem_subject'), lang('new_problem_message'));
            return TRUE;
        }
        return FALSE;

    }


    /**
     * @return bool
     */
    function addContact()
    {
        $child_id = $this->input->post('child_id');
        $contact_name = $this->input->post('name');
        $data = [
            'child_id' => $child_id,
            'contact_name' => $contact_name,
            'relation' => $this->input->post('relation'),
            'phone' => $this->input->post('phone'),
            'address' => $this->input->post('address'),
            'created_at' => date_stamp(),
            'user_id' => $this->user->uid(),
        ];
        if($this->db->insert('child_contacts', $data)) {
            $last_id = $this->db->insert_id();
            //log
            logEvent($id = NULL,"Added contact {$contact_name} for child {$this->child->child($child_id)->first_name}",$care_id = NULL);
            //notify parent
            $this->parent->notifyParents($data['child_id'], lang('new_contact_subject'), lang('new_contact_message'));
            return TRUE;
        }
        return FALSE;
    }

    /**
     * @return bool
     */
    function addProvider()
    {
        $child_id = $this->input->post('child_id');
        $provider_name = $this->input->post('name');

        $data = [
            'child_id' => $child_id,
            'provider_name' => $provider_name,
            'type_role' => $this->input->post('type_role'),
            'phone' => $this->input->post('phone'),
            'address' => $this->input->post('address'),
            'notes' => $this->input->post('notes'),
            'created_at' => date_stamp(),
            'user_id' => $this->user->uid(),
        ];
        if($this->db->insert('child_providers', $data)) {
            $last_id = $this->db->insert_id();
            //log event
            logEvent($id = NULL,"Added provider {$provider_name} for child {$this->child->child($child_id)->first_name}",$care_id = NULL);
            //notify parent
            $this->parent->notifyParents($data['child_id'], lang('new_provider_subject'), lang('new_provider_message'));
            return TRUE;
        }
        return FALSE;
    }
}