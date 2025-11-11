<?php
defined('BASEPATH') or exit('No direct script access allowed');
class Category_model extends CI_Model
{
    public function __construct()
    {
        $this->load->database();
        $this->load->library(['ion_auth', 'form_validation']);
        $this->load->helper(['url', 'language', 'function_helper']);
    }
    public function get_categories($id = NULL, $limit = '', $offset = '', $sort = 'row_order', $order = 'ASC', $has_child_or_item = 'true', $slug = '', $ignore_status = '', $seller_id = '')
    {

        $level = 0;
        if ($ignore_status == 1) {
            $where = (isset($id) && !empty($id)) ? ['c1.id' => $id] : ['c1.parent_id' => 0];
        } else {
            $where = (isset($id) && !empty($id)) ? ['c1.id' => $id, 'c1.status' => 1] : ['c1.parent_id' => 0, 'c1.status' => 1];
        }

        // Build the base query
        $this->db->select('c1.*');
        $this->db->from('categories c1');
        $this->db->where($where);

        if (!empty($slug)) {
            $this->db->where('c1.slug', $slug);
        }

        if ($has_child_or_item == 'false') {
            $this->db->join('categories c2', 'c2.parent_id = c1.id', 'left');
            $this->db->join('products p', 'p.category_id = c1.id', 'left');
            $this->db->group_start();
            $this->db->or_where(['c1.id ' => ' p.category_id ', ' c2.parent_id ' => ' c1.id '], NULL, FALSE);
            $this->db->group_end();
            $this->db->group_by('c1.id');
        }

        // Clone the query for counting before adding limit and offset
        $count_query = clone $this->db;
        $count_res = $count_query->count_all_results();



        // Continue with the main query
        if (!empty($limit) || !empty($offset)) {
            $this->db->limit($limit);
            $this->db->offset($offset);
        }


        $this->db->order_by((string)$sort, (string)$order);
        $parent = $this->db->get();
        $categories = $parent->result();


        $i = 0;
        foreach ($categories as $p_cat) {
            $categories[$i]->children = $this->sub_categories($p_cat->id, $level);
            $categories[$i]->text = output_escaping($p_cat->name);
            $categories[$i]->name = output_escaping($categories[$i]->name);
            $categories[$i]->state = ['opened' => true];
            $categories[$i]->icon = "jstree-folder";
            $categories[$i]->level = $level;
            $categories[$i]->relative_path = $categories[$i]->image;
            $categories[$i]->image = get_image_url($categories[$i]->image, 'thumb', 'sm');
            $categories[$i]->banner = get_image_url($categories[$i]->banner, 'thumb', 'md');
            $i++;
        }

        if (isset($categories[0])) {
            $categories[0]->total = $count_res;
        }

        return json_decode(json_encode($categories), 1);
    }


    // public function get_seller_categories($seller_id)
    // {
    //     $level = 0;
    //     $this->db->select('category_ids');
    //     $where = 'user_id = ' . $seller_id;
    //     $this->db->where($where);
    //     $result = $this->db->get('seller_data')->result_array();
    //     $count_res = $this->db->count_all_results('seller_data');
    //     $result = explode(",", (string)$result[0]['category_ids']);
    //     $categories =  fetch_details('categories', "status = 1", '*', "", "", "", "", "id", $result);

    //     $i = 0;
    //     foreach ($categories as $p_cat) {
    //         $categories[$i]['children'] = $this->sub_categories($p_cat['id'], $level);
    //         $categories[$i]['text'] = output_escaping($p_cat['name']);
    //         $categories[$i]['name'] = output_escaping($categories[$i]['name']);
    //         $categories[$i]['state'] = ['opened' => true];
    //         $categories[$i]['icon'] = "jstree-folder";
    //         $categories[$i]['level'] = $level;
    //         $categories[$i]['image'] = get_image_url($categories[$i]['image'], 'thumb', 'md');
    //         $categories[$i]['relative_path'] = $categories[$i]['image'];
    //         $categories[$i]['banner'] = get_image_url($categories[$i]['banner'], 'thumb', 'md');
    //         $i++;
    //     }
    //     if (isset($categories[0])) {
    //         $categories[0]['total'] = $count_res;
    //     }
    //     return  $categories;
    // }

