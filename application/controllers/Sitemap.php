<?php
defined('BASEPATH') or exit('No direct script access allowed');


class Sitemap extends CI_Controller
{


    public function __construct()
    {
        parent::__construct();
        $this->load->database();
        $this->load->helper(['url', 'language', 'xml']);
    }

    public function index()
    {
        $product_slugs = $this->db->select('p.slug')->where('p.slug != "" and p.status=1 and c.status=1 and pv.status=1')
            ->join('categories c', 'c.id = p.category_id')
            ->join('product_variants pv', 'pv.product_id = p.id')
            ->group_by('p.id')->get('products p')->result_array();
        $product_slugs = array_column($product_slugs, "slug");
        $data['product_slugs'] = $product_slugs;
        
        $categories_slugs = $this->db->select('c.slug,c.name')->where('p.slug != "" and p.status=1 and c.status=1 and pv.status=1')
            ->join('categories c', 'c.id = p.category_id')
            ->join('product_variants pv', 'pv.product_id = p.id')
            ->group_by('c.id')->get('products p')->result_array();
        $limit =  12;
        $offset =  0;
        $sections = $this->db->limit($limit, $offset)->order_by('row_order')->get('sections')->result_array();
        if (!empty($sections)) {
            for ($i = 0; $i < count($sections); $i++) {
                $sections[$i]['title'] =  output_escaping($sections[$i]['title']);
                $sections[$i]['slug'] =  url_title($sections[$i]['title'], 'dash', true);
                $sections[$i]['short_description'] =  output_escaping($sections[$i]['short_description']);
            }
        }
        $categories_slugs = array_column($categories_slugs, "slug");
        $data['categories_slugs'] = $categories_slugs;
        $data['feature_sections'] = $sections;
        $data['urls'] = array("products");
        header("Content-Type: text/xml;charset=iso-8859-1");
        $this->load->view('front-end/' . THEME . '/sitemap', $data);
    }
}