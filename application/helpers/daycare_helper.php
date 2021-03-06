<?php
/**
 * @package     carepro
 * @copyright   2017 A&M Digital Technologies
 * @author      John Muchiri
 * @link        https://amdtllc.com
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */
/**
 * easy date stamp for database entry
 * return date time stamp
 *
 * @return false|string
 */
function date_stamp()
{
    return date('Y-m-d H:i:s');
}

/**
 * format date based on config settings
 *
 * @param $date
 *
 * @return false|string
 */
function format_date($date, $time = TRUE, $timestamp = FALSE)
{
    if($timestamp == TRUE)
        $date = date('Y-m-d H:i:s', $date);

    $format = session('company_date_format');
    if($format == "")
        return date('d M Y H:ia', strtotime($date));

    if($time == FALSE)
        return date('d M Y', strtotime($date));
    return date($format, strtotime($date));
}

function format_time($time, $timestamp = FALSE)
{
    if($timestamp == FALSE)
        $time = strtotime($time);

    return date('h:ia', $time);
}

/**
 * set flash messages for next page load
 *
 * @param string $type
 * @param string $msg
 */
function flash($type = "", $msg = "")
{
    switch ($type) {
        case 'danger':
        case 'error':
            $icon = 'exclamation';
            break;
        case 'success':
            $icon = 'check';
            break;
        case 'info':
            $icon = 'info';
            break;
        case 'warning':
            $icon = 'warning';
            break;
        default:
            $icon = 'info';
            break;
    }
    if($type == "error") $type = "danger";

    $ci = &get_instance();
    if(validation_errors()) {
        if($msg == "") {
            $e = validation_errors('<div class="notice alert alert-danger alert-dismissable fade show" role="alert"> <span class="fa fa-exclamation-triangle"></span> ', '</div>');
            $notice = $e;
            $type = 'danger';
            $icon = 'danger';
        }
    }
    else {
        $notice = '<div class="notice alert alert-'.$type.' alert-dismissable fade show" role="alert">';
        $notice .= '<a href="#" class="close" data-dismiss="alert" aria-label="close">×</a>';
        $notice .= '<span class="fa fa-info"></span> '.$msg;
        $notice .= '</div>';
    }

    $ci->session->set_flashdata('notice', $notice);
    $ci->session->set_flashdata('type', $type);
    $ci->session->set_flashdata('icon', $icon);
}

/**
 * set session to redirect previous
 */
function setRedirect()
{
    $ci = &get_instance();
    if(isset($_SERVER['HTTP_REFERER'])) {
        $ci->session->set_userdata('last_page', $_SERVER['HTTP_REFERER']);
    }
    else {
        $ci->session->set_userdata('last_page', base_url());
    }
}

/**
 * @return mixed
 */
function last_page()
{
    return $_SERVER['HTTP_REFERER'];
}

/**
 * roles of users
 */
function user_roles()
{
    $roles = [
        'admin'   => 1,
        'manager' => 2,
        'staff'   => 3,
        'parent'  => 4,
        'owner'   => 5,
    ];
    return $roles;
}

/**
 * redirect to previous page
 */
function redirectPrev($msg = [], $tab = '', $type = 'info')
{
    $ci = &get_instance();

    if(!empty($msg)) {
        flash($type, $msg);
    }

    //dont redirect if json
    if($ci->input->is_ajax_request()) {
        die();
    }
    else {
        if(!empty($tab))
            $tab = '#'.$tab;

        redirect($ci->session->userdata('last_page').$tab);
    }
}

function redirectBack($msg = [], $type = 'info', $tab = '')
{
    redirectPrev($msg, $tab, $type);
}

/**
 * Check if user is in a group
 *
 * @param $group
 *
 * @return bool
 */
function is($group)
{
    $ci = &get_instance();
    //save user role to session to reduce database calls
    //    $role=$ci->session->userdata('role');
    //    if($role){
    //        if(is_array($group)){
    //            $found = 0;
    //            foreach($group as $g){
    //                if(in_array($g,$role))
    //                    $found=1;
    //            }
    //            if($found ==1)
    //                return true;
    //        }else{
    //            if(in_array($group,$role))
    //                return true;
    //        }
    //    }

    //session has failed so we continue
    if(logged_in())
        if($ci->ion_auth->in_group($group))
            return TRUE;

    return FALSE;
}