    public function get_seller_categories($seller_id, bool $all_categories = false)
    {
        $level = 0;

        // Get the seller's category IDs
        $this->db->select('category_ids');
        $this->db->where('user_id', $seller_id);
        $result = $this->db->get('seller_data')->row_array();

        // If no categories are found, return an empty array
        $category_ids = [];

        $category_ids = isset($result['category_ids']) && !empty($result['category_ids']) ? explode(",", $result['category_ids']) : [];

        $categories = fetch_details('categories', "status = 1", '*', "", "", "", "", $all_categories ? "" : "id", $all_categories ? "" :  $category_ids);

        // Recursively fetch all parent categories
        $all_needed_categories = $categories;
        $seen_ids = array_column($all_needed_categories, 'id');

        $pending_parents = array_filter(array_column($all_needed_categories, 'parent_id'), fn($id) => $id != 0);

        while (!empty($pending_parents)) {
            $parents = fetch_details('categories', "status = 1", '*', "", "", "", "", "id", $pending_parents);

            foreach ($parents as $p) {
                if (!in_array($p['id'], $seen_ids)) {
                    $all_needed_categories[] = $p;
                    $seen_ids[] = $p['id'];
                    if ($p['parent_id'] != 0) {
                        $pending_parents[] = $p['parent_id'];
                    }
                }
            }

            // Prepare next round
            $pending_parents = array_diff(array_unique($pending_parents), $seen_ids);
        }

        $categories = $all_needed_categories;

        // Build a map of categories by ID for quick access
        $assigned_ids = $category_ids;
        $disabled_parent_ids = [];

        foreach ($categories as $cat) {
            $cat_id = $cat['id'];
            $parent_id = $cat['parent_id'];

            // Disable only if not assigned and doesn't have assigned children
            $has_assigned_child = false;
            foreach ($categories as $child_check) {
                if ($child_check['parent_id'] == $cat_id && in_array($child_check['id'], $assigned_ids)) {
                    $has_assigned_child = true;
                    break;
                }
            }

            if (!in_array($cat_id, $assigned_ids) && !$has_assigned_child) {
                $disabled_parent_ids[] = $cat_id;
            }
        }

        // Create a mapping of categories by ID
        $categories_by_id = [];
        foreach ($categories as $cat) {
            $state = ['opened' => true];

            // Check if this category needs to be disabled
            if (in_array($cat['id'], $disabled_parent_ids)) {
                $state['disabled'] = true; // Disable the parent category
            }

            $categories_by_id[$cat['id']] = $cat;
            $categories_by_id[$cat['id']]['children'] = [];
            $categories_by_id[$cat['id']]['text'] = output_escaping($cat['name']);
            $categories_by_id[$cat['id']]['state'] = $state;
            $categories_by_id[$cat['id']]['icon'] = "jstree-folder";
            $categories_by_id[$cat['id']]['level'] = $level;
            $categories_by_id[$cat['id']]['image'] = get_image_url($cat['image'], 'thumb', 'md');
            $categories_by_id[$cat['id']]['relative_path'] = $categories_by_id[$cat['id']]['image'];
            $categories_by_id[$cat['id']]['banner'] = get_image_url($cat['banner'], 'thumb', 'md');
        }

        // Build a hierarchical structure
        $hierarchy = [];
        foreach ($categories_by_id as $id => $cat) {
            if ($cat['parent_id'] == 0) {
                // Top-level category
                $hierarchy[] = &$categories_by_id[$id];
            } else {
                // Nested category
                if (isset($categories_by_id[$cat['parent_id']])) {
                    $categories_by_id[$cat['parent_id']]['children'][] = &$categories_by_id[$id];
                }
            }
        }

        // Add total count to the first top-level category
        if (!empty($hierarchy)) {
            $hierarchy[0]['total'] = count($categories);
        }

        return $hierarchy;
    }

    public function sub_categories($id, $level)
    {
        $level = $level + 1;
        $this->db->select('c1.*');
        $this->db->from('categories c1');
        $this->db->where(['c1.parent_id' => $id, 'c1.status' => 1]);
        $child = $this->db->get();
        $categories = $child->result();
        $i = 0;
        foreach ($categories as $p_cat) {
            $categories[$i]->children = $this->sub_categories($p_cat->id, $level);
            $categories[$i]->text = output_escaping($p_cat->name);
            $categories[$i]->state = ['opened' => true];
            $categories[$i]->level = $level;
            $categories[$i]->relative_path = $categories[$i]->image;
            $categories[$i]->image = get_image_url($categories[$i]->image, 'thumb', 'md');
            $categories[$i]->banner = get_image_url($categories[$i]->banner, 'thumb', 'md');
            $i++;
        }
        return $categories;
    }

