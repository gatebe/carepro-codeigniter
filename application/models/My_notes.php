<?php if(!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * @package     daycarepro app
 * @copyright   2018 A&M Digital Technologies
 * @author      John Muchiri
 * @link        https://amdtllc.com
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */
class My_notes extends CI_Model
{

    /**
     * @return bool
     */
    function store()
    {
        $child_id = $this->input->post('child_id');

        $data = array(
            'child_id' => $child_id,
            'title' => $this->input->post('title'),
            'content' => htmlspecialchars($this->input->post('note-content')),
            'category_id' => $this->input->post('category_id'),
            'tags' => implode(',', $this->input->post('tags')),
            'user_id' => $this->user->uid(),
            'created_at' => date_stamp()
        );

        $this->db->insert('child_notes', $data);

        if($this->db->affected_rows() > 0) {
            //log event
            logEvent("Added note for child ID: {$child_id}");
            //notify parents
            $this->parent->notifyParents($child_id, lang('note_created_email_subject'), sprintf(lang('note_created_email_message'), $this->child->first($child_id)->first_name));
            return true;
        }
        return false;
    }

    /**
     * @return bool
     */
    function destroy()
    {
        $this->db->where('id', $this->uri->segment(3));
        $this->db->delete('child_notes');
        if($this->db->affected_rows() > 0)
            return true;
        return false;
    }

    /**
     * @param $id
     *
     * @return string
     */
    function category($id)
    {
        $cat = $this->db->where('id', $id)->get('notes_categories')->row();
        if(count((array)$cat) > 0)
            return $cat->name;
        return '';
    }

    /**
     * @param $child_id
     *
     * @return bool
     */
    function createIncident()
    {
        $child_id = $this->input->post('child_id');
        $date_occurred = $this->input->post('date').' '.$this->input->post('time');
        $data = array(
            'child_id' => $child_id,
            'title' => $this->input->post('title'),
            'location' => $this->input->post('location'),
            'incident_type' => $this->input->post('incident_type'),
            'description' => $this->input->post('description'),
            'actions_taken' => $this->input->post('actions_taken'),
            'witnesses' => $this->input->post('witnesses'),
            'remarks' => $this->input->post('remarks'),
            'date_occurred' => $date_occurred,
            'user_id' => $this->user->uid(),
            'created_at' => date_stamp()
        );
        $this->db->insert('child_incident', $data);
        $noteID = $this->db->insert_id();

        if($this->db->affected_rows() > 0) {
            logEvent("Added incident report for child ID: {$child_id}");
            $this->parent->notifyParents($child_id, lang('incident_email_subject'), sprintf(lang('incident_email_message'), $this->child->get($child_id, 'name')));
            return $noteID;
        }
        return false;
    }

    /**
     * @param $id
     *
     * @return bool
     */
    function deleteIncident($id)
    {

        //delete photos
        $photos = $this->db->where('incident_id', $id)->get('child_incident_photos');
        if($photos->num_rows() > 0) {

            foreach ($photos->result() as $photo) {
                @unlink('./assets/uploads/photos/'.$photo->photo);
            }
            $this->db->where('incident_id', $id)->delete('child_incident_photos');

        }
        $this->db->where('id', $id);
        $this->db->delete('child_incident');
        if($this->db->affected_rows() > 0)
            return true;
        return false;

    }

    /**
     * @param $childID
     * @return string
     */
    function storeIncidentPhotos($childID)
    {
        $table = 'child_incident_photos';
        $upload_path = './assets/uploads/photos';
        if(!file_exists($upload_path)) {
            mkdir($upload_path, 755, true);
        }
        $config = array(
            'upload_path' => $upload_path,
            'allowed_types' => 'gif|jpg|png|jpeg|svg',
            'max_size' => '3048',
            'encrypt_name' => true,
        );
        $this->load->library('upload', $config);
        if(!$this->upload->do_upload('file')) {
            $msg = lang('request_error');
            $type = 'error';
        } else {
            $upload_data = $this->upload->data();
            $this->db->insert($table, [
                'incident_id' => $this->input->post('incident_id'),
                'photo' => $upload_data['file_name'],
                'child_id' => $childID,
                'user_id' => $this->user->uid(),
                'created_at' => date_stamp()
            ]);
            if($upload_data) {
                $msg = lang('request_success');
                $type = 'success';
            } else {
                $msg = lang('request_error');
                $type = 'error';
            }
        }
        return json_encode($msg, $type);
    }

    function deleteIncidentPhoto()
    {
        $photo = $this->db->where('id', $this->input->post('id'))->get('child_incident_photos')->row();
        @unlink('./assets/uploads/photos/'.$photo->photo);
        if($this->db->where('id', $this->input->post('id'))->delete('child_incident_photos'))
            return true;
        return false;
    }

    /**
     * @return array
     */
    function getNote()
    {
        $noteId = $this->input->post('note_id');
        $note = $this->db->where('id', $noteId)->get('child_notes')->row();
        $data = [
            'title' => $note->title,
            'content' => htmlspecialchars_decode($note->content),
            'user' => '<strong>'.lang('Staff').':</strong> '.$this->user->get($note->user_id, 'name'),
            'created_at' => format_date($note->created_at),
            'category' => '<strong>'.lang('Category').':</strong> '.$this->notes->category($note->category_id),
            'tags' => '<strong>'.lang('Tags').':</strong> '.$this->getTags($note->tags)
        ];

        return $data;
    }

    /**
     * @param $tags
     *
     * @return string
     */
    function getTags($tags)
    {
        $tags = explode(',', $tags);

        $str = '';
        foreach ($tags as $tag) {
            $str .= '<span class="label label-default">'.$tag.'</span> ';
        }
        return $str;
    }
}

?>