<?php if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class My_parent extends CI_Model
{
    public function page($page, $data = [])
    {
        $data['page'] = $page;
        $this->load->view('parent/inc/home', $data);
    }

    /**
     * @return mixed
     */
    public function parents()
    {
        $daycare_id = $this->session->userdata('daycare_id');        
        $this->db->where('users_groups.group_id', 4);
        $this->db->select('users_groups.user_id,users_groups.group_id,users.*');
        $this->db->from('users')->where('users.daycare_id',$daycare_id);
        $this->db->join('users_groups', 'users_groups.user_id=users.id');
        return $this->db->get();
    }

    /**
     * @return mixed
     */
    public function parent()
    {
        $this->db->where('users_groups.group_id', 4);
        $this->db->select('*');
        $this->db->from('users');
        $this->db->join('users_groups', 'users_groups.user_id=users.id');
        return $this->db->get();
    }

    /*
     * selected child belongs to logged in parent
     */
    public function child_belongs_to_parent($child, $parent)
    {
        $this->db->where('user_id', $parent);
        $this->db->where('child_id', $child);
        $query = $this->db->get('child_parents');
        if ($query->num_rows() > 0) {
            return true;
        }

        return false;
    }

    public function register_child()
    {
        $data = [
            'first_name' => $this->input->post('first_name'),
            'last_name' => $this->input->post('last_name'),
            'national_id' => encrypt($this->input->post('national_id')),
            'bday' => $this->input->post('bday'),
            'gender' => $this->input->post('gender'),
            'created_at' => date_stamp(),
            'last_update' => date_stamp(),
        ];
        $this->db->insert('children', $data);
        $last_id = $this->db->insert_id();

        if ($this->db->affected_rows() > 0) {
            flash('success', lang('request_success'));
        } else {
            flash('warning', lang('request_error'));
        }

        //associate to this parent
        $data2 = [
            'child_id' => $last_id,
            'user_id' => $this->user,
        ];
        $this->db->insert('child_parents', $data2);
        redirect(site_url('parents/view_child/' . $last_id)); //go to child record
    }

    /**
     * @param $parent_id
     * @return mixed
     */
    public function getChildren($parent_id = null)
    {
        if ($parent_id == null) {
            $parent_id = $this->user->uid();
        }

        $this->db->select('children.*');
        $this->db->where('child_parents.user_id', $parent_id);
        $this->db->from('children');
        $this->db->join('child_parents', 'children.id=child_parents.child_id');
        return $this->db->get();
    }

    /**
     * @param null $parent_id
     * @return mixed
     */
    public function totalChildren($parent_id = null)
    {
        if ($parent_id == null) {
            return $this->getChildren($this->user->uid())->num_rows();
        } else {
            return $this->getChildren($parent_id)->num_rows();
        }
    }

    public function notify_check_out($child_id, $out_guardian)
    {
        $child = $this->db->select('id,first_name,last_name')->where('id', $child_id)->get('children')->row();

        $childName = $child->first_name . ' ' . $child->last_name;
        $message = sprintf(lang('child_checked_out_message'), $childName, date('d M Y @ H:i:A'), $out_guardian);
        $subject = sprintf(lang('child_checked_out_subject'), $childName);

        $this->notifyParents($child_id, $subject, $message);
    }

    public function notify_check_in($child_id, $in_guardian)
    {
        $child = $this->db->select('id,first_name,last_name')->where('id', $child_id)->get('children')->row();
        $childName = $child->first_name . ' ' . $child->last_name;
        $message = sprintf(lang('child_checked_in_message'), $childName, date('d M Y @ H:i:A'), $in_guardian);
        $subject = sprintf(lang('child_checked_in_subject'), $childName);

        $this->notifyParents($child_id, $subject, $message);
    }

    /**
     * @param $child_id
     * @param $subject
     * @param $message
     * @return bool
     */
    public function notifyParents($child_id, $subject, $message)
    {
        $child = $this->child->first($child_id);
        $childName = $child->first_name . ' ' . $child->last_name;
        //get parents info
        $parents = $this->child->getParents($child_id)->result();

        if (count((array) $parents) == 0) {
            return false;
        }

        $sent = 0;
        foreach ($parents as $row) {
            $data = [
                'subject' => $subject,
                'to' => $row->email,
                'message' => $message,
                'logo' => $this->session->userdata('company_logo'),
                'childName' => $childName,
                'name' => $row->first_name,
            ];
            if (send_email($data)) {
                $sent = 1;
                $sent++;
            }
        }
        if ($sent == count($parents)) {
            return true;
        }

        return false;
    }
}