    public function get_category_list($seller_id = NULL)
    {
        $offset = 0;
        $limit = 10;
        $sort = 'id';
        $order = 'ASC';
        $multipleWhere = [];
        $where = ['status !=' => NULL];

        if (isset($_GET['id'])) {
            $where['parent_id'] = $_GET['id'];
        }
        if (isset($_GET['offset'])) {
            $offset = $_GET['offset'];
        }
        if (isset($_GET['limit'])) {
            $limit = $_GET['limit'];
        }
        if (isset($_GET['sort'])) {
            $sort = $_GET['sort'];
        }
        if (isset($_GET['order'])) {
            $order = $_GET['order'];
        }
        if (isset($_GET['search']) && $_GET['search'] != '') {
            $search = $_GET['search'];
            $multipleWhere = [
                'id' => $search,
                'name' => $search
            ];
        }

        if (isset($seller_id) && $seller_id != "") {
            $this->db->select('category_ids');
            $this->db->where('user_id', $seller_id);
            $result = $this->db->get('seller_data')->row_array();
            $cat_ids = isset($result['category_ids']) ? explode(',', $result['category_ids']) : [];
        }

        $this->db->select('COUNT(id) as total');
        if (!empty($multipleWhere)) {
            $this->db->group_start();
            foreach ($multipleWhere as $key => $value) {
                $this->db->or_like($key, $value);
            }
            $this->db->group_end();
        }
        if (!empty($where)) {
            $this->db->where($where);
        }
        if (isset($cat_ids) && !empty($cat_ids)) {
            $this->db->where_in('id', $cat_ids);
        }
        $cat_count = $this->db->get('categories')->row_array();
        $total = $cat_count['total'];

        $this->db->select('*');
        if (!empty($multipleWhere)) {
            $this->db->group_start();
            foreach ($multipleWhere as $key => $value) {
                $this->db->or_like($key, $value);
            }
            $this->db->group_end();
        }
        if (!empty($where)) {
            $this->db->where($where);
        }
        if (isset($cat_ids) && !empty($cat_ids)) {
            $this->db->where_in('id', $cat_ids);
        }
        $cat_search_res = $this->db->order_by($sort, $order)->limit($limit, $offset)->get('categories')->result_array();

        $bulkData = array();
        $bulkData['total'] = $total;
        $rows = array();

        if (!empty($cat_search_res)) {
            foreach ($cat_search_res as $row) {
                $tempRow = array();
                $operate = '';
                if (!$this->ion_auth->is_seller()) {
                    $operate = '<a href="' . base_url('admin/category/create_category' . '?edit_id=' . $row['id']) . '" class=" btn action-btn btn-success btn-xs mr-1 mb-1" title="Edit" data-id="' . $row['id'] . '" data-url="admin/category/create_category"><i class="fa fa-pen"></i></a>';
                    $operate .= '<a class="delete-category btn action-btn btn-danger btn-xs mr-1 mb-1 ml-1" title="Delete" href="javascript:void(0)" data-id="' . $row['id'] . '" ><i class="fa fa-trash"></i></a>';
                }
                if ($row['status'] == '1') {
                    $tempRow['status'] = '<a class="badge badge-success text-white" >Active</a>';
                    if (!$this->ion_auth->is_seller()) {
                        $operate .= '<a class="btn btn-warning action-btn btn-xs update_active_status ml-1 mr-1 mb-1" data-table="categories" title="Deactivate" href="javascript:void(0)" data-id="' . $row['id'] . '" data-status="' . $row['status'] . '" ><i class="fa fa-eye-slash"></i></a>';
                    }
                } else {
                    $tempRow['status'] = '<a class="badge badge-danger text-white" >Inactive</a>';
                    if (!$this->ion_auth->is_seller()) {
                        $operate .= '<a class="btn btn-primary action-btn mr-1 mb-1 ml-1 btn-xs update_active_status" data-table="categories" href="javascript:void(0)" title="Active" data-id="' . $row['id'] . '" data-status="' . $row['status'] . '" ><i class="fa fa-eye"></i></a>';
                    }
                }

                $tempRow['id'] = $row['id'];
                if (!$this->ion_auth->is_seller()) {
                    $tempRow['name'] = '<a href="' . base_url() . 'admin/category?id=' . $row['id'] . '">' . output_escaping($row['name']) . '</a>';
                } else {

                    $tempRow['name'] = output_escaping($row['name']);
                }

                if (empty($row['image']) || !file_exists(FCPATH . $row['image'])) {
                    $row['image'] = base_url() . NO_IMAGE;
                    $row['image_main'] = base_url() . NO_IMAGE;
                } else {
                    $row['image_main'] = base_url($row['image']);
                    $row['image'] = get_image_url($row['image'], 'thumb', 'sm');
                }
                $tempRow['image'] = "<div class='image-box-100' ><a href='" . $row['image_main'] . "' data-toggle='lightbox' data-gallery='gallery'> <img class='rounded' src='" . $row['image'] . "' ></a></div>";

                if (empty($row['banner']) || !file_exists(FCPATH . $row['banner'])) {
                    $row['banner'] = base_url() . NO_IMAGE;
                    $row['banner_main'] = base_url() . NO_IMAGE;
                } else {
                    $row['banner_main'] = base_url($row['banner']);
                    $row['banner'] = get_image_url($row['banner'], 'thumb', 'sm');
                }
                $tempRow['banner'] = "<div class='image-box-100' ><a href='" . $row['banner_main'] . "' data-toggle='lightbox' data-gallery='gallery'> <img src='" . $row['banner'] . "' class='rounded'></a></div>";

                if (!$this->ion_auth->is_seller()) {
                    $tempRow['operate'] = $operate;
                }
                $rows[] = $tempRow;
            }
        }
        $bulkData['rows'] = $rows;
        echo json_encode($bulkData);
    }

