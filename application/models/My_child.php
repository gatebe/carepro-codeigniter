<?php if(!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class My_child extends CI_Model
{

    public function first($id)
    {
        return $this->db->where('id', $id)->get('children')->row();
    }

    /**
     * @param        $id
     * @param string $field
     *
     * @return string
     */
    public function get($id, $field = '')
    {
        if($field == 'name') {
            $field = ['first_name', 'last_name'];
        }

        $this->db->where('id', $id);
        $child = $this->db->get('children')->row();

        if(is_array($field) && !empty($field)) {
            $res = '';
            foreach ($field as $item) {
                $res .= $child->$item.' ';
            }
            return $res;
        }

        if($field !== '') {
            return $child->$field;
        }

        return $child;
    }

    /*
     * set child id session to be used in this instance
     * @params none
     * @return int
     */
    /*
     * getAllChildren
     */
    public function children()
    {
        $daycare_id = $this->session->userdata('daycare_id');
        return $this->db->where(['daycare_id'=>$daycare_id,'status' => 1])->get('children');
    }

    /**
     * @param null $id
     *
     * @return mixed
     */
    public function child($id = NULL)
    {
        return $this->db->where('id', $id)->get('children')->row();
    }

    /**
     * @param $child_id
     *
     * @return mixed|object
     */
    public function getParents($child_id)
    {
        $this->db->where('child_parents.child_id', $child_id);
        $this->db->select('*');
        $this->db->from('users');
        $this->db->join('address','users.address_id=address.id');
        $this->db->join('child_parents', 'child_parents.user_id=users.id');
        return $this->db->get();
    }

    /**
     * @param null $id
     *
     * @return mixed
     */
    public function getParent($id = NULL)
    {
        $this->db->where('children.id', $id);
        $this->db->select('*');
        $this->db->from('children');
        $this->db->join('child_parents', 'child_parents.child_id=children.id');
        $this->db->join('users', 'users.id=child_parents.user_id');
        return $this->db->get()->row();
    }

    /**
     * @param $db
     *
     * @return mixed
     */
    public function getData($db, $child_id)
    {
        $data = [];
        if($db == 'child_checkin') {
            $this->db->order_by('id', 'DESC');
        }

        $this->db->where('child_id', $child_id);
        return $this->db->get($db)->result();
    }

    /**
     * @return mixed
     */
    public function getCount($active = TRUE)
    {
        if(is('parent')) {
            $query = $this->parent->getChildren($this->user->uid());
            return $query->num_rows();
        }
        if($active == TRUE) {
            $this->db->where('status', 1);
        } else {
            $this->db->where('status', 0);
        }
        return $this->children()->num_rows();
    }

    /**
     * @param $db
     * @param $child_id
     *
     * @return int|string
     */
    public function totalRecords($db, $child_id)
    {
        $this->db->where('child_id', $child_id);
        return $this->db->count_all_results($db);
    }

    /**
     * @param $user_id
     * @param $child_id
     *
     * @return bool
     */
    public function belongsTo($user_id, $child_id)
    {
        $this->db->where('child_id', $child_id);
        $this->db->where('user_id', $user_id);
        $query = $this->db->get('child_parents');
        if($query->num_rows() > 0) {
            return TRUE;
        } else {
            return FALSE;
        }
    }

    /*
     * get_child
     * get all child information
     */

    public function register($getID = FALSE,$daycare_id)
    {        
        $UNLIMITED = "Unlimited";
        $daycare_details = $this->db->get_where('daycare',array(   //daycare details
            'daycare_id' => $daycare_id
        ));
        $daycare = $daycare_details->row_array();

        $plans = $this->session->userdata('plans');
        $managers = $this->db->select('us.*,ug.*')
                    ->where('daycare_id', $daycare['id'])
                    ->from('users as us')
                    ->join('users_groups as ug', 'ug.user_id = us.id')
                    ->get()->result_array(); //get all managers of daycare

        $childs = $this->db->get_where('children',array(
            'daycare_id' => $daycare['id']
        ))->result_array(); // get all childrenof daycare
        
        $child_count = count($childs); //child count
        $plan_child_count = $plans['children']; //child number in plan
        
        if(is('parent') || is('staff')){
            $status = 0;
        }else{
            $status = $this->input->post('status');
        }
        $data = [
            'nickname' => $this->input->post('nickname'),
            'first_name' => $this->input->post('first_name'),
            'last_name' => $this->input->post('last_name'),
            'national_id' => encrypt($this->input->post('national_id')),
            'bday' => $this->input->post('bday'),
            'gender' => $this->input->post('gender'),
            'last_update' => date_stamp(),
            'status' => $status,
            'created_at' => date_stamp(),
            'user_id' => $this->user->uid(),
            'daycare_id' => $daycare['id'],
            'religion' => $this->input->post('religion'),
            'ethnicity' => $this->input->post('ethnicity'),
            'birthplace' => $this->input->post('birthplace'),
            'blood_type' => $this->input->post('blood_type'),
        ];
        
        //check subscription plan
        if(($child_count < $plan_child_count) || ($plan_child_count == $UNLIMITED)){
            $this->db->insert('children', $data);
            $last_id = $this->db->insert_id();
            if($this->db->affected_rows() > 0) {
                flash('success', lang('request_success'));
            } else {
                return FALSE;
            }
            if(is('staff')){
                foreach($managers as $mg){
                    if($mg['group_id'] == 2){
                        $data = [                    
                            'to' => $mg['email'],
                            'subject' => lang('child_subject'),
                            'logo' => $this->session->userdata('company_logo'),
                            'name' => $mg['first_name'] . " " . $mg['last_name'],
                        ];
                        $child_name = $this->input->post('first_name') . " " . $this->input->post('last_name');
                        $message = sprintf(lang('assigned_child'),$child_name);     
                        $data['message'] = $message;
                        send_email($data);
                    }
                }
            }
            //assign child to user if this user is parent
            if(is('parent')) {            
                $data2 = [
                    'child_id' => $last_id,
                    'user_id' => $this->user->uid(),
                ];
                $this->db->insert('child_parents', $data2);
                $user_details = $this->db->get_where('users',array(
                    'daycare_id' => $daycare['id']
                ));
                $users = $user_details->result();
        
                $child_name = $this->input->post('first_name') . " " .$this->input->post('last_name');
                $parent_id = $this->session->userdata('user_id');
                foreach ($users as $row) {
                    if($parent_id == $row->id){
                        if($row->first_name == ''){
                            $parent_name = $row->name;
                        }else{
                            $parent_name = $row->first_name;
                        }
                        if($row->first_name == ''){
                            $name = $row->name;
                        }else{
                            $name = $row->first_name . " " . $row->last_name;
                        }
                        $user_group = $this->db->get_where('users_groups',array(
                            'user_id' => $row->id
                        ));
                        $group_row = $user_group->row();
                        $group = $group_row->group_id;
                        if($group == 1 || $group == 2){
                            $message = "A child " . $child_name . " added to daycare by parent " . $parent_name .".";
                            $data = [
                                'subject' => 'Child Register',
                                'to' => $row->email,
                                'message' => $message,
                                'logo' => $this->session->userdata('company_logo'),
                                'name' => $name,
                            ];
                            send_email($data);
                        }
                    }                
                }
            }
    
            //log event
            logEvent($id = NULL,"Added child {$this->input->post('first_name')} {$this->input->post('last_name')}",$care_id = NULL);
    
            if($getID) {
                return $last_id;
            }    
            return TRUE;
        }else{
            $error = "error";
            return $error;
        }  
    }

    /*
     * get_child_info
     *
     */

    public function update_child($child_id,$daycare_id)
    {
        $this->load->config('email');
        $this->load->library('email');

        //get children detail to check child status
        $child_details = $this->db->get_where('children', array(
            'id' => $child_id
        ));
        $children = $child_details->row_array();

        $status = $this->input->post('status');
        $first_name = $this->input->post('first_name');
        $data = [
            'nickname' => $this->input->post('nickname'),
            'first_name' => $first_name,
            'last_name' => $this->input->post('last_name'),
            'bday' => $this->input->post('bday'),
            'national_id' => encrypt($this->input->post('national_id')),
            'blood_type' => $this->input->post('blood_type'),
            'gender' => $this->input->post('gender'),
            'status' => $status,
            'ethnicity' => $this->input->post('ethnicity'),
            'religion' => $this->input->post('religion'),
            'birthplace' => $this->input->post('birthplace'),
            'last_update' => date_stamp(),
        ];
        $this->db->where('id', $child_id);
        $this->db->update('children', $data);

        //Send email to parent if child status changed from inactive to active
        if($status != $children['status']){            
            $get_parent = $this->db->get_where('child_parents',array(
                'child_id' => $child_id
            ));
            $get_parent_id = $get_parent->row_array();

            $parent_details = $this->db->get_where('users',array(
                'id' => $get_parent_id['user_id']
            ));

            $parent = $parent_details->row_array();
            
            if($parent !== NULL){
                $email_data = array(
                    'first_name' => $parent['first_name'],
                    'last_name' => $parent['last_name'],
                    'logo' => $this->session->userdata('company_logo'),
                    'child_first_name' => $first_name,
                    'child_last_name' => $this->input->post('last_name'),
                    'daycare_id'     => $daycare_id,
                    'child_status' => $status
                );
                $this->email->set_mailtype('html');
                $from = $this->config->item('smtp_user');
                $to = $parent['email'];
                $this->email->from($from, 'Daycare');
                $this->email->to($to);
                $this->email->subject('Child Register Successful');
    
                $body = $this->load->view('custom_email/child_register_email', $email_data, true);
                $this->email->message($body);        //Send mail
                if ($this->email->send()) {
                    $this->session->set_flashdata("verify_email", "Please check your email to confirm your account.");
                }
            }
        }       
        if($this->db->affected_rows() > 0) {
            //log event
            logEvent($id = NULL,"Updated child {$first_name}",$care_id = NULL);

            flash('success', lang('request_success'));
        } else {
            flash('warning', lang('no_change_to_db'));
        }
    }

    /*
     * add emergency contact to db
     */

    public function createPickup($id)
    {
        $photo = $this->pickup_photo();        
        if(isset($photo['error'])){           
            $error =  $photo['error'];
            return $error;
        }
        if($_FILES['photo']['name'] !== ''){
            $image = $photo['photo']['file_name'];
        }else{
            $image = '';
        }
        $first_name = $this->input->post('first_name');
        $data = [
            'child_id' => $id,
            'first_name' => $first_name,
            'last_name' => $this->input->post('last_name'),
            'cell' => $this->input->post('cell'),
            'other_phone' => $this->input->post('other_phone'),
            'address' => $this->input->post('address'),
            'pin' => $this->input->post('pin'),
            'photo' => $image,
            'relation' => $this->input->post('relation'),
            'user_id' => $this->user->uid(),
            'created_at' => date_stamp(),
        ];        
        $this->db->insert('child_pickup', $data);
        $insert_id = $this->db->insert_id();
        if($this->db->affected_rows() > 0) {
            //log event
            logEvent($user_id = NULL,"Added pickup contact {$first_name} for child {$this->child->child($id)->first_name}",$care_id = NULL);
            $this->parent->notifyParents($id, lang('pickup_added_email_subject'), sprintf(lang('pickup_added_email_message'), $data['first_name'].' '.$data['last_name']));
            return $insert_id;
        } else {
            return FALSE;
        }
    }

    public function pickup_photo(){
        $upload_folder = './assets/uploads/pickup';        
        if(!file_exists($upload_folder)) {
            mkdir($upload_folder, 0777, TRUE);
            chmod($upload_folder, 0777);
        }      
        $config['upload_path'] = $upload_folder;
        $config['allowed_types'] = 'jpeg|jpg|png';
        $config['max_size'] = 2000;
        $config['max_width'] = 1500;
        $config['max_height'] = 1500;    
        $config['encrypt_name'] = TRUE;

        $this->load->library('upload', $config);        
        if (!$this->upload->do_upload('photo')) {
            $error = array('error' => $this->upload->display_errors());            
            if ($errors = $this->lang->line('upload_no_file_selected')) {
                $errors = '';
            }
            if ($errors == '') {
                return true;
            } else {
                flash('error', $errors);
                return false;
            }
        }else {
            $data = array('photo' => $this->upload->data());
            return $data;
        }
    }

    /**
     * @param $child_id
     *
     * @return bool
     */
    public function check_in($child_id)
    {
        $data = [
            'child_id' => $child_id,
            'in_guardian' => $this->input->post('in_guardian'),
            'time_in' => date_stamp(),
            'in_staff_id' => $this->user->uid(),
        ];
        $this->db->insert('child_checkin', $data);

        if($this->db->affected_rows() > 0) {
            //mark as checked in
            $this->db->where('id', $child_id)->update('children', ['checkin_status' => 1]);

            $this->parent->notify_check_in($child_id, $this->input->post('in_guardian'));

            logEvent($id = NULL,"Added checked in for {$this->child($child_id)->first_name}",$care_id = NULL);
            return TRUE;
        }
        return FALSE;
    }

    /**
     * @param $child_id
     *
     * @return bool
     */
    public function check_out($child_id)
    {
        $data = [
            'out_guardian' => $this->input->post('out_guardian'),
            'time_out' => date_stamp(),
            'out_staff_id' => $this->user->uid(),
        ];

        $this->db->where('child_id', $child_id)->where('time_out', NULL)->update('child_checkin', $data);

        if($this->db->affected_rows() > 0) {
            //mark as checked out
            $this->db->where('id', $child_id)->update('children', ['checkin_status' => 0]);

            $this->parent->notify_check_out($child_id, $this->input->post('out_guardian'));

            logEvent($id = NULL,"Added checked out for {$this->child($child_id)->first_name}",$care_id = NULL);
            return TRUE;
        }
        return FALSE;
    }

    /**
     * Determine if child was checked in a given day
     *
     * @param      $id
     * @param bool $date
     *
     * @return bool
     */
    public function checkedIn($id, $date = FALSE, $checkedOut = FALSE)
    {
        if($checkedOut == FALSE) {
            $this->db->where('time_out', NULL);
        }

        if($date !== FALSE) {
            if(valid_date($date)) {
                $d = new DateTime($date);
                $date = $d->format('Y-m-d ');
                $this->db->where('DATE(time_in)', $date);
            }
        }
        $this->db->where('child_id', $id);
        $this->db->from('child_checkin');
        $query = $this->db->count_all_results();
        if(empty($query)) { //child is out
            return FALSE;
        } else { //child is in
            return TRUE;
        }
    }

    /**
     * @param      $id
     * @param null $date
     *
     * @return mixed
     */
    public function checkinCounter($id, $date = NULL)
    {
        $count = '0.00';
        if($date == NULL || $date == 'today') {
            $date = date('Y-m-d');
        }

        //limit checkin count to daily only
        if(session('company_daily_checkin') == 1) {
            $this->db->where('DATE(time_in)', $date);
        }

        $this->db->where('child_id', $id);
        $this->db->where('time_out', NULL);
        $res = $this->db->get('child_checkin');
        if($res->num_rows() > 0) {
            $result = $res->row();
            $count = checkinTimer($result->time_in, date('Y-m-d H:i:s'))->h.' '.lang('hrs').
                ' '.checkinTimer($result->time_in, date('Y-m-d H:i:s'))->i.' '.lang('mins');
        }
        //
        return $count;
    }

    /**
     * Checkin status info for a child
     *
     * @param      $id
     * @param      $item
     * @param bool $date
     *
     * @return array|mixed
     */
    public function checkedInLog($id, $item, $date = FALSE)
    {
        if($date !== FALSE) {
            if(valid_date($date)) {
                $d = new DateTime($date);
                $date = $d->format('Y-m-d ');
                $this->db->where('DATE(time_in)', $date);
            }
        }

        $this->db->where('time_out', NULL);
        $this->db->where('child_id', $id);
        $row = $this->db->get('child_checkin')->row();

        if(count((array)$row) > 0) {
            $data = [
                'in_guardian' => $row->in_guardian,
                'date_in' => format_date($row->time_in, FALSE),
                'time_in' => format_time($row->time_in),
                'timer' => $this->checkinCounter($id),
            ];
            return $data[$item];
        }
        return [];
    }

    /**
     * returns last time child was checked out
     *
     * @param $id
     *
     * @return false|mixed|string
     */
    public function lastCheckedOut($id)
    {
        $this->db->limit(1);
        $this->db->select('child_id,time_out');
        $this->db->where('child_id', $id);
        $this->db->where('time_out IS NOT NULL', NULL, FALSE);
        $this->db->order_by('time_out', 'DESC');
        $row = $this->db->get('child_checkin')->row();
        if(count((array)$row) > 0) {
            return format_date($row->time_out);
        }

        return lang('No record found');
    }

    /**
     * @param      $id
     * @param null $date
     *
     * @return mixed
     */
    public function attendance($id, $date = NULL)
    {
        $this->db->where('child_id', $id);
        if($date !== NULL) {
            $this->db->where('DATE(time_in)', $date);
        }

        $this->db->order_by('time_in', 'DESC');
        return $this->db->get('child_checkin');
    }

    /**
     * @param $id
     *
     * @return mixed
     */
    public function countAllergies($id)
    {
        $this->db->where('child_id', $id);
        $this->db->from('child_allergy');
        return $this->db->count_all_results();
    }

    /**
     * @param $id
     *
     * @return mixed
     */
    public function countMeds($id)
    {
        $this->db->where('child_id', $id);
        $this->db->from('child_meds');
        return $this->db->count_all_results();
    }

    /**
     * @param $id
     *
     * @return int
     */
    public function roomCount($id, $type = 'children')
    {
        $this->db->where('room_id', $id);
        if($type == 'children') {
            $res = $this->db->count_all_results('child_room');
        } else {
            $res = $this->db->count_all_results('child_room_staff');
        }
        if(count((array)$res) > 0) {
            return $res;
        }

        return 0;
    }

    /**
     * @param $photo
     *
     * @return string
     */
    public function photo($photo)
    {
        if(!empty($photo)) {
            $photo = 'assets/uploads/children/'.$photo;
        } else {
            $photo = 'assets/img/content/no-image.png';
        }

        return base_url($photo);
    }

    public function uploadPhoto($id = '')
    {
        $upload_path = './assets/uploads/children';
        $upload_db = 'children';
        if(!file_exists($upload_path)) {
            mkdir($upload_path, 0777, TRUE);
            chmod($upload_path, 0777);
        }

        if($id == '') {
            return FALSE;
        }

        $config = [
            'upload_path' => $upload_path,
            'allowed_types' => 'gif|jpg|png|jpeg|svg',
            //'max_size'      => '100',
            'encrypt_name' => TRUE,
        ];
        $this->load->library('upload', $config);
        if(!$this->upload->do_upload()) {
            return FALSE;
        }

        $this->db->where('id', $id);

        foreach ($this->db->get($upload_db)->result() as $r) {
            if('' !== $r->photo):
                unlink($upload_path.'/'.$r->photo);
                $data['photo'] = '';
                $this->db->where('id', $id);

                $this->db->update($upload_db, $data);
            endif;
        }
        //upload new photo
        $upload_data = $this->upload->data();
        $data_ary = [
            'photo' => $upload_data['file_name'],
        ];
        $this->db->where('id', $id);
        $this->db->update($upload_db, $data_ary);
        $data = ['upload_data' => $upload_data];

        $this->conf->photoResize([
            'image' => $upload_path.'/'.$data_ary['photo'],
            'width' => 150,
            'height' => 150,
        ]);

        if($data) {
            return TRUE;
        }

        return FALSE;
    }
}