function send_email($data)
{
    $ci = &get_instance();
    $ci->load->config('email');
    $ci->load->library('email');

    $ci->email->set_mailtype('html');

    $ci->email->from(isset($data['from']) ? $data['from'] : config('company', 'email'), 'Daycare');
    $ci->email->to(isset($data['to']) ? $data['to'] : config('company', 'email'));
    $ci->email->subject($data['subject']);

    $template = isset($data['template']) ? $data['template'] : 'report_activity_email';

    $body = $ci->load->view('custom_email/'.$template, $data, TRUE);

    $ci->email->message($body);
    if($ci->email->send()) {
        return TRUE;
    }
    return FALSE;
}

/**
 * check if authenticated or send to login
 *
 * @return bool
 */
function auth($redirect = FALSE)
{
    $ci = &get_instance();
    $parent = $ci->uri->segment(1);
    if($parent === "parents") {
        $ci->session->set_userdata("users", "parent");
    }
    if(logged_in() == TRUE) {
        return TRUE;
    }
    else {
        if($redirect) {
            redirect('auth/login', 'refresh');
        }
        return FALSE;
    }
}

/**
 * check if you is in requested group
 *
 * @param $id
 * @param $group
 *
 * @return bool
 */
function in_group($id, $group)
{
    $ci = &get_instance();
    $query = $ci->db
        ->select('id')
        ->where('users_groups.user_id', $id)
        ->where('groups.name', $group)
        ->from('groups')
        ->join('users_groups', 'users_groups.group_id=groups.id')
        ->count_all_results();

    if($query > 0)
        return TRUE;
    return FALSE;
}

/**
 * @param $option
 * @param $value
 *
 * @return bool|string
 */
function selected_option($option, $value)
{
    if($option == $value) {
        return 'selected';
    }
    return FALSE;
}

/**
 * @param $option
 * @param $value
 *
 * @return bool|string
 */
function checked_option($option, $value)
{
    if($option == $value) {
        return 'checked';
    }
    return FALSE;
}

function related($db, $field1, $value1, $field2, $value2)
{
    $ci = &get_instance();
    $res = $ci->db->where($field1, $value1)
                  ->where($field2, $value2)
                  ->get($db)->result();
    if(count((array)$res) > 0) {
        return TRUE;
    }
    return FALSE;
}

/*
* encrypt
* encrypt text
* @params string
* @return string
*/
function encrypt($msg)
{
    $ci = &get_instance();
    $ci->conf->check_encrypt_key();
    return $ci->encryption->encrypt($msg);
}

/*
* decrypt
* decrypt text
* @params string
* @return string
*/
function decrypt($msg)
{
    $ci = &get_instance();
    $ci->conf->check_encrypt_key();
    return $ci->encryption->decrypt($msg);
}

/**
 * @return bool
 */
function logged_in()
{
    $ci = &get_instance();
    if($ci->ion_auth->logged_in() == TRUE)
        return TRUE;
    return FALSE;
}

/*
* log events to database
* logs changes made by users
* @param string
* @return boolean
*/
function logEvent($user_id = NULL, $event, $daycare_id = NULL)
{
    $ci = &get_instance();

    if($user_id === NULL) {
        $first_name = $ci->session->userdata('first_name');
        if($first_name == '') {
            $user_id = $ci->session->userdata('name');
        }
        else {
            $user_id = $ci->session->userdata('first_name')." ".$user_id = $ci->session->userdata('last_name');
        }
    }
    if($daycare_id === NULL) {
        $daycare_id = $ci->session->userdata('daycare_id');
    }
    $data = [
        'user_name'  => $user_id,
        'date'       => date("Y-m-d H:i:s", time()),
        'daycare_id' => $daycare_id,
        'event'      => $event,
    ];
    if($ci->db->insert('event_log', $data))
        return TRUE;
    return FALSE;
}

/**
 * Allow specific group to access
 *
 * @param $g
 *
 * @return bool
 */