    public function add_category($data)
    {
        $data = escape_array($data);

        if (isset($data['edit_category']) && !empty($data['edit_category'])) {
            $category_id = fetch_details('categories', ['id' => $data['edit_category']]);
            $category_name = $category_id[0]['name'];
        } else {
            $category_id = "";
            $category_name = "";
        }
        if ($category_name != $data['category_input_name']) {
            $cat_data = [
                'name' => $data['category_input_name'],
                'parent_id' => ($data['category_parent'] == NULL && isset($data['category_parent']) && !empty($data['category_parent'])) ? '0' : $data['category_parent'],
                'slug' => create_unique_slug($data['category_input_name'], 'categories'),
                'status' => '1',
                'seo_page_title' => $data['seo_page_title'],
                'seo_meta_keywords' => $data['seo_meta_keywords'],
                'seo_meta_description' => $data['seo_meta_description'],
                'seo_og_image' => isset($data['seo_og_image']) && !empty($data['seo_og_image']) ? $data['seo_og_image'] : '',
            ];
        } else {
            $cat_data = [
                'name' => $data['category_input_name'],
                'parent_id' => ($data['category_parent'] == NULL && isset($data['category_parent'])) ? '0' : $data['category_parent'],
                'status' => '1',
                'seo_page_title' => $data['seo_page_title'],
                'seo_meta_keywords' => $data['seo_meta_keywords'],
                'seo_meta_description' => $data['seo_meta_description'],
                'seo_og_image' => isset($data['seo_og_image']) && !empty($data['seo_og_image']) ? $data['seo_og_image'] : '',
            ];
        }

        if (isset($data['edit_category']) && !empty($data['edit_category'])) {
            unset($cat_data['status']);
            if (isset($data['category_input_image']) && !empty($data['category_input_image'])) {
                $cat_data['image'] = $data['category_input_image'];
            }

            $cat_data['banner'] = (isset($data['banner']) && !empty($data['banner'])) ? $data['banner'] : '';

            $this->db->set($cat_data)->where('id', $data['edit_category'])->update('categories');
        } else {
            if (isset($data['category_input_image']) && !empty($data['category_input_image'])) {
                $cat_data['image'] = $data['category_input_image'];
            }
            if (isset($data['banner']) && !empty($data['banner'])) {
                $cat_data['banner'] = (isset($data['banner']) && !empty($data['banner'])) ? $data['banner'] : '';
            }
            $this->db->insert('categories', $cat_data);
        }
    }

