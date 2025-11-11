<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Chat_model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
        $this->load->database();
        $this->load->library(['ion_auth', 'form_validation']);
        $this->load->helper(['url', 'language']);
    }

    function make_me_online($id, $data)
    {
        $this->db->where('id', $id);
        if ($this->db->update('users', $data))
            return true;
        else
            return false;
    }

    function delete_msg($from_id, $msg_id)
    {

        $query = $this->db->query("SELECT * FROM chat_media WHERE message_id=$msg_id ");
        $data = $query->result_array();
        if (!empty($data)) {
            $abspath = getcwd();
            foreach ($data as $row) {
                unlink('assets/chats/' . $row['file_name']);
            }
            $this->db->delete('chat_media', array('message_id' => $msg_id));
        }

        if ($this->db->query("DELETE FROM messages WHERE from_id=$from_id AND id=$msg_id")) {
            return true;
        } else {
            return false;
        }
    }

    function get_members($user_id)
    {
        $this->db->select('id, username'); // Specify the columns to select
        $this->db->from('users');
        $this->db->where_in('id', $user_id);
        $this->db->order_by("username", "asc");
        $query = $this->db->get();
        return $query->result_array();
    }    

    function get_supporters()
    {
        $sql =
            "SELECT up.user_id as user_permission_id, up.role as user_role, u.id as userto_id, u.username, u.last_online, 
                m.* FROM users u
                LEFT JOIN user_permissions up ON up.user_id = u.id
                LEFT JOIN messages m ON m.to_id = u.id OR m.from_id = u.id
                WHERE up.user_id = u.id AND up.role = 3
                GROUP BY u.id";

        $query = $this->db->query($sql);
        $supporters =  $query->result_array();

        return $supporters;
    }

    function get_unread_msg_count($type, $from_id, $to_id)
    {
        $query1 = "SELECT count(id) as total FROM messages WHERE type='$type' AND is_read=1 AND from_id=$from_id AND to_id=$to_id";
        $query1 = $this->db->query($query1);
        $total = $query1->result_array();
        return $total[0]['total'];
    }

    function get_chat_history($from_id, $limit = '', $offset = '', $from_user = false)
    {
        $members = $this->db->select('m1.id, m1.from_id, m1.to_id, m1.is_read, m1.message, m1.type, m1.media, u.id AS opponent_user_id, u.image AS opponent_user_profile, u.username AS opponent_username, u.email, u.mobile, u.image, u.active, u.last_online')
            ->from('messages m1')
            ->join('users u', '(m1.from_id = ' . $from_id . ' AND u.id = m1.to_id) OR (m1.to_id = ' . $from_id . ' AND u.id = m1.from_id)', 'inner')
            ->where('m1.id = (SELECT MAX(m2.id) FROM messages m2 WHERE (m2.from_id = ' . $from_id . ' OR m2.to_id = ' . $from_id . ') AND ((m2.from_id = m1.from_id AND m2.to_id = m1.to_id) OR (m2.from_id = m1.to_id AND m2.to_id = m1.from_id)))')
            ->order_by('m1.id', 'desc')
            ->get()
            ->result_array();

        return $members;
    }

    function mark_msg_read($type, $from_id, $to_id)
    {
        if ($type == 'person') {
            if ($this->db->query("UPDATE messages SET is_read=0 WHERE type='$type' AND is_read=1 AND from_id=$from_id AND to_id=$to_id")) {
                return true;
            } else {

                return false;
            }
        }

    }


    function update_web_fcm($user_id, $fcm)
    {
        if ($this->db->query('UPDATE users SET web_fcm="' . $fcm . '" WHERE id=' . $user_id . ' '))
            return true;
        else
            return false;
    }

    function send_msg($data)
    {

        if ($this->db->insert('messages', $data))
            return $this->db->insert_id();
        else
            return false;
    }

    function get_msg_by_id($msg_id, $to_id, $from_id, $type)
    {

        $sql = "SELECT * FROM messages WHERE id='$msg_id' ";
        $query = $this->db->query($sql);
        $messages =  $query->result_array();
        $product = array();
        $i = 0;
        foreach ($messages as $message) {
            $product[$i] = $message;

            if ($type == 'person') {
                if ($to_id == $message['to_id']) {
                    $me_user = $this->switch_chat($message['from_id'], $type);

                    if (isset($me_user) && !empty($me_user)) {
                        $product[$i]['picture'] = $me_user[0]['username'];

                        $product[$i]['senders_name'] = $me_user[0]['username'];

                        $product[$i]['position'] = 'right';
                    } else {
                        return $responce['error'] = true;
                    }
                } else {
                    $oppo_user = $this->switch_chat($message['from_id'], $type);
                    $product[$i]['picture'] = $oppo_user[0]['username'];

                    $product[$i]['profile'] = $oppo_user[0]['profile'];

                    $product[$i]['senders_name'] = $oppo_user[0]['username'];

                    $product[$i]['position'] = 'left';
                }
            } else {

                // new group msg arrived and you have change here

                $oppo_user = $this->switch_chat($message['from_id'], 'person');
                $product[$i]['picture'] = $oppo_user[0]['username'];

                $product[$i]['senders_name'] = $oppo_user[0]['username'];

                if ($from_id == $message['from_id']) {
                    $product[$i]['position'] = 'right';
                } else {
                    $product[$i]['position'] = 'left';
                }
            }

            $i++;
        }
        return $product;
    }

    function load_chat($from_id, $to_id, $type = '',  $offset = '', $limit = '', $sort = '', $order = '', $search = '')
    {

        // $from_id is a group id when $type is = group 

        $search = ($search !== '' && $search !== '') ? " AND (`message` like '%" . $search . "%') " : "";

        if ($type == 'person') {
            $query1 = "SELECT count(id) as total FROM messages WHERE type='$type' AND ((from_id=$from_id AND to_id=$to_id) OR (from_id=$to_id AND to_id=$from_id)) ";
        } else {
            $query1 = "SELECT count(id) as total FROM messages ";
        }
        $query1 = $this->db->query($query1);
        $rowcount = $query1->result_array();
        $rowcount = $rowcount[0]['total'];

        if ($type == 'person') {
            $sql = "SELECT * FROM messages WHERE type='$type' AND ((from_id=$from_id AND to_id=$to_id) OR (from_id=$to_id AND to_id=$from_id)) ";
        } else {
            $sql = "SELECT * FROM messages";
        }


        $sql .= ($sort !== '' && $order !== '') ? " ORDER BY $sort $order " : "";
        $sql .= ($offset !== '' && $limit !== '') ? " Limit $offset,$limit " : "";



        $query = $this->db->query($sql);
        $messages =  $query->result_array();

        $product = array();
        $i = 0;

        foreach ($messages as $message) {
            $product['msg'][$i] = $message;
            $me_user = $this->switch_chat($message['from_id'], 'person');

            $product['msg'][$i]['picture'] = $me_user[0]['username'];

            $product['msg'][$i]['profile'] = base_url() . USER_IMG_PATH . $me_user[0]['image'];

            $product['msg'][$i]['senders_name'] = $me_user[0]['username'];

            $i++;
        }
        $product['total_msg'] = $rowcount;
        return $product;
    }

    function get_media($msg_id)
    {
        $query = $this->db->query("SELECT * FROM chat_media WHERE message_id=$msg_id ");

        return $query->result_array();
    }

    function add_file($data)
    {
        if ($this->db->insert('chat_media', $data))
            return $this->db->insert_id();
        else
            return false;
    }

    function switch_chat($user_or_group_id, $type)
    {

        if ($type == 'person') {
            $query = $this->db->query("SELECT * FROM users WHERE id=$user_or_group_id ");
        } else {
            $query = $this->db->query("SELECT * FROM chat_groups WHERE id=$user_or_group_id ");
        }

        $messages =  $query->result_array();
        return $messages;
    }

    function get_user_picture($user_id)
    {
        $query = $this->db->query("SELECT * FROM users WHERE id='$user_id' ");
        $messages =  $query->result_array();
        $picture = substr($messages[0]['first_name'], 0, 1) . '' . substr($messages[0]['last_name'], 0, 1);
        return $picture;
    }

    function get_web_fcm($user_id)
    {
        $query = $this->db->query("SELECT web_fcm FROM users WHERE id=$user_id ");
        return $query->result_array();
    }

    function add_media_ids_to_msg($msg_id, $media_id)
    {

        $query = $this->db->query('SELECT media FROM messages WHERE id=' . $msg_id . ' ');

        if (!empty($query)) {
            foreach ($query->result_array() as $row) {
                $product_ids = $row['media'];
            }
            $ids = !empty($product_ids) ? $product_ids . ',' . $media_id : $media_id;
        }

        if ($this->db->query('UPDATE messages SET media="' . $ids . '" WHERE id=' . $msg_id . ' '))
            return true;
        else
            return false;
    }

    function make_user_admin($workspace_id, $user_id)
    {

        // in this func we are adding users id in the workspace - data format 1,2,3 

        $query = $this->db->query('SELECT admin_id FROM workspace WHERE id=' . $workspace_id . ' ');

        if (!empty($query)) {
            foreach ($query->result_array() as $row) {
                $product_ids = $row['admin_id'];
            }
            $admin_id = $product_ids . ',' . $user_id;
        }

        if ($this->db->query('UPDATE workspace SET admin_id="' . $admin_id . '" WHERE id=' . $workspace_id . ' '))
            return true;
        else
            return false;
    }

    function remove_user_from_admin($workspace_id, $user_id)
    {

        // in this func we are adding users id in the workspace - data format 1,2,3 
        $query = $this->db->query('SELECT admin_id FROM workspace WHERE FIND_IN_SET(' . $user_id . ',`admin_id`) and id =' . $workspace_id . ' ');
        $result = $query->result_array();
        if (!empty($result)) {
            $admin_id = $result[0]['admin_id'];
            $admin_id = preg_replace('/\s+/', '', $admin_id);
            $admin_ids = explode(",", $admin_id);
            if (($key = array_search($user_id, $admin_ids)) !== false) {
                unset($admin_ids[$key]);
            }
            $admin_id = implode(",", $admin_ids);
            if ($this->db->query('UPDATE workspace SET admin_id="' . $admin_id . '" WHERE id=' . $workspace_id . ' '))
                return true;
            else
                return false;
        } else {
            return false;
        }
    }

    function remove_user_from_workspace($workspace_id, $user_id)
    {
        $this->remove_user_from_admin($workspace_id, $user_id);
        $query = $this->db->query('SELECT user_id FROM workspace WHERE FIND_IN_SET(' . $user_id . ',`user_id`) and id =' . $workspace_id . ' ');
        $result = $query->result_array();
        if (!empty($result)) {
            $admin_id = $result[0]['user_id'];
            $admin_id = preg_replace('/\s+/', '', $admin_id);
            $admin_ids = explode(",", $admin_id);
            if (($key = array_search($user_id, $admin_ids)) !== false) {
                unset($admin_ids[$key]);
            }
            $admin_id = implode(",", $admin_ids);
            if ($this->db->query('UPDATE workspace SET user_id="' . $admin_id . '" WHERE id=' . $workspace_id . ' ')) {

                $query = $this->db->query('SELECT workspace_id FROM users WHERE FIND_IN_SET(' . $workspace_id . ',`workspace_id`) and id =' . $user_id . ' ');
                $result = $query->result_array();
                if (!empty($result)) {
                    $admin_id = $result[0]['workspace_id'];
                    $admin_id = preg_replace('/\s+/', '', $admin_id);
                    $admin_ids = explode(",", $admin_id);
                    if (($key = array_search($workspace_id, $admin_ids)) !== false) {
                        unset($admin_ids[$key]);
                    }
                    $admin_id = implode(",", $admin_ids);
                    if ($this->db->query('UPDATE users SET workspace_id="' . $admin_id . '" WHERE id=' . $user_id . ' ')) {
                        $query = $this->db->query('SELECT id,user_id FROM projects WHERE FIND_IN_SET(' . $user_id . ',`user_id`) and workspace_id =' . $workspace_id . ' ');
                        $results = $query->result_array();
                        if (!empty($results)) {
                            foreach ($results as $result) {
                                $admin_id = $result['user_id'];
                                $id = $result['id'];
                                $admin_id = preg_replace('/\s+/', '', $admin_id);
                                $admin_ids = explode(",", $admin_id);
                                if (($key = array_search($user_id, $admin_ids)) !== false) {
                                    unset($admin_ids[$key]);
                                }
                                $admin_id = implode(",", $admin_ids);
                                $this->db->query('UPDATE projects SET user_id="' . $admin_id . '" WHERE id=' . $id . ' ');
                            }
                        }
                        return true;
                    } else {
                        return false;
                    }
                }
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    function get_user($user_id)
    {

        // $user_id is array of users ids 

        $this->db->from('users');
        $this->db->where_in('id', $user_id);
        $query = $this->db->get();
        return $query->result();
    }

    function get_user_array_responce($user_id)
    {

        // $user_id is array of users ids 

        $this->db->from('users');
        $this->db->where_in('id', $user_id);
        $query = $this->db->get();
        return $query->result_array();
    }

    function get_user_not_in_workspace($user_id)
    {

        // $user_id is array of users ids 

        $this->db->from('users');
        $this->db->where_not_in('id', $user_id);
        $query = $this->db->get();
        return $query->result();
    }

    function get_users_by_email($email)
    {

        $this->db->from('users');
        $this->db->where('`email` like "%' . $email . '%" or `first_name` like "%' . $email . '%" or `last_name` like "%' . $email . '%" ');
        $query = $this->db->get();
        return $query->result_array();
    }

    function get_users_by_email_for_add($email)
    {

        $this->db->from('users');
        $this->db->where('email', $email);
        $query = $this->db->get();
        return $query->result_array();
    }

    function get_user_by_id($user_id)
    {

        $this->db->from('users');
        $this->db->where('id', $user_id);
        $query = $this->db->get();
        return $query->result_array();
    }
}