function allow($group)
{
    $ci = &get_instance();
    auth(TRUE);

    //check demo
    if(session('daycare_id') == 1 && ENVIRONMENT == 'production') {
        demo(TRUE);
    }

    if($ci->ion_auth->in_group($group)) {
        return TRUE;
    }
    else {
        flash('danger', lang('access_denied'));
        if($ci->input->is_ajax_request()) {
            return 'error';
        }
        redirectPrev();
        exit();
    }
}

//convert date format to 4 june 2019 and time to am pm format
function event_log_date($log_date)
{
    $time = new DateTime($log_date);
    $date = $time->format('n-j-Y');
    $dateObj = DateTime::createFromFormat('m-d-Y', $date);
    $date_time = $dateObj->format('d M Y')." ".$time->format('g:ia');
    return $date_time;
}

function page($page, $data = [])
{
    $ci = &get_instance();
    $data['page'] = $page;
    if(is('parent')) {
        $ci->load->view('layouts/template', $data);
    }
    else {
        $ci->load->view('layouts/template', $data);
    }
}

function dashboard_page($page, $data = [], $daycare_id)
{
    $ci = &get_instance();
    $data['page'] = $page;
    $data['daycare_id'] = $daycare_id;
    if(is('parent')) {
        $ci->load->view('layouts/template', $data);
    }
    else {
        $ci->load->view('layouts/template', $data);
    }
}

function parents_page($page, $data = [])
{
    $ci = &get_instance();
    $data['page'] = $page;
}

function demo($state = FALSE)
{
    $allowed_demo_routes = [
        'child',
        'children',
        'rooms',
        'calendar',
        'messaging',
    ];

    $ci = &get_instance();

    $seg1 = $ci->uri->segment(1);
    $seg2 = $ci->uri->segment(2);
    $seg3 = $ci->uri->segment(3);
    $seg4 = $ci->uri->segment(4);

    $deleteLinks = [
        $seg1,
        $seg2,
        $seg3,
        $seg4,
    ];

    if($ci->user->uid() > 0) {
        if($state == TRUE) {
            $ci->load->helper('language');

            //prevent all post methods
            if(!is('super')) {
                if($ci->input->server('REQUEST_METHOD') == 'POST') {

                    if(in_array($seg1, $allowed_demo_routes)) {
                        //good
                    }
                    else {
                        flash('danger', lang('feature_disabled_in_demo'));
                        redirectPrev();
                    }
                }

                //prevent delete
                if(in_array('delete', $deleteLinks)) {
                    flash('danger', lang('feature_disabled_in_demo'));
                    redirectPrev();
                }
                if(
                    strstr($seg1, 'delete')
                    || strstr($seg2, 'delete')
                    || strstr($seg3, 'delete')
                    || strstr($seg4, 'delete')
                    || strstr($seg4, 'remove')
                ) {
                    flash('danger', lang('feature_disabled_in_demo'));
                    redirectPrev();
                }
            }
        }
    }
}