    public function top_category()
    {
        $query = $this->db->select('*')
            ->where('status', 1)
            ->limit('4')
            ->order_by('clicks', 'Desc')
            ->get('categories');

        $data['total'] = $query->num_rows();
        $categories = $query->result_array();
        $rows = array();

        $bulkData = array();
        $bulkData['total'] = $data['total'];
        $rows = array();

        if (!empty($query)) {
            foreach ($categories as $category) {
                $tempRow = array();
                $tempRow['id'] = $category['id'];
                $tempRow['name'] = str_replace('\\', '', $category['name']);
                $tempRow['clicks'] = $category['clicks'];
                $rows[] = $tempRow;
            }
        }
        $data['rows'] = $rows;
        echo json_encode($data);
    }

    public function get_categories_list($data)
    {
        $offset = 0;
        $limit = 10000;
        $sort = 'id';
        $order = 'ASC';
        $multipleWhere = [];
        $where = ['status !=' => NULL];

        if (isset($data['id'])) {
            $where['parent_id'] = $data['id'];
        }
        if (isset($data['offset'])) {
            $offset = $data['offset'];
        }
        if (isset($data['limit'])) {
            $limit = $data['limit'];
        }
        if (isset($data['sort'])) {
            $sort = $data['sort'];
        }
        if (isset($data['order'])) {
            $order = $data['order'];
        }
        if (isset($data['search']) && $data['search'] != '') {
            $search = $data['search'];
            $multipleWhere = [
                'id' => $search,
                'name' => $search
            ];
        }

        if (isset($seller_id) && $seller_id != "") {
            $this->db->select('category_ids');
            $this->db->where('user_id', $seller_id);
            $result = $this->db->get('seller_data')->row_array();
            $cat_ids = isset($result['category_ids']) ? explode(',', $result['category_ids']) : [];
        }

        $this->db->select('COUNT(id) as total');
        if (!empty($multipleWhere)) {
            $this->db->group_start();
            foreach ($multipleWhere as $key => $value) {
                $this->db->or_like($key, $value);
            }
            $this->db->group_end();
        }
        if (!empty($where)) {
            $this->db->where($where);
        }
        if (isset($cat_ids) && !empty($cat_ids)) {
            $this->db->where_in('id', $cat_ids);
        }
        $cat_count = $this->db->get('categories')->row_array();
        $total = $cat_count['total'];

        $this->db->select('*');
        if (!empty($multipleWhere)) {
            $this->db->group_start();
            foreach ($multipleWhere as $key => $value) {
                $this->db->or_like($key, $value);
            }
            $this->db->group_end();
        }
        if (!empty($where)) {
            $this->db->where($where);
        }
        if (isset($cat_ids) && !empty($cat_ids)) {
            $this->db->where_in('id', $cat_ids);
        }
        $cat_search_res = $this->db->order_by($sort, $order)->limit($limit, $offset)->get('categories')->result_array();

        $bulkData = array();
        $bulkData['error'] = false;
        $bulkData['message'] = 'Category retrived successfully';
        $bulkData['total'] = $total;
        $rows = array();

        if (!empty($cat_search_res)) {
            foreach ($cat_search_res as $row) {

                $tempRow['id'] = $row['id'];
                $tempRow['name'] = $row['name'];

                if (empty($row['image']) || !file_exists(FCPATH . $row['image'])) {
                    $row['image'] = base_url() . NO_IMAGE;
                    $row['image_main'] = base_url() . NO_IMAGE;
                } else {
                    $row['image_main'] = base_url($row['image']);
                    $row['image'] = get_image_url($row['image'], 'thumb', 'sm');
                }
                $tempRow['image'] = $row['image_main'];

                if (empty($row['banner']) || !file_exists(FCPATH . $row['banner'])) {
                    $row['banner'] = base_url() . NO_IMAGE;
                    $row['banner_main'] = base_url() . NO_IMAGE;
                } else {
                    $row['banner_main'] = base_url($row['banner']);
                    $row['banner'] = get_image_url($row['banner'], 'thumb', 'sm');
                }
                $tempRow['banner'] =  $row['banner_main'];

                $rows[] = $tempRow;
            }
        }
        $bulkData['rows'] = $rows;
        return ($bulkData);
    }

    public function get_download_categories()
    {
        $categories = $this->db->get('categories')->result_array();
        return $categories;
    }
}