/*
* check if system is in maintenance mode
* @params 0
* redirect to prev
*/
function maintenance()
{
    $ci = &get_instance();

    if(session('company_maintenance_mode') == 1 && !is('admin')) {
        $ci->load->helper('language');
        die('<div style="color:red; font-size:26px; text-align:center; font-family:Tahoma; width: 600px; margin: 0 auto;">'
            .lang('maintenance_mode').'
</div>');
    }
}

if(!function_exists('ddo')) {
    /**
     * dump and die
     *
     * @param $array
     */
    function ddo($array)
    {
        print_r($array);
        die();
    }
}

/**
 * @param $num
 *
 * @return mixed
 */
function uri_segment($num)
{
    $ci = &get_instance();
    return $ci->uri->segment($num);
}

/**
 * @param $page
 *
 * @return string
 */
function set_active($page)
{
    $uri = uri_string();
    if(is_array($page)) {
        $uri = uri_segment(1);
        if(in_array($uri, $page))
            return 'active';
    }
    return ($page == $uri) ? 'active' : '';
}

function moneyFormat($amount, $symbol = FALSE)
{
    $amount = str_replace(',', '', $amount);
    $amount = str_replace(session('company_currency_symbol'), '', $amount);

    if($symbol == TRUE)
        return session('company_currency_symbol').number_format((float)$amount, 2);

    return number_format((float)$amount, 2);
}

function authorizedToChild($staff_id, $child_id)
{
    if(is([
        'admin',
        'manager',
    ]))
        return TRUE;
    $ci = &get_instance();

    //test staff assigment
    $staff = $ci->db
        ->from('child_room')
        ->join('child_room_staff', 'child_room_staff.room_id=child_room.room_id')
        ->where('child_room_staff.user_id', $staff_id)
        ->where('child_room.child_id', $child_id)
        ->count_all_results();

    if($staff > 0)
        return TRUE;

    //test parent
    $parent = $ci->db->from('child_parents')
                     ->where('child_id', $child_id)
                     ->where('user_id', $staff_id)
                     ->count_all_results();
    if($parent > 0)
        return TRUE;

    return FALSE;
}

function valid_date($date, $format = 'Y-m-d')
{
    $d = DateTime::createFromFormat($format, $date);
    //    return $d && $d->format($format) === $date;
    return TRUE;
}

/**
 * @param $in
 * @param $out
 *
 * @return string
 */
function checkinTimer($in, $out)
{
    $start = new DateTime($in);
    $end = new DateTime($out);

    $timeDiff = $end->diff($start);
    //    $strDiff    = $timeDiff->h . " Hours, " . $timeDiff->i . " Minutes";

    if($timeDiff->d > 0)
        $timeDiff->h = $timeDiff->h + (2 * 24);

    return $timeDiff;
}

function time_difference($in, $out)
{
    return checkinTimer($in, $out);
}

function sensitive_options()
{
    return [
        'smtp_user'      => '',
        'smtp_pass'      => '',
        'stripe_pk_live' => '',
        'stripe_sk_live' => '',
        'stripe_pk_test' => '',
        'stripe_sk_test' => '',
        'smtp_user',
        'smtp_pass',
        'stripe_pk_live',
        'stripe_sk_live',
        'stripe_pk_test',
        'stripe_sk_test',
    ];
}

function general_options()
{
    return [
        'name'                  => 'CarePRO',
        'slogan'                => 'daycare management',
        'email'                 => 'app@admin.com',
        'phone'                 => '',
        'fax'                   => '',
        'street'                => '',
        'street2'               => '',
        'city'                  => '',
        'state'                 => '',
        'postal_code'           => '',
        'country'               => 'USA',
        'timezone'              => 'America/New_York',
        'google_analytics'      => '',
        'currency_symbol'       => '$',
        'currency_abbreviation' => 'USD',
        'date_format'           => 'm/d/Y h:ia',
        'allow_registration'    => 0,
        'allow_reset_password'  => 1,
        'enable_captcha'        => 0,
        'demo_mode'             => 0,
        'maintenance_mode'      => 0,
        'use_smtp'              => 0,
        'smtp_host'             => '',
        'smtp_port'             => '',
        'logo'                  => 'logo.png',
        'invoice_logo'          => 'invoice_logo.png',
        'paypal_email'          => '',
        'paypal_locale'         => 'US',
        'page'                  => 'settings',
        'daily_checkin'         => 1,
        'tawkto_embed_url'      => '',
        'login_bg_image'        => 'login-bg-02.jpg',
        'invoice_terms'         => 'Invoice due on receipt. Thank you for your business',
        'facility_id'           => '',
        'tax_id'                => '',
        'lockscreen_timer'      => '',
        'custom_css'            => '',
        'hours_start'           => '',
        'hours_end'             => '',
        'stripe_enabled'        => 0,
    ];
}

function special_options()
{
    return array_merge(general_options(), sensitive_options());
}

function protected_special_option($option)
{
    if(in_array($option, special_options())) {
        return TRUE;
    }
    return FALSE;
}

/**
 * retrieve option in options table
 *
 * @param $name
 *
 * @return string
 */
function get_option($name, $default = '')
{
    $ci = &get_instance();
    $name = trim($name);
    if($ci->db->table_exists('options')) {
        $res = $ci->db->where('option_name', $name)
                      ->limit(1)
                      ->get('options');
    }
    else {
        return '';
    }
    if($res->num_rows() > 0) {
        $value = $res->row()->option_value;
        if(empty($value))
            return $default;

        $data = @unserialize($value);
        if($value === 'b:0;' || $data !== FALSE) {
            return unserialize($value);
        }
        else {
            return $value;
        }
    }
    return $default;
}

/**
 * sets an option in options table
 *
 * @param $name
 * @param $value
 *
 * @return bool
 */
function add_option($name, $value, $special = FALSE)
{
    if(empty($name))
        return FALSE;

    if($special == FALSE && protected_special_option($name)) {
        flash('error', sprintf(lang('You are using a protected option'), $name));
        return FALSE;
    }

    if(is_object($value))
        $value = clone $value;

    if(is_array($value))
        $value = serialize($value);

    $ci = &get_instance();
    $ci->db->insert('options', [
        'option_name'  => $name,
        'option_value' => $value,
    ]);
    if($ci->db->affected_rows() > 0) {
        reload_company();
        return TRUE;
    }
    return FALSE;
}

/**
 * updates an option in options table
 *
 * @param $name
 * @param $value
 *
 * @return bool
 */
function update_option($name, $value, $special = FALSE)
{
    if(empty($name))
        return FALSE;

    if(is_object($value))
        $value = clone $value;

    if(is_array($value))
        $value = serialize($value);

    $ci = &get_instance();
    $test = $ci->db->where('option_name', $name)->from('options')->count_all_results();

    if($test > 0) {
        $ci->db->where('option_name', $name)->update('options', ['option_value' => $value]);

        if($ci->db->affected_rows() > 0) {
            reload_company();
            return TRUE;
        }
    }
    else {

        add_option($name, $value, $special);
    }
    return FALSE;
}

/**
 * @param $name
 *
 * @return bool
 */
function remove_option($name)
{
    if(empty($name))
        return FALSE;

    if(protected_special_option($name)) {
        flash('error', sprintf(lang('You are using a protected option'), $name));
        return FALSE;
    }
    $ci = &get_instance();
    $ci->db->where('option_name', $name)->delete('options');
    reload_company();
    return TRUE;
}

function empty_option($name)
{
    $ci = &get_instance();
    $ci->db->where('option_name', $name)->update('options', ['option_value' => '']);
    reload_company();
    return TRUE;
}

function g_decor($name)
{
    switch ($name) {
        case 'admin':
            return 'danger';
            break;
        case 'manager':
            return 'success';
            break;
        case 'staff':
            return 'primary';
            break;
        case 'parent':
            return 'default';
            break;
        default:
            return 'warning';
            break;
    }
}

function blood_types()
{
    $types = [
        'A-',
        'A+',
        'B-',
        'B+',
        'AB-',
        'AB+',
        'O-',
        'O+',
    ];
    $res = [];
    foreach ($types as $type) {
        $res[$type] = $type;
    }
    return $res;
}

if(!function_exists('assets()')) {
    function assets($item = '')
    {
        return base_url().'assets/'.$item;
    }
}

function user_id()
{
    $ci = &get_instance();
    return $ci->session->userdata('user_id');
}

function set_flash($fields)
{
    $ci = &get_instance();

    if(is_array($fields)) {
        foreach ($fields as $field) {
            $ci->session->set_flashdata($field, $ci->input->post($field));
        }
    }
    else {
        $ci->session->set_flashdata($fields, $ci->input->post($fields));
    }
}

function is_checked_in($id, $date = FALSE, $checkedOut = FALSE)
{
    $ci = &get_instance();

    if($checkedOut == FALSE)
        $ci->db->where('time_out', NULL);

    if($date !== FALSE) {

        if(valid_date($date)) {
            $d = new DateTime($date);
            $date = $d->format('Y-m-d ');
            $ci->db->where('DATE(time_in)', $date);
        }
    }

    $ci->db->where('child_id', $id);
    $ci->db->from('child_checkin');
    $query = $ci->db->count_all_results();
    if(empty($query)) { //child is out
        return FALSE;
    }
    else { //child is in
        return TRUE;
    }
}

if(!function_exists('str_replace_first')) {
    function str_replace_first($from, $to, $content)
    {
        $from = '/'.preg_quote($from, '/').'/';

        return preg_replace($from, $to, $content, 1);
    }
}

/**
 * @param $item
 *
 * @return bool
 */
function session($item, $opts = '')
{
    if(is_array($item)) { //means we are requesting setting session
        $ci = &get_instance();
        $ci->session->set_userdata($item);
        return TRUE;
    }
    $ci = &get_instance();

    if(!empty($item) && array_key_exists(str_replace('company_', '', $item), general_options())) {

        return $ci->session->userdata($item);
    }

    elseif(!empty($item) && array_key_exists(str_replace('company_', '', $item), sensitive_options()))
        return get_option($item);

    else return $ci->session->userdata($item);
}

/**
 * @param $item
 */
function unset_userdata($item)
{
    $ci = &get_instance();
    if(is_array($item)) {
        foreach ($item as $i) {
            $ci->session->unset_userdata($i);
        }
    }
    else {
        $ci->session->unset_userdata($item);
    }
}

/**
 * @return array
 */
function default_payment_methods()
{
    return [
        'Cash',
        'Check',
        'Debit',
        'Money order',
        'PayPal',
        'Stripe',
    ];
}

function icon($name)
{
    return '<i class="fa fa-'.$name.'"></i>';
}

function enable_debug()
{
    $ci = &get_instance();
    $ci->output->enable_profiler(TRUE);
}

function disable_debug()
{
    $ci = &get_instance();
    $ci->output->enable_profiler(FALSE);
}

function reload_company()
{
    //todo check if user data has changed and refresh session
    $ci = &get_instance();
    $ci->session->unset_userdata('init_company');
    foreach (general_options() as $opt => $val) {
        $ci->session->unset_userdata($opt);
    }
}

function init_company()
{
    if(session('init_company') !== 1) {

        //query db once
        $company_data = [];
        foreach (general_options() as $opt => $val) {

            $value = get_option(str_replace('company_'.$opt, '', $opt));

            if(empty($value))
                continue;

            $company_data['company_'.$opt] = $value;
        }
        if(empty($company_data)) {
            if(is_cli()) {
                session_start();
            }
            else {
                die('Please complete installation');
            }
        }
        else {
            session(['init_company' => 1]);

            session($company_data);
        }
    }
}

function gravatar($email, $size = 50)
{
    return "https://www.gravatar.com/avatar/".md5(strtolower(trim($email)))."&s=".$size;
}

function is_childs_parent($user_id, $child_id)
{
    $ci = &get_instance();
    $res = $ci->db->where('user_id', $user_id)->where('child_id', $child_id)->get('child_parents')->row();
    if(empty($res)) return FALSE;
    return TRUE;
}

function daycare($id = '', $item = '')
{
    $ci = &get_instance();
    if(!is_numeric($id) && !empty(session('daycare_id'))) {
        $item = $id;
        $id = session('daycare_id');
    }

    $daycare = $ci->db->where('daycare.id', $id)
                      ->select('daycare.*,ds.*, a.address_line_1,a.address_line_2,a.phone,a.fax,a.city,a.state,a.zip_code,a.country')
                      ->join('daycare_settings as ds', 'daycare.id = ds.daycare_id', 'left')
                      ->join('address as a', 'daycare.address_id = a.id', 'left')
                      ->get('daycare')->row();

    if(empty($item))
        return $daycare;

    return $daycare->{$item};
}

function config($item, $value = '', $separator = '  ')
{
    $ci = &get_instance();
    if(is_array($value)) {
        $items = $ci->config->item($item);

        $res = '';
        if(is_array($items)) {
            foreach ($value as $v) {
                $res .= $items[$v].$separator.' ';
            }
        }

        return substr($res, 0, -2);
    }

    $items = $ci->config->item($item);

    if(is_array($items) && !empty($value)) {
        return $ci->config->item($item)[$value];
    }
    else {
        return $ci->config->item($item);
    }
}

function verify_captcha($response)
{
    if(empty($response) && isset($_POST['recaptcha_response']) && !empty($_POST['recaptcha_response'])) {
        $response = $_POST['recaptcha_response'];
    }

    $recaptcha_url = 'https://www.google.com/recaptcha/api/siteverify';
    $recaptcha_secret = config('recaptcha', 'secret');

    $recaptcha = file_get_contents($recaptcha_url.'?secret='.$recaptcha_secret.'&response='.$response);
    $recaptcha = json_decode($recaptcha);

    if($recaptcha->success && $recaptcha->score >= 0.5) return TRUE;

    return FALSE;
}