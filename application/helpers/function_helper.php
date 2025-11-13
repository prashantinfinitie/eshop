<?php
defined('BASEPATH') or exit('No direct script access allowed');

/*
    1. create_unique_slug($string,$table,$field='slug',$key=NULL,$value=NULL)
    2. ($type = 'store_settings', $is_json = false)
    3. get_logo()
    4. fetch_details($where = NULL,$table,$fields = '*')
    5. fetch_product($user_id = NULL, $filter = NULL, $id = NULL, $category_id = NULL, $limit = NULL, $offset = NULL, $sort = NULL, $order = NULL, $return_count = NULL)
    6. update_details($set,$where,$table)
    7. delete_image($id,$path,$field,$img_name,$table_name,$isjson = TRUE)
    8. delete_details($where,$table)
    9. is_json($data=NULL)
   10. validate_promo_code($promo_code,$user_id,$final_total)
   11. update_wallet_balance($operation,$user_id,$amount,$message="Balance Debited")
   12. send_notification($fcmMsg, $registrationIDs_chunks)
   13. get_attribute_values_by_pid($id)
   14. get_attribute_values_by_id($id)
   15. get_variants_values_by_pid($id)
   16. update_stock($product_variant_ids, $qtns)
   17. validate_stock($product_variant_ids, $qtns)
   18. stock_status($product_variant_id)
   19. verify_user($data)
   20. edit_unique($field,$table,$except)
   21. validate_order_status($order_ids, $status, $table = 'order_items', $user_id = null)
   22. is_exist($where,$table)
   23. get_categories_option_html($categories, $selected_vals = null)
   24. get_subcategory_option_html($subcategories, $selected_vals)
   25. get_cart_total($user_id,$product_variant_id)
   26. get_frontend_categories_html()
   27. get_frontend_subcategories_html($subcategories)
   28. resize_image($image_data, $source_path, $id = false)
   29. has_permissions($role,$module)
   30. print_msg($error,$message)
   31. get_system_update_info()
   32. send_mail($to,$subject,$message)
   33. fetch_orders($order_id = NULL, $user_id = NULL, $status = NULL, $delivery_boy_id = NULL, $limit = NULL, $offset = NULL, $sort = NULL, $order = NULL, $download_invoice = false)
   34. find_media_type($extenstion)
   35. formatBytes($size, $precision = 2)
   36. delete_images($subdirectory, $image_name)
   37. get_image_url($path, $image_type = '', $image_size = '')
   38. fetch_users($id)
   39. escape_array($array)
   40. allowed_media_types()
   41. get_current_version()
   42. resize_review_images($image_data, $source_path, $id = false)
   43. get_invoice_html($order_id)
   44. is_modification_allowed($module)
   45. output_escaping($array)
   46. get_min_max_price_of_product($product_id = '')
   47. find_discount_in_percentage($special_price, $price)
   48. get_attribute_ids_by_value($values,$names)
   49. insert_details($data,$table)
   50. get_category_id_by_slug($slug)
   51. get_variant_attributes($product_id)
   52. get_product_variant_details($product_variant_id)
   53. get_cities($id = NULL, $limit = NULL, $offset = NULL)
   54. get_favorites($user_id, $limit = NULL, $offset = NULL)
   55. current_theme($id='',$name='',$slug='',$is_default=1,$status='')
   56. get_languages($id='',$language_name='',$code='',$is_rtl='')
   60. verify_payment_transaction($txn_id,$payment_method)
   62. process_refund($id, $status, $type = 'order_items')
   63. get_user_balance($id)
   64. get_stock()
   65. get_delivery_charge($address_id)
   66. validate_otp($otp, $order_item_id = NULL, $order_id = NULL, $seller_id = NULL)
   67. is_product_delivarable($type, $type_id, $product_id)
   68. check_cart_products_delivarable($area_id, $user_id)
   69. orders_count($status = "")
   70. curl($url, $method = 'GET', $data = [], $authorization = "")
   71. get_seller_permission($seller_id, $permit = NULL)
   72. get_price($type = "max")
   73. check_for_parent_id($category_id)
   74. update_balance($amount, $delivery_boy_id, $action)
*/

function create_unique_slug($string, $table, $field = 'slug', $key = NULL, $value = NULL)
{
    $t = &get_instance();
    $slug = url_title($string);
    $slug = strtolower($slug);
    $i = 0;
    $params = array();
    $params[$field] = $slug;

    if ($key)
        $params["$key !="] = $value;

    while ($t->db->where($params)->get($table)->num_rows()) {
        if (!preg_match('/-{1}[0-9]+$/', $slug))
            $slug .= '-' . ++$i;
        else
            $slug = preg_replace('/[0-9]+$/', ++$i, $slug);

        $params[$field] = $slug;
    }
    return $slug;
}

function get_settings($type = 'system_settings', $is_json = false)
{
    $t = &get_instance();

    $res = $t->db->select(' * ')->where('variable', $type)->get('settings')->result_array();
    if (!empty($res)) {
        if ($is_json) {
            $setting = json_decode($res[0]['value'], true);
            if ($type === "payment_method") {
                if (!isset($setting['max_cod_amount'])) {
                    $setting['max_cod_amount'] = 0;
                } else {
                    $setting['max_cod_amount'] = (float) $setting['max_cod_amount'];
                }
                if (!isset($setting['min_cod_amount'])) {
                    $setting['min_cod_amount'] = 0;
                } else {
                    $setting['min_cod_amount'] = (float) $setting['min_cod_amount'];
                }
            }

            return $setting;
        } else {
            return output_escaping($res[0]['value']);
        }
    }
}

function get_logo()
{
    $t = &get_instance();
    $res = $t->db->select(' * ')->where('variable', 'logo')->get('settings')->result_array();
    if (!empty($res)) {
        $logo['is_null'] = FALSE;
        $logo['value'] = base_url() . $res[0]['value'];
    } else {
        $logo['is_null'] = TRUE;
        $logo['value'] = base_url() . NO_IMAGE;
    }
    return $logo;
}

function fetch_details($table, $where = NULL, $fields = '*', $limit = '', $offset = '', $sort = '', $order = '', $where_in_key = '', $where_in_value = '')
{
    $t = &get_instance();
    $t->db->select($fields);
    if (!empty($where)) {
        $t->db->where($where);
    }

    if (!empty($where_in_key) && !empty($where_in_value)) {
        $t->db->where_in($where_in_key, $where_in_value);
    }

    if (!empty($limit)) {
        $t->db->limit($limit);
    }

    if (!empty($offset)) {
        $t->db->offset($offset);
    }

    if (!empty($order) && !empty($sort)) {
        $t->db->order_by($sort, $order);
    }
    $res = $t->db->get($table)->result_array();
    return $res;
}
// Function to convert image to PNG
function convert_to_png($source)
{
    $image = imagecreatefromstring(file_get_contents($source));
    if ($image !== false) {
        $png_image_path = tempnam(sys_get_temp_dir(), 'converted_image_') . '.png';
        imagealphablending($image, true);
        imagesavealpha($image, true);
        if (imagepng($image, $png_image_path)) {
            return $png_image_path;
        }
    }
    return false;
}

function fetch_tags($category_id)
{
    $t = &get_instance();  // Getting CodeIgniter instance

    // Check if category_ids are passed and convert them into an array if not already
    if (!empty($category_id)) {
        $category_ids = is_array($category_id) ? $category_id : explode(',', $category_id);  // Ensure it's an array
        $category_filter = " AND p.category_id IN (" . implode(',', $category_ids) . ") ";  // Build category filter
    } else {
        $category_filter = "";  // No category filter if not passed
    }

    // Build the query with the optional category filter
    $query = $t->db->query("
    SELECT DISTINCT TRIM(SUBSTRING_INDEX(SUBSTRING_INDEX(p.tags, ',', n.n), ',', -1)) AS tag
    FROM products p
    JOIN (
        SELECT 1 AS n UNION SELECT 2 UNION SELECT 3 UNION SELECT 4 UNION SELECT 5 UNION
        SELECT 6 UNION SELECT 7 UNION SELECT 8 UNION SELECT 9 UNION SELECT 10
    ) n
    ON CHAR_LENGTH(p.tags) - CHAR_LENGTH(REPLACE(p.tags, ',', '')) >= n.n - 1
    WHERE p.status = 1
    " . $category_filter . "
    ORDER BY tag
    ");

    $distinct_tags = $query->result_array();

    return $distinct_tags;
}

function recalculateTaxedPrice(array $product_ids)
{
    $t = &get_instance();


    if (count($product_ids) == 0) {
        $allProducts = $t->db->select("id")->get("products")->result_array();

        $product_ids = array_map(function ($product) {
            return $product["id"];
        }, $allProducts);
    }

    $allTaxes = $t->db->select("*")->where("status", '1')->get("taxes")->result_array();

    // get variant prices of the products  given in product_ids array
    $result = $t->db->select("pv.special_price, pv.id as variant_id, pv.product_id as product_id, p.tax, p.is_prices_inclusive_tax")
        ->join("products p", "pv.product_id =p.id", "left")
        ->where_in("product_id", $product_ids)
        ->get("product_variants pv")->result_array();

    foreach ($result as $key => $variant) {
        $tax_ids = explode(',', $variant['tax']);
        $percent = 0;
        foreach ($tax_ids as $id) {
            if (trim($id) != "" && ((int) trim($id)) != 0) {
                foreach ($allTaxes as $tax) {
                    if (($tax['id'] == (int) trim($id))) {
                        $percent = (float) $tax['percentage'] + $percent;
                    }
                }
            }
        }

        if (isset($result[$key]['is_prices_inclusive_tax']) && $result[$key]['is_prices_inclusive_tax'] == 1 && $result[$key]['is_prices_inclusive_tax'] == '1') {
            $variant['total'] = $variant['special_price'];
        } else {
            $variant['total'] = $variant['special_price'] + ($variant['special_price'] * $percent / 100);
        }
        $result[$key] = $variant;
        update_details(["final_taxed_price" => $variant['total']], ["id" => $variant["variant_id"]], "product_variants");
    }
}

function fetch_product($user_id = NULL, $filter = NULL, $id = NULL, $category_id = NULL, $limit = NULL, $offset = NULL, $sort = NULL, $order = NULL, $return_count = NULL, $is_deliverable = NULL, $seller_id = NULL, $is_detailed_data = 0)
{
    $t = &get_instance();


    if ($sort == 'pv.special_price' && !empty($sort) && $sort != NULL) {
        // $t->db->order_by("pv.final_taxed_price " . $order, False);
        $t->db->order_by("min_price " . $order, False);
    }


    if (isset($filter['show_only_active_products']) && $filter['show_only_active_products'] == 0) {
        $where = [];
    } else {
        $where = ['p.status' => '1', 'pv.status' => 1, 'sd.status' => 1];
    }

    $discount_filter_data = (isset($filter['discount']) && !empty($filter['discount'])) ? ' pv.*,( if(pv.special_price > 0,( (pv.price-pv.special_price)/pv.price)*100,0)) as cal_discount_percentage, ' : '';

    $t->db->select($discount_filter_data . 'count(p.id) as sales, p.stock_type ,
     p.is_prices_inclusive_tax, p.type,p.low_stock_limit,GROUP_CONCAT(DISTINCT(pa.attribute_value_ids)) as attr_value_ids,sd.rating as seller_rating,sd.slug as seller_slug,
     sd.seo_page_title as seller_seo_page_title, sd.seo_meta_keywords as seller_seo_meta_keywords, sd.seo_meta_description as seller_seo_meta_description, sd.seo_og_image as seller_seo_og_image,
     c.seo_page_title as caregory_seo_page_title, c.seo_meta_keywords as caregory_seo_meta_keywords, c.seo_meta_description as caregory_seo_meta_description, c.seo_og_image as caregory_seo_og_image,
     p.seo_page_title as product_seo_page_title, p.seo_meta_keywords as product_seo_meta_keywords, p.seo_meta_description as product_seo_meta_description, p.seo_og_image as product_seo_og_image,
     sd.no_of_ratings as seller_no_of_ratings,sd.logo as seller_profile, sd.store_name as store_name,sd.store_description, p.seller_id, u.username as seller_name,
     p.id,p.stock,pv.stock as product_variant_stock,p.name,pv.special_price,p.category_id, p.attribute_order,p.short_description,p.slug,p.description,p.extra_description,p.total_allowed_quantity,
     p.status,p.deliverable_type,p.is_attachment_required,p.deliverable_zipcodes,p.deliverable_cities,p.deliverable_city_type,p.minimum_order_quantity,p.sku,
     p.quantity_step_size,p.cod_allowed,p.row_order,p.rating,p.no_of_ratings,p.image,p.is_returnable,p.is_cancelable,p.cancelable_till,p.indicator,p.other_images,
     p.video_type, p.video, p.tags, p.warranty_period, p.guarantee_period, p.made_in,p.hsn_code,p.download_allowed,p.download_type,p.download_link,p.pickup_location,
     p.brand as product_brand,b.id as brand_id,b.name as brand, b.image as brand_image,p.availability, p.slug as product_slug, b.slug as brand_slug,c.name as category_name,c.slug as category_slug,
     (SELECT GROUP_CONCAT(tax.percentage) FROM taxes as tax WHERE FIND_IN_SET(tax.id, p.tax)) as tax_percentage ,
     (SELECT GROUP_CONCAT(tax.id) FROM taxes as tax WHERE FIND_IN_SET(tax.id, p.tax)) as tax_ids,
     (SELECT GROUP_CONCAT(tax.id) FROM taxes as tax WHERE FIND_IN_SET(tax.id, p.tax)) tax_id,MIN(IF(pv.special_price > 0, pv.special_price, pv.price)) as min_price')
        ->join(" categories c", "p.category_id=c.id ", 'LEFT')
        ->join(" brands b", "p.brand=b.id", 'LEFT')
        ->join(" seller_data sd", "p.seller_id=sd.user_id ", 'LEFT')
        ->join(" users u", "p.seller_id=u.id", 'LEFT')
        ->join('product_variants pv', 'p.id = pv.product_id', 'LEFT')
        ->join('taxes tax', 'tax.id = p.tax', 'LEFT')
        ->join('product_attributes pa', ' pa.product_id = p.id ', 'LEFT');

    if (isset($filter['show_only_stock_product']) && $filter['show_only_stock_product'] == 1) {
        $t->db->where('(p.stock != "" or pv.stock != "")');
    }

    if (isset($filter) && !empty($filter['product_type']) && strtolower($filter['product_type']) == 'most_selling_products') {
        $t->db->join('order_items oi', 'oi.product_variant_id = pv.id', 'LEFT');
        $sort = 'count(p.id)';
        $order = 'DESC';
    }

    if (isset($filter) && !empty($filter['search'])) {
        $tags = explode(" ", $filter['search']);
        $t->db->group_Start();
        foreach ($tags as $i => $tag) {
            if ($i == 0) {
                $t->db->like('p.tags', trim($tag));
            } else {
                $t->db->or_like('p.tags', trim($tag));
            }
        }
        $t->db->or_like('p.name', trim($filter['search']));
        $t->db->group_end();
    }
    if (isset($filter) && !empty($filter['flag']) && $filter['flag'] != "null" && $filter['flag'] != "") {
        $flag = $filter['flag'];
        if ($flag == 'low') {
            $t->db->group_Start();
            $where1 = "p.stock_type is  NOT NULL";
            $t->db->where($where1);
            if ('p.stock_type' == '0') {

                $t->db->where('(CASE
                    WHEN p.low_stock_limit > 0 THEN p.stock <= p.low_stock_limit
                    ELSE p.stock <= sd.low_stock_limit
                END)');
                $t->db->where('p.availability =', '1');
            } else {

                // Check product low_stock_limit first, if 0, use seller_data low_stock_limit
                // $t->db->where('(CASE
                //     WHEN p.low_stock_limit > 0 THEN p.stock <= p.low_stock_limit
                //     ELSE p.stock <= sd.low_stock_limit
                // END)');
                // $t->db->where('p.availability =', '1');

                $t->db->where('(CASE
                    WHEN p.low_stock_limit > 0 THEN pv.stock <= p.low_stock_limit
                    ELSE pv.stock <= sd.low_stock_limit
                END)');
                $t->db->where('pv.availability =', '1');
            }
            $t->db->group_End();
        } else if ($flag == 'sold') {
            $t->db->group_Start();
            $where1 = "p.stock_type is  NOT NULL";
            $t->db->where($where1);
            $t->db->where('p.stock ', '0');
            $t->db->where('p.availability ', '0');
            $t->db->or_where('pv.stock ', '0');
            $t->db->where('pv.availability ', '0');
            $t->db->group_End();
        } else {
            $t->db->group_Start();
            $t->db->or_where('p.availability ', '0');
            $t->db->or_where('pv.availability ', '0');
            $t->db->where('p.stock ', '0');
            $t->db->or_where('pv.stock ', '0');
            $t->db->group_End();
        }
    }
    if (isset($filter['min_price']) && $filter['min_price'] > 0) {
        $min_price = $filter['min_price'];
        $where_min = "if( pv.special_price > 0 , pv.special_price , pv.price ) >=$min_price";
        $t->db->group_Start();
        $t->db->where($where_min);
        $t->db->group_End();
    }
    if (isset($filter['max_price']) && $filter['max_price'] > 0 && isset($filter['min_price']) && $filter['min_price'] > 0) {
        $max_price = $filter['max_price'];
        $where_max = "if( pv.special_price > 0 , pv.special_price , pv.price ) <=$max_price";
        $t->db->group_Start();
        $t->db->where($where_max);
        $t->db->group_End();
    }

    if (isset($filter) && !empty($filter['tags'])) {
        $tags = explode(",", $filter['tags']);
        $t->db->group_Start();
        foreach ($tags as $i => $tag) {
            if ($i == 0) {
                $t->db->like('p.tags', trim($tag));
            } else {
                $t->db->or_like('p.tags', trim($tag));
            }
        }
        $t->db->group_end();
    }

    if (isset($filter) && !empty($filter['brand'])) {
        if (is_array($filter['brand']) && !empty($filter['brand'])) {
            $t->db->group_Start();
            $t->db->where_in('p.brand', $filter['brand']);
            $t->db->group_End();
            $t->db->where($where);
        } else {
            $where['p.brand'] = $filter['brand'];
        }
    }

    if (isset($filter) && !empty($filter['category_slug'])) {
        $where['c.slug'] = $filter['category_slug'];
    }
    if (isset($filter) && !empty($filter['brand_slug'])) {
        $where['b.slug'] = $filter['brand_slug'];
    }

    if (isset($filter) && !empty($filter['slug'])) {
        $where['p.slug'] = $filter['slug'];
    }
    if (isset($seller_id) && !empty($seller_id) && $seller_id != "") {
        $where['p.seller_id'] = $seller_id;
    }


    /* https://stackoverflow.com/questions/5015403/mysql-find-in-set-with-multiple-search-string */
    if (isset($filter) && !empty($filter['attribute_value_ids'])) {
        $str = str_replace(',', '|', $filter['attribute_value_ids']); //str_replace(find,replace,string,count)
        $t->db->where('CONCAT(",", pa.attribute_value_ids , ",") REGEXP ",(' . $str . ')," !=', 0, false);
    }

    if (isset($category_id) && !empty($category_id)) {
        if (is_array($category_id) && !empty($category_id)) {
            $t->db->group_Start();
            $t->db->where_in('p.category_id', $category_id);
            $t->db->group_End();
            $t->db->where($where);
        } else {
            $where['p.category_id'] = $category_id;
        }
    }

    if (isset($filter['zipcode_id']) && !empty($filter['zipcode_id'])) {
        $zipcode_id = $filter['zipcode_id'];
        $where2 = "((deliverable_type='2' and FIND_IN_SET('$zipcode_id', deliverable_zipcodes)) or deliverable_type = '1') OR (deliverable_type='3' and NOT FIND_IN_SET('$zipcode_id', deliverable_zipcodes)) ";
        $t->db->group_Start();
        $t->db->where($where2);
        $t->db->group_End();
    }

    if (isset($filter['city_id']) && !empty($filter['city_id'])) {
        $city_id = $filter['city_id'];
        $where2 = "((deliverable_city_type='2' and FIND_IN_SET('$city_id', deliverable_cities)) or deliverable_city_type = '1') OR (deliverable_city_type='3' and NOT FIND_IN_SET('$city_id', deliverable_cities)) ";
        $t->db->group_Start();
        $t->db->where($where2);
        $t->db->group_End();
    }

    if (isset($filter) && !empty($filter['product_type']) && strtolower($filter['product_type']) == 'products_on_sale') {
        $t->db->where('pv.special_price >', 0);
        $t->db->where('pv.special_price < pv.price', null, false); // Correct column-to-column comparison
    }

    if (isset($filter) && !empty($filter['product_type']) && strtolower($filter['product_type']) == 'top_rated_products') {
        $sort = null;
        $order = null;
        $t->db->order_by("p.rating", "desc");
        $t->db->order_by("p.no_of_ratings", "desc");
        $where = ['p.no_of_ratings > ' => 0];
    }

    if (isset($filter) && !empty($filter['product_type']) && strtolower($filter['product_type']) == 'top_rated_product_including_all_products') {
        $sort = null;
        $order = null;
        $t->db->order_by("p.rating", "desc");
        $t->db->order_by("p.no_of_ratings", "desc");
    }

    if (isset($filter) && !empty($filter['product_type']) && $filter['product_type'] == 'new_added_products') {
        $sort = 'p.id';
        $order = 'desc';
    }

    if (isset($filter) && !empty($filter['product_variant_ids'])) {
        if (is_array($filter['product_variant_ids'])) {
            $t->db->where_in('pv.id', $filter['product_variant_ids']);
        }
    }

    if (isset($id) && !empty($id) && $id != null) {
        if (is_array($id) && !empty($id)) {
            $t->db->where_in('p.id', $id);
            $t->db->where($where);
        } else {
            if (isset($filter) && !empty($filter['is_similar_products']) && $filter['is_similar_products'] == '1' && isset($filter['similar_product_slug']) && !empty($filter['similar_product_slug'])) {
                if (isset($filter) && !empty($filter['is_similar_products']) && $filter['is_similar_products'] == '1') {
                    $where[' p.slug != '] = $filter['similar_product_slug'];
                } else {
                    $where['p.slug'] = $filter['similar_product_slug'];
                }
            } else {

                if (isset($filter) && !empty($filter['is_similar_products']) && $filter['is_similar_products'] == '1') {
                    $where[' p.id != '] = $id;
                } else {
                    $where['p.id'] = $id;
                }
            }

            $t->db->where($where);
        }
    } else {
        $t->db->where($where);
    }
    if (!isset($filter['flag']) && empty($filter['flag'])) {
        $t->db->group_Start();
        $t->db->or_where('c.status', '1');
        $t->db->or_where('c.status', '0');
        $t->db->group_End();
    }
    if (isset($filter['discount']) && !empty($filter['discount']) && $filter['discount'] != "") {
        $discount_pr = $filter['discount'];
        $t->db->group_by('p.id')->having("cal_discount_percentage  <= " . $discount_pr, null, false)->having("cal_discount_percentage  > 0 ", null, false);
    } else {
        $t->db->group_by('p.id');
    }

    if ($limit != null || $offset != null) {
        $t->db->limit($limit, $offset);
    }
    // if (isset($filter['discount']) && !empty($filter['discount']) && $filter['discount'] != "") {
    //     $t->db->order_by('cal_discount_percentage', 'DESC');
    // } else {
    //     if ($sort != null || $order != null && $sort != 'pv.special_price') {
    //         $t->db->order_by($sort, $order);
    //     }
    //     $t->db->order_by('p.row_order', 'ASC');
    // }

    if ($sort == 'pv.special_price' && !empty($sort) && $sort != NULL) {
        $t->db->order_by("MIN(IF(pv.special_price > 0, pv.special_price, pv.price)) " . $order, False);
    } elseif (isset($filter['discount']) && !empty($filter['discount']) && $filter['discount'] != "") {
        $t->db->order_by('cal_discount_percentage', 'DESC');
    } else {
        if ($sort != null || $order != null && $sort != 'pv.special_price') {
            $t->db->order_by($sort, $order);
        }
        $t->db->order_by('p.row_order', 'ASC');
    }

    if (!empty($return_count)) {
        return $t->db->count_all_results('products p');
    } else {
        $product = $t->db->get('products p')->result_array();
    }

    // echo $t->db->last_query();

    $count = isset($filter) && !empty($filter['flag']) ? 'count(DISTINCT(p.id))' : 'count(DISTINCT(p.id))';

    $discount_filter = (isset($filter['discount']) && !empty($filter['discount'])) ? ' , GROUP_CONCAT( IF( ( IF( pv.special_price > 0, ((pv.price - pv.special_price) / pv.price) * 100, 0 ) ) > ' . $filter['discount'] . ', ( IF( pv.special_price > 0, ((pv.price - pv.special_price) / pv.price) * 100, 0 ) ), 0 ) ) AS cal_discount_percentage ' : '';
    $product_count = $t->db->select('count(DISTINCT(p.id)) as total , GROUP_CONCAT(pa.attribute_value_ids) as attr_value_ids' . $discount_filter)
        ->join(" categories c", "p.category_id=c.id ", 'LEFT')
        ->join(" brands b", "p.brand=b.id", 'LEFT')
        ->join(" seller_data sd", "p.seller_id=sd.user_id ")
        ->join('product_variants pv', 'p.id = pv.product_id', 'LEFT')
        ->join('product_attributes pa', ' pa.product_id = p.id ', 'LEFT');

    if (isset($filter) && !empty($filter['search'])) {
        $tags = explode(" ", $filter['search']);
        $t->db->group_Start();
        foreach ($tags as $i => $tag) {
            if ($i == 0) {
                $t->db->like('p.tags', trim($tag));
            } else {
                $t->db->or_like('p.tags', trim($tag));
            }
        }
        $product_count->or_like('p.name', $filter['search']);
        $t->db->group_End();
    }
    if (isset($filter) && !empty($filter['flag']) && $filter['flag'] != "null" && $filter['flag'] != "") {
        $flag = $filter['flag'];
        if ($flag == 'low') {
            $t->db->group_Start();
            $where1 = "p.stock_type is  NOT NULL";
            $t->db->where($where1);
            if ('p.stock_type' == '0') {

                $t->db->where('(CASE
                    WHEN p.low_stock_limit > 0 THEN p.stock <= p.low_stock_limit
                    ELSE p.stock <= sd.low_stock_limit
                END)');
                $t->db->where('p.availability =', '1');
            } else {

                // Check product low_stock_limit first, if 0, use seller_data low_stock_limit
                // $t->db->where('(CASE
                //     WHEN p.low_stock_limit > 0 THEN p.stock <= p.low_stock_limit
                //     ELSE p.stock <= sd.low_stock_limit
                // END)');
                // $t->db->where('p.availability =', '1');

                $t->db->where('(CASE
                    WHEN p.low_stock_limit > 0 THEN pv.stock <= p.low_stock_limit
                    ELSE pv.stock <= sd.low_stock_limit
                END)');
                $t->db->where('pv.availability =', '1');
            }
            $t->db->group_End();
        } else if ($flag == 'sold') {
            $t->db->group_Start();
            $where1 = "p.stock_type is  NOT NULL";
            $t->db->where($where1);
            $t->db->where('p.stock ', '0');
            $t->db->where('p.availability ', '0');
            $t->db->or_where('pv.stock ', '0');
            $t->db->where('pv.availability ', '0');
            $t->db->group_End();
        } else {
            $t->db->group_Start();
            $t->db->or_where('p.availability ', '0');
            $t->db->or_where('pv.availability ', '0');
            $t->db->where('p.stock ', '0');
            $t->db->or_where('pv.stock ', '0');
            $t->db->group_End();
        }
    }

    if (isset($filter) && !empty($filter['tags'])) {
        $tags = explode(",", $filter['tags']);
        $t->db->group_Start();
        foreach ($tags as $i => $tag) {
            if ($i == 0) {
                $t->db->like('p.tags', trim($tag));
            } else {
                $t->db->or_like('p.tags', trim($tag));
            }
        }
        $t->db->group_End();
    }

    if (isset($filter) && !empty($filter['brand'])) {
        if (is_array($filter['brand']) && !empty($filter['brand'])) {
            $t->db->group_Start();
            $t->db->where_in('p.brand', $filter['brand']);
            $t->db->group_End();
            $t->db->where($where);
        } else {
            $where['p.brand'] = $filter['brand'];
        }
    }

    if (isset($filter['min_price']) && $filter['min_price'] > 0) {
        $min_price = $filter['min_price'];
        $where_min = "if( pv.special_price > 0 , pv.special_price , pv.price ) >=$min_price";
        $t->db->group_Start();
        $t->db->where($where_min);
        $t->db->group_End();
    }
    if (isset($filter['max_price']) && $filter['max_price'] > 0 && isset($filter['min_price']) && $filter['min_price'] > 0) {
        $max_price = $filter['max_price'];
        $where_max = "if( pv.special_price > 0 , pv.special_price , pv.price ) <=$max_price";
        $t->db->group_Start();
        $t->db->where($where_max);
        $t->db->group_End();
    }

    if (isset($filter) && !empty($filter['attribute_value_ids'])) {
        $str = str_replace(',', '|', $filter['attribute_value_ids']); // Ids should be in string and comma separated
        $product_count->where('CONCAT(",", pa.attribute_value_ids, ",") REGEXP ",(' . $str . ')," !=', 0, false);
    }
    if (isset($filter) && !empty($filter['product_type']) && strtolower($filter['product_type']) == 'most_selling_products') {
        $product_count->join('order_items oi', 'oi.product_variant_id = pv.id', 'LEFT');
    }
    if (isset($category_id) && !empty($category_id)) {
        if (is_array($category_id) && !empty($category_id)) {
            $product_count->where_in('p.category_id', $category_id);
            $product_count->where($where);
        }
    }

    if (isset($filter['zipcode_id']) && !empty($filter['zipcode_id'])) {
        $zipcode_id = $filter['zipcode_id'];
        $where2 = "((deliverable_type='2' and FIND_IN_SET('$zipcode_id', deliverable_zipcodes)) or deliverable_type = '1') OR (deliverable_type='3' and NOT FIND_IN_SET('$zipcode_id', deliverable_zipcodes)) ";
        $t->db->group_Start();
        $t->db->where($where2);
        $t->db->group_End();
    }

    if (isset($filter['city_id']) && !empty($filter['city_id'])) {
        $city_id = $filter['city_id'];
        $where2 = "((deliverable_city_type='2' and FIND_IN_SET('$city_id', deliverable_cities)) or deliverable_city_type = '1') OR (deliverable_city_type='3' and NOT FIND_IN_SET('$city_id', deliverable_cities)) ";
        $t->db->group_Start();
        $t->db->where($where2);
        $t->db->group_End();
    }

    if (isset($filter) && !empty($filter['product_type']) && strtolower($filter['product_type']) == 'products_on_sale') {
        $product_count->where('pv.special_price >=', '0');
    }
    if (isset($id) && !empty($id) && $id != null) {
        if (is_array($id) && !empty($id)) {
            $product_count->where_in('p.id', $id);
        }
    }
    if (isset($filter) && !empty($filter['category_slug'])) {
        $where['c.slug'] = $filter['category_slug'];
    }

    if (isset($filter) && !empty($filter['brand_slug'])) {
        $where['b.slug'] = $filter['brand_slug'];
    }

    if (isset($filter) && !empty($filter['slug'])) {
        $where['p.slug'] = $filter['slug'];
    }
    if (isset($seller_id) && !empty($seller_id) && $seller_id != "") {
        $where['p.seller_id'] = $seller_id;
    }
    if (isset($seller_id) && !empty($seller_id) && $seller_id != "") {
        if (isset($filter['show_only_stock_product']) && $filter['show_only_stock_product'] == 1) {
            $t->db->where('(p.stock != "" or pv.stock != "")');
        }
    }
    if (isset($filter['show_only_stock_product']) && $filter['show_only_stock_product'] == 1) {
        $t->db->where('(p.stock != "" or pv.stock != "")');
    }

    $product_count->where($where);
    if (!isset($filter['flag']) && empty($filter['flag'])) {
        $product_count->group_Start();
        $product_count->or_where('c.status', '1');
        $product_count->or_where('c.status', '0');
        $product_count->group_End();
    }

    $count_res = $product_count->get('products p')->result_array();

    // echo $t->db->last_query();


    $attribute_values_ids = array();
    $temp = [];
    $prices = get_price();
    $min_price = $prices['min'];
    $max_price = $prices['max'];

    if (!empty($product)) {

        $t->load->model('rating_model');
        for ($i = 0; $i < count($product); $i++) {
            if (($is_detailed_data != null && $is_detailed_data == 1)) {
                $rating = $t->rating_model->fetch_rating($product[$i]['id'], '', 8, 0, 'pr.id', 'desc', '', 1);
                $product[$i]['review_images'] = (!empty($rating)) ? [$rating] : array();
                $product[$i]['attributes'] = get_attribute_values_by_pid($product[$i]['id']);
            }

            $product[$i]['tax_percentage'] = (isset($product[$i]['tax_percentage']) && intval($product[$i]['tax_percentage']) > 0) ? $product[$i]['tax_percentage'] : '0';
            $product[$i]['tax_id'] = ((isset($product[$i]['tax_id']) && intval($product[$i]['tax_id']) > 0) && $product[$i]['tax_id'] != "") ? $product[$i]['tax_id'] : '0';

            $total_tax_percentage = (isset($product[$i]['tax_percentage']) && intval($product[$i]['tax_percentage']) > 0) ? explode(',', $product[$i]['tax_percentage']) : '';
            $product[$i]['total_tax_percentage'] = (isset($product[$i]['tax_percentage']) && intval($product[$i]['tax_percentage']) > 0) ? array_sum($total_tax_percentage) : 0;

            $product[$i]['variants'] = get_variants_values_by_pid($product[$i]['id']);

            $total_stock = 0;
            foreach ($product[$i]['variants'] as $variant) {
                $stock = (isset($variant['stock']) && !empty($variant['stock'])) ? (string) $variant['stock'] : 0;
                $total_stock += $stock;
                $product[$i]['total_stock'] = isset($total_stock) && !empty($total_stock) ? (string) $total_stock : '';
            }
            $product[$i]['min_max_price'] = get_min_max_price_of_product($product[$i]['id']);
            $product[$i]['stock_type'] = isset($product[$i]['stock_type']) && ($product[$i]['stock_type'] != '') ? $product[$i]['stock_type'] : '';
            $product[$i]['stock'] = isset($product[$i]['stock']) && !empty($product[$i]['stock']) ? $product[$i]['stock'] : '';
            $product[$i]['relative_path'] = isset($product[$i]['image']) && !empty($product[$i]['image']) ? $product[$i]['image'] : '';

            if (($is_detailed_data != null && $is_detailed_data == 1)) {

                $product[$i]['other_images_relative_path'] = isset($product[$i]['other_images']) && !empty($product[$i]['other_images']) ? json_decode($product[$i]['other_images']) : [];
                $product[$i]['video_relative_path'] = (isset($product[$i]['video']) && (!empty($product[$i]['video']))) ? $product[$i]['video'] : "";
                $product[$i]['video_type'] = isset($product[$i]['video_type']) && !empty($product[$i]['video_type']) ? $product[$i]['video_type'] : '';
            }

            $product[$i]['attr_value_ids'] = isset($product[$i]['attr_value_ids']) && !empty($product[$i]['attr_value_ids']) ? $product[$i]['attr_value_ids'] : '';
            $product[$i]['made_in'] = isset($product[$i]['made_in']) && !empty($product[$i]['made_in']) ? $product[$i]['made_in'] : '';
            $product[$i]['hsn_code'] = isset($product[$i]['hsn_code']) && !empty($product[$i]['hsn_code']) ? $product[$i]['hsn_code'] : '';
            $product[$i]['brand'] = isset($product[$i]['brand']) && !empty($product[$i]['brand']) ? $product[$i]['brand'] : '';
            $product[$i]['brand_image'] = isset($product[$i]['brand_image']) && !empty($product[$i]['brand_image']) ? base_url() . $product[$i]['brand_image'] : '';

            $product[$i]['warranty_period'] = isset($product[$i]['warranty_period']) && !empty($product[$i]['warranty_period']) ? $product[$i]['warranty_period'] : '';
            $product[$i]['guarantee_period'] = isset($product[$i]['guarantee_period']) && !empty($product[$i]['guarantee_period']) ? $product[$i]['guarantee_period'] : '';
            $product[$i]['total_allowed_quantity'] = isset($product[$i]['total_allowed_quantity']) && !empty($product[$i]['total_allowed_quantity']) ? $product[$i]['total_allowed_quantity'] : '';
            $product[$i]['download_allowed'] = isset($product[$i]['download_allowed']) && !empty($product[$i]['download_allowed']) ? $product[$i]['download_allowed'] : '';
            $product[$i]['download_type'] = isset($product[$i]['download_type']) && !empty($product[$i]['download_type']) ? $product[$i]['download_type'] : '';
            $product[$i]['download_link'] = isset($product[$i]['download_link']) && !empty($product[$i]['download_link']) ? $product[$i]['download_link'] : '';
            $product[$i]['status'] = isset($product[$i]['status']) && !empty($product[$i]['status']) ? $product[$i]['status'] : '';

            if (($is_detailed_data != null && $is_detailed_data == 1)) {
                $total_product = $t->db->query("select count(id) as total  from products where products.seller_id=" . $product[$i]['seller_id'] . " AND products.status='1'")->result_array();
                $product[$i]['total_product'] = ($total_product[0]['total']);
            }

            /* outputing escaped data */
            $product[$i]['name'] = output_escaping($product[$i]['name']);
            $product[$i]['store_name'] = output_escaping($product[$i]['store_name']);
            $product[$i]['seller_rating'] = (isset($product[$i]['seller_rating']) && !empty($product[$i]['seller_rating'])) ? output_escaping(number_format($product[$i]['seller_rating'], 1)) : 0;
            $product[$i]['store_description'] = (isset($product[$i]['store_description']) && !empty($product[$i]['store_description'])) ? output_escaping($product[$i]['store_description']) : "";
            $product[$i]['seller_profile'] = output_escaping(base_url() . $product[$i]['seller_profile']);
            $product[$i]['seller_name'] = output_escaping($product[$i]['seller_name']);
            $product[$i]['short_description'] = output_escaping($product[$i]['short_description']);
            $product[$i]['description'] = (isset($product[$i]['description']) && !empty($product[$i]['description'])) ? output_escaping($product[$i]['description']) : "";
            $product[$i]['extra_description'] = (isset($product[$i]['extra_description']) && !empty($product[$i]['extra_description']) && $product[$i]['extra_description'] != 'NULL') ? output_escaping($product[$i]['extra_description']) : "";
            $product[$i]['pickup_location'] = (isset($product[$i]['pickup_location']) && !empty($product[$i]['pickup_location']) && $product[$i]['pickup_location']) != '' ? $product[$i]['pickup_location'] : '';

            $product[$i]['seller_slug'] = isset($product[$i]['seller_slug']) && !empty($product[$i]['seller_slug']) ? output_escaping($product[$i]['seller_slug']) : "";
            $product[$i]['deliverable_type'] = isset($product[$i]['deliverable_type']) && !empty($product[$i]['deliverable_type']) ? output_escaping($product[$i]['deliverable_type']) : '';

            //end
            $product[$i]['deliverable_city_type'] = isset($product[$i]['deliverable_city_type']) && !empty($product[$i]['deliverable_city_type']) ? output_escaping($product[$i]['deliverable_city_type']) : '';


            $product[$i]['deliverable_zipcodes_ids'] = output_escaping($product[$i]['deliverable_zipcodes']);
            if (isset($filter['discount']) && !empty($filter['discount']) && $filter['discount'] != "") {
                $product[$i]['cal_discount_percentage'] = output_escaping(number_format($product[$i]['cal_discount_percentage'], 2));
            }
            $product[$i]['cancelable_till'] = isset($product[$i]['cancelable_till']) && !empty($product[$i]['cancelable_till']) ? $product[$i]['cancelable_till'] : '';
            $product[$i]['is_attachment_required'] = isset($product[$i]['is_attachment_required']) && !empty($product[$i]['is_attachment_required']) ? $product[$i]['is_attachment_required'] : '0';
            $product[$i]['indicator'] = isset($product[$i]['indicator']) && !empty($product[$i]['indicator']) ? $product[$i]['indicator'] : '0';
            $product[$i]['deliverable_zipcodes_ids'] = isset($product[$i]['deliverable_zipcodes_ids']) && !empty($product[$i]['deliverable_zipcodes_ids']) ? $product[$i]['deliverable_zipcodes_ids'] : '';
            $product[$i]['rating'] = output_escaping(number_format($product[$i]['rating'], 2));
            $product[$i]['availability'] = isset($product[$i]['availability']) && ($product[$i]['availability'] != "") ? $product[$i]['availability'] : '';
            $product[$i]['sku'] = isset($product[$i]['sku']) && ($product[$i]['sku'] != "") ? $product[$i]['sku'] : '';
            $product[$i]['category_name'] = (isset($product[$i]['category_name']) && !empty($product[$i]['category_name'])) ? output_escaping($product[$i]['category_name']) : '';
            $product[$i]['attribute_order'] = (isset($product[$i]['attribute_order']) && !empty($product[$i]['attribute_order'])) ? output_escaping($product[$i]['attribute_order']) : '';

            /* getting zipcodes from ids */
            if ($product[$i]['deliverable_city_type'] != NONE && $product[$i]['deliverable_city_type'] != ALL) {
                $cities = array();
                $city_ids = explode(",", $product[$i]['deliverable_cities'] ?? '');
                $t->db->select('id,name');
                $t->db->where_in('id', $city_ids);
                $cities = $t->db->get('cities')->result_array();
                $deliverableCities = array();
                foreach ($cities as $city) {
                    $deliverableCities[] = array(
                        'id' => $city['id'],
                        'name' => $city['name']
                    );
                }
                $product[$i]['deliverable_cities'] = isset($product[$i]['deliverable_cities']) && !empty($product[$i]['deliverable_cities']) ? $deliverableCities : '';
            } else {
                $product[$i]['deliverable_cities'] = '';
            }

            /* check product delivrable or not */
            if ($is_deliverable != NULL) {
                $zipcode = fetch_details('zipcodes', ['zipcode' => $is_deliverable], 'id');
                if (!empty($zipcode)) {
                    $product[$i]['is_deliverable'] = is_product_delivarable($type = 'zipcode', $zipcode[0]['id'], $product[$i]['id']);
                } else {
                    $product[$i]['is_deliverable'] = false;
                }
            } else {
                $product[$i]['is_deliverable'] = false;
            }

            if ($product[$i]['deliverable_type'] == 1) {
                $product[$i]['is_deliverable'] = true;
            }

            $product[$i]['tags'] = (!empty($product[$i]['tags'])) ? explode(",", $product[$i]['tags']) : [];
            $product[$i]['video'] = (isset($product[$i]['video_type']) && (!empty($product[$i]['video_type']) || $product[$i]['video_type'] != NULL)) ? (($product[$i]['video_type'] == 'youtube' || $product[$i]['video_type'] == 'vimeo') ? $product[$i]['video'] : base_url($product[$i]['video'])) : "";
            $product[$i]['minimum_order_quantity'] = isset($product[$i]['minimum_order_quantity']) && (!empty($product[$i]['minimum_order_quantity'])) ? $product[$i]['minimum_order_quantity'] : 1;
            $product[$i]['quantity_step_size'] = isset($product[$i]['quantity_step_size']) && (!empty($product[$i]['quantity_step_size'])) ? $product[$i]['quantity_step_size'] : 1;

            if (!empty($product[$i]['variants'])) {

                $count_stock = array();
                $is_purchased_count = array();

                for ($k = 0; $k < count($product[$i]['variants']); $k++) {

                    $variant_other_images = $variant_other_images_sm = $variant_other_images_md = json_decode((string) $product[$i]['variants'][$k]['images'], 1);

                    if (!empty($variant_other_images[0]) && isset($variant_other_images[0])) {

                        $product[$i]['variants'][$k]['variant_relative_path'] = isset($product[$i]['variants'][$k]['images']) && !empty($product[$i]['variants'][$k]['images']) ? json_decode($product[$i]['variants'][$k]['images']) : [];
                        $counter = 0;
                        foreach ($variant_other_images_md as $row) {
                            $variant_other_images_md[$counter] = get_image_url($variant_other_images_md[$counter], 'thumb', 'md');
                            $counter++;
                        }
                        $product[$i]['variants'][$k]['images_md'] = isset($variant_other_images_md) && !empty($variant_other_images_md) ? $variant_other_images_md : "";

                        $counter = 0;
                        foreach ($variant_other_images_sm as $row) {
                            $variant_other_images_sm[$counter] = get_image_url($variant_other_images_sm[$counter], 'thumb', 'sm');
                            $counter++;
                        }
                        $product[$i]['variants'][$k]['images_sm'] = $variant_other_images_sm;

                        $counter = 0;
                        foreach ($variant_other_images as $row) {
                            $variant_other_images[$counter] = get_image_url($variant_other_images[$counter]);
                            $counter++;
                        }
                        $product[$i]['variants'][$k]['images'] = isset($variant_other_images) && !empty($variant_other_images) ? $variant_other_images : "";
                    } else {
                        $product[$i]['variants'][$k]['images'] = array();
                        $product[$i]['variants'][$k]['images_md'] = array();
                        $product[$i]['variants'][$k]['images_sm'] = array();
                        $product[$i]['variants'][$k]['variant_relative_path'] = array();
                    }

                    $product[$i]['variants'][$k]['swatche_type'] = (!empty($product[$i]['variants'][$k]['swatche_type'])) ? $product[$i]['variants'][$k]['swatche_type'] : "0";
                    $product[$i]['variants'][$k]['swatche_value'] = (!empty($product[$i]['variants'][$k]['swatche_value'])) ? $product[$i]['variants'][$k]['swatche_value'] : "0";
                    if (($product[$i]['stock_type'] == 0 || $product[$i]['stock_type'] == null)) {
                        if ($product[$i]['availability'] != null) {
                            $product[$i]['variants'][$k]['availability'] = $product[$i]['availability'];
                        }
                    } else {
                        $product[$i]['variants'][$k]['availability'] = ($product[$i]['variants'][$k]['availability'] != null) ? $product[$i]['variants'][$k]['availability'] : '1';
                        array_push($count_stock, $product[$i]['variants'][$k]['availability']);
                    }
                    if (($product[$i]['stock_type'] == 0)) {
                        $product[$i]['variants'][$k]['stock'] = isset($product[$i]['variants'][$k]['stock']) && !empty($product[$i]['variants'][$k]['stock']) ? get_stock($product[$i]['id'], 'product') : '';
                    } else {
                        $product[$i]['variants'][$k]['stock'] = isset($product[$i]['variants'][$k]['stock']) && !empty($product[$i]['variants'][$k]['stock']) ? get_stock($product[$i]['variants'][$k]['id'], 'variant') : '';
                    }

                    $percentage = (isset($product[$i]['tax_percentage']) && intval($product[$i]['tax_percentage']) > 0 && $product[$i]['tax_percentage'] != null) ? $product[$i]['tax_percentage'] : '0';

                    if ((isset($product[$i]['is_prices_inclusive_tax']) && $product[$i]['is_prices_inclusive_tax'] == 0) || (!isset($product[$i]['is_prices_inclusive_tax'])) && $percentage > 0) {
                        $product[$i]['variants'][$k]['orignal_price'] = strval($product[$i]['variants'][$k]['price']);
                        $product[$i]['variants'][$k]['orignal_special_price'] = strval($product[$i]['variants'][$k]['special_price']);
                        $product[$i]['variants'][$k]['price'] = strval(calculatePriceWithTax($percentage, $product[$i]['variants'][$k]['price']));
                        $product[$i]['variants'][$k]['special_price'] = strval(calculatePriceWithTax($percentage, $product[$i]['variants'][$k]['special_price']));
                    } else {
                        $product[$i]['variants'][$k]['orignal_price'] = strval($product[$i]['variants'][$k]['price']);
                        $product[$i]['variants'][$k]['orignal_special_price'] = strval($product[$i]['variants'][$k]['special_price']);
                        $product[$i]['variants'][$k]['price'] = strval($product[$i]['variants'][$k]['price']);
                        $product[$i]['variants'][$k]['special_price'] = strval($product[$i]['variants'][$k]['special_price']);
                    }

                    if (isset($user_id) && $user_id != NULL && $is_detailed_data != '' && $is_detailed_data == 1) {
                        $user_cart_data = $t->db->select('qty as cart_count')->where(['product_variant_id' => $product[$i]['variants'][$k]['id'], 'user_id' => $user_id, 'is_saved_for_later' => 0])->get('cart')->result_array();
                        if (!empty($user_cart_data)) {
                            $product[$i]['variants'][$k]['cart_count'] = $user_cart_data[0]['cart_count'];
                        } else {
                            $product[$i]['variants'][$k]['cart_count'] = "0";
                        }
                        $is_purchased = $t->db->where(['oi.product_variant_id' => $product[$i]['variants'][$k]['id'], 'oi.user_id' => $user_id])->order_by('oi.id', 'DESC')->get('order_items oi')->result_array();

                        foreach ($is_purchased as $item) {
                            if (strtolower($item['active_status']) == 'delivered') {
                                array_push($is_purchased_count, 1);
                                $product[$i]['variants'][$k]['is_purchased'] = 1;
                                // If any item meets the condition, set the flag and break out of the loop
                            } else {
                                array_push($is_purchased_count, 0);
                                $product[$i]['variants'][$k]['is_purchased'] = 0;
                            }
                        }

                        $user_rating = $t->db->select('rating,comment')->where(['user_id' => $user_id, 'product_id' => $product[$i]['id']])->get('product_rating')->result_array();
                        if (!empty($user_rating)) {
                            $product[$i]['user']['user_rating'] = (isset($product[$i]['user']['user_rating']) && (!empty($product[$i]['user']['user_rating']))) ? $user_rating[0]['rating'] : '';
                            $product[$i]['user']['user_comment'] = (isset($product[$i]['user']['user_comment']) && (!empty($product[$i]['user']['user_comment']))) ? $user_rating[0]['user_comment'] : '';
                        }
                    } else {
                        $product[$i]['variants'][$k]['cart_count'] = "0";
                    }
                }
            }

            if (($is_detailed_data != null && $is_detailed_data == 1)) {

                $is_purchased_count = array_count_values($is_purchased_count);
                $is_purchased_count = array_keys($is_purchased_count);
                $product[$i]['is_purchased'] = (isset($is_purchased) && array_sum($is_purchased_count) == 1) ? true : false;
                if (($product[$i]['stock_type'] != null && !empty($product[$i]['stock_type']))) {

                    //Case 2 & 3 : Product level(variable product) ||  Variant level(variable product)
                    if ($product[$i]['stock_type'] == 1 || $product[$i]['stock_type'] == 2) {
                        $counts = array_count_values($count_stock);
                        $counts = array_keys($counts);
                        if (isset($counts)) {
                            $product[$i]['availability'] = array_sum($counts);
                        }
                    }
                }
            }

            if (isset($user_id) && $user_id != null) {
                $fav = $t->db->where(['product_id' => $product[$i]['id'], 'user_id' => $user_id])->get('favorites')->num_rows();
                $product[$i]['is_favorite'] = $fav;
            } else {
                $product[$i]['is_favorite'] = '0';
            }

            $product[$i]['image_md'] = get_image_url($product[$i]['image'], 'thumb', 'md');
            $product[$i]['image_sm'] = get_image_url($product[$i]['image'], 'thumb', 'sm');
            $product[$i]['image'] = get_image_url($product[$i]['image']);
            $other_images = $other_images_sm = $other_images_md = json_decode($product[$i]['other_images'], 1);

            if (!empty($other_images)) {

                $k = 0;
                foreach ($other_images_md as $row) {
                    $other_images_md[$k] = get_image_url($row, 'thumb', 'md');
                    $k++;
                }
                $other_images_md = (array) $other_images_md;
                $other_images_md = array_values($other_images_md);
                $product[$i]['other_images_md'] = $other_images_md;

                $k = 0;
                foreach ($other_images_sm as $row) {
                    $other_images_sm[$k] = get_image_url($row, 'thumb', 'sm');
                    $k++;
                }
                $other_images_sm = (array) $other_images_sm;
                $other_images_sm = array_values($other_images_sm);
                $product[$i]['other_images_sm'] = $other_images_sm;

                $k = 0;
                foreach ($other_images as $row) {
                    $other_images[$k] = get_image_url($row);
                    $k++;
                }
                $other_images = (array) $other_images;
                $other_images = array_values($other_images);
                $product[$i]['other_images'] = $other_images;
            } else {
                $product[$i]['other_images'] = array();
                $product[$i]['other_images_sm'] = array();
                $product[$i]['other_images_md'] = array();
            }
            $tags_to_strip = array("table", "<th>", "<td>");
            $replace_with = array("", "h3", "p");
            $n = 0;
            foreach ($tags_to_strip as $tag) {
                $product[$i]['description'] = !empty($product[$i]['description']) ? output_escaping(str_replace('\r\n', '&#13;&#10;', (string) $product[$i]['description'])) : "";
                $product[$i]['extra_description'] = !empty($product[$i]['extra_description']) && $product[$i]['extra_description'] != null ? output_escaping(str_replace('\r\n', '&#13;&#10;', (string) $product[$i]['extra_description'])) : "";
                $n++;
            }

            if (($is_detailed_data != null && $is_detailed_data == 1)) {

                $variant_attributes = [];
                $attributes_array = explode(',', $product[$i]['variants'][0]['attr_name']);

                foreach ($attributes_array as $attribute) {
                    $attribute = trim($attribute);
                    $key = array_search($attribute, array_column($product[$i]['attributes'], 'name'), false);
                    if (($key === 0 || !empty($key)) && isset($product[0]['attributes'][$key])) {
                        $variant_attributes[$key]['ids'] = $product[0]['attributes'][$key]['ids'];
                        $variant_attributes[$key]['values'] = $product[0]['attributes'][$key]['value'];
                        $variant_attributes[$key]['swatche_type'] = $product[0]['attributes'][$key]['swatche_type'];
                        $variant_attributes[$key]['swatche_value'] = $product[0]['attributes'][$key]['swatche_value'];
                        $variant_attributes[$key]['attr_name'] = $attribute;
                    }
                }
                $product[$i]['variant_attributes'] = $variant_attributes;
            }
        }

        if (isset($count_res[0]['cal_discount_percentage'])) {
            $dicounted_total = array_values(array_filter(explode(',', $count_res[0]['cal_discount_percentage'])));
        } else {
            $dicounted_total = 0;
        }
        $response['total'] = (isset($filter) && !empty($filter['discount'])) ? count($dicounted_total) : $count_res[0]['total'];

        array_push($attribute_values_ids, $count_res[0]['attr_value_ids']);
        $attribute_values_ids = implode(",", $attribute_values_ids);
        $attr_value_ids = array_filter(array_unique(explode(',', $attribute_values_ids)));
    }
    $response['min_price'] = $min_price;
    $response['max_price'] = $max_price;
    $response['product'] = $product;
    if (isset($filter) && $filter != null) {
        if (!empty($attr_value_ids)) {
            $response['filters'] = get_attribute_values_by_id($attr_value_ids);
        }
    } else {
        $response['filters'] = [];
    }

    return $response;
}

function update_details($set, $where, $table, $escape = true)
{
    $t = &get_instance();
    $t->db->trans_start();
    if ($escape) {
        $set = escape_array($set);
    }
    $t->db->set($set)->where($where)->update($table);
    $t->db->trans_complete();
    $response = FALSE;
    if ($t->db->trans_status() === TRUE) {
        $response = TRUE;
    }
    return $response;
}

function delete_image($id, $path, $field, $img_name, $table_name, $isjson = TRUE)
{
    $t = &get_instance();
    $t->db->trans_start();
    if ($isjson == TRUE) {
        $image_set = fetch_details($table_name, ['id' => $id], $field);
        $diff_new_image_set = json_decode($image_set[0][$field], 1);
        $new_image_set = escape_array(array_diff((array) $diff_new_image_set, array($img_name)));
        $new_image_set = json_encode($new_image_set);
        $t->db->set([$field => $new_image_set])->where('id', $id)->update($table_name);
        $t->db->trans_complete();
        $response = FALSE;
        if ($t->db->trans_status() === TRUE) {
            $response = TRUE;
        }
    } else {
        $t->db->set([$field => ' '])->where(['id' => $id])->update($table_name);
        $t->db->trans_complete();
        $response = FALSE;
        if ($t->db->trans_status() === TRUE) {
            $response = TRUE;
        }
    }
    return $response;
}

function delete_details($where, $table)
{
    $t = &get_instance();
    if ($t->db->where($where)->delete($table)) {
        return true;
    } else {
        return false;
    }
}

//JSON Validator function
function is_json($data = NULL)
{
    if (!empty($data)) {
        @json_decode($data);
        return (json_last_error() === JSON_ERROR_NONE);
    }
    return false;
}

//validate_promo_code
function validate_promo_code($promo_code, $user_id, $final_total)
{

    if (isset($promo_code) && !empty($promo_code)) {
        $t = &get_instance();

        //Fetch Promo Code Details
        $promo_code = $t->db->select('pc.*,count(o.id) as promo_used_counter ,( SELECT count(user_id) from orders where user_id =' . $user_id . ' and promo_code ="' . $promo_code . '") as user_promo_usage_counter ')
            ->join('orders o', 'o.promo_code=pc.promo_code', 'left')
            ->where(['pc.promo_code' => $promo_code, 'pc.status' => '1', ' start_date <= ' => date('Y-m-d'), '  end_date >= ' => date('Y-m-d')])
            ->get('promo_codes pc')->result_array();
        if (!empty($promo_code[0]['id'])) {

            if (intval($promo_code[0]['promo_used_counter']) < intval($promo_code[0]['no_of_users'])) {

                if ($final_total >= intval($promo_code[0]['minimum_order_amount'])) {

                    if ($promo_code[0]['repeat_usage'] == 1 && ($promo_code[0]['user_promo_usage_counter'] <= $promo_code[0]['no_of_repeat_usage'])) {
                        if (intval($promo_code[0]['user_promo_usage_counter']) <= intval($promo_code[0]['no_of_repeat_usage'])) {

                            $response['error'] = false;
                            $response['message'] = 'The promo code is valid';

                            if ($promo_code[0]['discount_type'] == 'percentage') {
                                $promo_code_discount = floatval($final_total * $promo_code[0]['discount'] / 100);
                            } else {
                                $promo_code_discount = $promo_code[0]['discount'];
                            }
                            if ($promo_code_discount <= $promo_code[0]['max_discount_amount']) {
                                $total = (isset($promo_code[0]['is_cashback']) && $promo_code[0]['is_cashback'] == 0) ? floatval($final_total) - $promo_code_discount : floatval($final_total);
                            } else {
                                $total = (isset($promo_code[0]['is_cashback']) && $promo_code[0]['is_cashback'] == 0) ? floatval($final_total) - $promo_code[0]['max_discount_amount'] : floatval($final_total);
                                $promo_code_discount = $promo_code[0]['max_discount_amount'];
                            }
                            $promo_code[0]['final_total'] = strval(floatval($total));
                            $promo_code[0]['image'] = (isset($promo_code[0]['image']) && !empty($promo_code[0]['image'])) ? $promo_code[0]['image'] : '';
                            $promo_code[0]['final_discount'] = strval(floatval($promo_code_discount));
                            $response['data'] = $promo_code;
                            return $response;
                        } else {

                            $response['error'] = true;
                            $response['message'] = 'This promo code cannot be redeemed as it exceeds the usage limit';
                            $response['data']['final_total'] = strval(floatval($final_total));
                            return $response;
                        }
                    } else if ($promo_code[0]['repeat_usage'] == 0 && ($promo_code[0]['user_promo_usage_counter'] <= 0)) {
                        if (intval($promo_code[0]['user_promo_usage_counter']) <= intval($promo_code[0]['no_of_repeat_usage'])) {

                            $response['error'] = false;
                            $response['message'] = 'The promo code is valid';

                            if ($promo_code[0]['discount_type'] == 'percentage') {
                                $promo_code_discount = floatval($final_total * $promo_code[0]['discount'] / 100);
                            } else {
                                $promo_code_discount = floatval($final_total - $promo_code[0]['discount']);
                            }
                            if ($promo_code_discount <= $promo_code[0]['max_discount_amount']) {
                                $total = (isset($promo_code[0]['is_cashback']) && $promo_code[0]['is_cashback'] == 0) ? floatval($final_total) - $promo_code_discount : floatval($final_total);
                            } else {
                                $total = (isset($promo_code[0]['is_cashback']) && $promo_code[0]['is_cashback'] == 0) ? floatval($final_total) - $promo_code[0]['max_discount_amount'] : floatval($final_total);
                                $promo_code_discount = $promo_code[0]['max_discount_amount'];
                            }
                            $promo_code[0]['final_total'] = strval(floatval($total));
                            $promo_code[0]['final_discount'] = strval(floatval($promo_code_discount));
                            $response['data'] = $promo_code;
                            return $response;
                        } else {

                            $response['error'] = true;
                            $response['message'] = 'This promo code cannot be redeemed as it exceeds the usage limit';
                            $response['data']['final_total'] = strval(floatval($final_total));
                            return $response;
                        }
                    } else {
                        $response['error'] = true;
                        $response['message'] = 'The promo has already been redeemed. cannot be reused';
                        $response['data']['final_total'] = strval(floatval($final_total));
                        return $response;
                    }
                } else {

                    $response['error'] = true;
                    $response['message'] = 'This promo code is applicable only for amount greater than or equal to ' . $promo_code[0]['minimum_order_amount'];
                    $response['data']['final_total'] = strval(floatval($final_total));
                    return $response;
                }
            } else {

                $response['error'] = true;
                $response['message'] = "This promo code is applicable only for first " . $promo_code[0]['no_of_users'] . " users";
                $response['data']['final_total'] = strval(floatval($final_total));
                return $response;
            }
        } else {
            $response['error'] = true;
            $response['message'] = 'The promo code is not available or expired';
            $response['data']['final_total'] = strval(floatval($final_total));
            return $response;
        }
    }
}

//update_wallet_balance
function update_wallet_balance($operation, $user_id, $amount, $message = "Balance Debited", $order_item_id = "", $is_refund = 0, $transaction_type = 'wallet', $status = '')
{

    $t = &get_instance();
    $user_balance = $t->db->select('balance')->where(['id' => $user_id])->get('users')->result_array();
    if (!empty($user_balance)) {
        if ($operation == 'debit' && $amount > $user_balance[0]['balance']) {
            $response['error'] = true;
            $response['message'] = "Debited amount can't exceeds the user balance !";
            $response['data'] = array();
            return $response;
        }

        if ($amount == 0) {
            $response['error'] = true;
            $response['message'] = "Amount can't be Zero !";
            $response['data'] = array();
            return $response;
        }

        if ($user_balance[0]['balance'] >= 0) {
            $t = &get_instance();
            $data = [
                'transaction_type' => $transaction_type,
                'user_id' => $user_id,
                'type' => $operation,
                'amount' => $amount,
                'message' => $message,
                'order_item_id' => $order_item_id,
                'is_refund' => $is_refund,
                'status' => (isset($status) && !empty($status)) ? $status : 'success',
            ];
            $payment_data = fetch_details('transactions', ['order_item_id' => $order_item_id], 'type');
            if ($operation == 'debit') {
                $data['message'] = (isset($message)) ? $message : 'Balance Debited';
                $data['type'] = 'debit';
                $t->db->set('balance', 'balance - ' . $amount, false)->where('id', $user_id)->update('users');
            } else if ($operation == 'credit') {
                $data['message'] = (isset($message)) ? $message : 'Balance Credited';
                $data['type'] = 'credit';
                if ($payment_data[0]['type'] != 'razorpay') {
                    $t->db->set('balance', 'balance + ' . $amount, false)->where('id', $user_id)->update('users');
                }
            } else {
                $data['message'] = (isset($message)) ? $message : 'Balance refuned';
                $data['type'] = 'refund';
                if ($payment_data[0]['type'] != 'razorpay') {
                    $t->db->set('balance', 'balance + ' . $amount, false)->where('id', $user_id)->update('users');
                }
            }
            $data = escape_array($data);
            $t->db->insert('transactions', $data);
            $response['error'] = false;
            $response['message'] = "Balance Update Successfully";
            $response['data'] = array();
        } else {
            $response['error'] = true;
            $response['message'] = ($user_balance[0]['balance'] != 0) ? "User's Wallet balance less than " . $user_balance[0]['balance'] . " can be used only" : "Doesn't have sufficient wallet balance to proceed further.";
            $response['data'] = array();
        }
    } else {
        $response['error'] = true;
        $response['message'] = "User does not exist";
        $response['data'] = array();
    }
    return $response;
}

function get_token()
{
    $file_name = get_settings("service_account_file");

    $privateKeyFile = FCPATH . $file_name;
    $scope = 'https://www.googleapis.com/auth/firebase.messaging';

    // Read the private key file
    $privateKey = file_get_contents($privateKeyFile);
    $privateKeyData = json_decode($privateKey, true);

    // Get the private key and client email from the JSON data
    $privateKeyPem = $privateKeyData['private_key'];
    $clientEmail = $privateKeyData['client_email'];

    // Create a JSON Web Token (JWT)
    $header = [
        'alg' => 'RS256',
        'typ' => 'JWT'
    ];
    $payload = [
        'iss' => $clientEmail,
        'scope' => $scope,
        'aud' => 'https://oauth2.googleapis.com/token',
        'exp' => time() + 3600,
        'iat' => time()
    ];

    $headerEncoded = base64_encode(json_encode($header));
    $payloadEncoded = base64_encode(json_encode($payload));

    $dataEncoded = $headerEncoded . '.' . $payloadEncoded;

    // Sign the JWT with the private key
    openssl_sign($dataEncoded, $signature, $privateKeyPem, 'SHA256');
    $signatureEncoded = base64_encode($signature);

    $jwtEncoded = $dataEncoded . '.' . $signatureEncoded;

    // Exchange the JWT for an access token
    $postData = http_build_query([
        'grant_type' => 'urn:ietf:params:oauth:grant-type:jwt-bearer',
        'assertion' => $jwtEncoded
    ]);

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, 'https://oauth2.googleapis.com/token');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);

    $response = curl_exec($ch);
    curl_close($ch);

    $responseData = json_decode($response, true);
    $accessToken = $responseData['access_token'];

    return $accessToken;
}


function sendNotificationToPlatform($platform, $chunk, $url, $headers, $customBodyFields, $title, $message)
{
    $mh = curl_multi_init();
    $curl_handles = [];


    foreach ($chunk as $registrationID) {

        if (in_array($registrationID, ['BLACKLISTED', '', '-'])) {
            continue;
        }
        $data = [
            "message" => [
                "token" => $registrationID,
                "data" => $customBodyFields,
                "notification" => [
                    "title" => $customBodyFields['title'],
                    "body" => $customBodyFields['body'],
                    "image" => (isset($customBodyFields['image']) && !empty($customBodyFields['image'])) ? $customBodyFields['image'] : '',
                ],
            ]
        ];

        // Set platform-specific notification details
        if ($platform == 'android') {
            $data["message"]["android"] = [
                "data" => [
                    "title" => $customBodyFields['title'],
                    "body" => $customBodyFields['body'],
                    "type" => $customBodyFields['type'],
                    "type_id" => (isset($customBodyFields['type_id']) && !empty($customBodyFields['type_id'])) ? $customBodyFields['type_id'] : '',
                    "image" => (isset($customBodyFields['image']) && !empty($customBodyFields['image'])) ? $customBodyFields['image'] : '',
                ]
            ];
        } elseif ($platform == 'ios') {
            $data["message"]["apns"] = [
                "headers" => ["apns-priority" => "10"],
                "payload" => [
                    "aps" => [
                        "alert" => ["title" => $customBodyFields['title'], "body" => $customBodyFields['body']],
                        "data" => $customBodyFields,
                    ]
                ]
            ];
        }

        $encodedData = json_encode($data);
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
        curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $encodedData);
        curl_multi_add_handle($mh, $ch);
        $curl_handles[] = $ch;
    }

    // Execute multi-curl request
    $running = null;
    do {
        curl_multi_exec($mh, $running);
    } while ($running > 0);

    // Handle responses
    foreach ($curl_handles as $ch) {
        $result = curl_multi_getcontent($ch);
        if ($result === false) {
            error_log('Curl failed: ' . curl_error($ch));
        }

        curl_multi_remove_handle($mh, $ch);
        curl_close($ch);
    }

    curl_multi_close($mh);
}

function send_notification($fcmMsg, $registrationIDs_chunks, $customBodyFields = [], $title = "test title", $message = "test message", $type = "test type", $platform_type = null)
{
    $project_id = get_settings("firebase_project_id");
    $url = 'https://fcm.googleapis.com/v1/projects/' . $project_id . '/messages:send';
    $access_token = get_token();
    $headers = [
        'Authorization: Bearer ' . $access_token,
        'Content-Type: application/json',
    ];

    // Determine the platforms to process based on platform_type
    $platforms_to_process = [];
    if ($platform_type) {
        // Process only the specified platform
        if (isset($registrationIDs_chunks[$platform_type])) {
            $platforms_to_process[] = $platform_type;
        }
    } else {
        // Process both platforms by default
        $platforms_to_process = ['android', 'ios'];
    }

    // print_r($registrationIDs_chunks);
    // Loop through the selected platforms
    foreach ($platforms_to_process as $platform) {
        if (isset($registrationIDs_chunks[$platform])) {
            foreach ($registrationIDs_chunks[$platform] as $chunk) {
                sendNotificationToPlatform($platform, array_unique($chunk), $url, $headers, $customBodyFields, $title, $message);
            }
        }
    }

    return true;
}


function get_attribute_values_by_pid($id)
{
    $t = &get_instance();
    $swatche_type = $swatche_values1 = array();
    $attribute_values = $t->db->select(" group_concat(av.id ORDER BY av.id ASC) as ids,group_concat(' ', av.value  ORDER BY av.id ASC ) as value ,a.name as attr_name, a.name, GROUP_CONCAT(av.swatche_type ORDER BY av.id ASC ) as swatche_type , GROUP_CONCAT(av.swatche_value  ORDER BY av.id ASC) as swatche_value")
        ->join('attribute_values av ', 'FIND_IN_SET(av.id, pa.attribute_value_ids ) > 0', 'left')
        ->join('attributes a', 'a.id = av.attribute_id', 'left')
        ->where('pa.product_id', $id)->group_by('a.name')->get('product_attributes pa')->result_array();
    if (!empty($attribute_values)) {

        for ($i = 0; $i < count($attribute_values); $i++) {
            $swatche_type = array();
            $swatche_values1 = array();
            $swatche_type = (isset($attribute_values[$i]['swatche_type']) && !empty($attribute_values[$i]['swatche_type'])) ? explode(",", $attribute_values[$i]['swatche_type']) : [];
            $swatche_values = (isset($attribute_values[$i]['swatche_value']) && !empty($attribute_values[$i]['swatche_value'])) ? explode(",", $attribute_values[$i]['swatche_value']) : [];

            for ($j = 0; $j < count($swatche_type); $j++) {
                if ($swatche_type[$j] == "2") {
                    $swatche_values1[$j] = get_image_url($swatche_values[$j], 'thumb', 'sm');
                } else if ($swatche_type[$j] == "0") {
                    $swatche_values1[$j] = '0';
                } else if ($swatche_type[$j] == "1") {
                    $swatche_values1[$j] = $swatche_values[$j];
                }
                $row = implode(',', $swatche_values1);
                $attribute_values[$i]['swatche_value'] = $row;
            }
            $attribute_values[$i] = output_escaping($attribute_values[$i]);
        }
    }
    return $attribute_values;
}

function get_attribute_values_by_id($id)
{
    $t = &get_instance();
    $attribute_values = $t->db->select(" GROUP_CONCAT(av.value  ORDER BY av.id ASC) as attribute_values ,GROUP_CONCAT(av.id ORDER BY av.id ASC ) as attribute_values_id ,a.name , GROUP_CONCAT(av.swatche_type ORDER BY av.id ASC ) as swatche_type , GROUP_CONCAT(av.swatche_value ORDER BY av.id ASC ) as swatche_value")
        ->join(' attributes a ', 'av.attribute_id = a.id ', 'inner')
        ->where_in('av.id', $id)->group_by('a.name')->get('attribute_values av')->result_array();
    if (!empty($attribute_values)) {
        for ($i = 0; $i < count($attribute_values); $i++) {
            if ($attribute_values[$i]['swatche_type'] != "") {
                $swatche_type = array();
                $swatche_values1 = array();
                $swatche_type = explode(",", $attribute_values[$i]['swatche_type']);
                $swatche_values = isset($attribute_values[$i]['swatche_value']) && !empty($attribute_values[$i]['swatche_value']) ? explode(",", $attribute_values[$i]['swatche_value']) : [];

                for ($j = 0; $j < count($swatche_type); $j++) {
                    if ($swatche_type[$j] == "2") {
                        $swatche_values1[$j] = get_image_url($swatche_values[$j], 'thumb', 'sm');
                    } else if ($swatche_type[$j] == "0") {
                        $swatche_values1[$j] = '0';
                    } else if ($swatche_type[$j] == "1") {
                        $swatche_values1[$j] = $swatche_values[$j];
                    }
                    $row = implode(',', $swatche_values1);
                    $attribute_values[$i]['swatche_value'] = $row;
                }
            }
            $attribute_values[$i] = output_escaping($attribute_values[$i]);
        }
    }

    return $attribute_values;
}

function get_variants_values_by_pid($id, $status = [1])
{
    $t = &get_instance();
    $varaint_values = $t->db->select("pv.*,pv.product_id,group_concat(av.id  ORDER BY av.id ASC) as variant_ids,group_concat( ' ' ,a.name ORDER BY av.id ASC) as attr_name, group_concat(av.value ORDER BY av.id ASC) as variant_values , pv.price as price , GROUP_CONCAT(av.swatche_type ORDER BY av.id ASC ) as swatche_type , GROUP_CONCAT(av.swatche_value ORDER BY av.id ASC ) as swatche_value")
        ->join('attribute_values av ', 'FIND_IN_SET(av.id, pv.attribute_value_ids ) > 0', 'left')
        ->join('attributes a', 'a.id = av.attribute_id', 'left')
        ->where(['pv.product_id' => $id])->where_in('pv.status', $status)->group_by('pv.id')->order_by('pv.id')->get('product_variants pv')->result_array();
    if (!empty($varaint_values)) {
        for ($i = 0; $i < count($varaint_values); $i++) {
            if ($varaint_values[$i]['swatche_type'] != "") {
                $swatche_type = array();
                $swatche_values1 = array();
                $swatche_type = explode(",", $varaint_values[$i]['swatche_type']);
                $swatche_values = explode(",", (string) $varaint_values[$i]['swatche_value']);

                for ($j = 0; $j < count($swatche_type); $j++) {
                    if ($swatche_type[$j] == "2") {
                        $swatche_values1[$j] = get_image_url($swatche_values[$j], 'thumb', 'sm');
                    } else if ($swatche_type[$j] == "0") {
                        $swatche_values1[$j] = '0';
                    } else if ($swatche_type[$j] == "1") {
                        $swatche_values1[$j] = $swatche_values[$j];
                    }
                    $row = implode(',', $swatche_values1);
                    $varaint_values[$i]['swatche_value'] = $row;
                }
            }
            $varaint_values[$i] = output_escaping($varaint_values[$i]);
            $varaint_values[$i]['availability'] = isset($varaint_values[$i]['availability']) && ($varaint_values[$i]['availability'] != "") ? $varaint_values[$i]['availability'] : '';
        }
    }
    return $varaint_values;
}

function get_variants_values_by_id($id)
{
    $t = &get_instance();
    $varaint_values = $t->db->select("pv.*,pv.product_id,group_concat(av.id separator ', ') as varaint_ids,group_concat(a.name separator ', ') as attr_name, group_concat(av.value separator ', ') as variant_values")
        ->join('attribute_values av ', 'FIND_IN_SET(av.id, pv.attribute_value_ids ) > 0', 'left')
        ->join('attributes a', 'a.id = av.attribute_id', 'left')
        ->where('pv.id', $id)->group_by('pv.id')->order_by('pv.id')->get('product_variants pv')->result_array();
    if (!empty($varaint_values)) {
        for ($i = 0; $i < count($varaint_values); $i++) {
            $varaint_values[$i] = output_escaping($varaint_values[$i]);
            $varaint_values[$i]['availability'] = isset($varaint_values[$i]['availability']) && ($varaint_values[$i]['availability'] != "") ? $varaint_values[$i]['availability'] : '';
            $varaint_values[$i]['images'] = isset($varaint_values[$i]['images']) && (!empty($varaint_values[$i]['images'])) ? $varaint_values[$i]['images'] : '';
        }
    }
    return $varaint_values;
}

//Used in form validation(API)
function userrating_check()
{
    $t = &get_instance();
    $user_id = $t->input->post('user_id', true);
    $product_id = $t->input->post('product_id', true);
    $res = $t->db->select('*')->where(['user_id' => $user_id, 'product_id' => $product_id])->get('product_rating');
    if ($res->num_rows() > 0) {
        return false;
    } else {
        return true;
    }
}

//update_stock()
function update_stock($product_variant_ids, $qtns, $type = '')
{

    /*
        --First Check => Is stock management active (Stock type != NULL)
        Case 1 : Simple Product
        Case 2 : Variable Product (Product Level,Variant Level)

        Stock Type :
            0 => Simple Product(simple product)
                  -Stock will be stored in (product)master table
            1 => Product level(variable product)
                -Stock will be stored in product_variant table
            2 => Variant level(variable product)
                -Stock will be stored in product_variant table
        */
    $t = &get_instance();
    $ids = implode(',', (array) $product_variant_ids);
    $res = $t->db->select('p.*,pv.*,p.id as p_id,pv.id as pv_id,p.stock as p_stock,pv.stock as pv_stock')->where_in('pv.id', $product_variant_ids)->join('products p', 'pv.product_id = p.id')->order_by('FIELD(pv.id,' . $ids . ')')->get('product_variants pv')->result_array();

    for ($i = 0; $i < count($res); $i++) {
        if (($res[$i]['stock_type'] != null || $res[$i]['stock_type'] != "")) {

            /* Case 1 : Simple Product(simple product) */
            if ($res[$i]['stock_type'] == 0) {
                if ($type == 'plus') {
                    if ($res[$i]['p_stock'] != null) {
                        $stock = intval($res[$i]['p_stock']) + intval($qtns[$i]);
                        $t->db->where('id', $res[$i]['p_id'])->update('products', ['stock' => $stock]);
                        if ($stock > 0) {
                            $t->db->where('id', $res[$i]['p_id'])->update('products', ['availability' => '1']);
                        }
                    }
                } else {
                    if ($res[$i]['p_stock'] != null && $res[$i]['p_stock'] > 0) {
                        $stock = intval($res[$i]['p_stock']) - intval($qtns[$i]);
                        $t->db->where('id', $res[$i]['p_id'])->update('products', ['stock' => $stock]);
                        if ($stock == 0) {
                            $t->db->where('id', $res[$i]['p_id'])->update('products', ['availability' => '0']);
                        }
                    }
                }
            }

            /* Case 2 : Product level(variable product) */
            if ($res[$i]['stock_type'] == 1) {
                if ($type == 'plus') {
                    if ($res[$i]['pv_stock'] != null) {
                        $stock = intval($res[$i]['pv_stock']) + intval($qtns[$i]);
                        $t->db->where('product_id', $res[$i]['p_id'])->update('product_variants', ['stock' => $stock]);
                        if ($stock > 0) {
                            $t->db->where('product_id', $res[$i]['p_id'])->update('product_variants', ['availability' => '1']);
                        }
                    }
                } else {
                    if ($res[$i]['pv_stock'] != null && $res[$i]['pv_stock'] > 0) {
                        $stock = intval($res[$i]['pv_stock']) - intval($qtns[$i]);
                        $t->db->where('product_id', $res[$i]['p_id'])->update('product_variants', ['stock' => $stock]);
                        if ($stock == 0) {
                            $t->db->where('product_id', $res[$i]['p_id'])->update('product_variants', ['availability' => '0']);
                        }
                    }
                }
            }

            /* Case 3 : Variant level(variable product) */
            if ($res[$i]['stock_type'] == 2) {
                if ($type == 'plus') {
                    if ($res[$i]['pv_stock'] != null) {

                        $stock = intval($res[$i]['pv_stock']) + intval($qtns[$i]);
                        $t->db->where('id', $res[$i]['id'])->update('product_variants', ['stock' => $stock]);
                        if ($stock > 0) {
                            $t->db->where('id', $res[$i]['id'])->update('product_variants', ['availability' => '1']);
                        }
                    }
                } else {
                    if ($res[$i]['pv_stock'] != null && $res[$i]['pv_stock'] > 0) {

                        $stock = intval($res[$i]['pv_stock']) - intval($qtns[$i]);
                        $t->db->where('id', $res[$i]['id'])->update('product_variants', ['stock' => $stock]);
                        if ($stock == 0) {
                            $t->db->where('id', $res[$i]['id'])->update('product_variants', ['availability' => '0']);
                        }
                    }
                }
            }
        }
    }
}

function validate_stock($product_variant_ids, $qtns)
{
    /*
        --First Check => Is stock management active (Stock type != NULL)
        Case 1 : Simple Product
        Case 2 : Variable Product (Product Level,Variant Level)

        Stock Type :
            0 => Simple Product(simple product)
                  -Stock will be stored in (product)master table
            1 => Product level(variable product)
                -Stock will be stored in product_variant table
            2 => Variant level(variable product)
                -Stock will be stored in product_variant table
        */
    $t = &get_instance();
    $response = array();
    $is_exceed_allowed_quantity_limit = false;
    $error = false;
    $count = isset($product_variant_ids) ? count($product_variant_ids) : '';
    for ($i = 0; $i < $count; $i++) {
        $res = $t->db->select('p.*,pv.*,pv.id as pv_id,p.stock as p_stock,p.availability as p_availability,pv.stock as pv_stock,pv.availability as pv_availability,p.name as product_name')->where('pv.id = ', $product_variant_ids[$i])->join('products p', 'pv.product_id = p.id')->get('product_variants pv')->result_array();
        if ($res[0]['total_allowed_quantity'] != null && $res[0]['total_allowed_quantity'] >= 0) {
            $total_allowed_quantity = intval($res[0]['total_allowed_quantity']) - intval($qtns[$i]);
            if ($total_allowed_quantity < 0) {
                $error = true;
                $is_exceed_allowed_quantity_limit = true;
                break;
            }
        }

        if (($res[0]['stock_type'] != null && $res[0]['stock_type'] != '')) {
            //Case 1 : Simple Product(simple product)
            if ($res[0]['stock_type'] == 0) {
                if ($res[0]['p_stock'] != null && $res[0]['p_stock'] != '') {
                    $stock = intval($res[0]['p_stock']) - intval($qtns[$i]);
                    if ($stock < 0 || $res[0]['p_availability'] == 0) {
                        $error = true;
                        break;
                    }
                }
            }
            //Case 2 & 3 : Product level(variable product) ||  Variant level(variable product)
            if ($res[0]['stock_type'] == 1 || $res[0]['stock_type'] == 2) {
                if ($res[0]['pv_stock'] != null && $res[0]['pv_stock'] != '') {
                    $stock = intval($res[0]['pv_stock']) - intval($qtns[$i]);
                    if ($stock < 0 || $res[0]['pv_availability'] == 0) {
                        $error = true;
                        break;
                    }
                }
            }
        }
    }

    if ($error) {
        $response['error'] = true;
        if ($is_exceed_allowed_quantity_limit) {
            $response['message'] = $res[0]['product_name'] . " product's quantity exceeds the allowed limit.Please deduct some quanity in order to purchase the item";
        } else {
            $response['message'] = $res[0]['product_name'] . " product is out of stock.";
        }
    } else {
        $response['error'] = false;
        $response['message'] = "Stock available for purchasing.";
    }
    return $response;
}

//stock_status()
function stock_status($product_variant_id)
{
    /*
        --First Check => Is stock management active (Stock type != NULL)
        Case 1 : Simple Product
        Case 2 : Variable Product (Product Level,Variant Level)

        Stock Type :
            0 => Simple Product(simple product)
                  -Stock will be stored in (product)master table
            1 => Product level(variable product)
                -Stock will be stored in product_variant table
            2 => Variant level(variable product)
                -Stock will be stored in product_variant table
        */
    $t = &get_instance();
    $res = $t->db->select('p.*,pv.*,pv.id as pv_id,p.stock as p_stock,pv.stock as pv_stock')->where_in('pv.id', $product_variant_id)->join('products p', 'pv.product_id = p.id')->get('product_variants pv')->result_array();
    $out_of_stock = false;
    for ($i = 0; $i < count($res); $i++) {
        if (($res[$i]['stock_type'] != null && !empty($res[$i]['stock_type']))) {
            //Case 1 : Simple Product(simple product)
            if ($res[$i]['stock_type'] == 0) {

                if ($res[$i]['p_stock'] == null || $res[$i]['p_stock'] == 0) {
                    $out_of_stock = true;
                    break;
                }
            }
            //Case 2 & 3 : Product level(variable product) ||  Variant level(variable product)
            if ($res[$i]['stock_type'] == 1 || $res[$i]['stock_type'] == 2) {
                if ($res[$i]['pv_stock'] == null || $res[$i]['pv_stock'] == 0) {
                    $out_of_stock = true;
                    break;
                }
            }
        }
    }
    return $out_of_stock;
}

//verify_user()
function verify_user($data)
{
    $t = &get_instance();
    $res = $t->db->where('mobile', $data['mobile'])->get('users')->result_array();
    return $res;
}

//edit_unique($value, $params)
function edit_unique($value, $params)
{
    $CI = &get_instance();

    $CI->form_validation->set_message('edit_unique', "Sorry, that %s is already being used.");

    list($table, $field, $current_id) = explode(".", $params);

    $query = $CI->db->select()->from($table)->where($field, $value)->limit(1)->get();
    if ($query->row() && $query->row()->id != $current_id) {
        return FALSE;
    } else {
        return TRUE;
    }
}

function create_consignment($data)
{
    $t = &get_instance();
    $t->load->model('Order_model');
    $consignment_title = $data['consignment_title'];
    $order_item_ids = $data['selected_items'];
    $order_id = $data['order_id'];
    $product_variant_ids = [];
    $items = fetch_details('order_items', ['order_id' => $order_id], 'active_status,id,product_variant_id,seller_id,user_id');
    $user_res = fetch_details('users', ['id' => $items[0]['user_id']], 'username');
    $user_fcm_ids = fetch_details("user_fcm", ['user_id' => $items[0]['user_id']], 'fcm_id,platform_type');
    $seller_id = $items[0]['seller_id'];
    foreach ($items as $item) {
        foreach ($order_item_ids as $order_item_id) {
            if ($order_item_id == $item['id']) {
                if (is_exist(['order_item_id' => $item['id']], 'consignment_items')) {
                    return [
                        "error" => true,
                        "message" => 'Consignment Already Created!',

                    ];
                }
                array_push($product_variant_ids, $item['product_variant_id']);
                if ($item['active_status'] == 'draft' || $item['active_status'] == 'awaiting') {
                    return [
                        "error" => true,
                        "message" => 'You can\'t ship order Right now Because Order is In Awaiting State, Payment verification is not Done Yet!',

                    ];
                }
                if ($item['active_status'] == 'cancelled' || $item['active_status'] == 'delivered') {
                    return [
                        "error" => true,
                        "message" => 'You can\'t ship Order Because Order is ' . $item['active_status'],
                    ];
                }
            }
        }
    }
    $orders = fetch_details('orders', ['id' => $order_id], 'delivery_charge');
    if (isset($orders) && empty($orders)) {
        return [
            "error" => true,
            "message" => 'Order Not Found',

        ];
    }
    $status = "processed";

    $orders_delivery_charges = $orders[0]['delivery_charge'];
    $consignments = fetch_details('consignments', ['order_id' => $order_id], 'delivery_charge');
    $flag = false;
    $delivery_charge = "0";
    foreach ($consignments as $set_delivery_charge) {
        if ($set_delivery_charge['delivery_charge'] == $orders_delivery_charges) {
            $flag = true;
            break;
        }
    }
    if ($flag == false) {
        $delivery_charge = $orders_delivery_charges;
    }
    $otp = random_int(100000, 999999);
    if (isset($consignment_title) && !empty($consignment_title)) {
        $consignment = [
            'name' => $consignment_title,
            'order_id' => $order_id,
            'otp' => $otp,
            'delivery_charge' => $delivery_charge,
            'active_status' => $status,
            'status' => json_encode([["received", date("Y-m-d") . " " . date("h:i:sa")], ["processed", date("Y-m-d") . " " . date("h:i:sa")]]),
        ];
    } else {
        return [
            "error" => true,
            "message" => 'Please Enter Consignment Title',

        ];
    }
    if (isset($product_variant_ids) && empty($product_variant_ids)) {
        return [
            "error" => true,
            "message" => 'Product Variant Id not found',
        ];
    }
    $product_variant_id = is_string($product_variant_ids) ? explode(",", $product_variant_ids) : $product_variant_ids;
    $order_items_data = $t->db->select(["product_variant_id", "quantity", "delivered_quantity", "id", "order_id", 'price'])->where_in("product_variant_id", $product_variant_id)->where("order_id", $order_id)->get("order_items")->result_array();
    update_details(['updated_by' => $seller_id], ['order_id' => $order_id, 'seller_id' => $seller_id], 'order_items');

    $t->db->insert('consignments', $consignment);
    $consignment_id = $t->db->insert_id();
    $consignment_data = [];
    $response_data = [];
    foreach ($order_items_data as $row) {
        $unit_price = $row['price'];
        $response_data[] = [
            "id" => $row["id"],
            "quantity" => (int) $row["quantity"],
            "unit_price" => $unit_price,
            "delivered_quantity" => (int) $row["quantity"],
            "product_variant_id" => $row["product_variant_id"],
            "consignment_id" => $consignment_id
        ];
        $consignment_data[] = [
            "consignment_id" => $consignment_id,
            "order_item_id" => $row["id"],
            "quantity" => $row["quantity"],
            "unit_price" => $unit_price,
            "product_variant_id" => $row["product_variant_id"],
        ];
        //custom message
        $settings = get_settings('system_settings', true);
        $app_name = isset($settings['app_name']) && !empty($settings['app_name']) ? $settings['app_name'] : '';
        $custom_notification = fetch_details('custom_notifications', ['type' => "customer_order_processed"], '');
        $hashtag_cutomer_name = '< cutomer_name >';
        $hashtag_order_id = '< order_item_id >';
        $hashtag_application_name = '< application_name >';
        $string = json_encode($custom_notification[0]['message'], JSON_UNESCAPED_UNICODE);
        $hashtag = html_entity_decode($string);
        $data = str_replace(array($hashtag_cutomer_name, $hashtag_order_id, $hashtag_application_name), array($user_res[0]['username'], $order_id, $app_name), $hashtag);

        $message = output_escaping(trim($data, '"'));

        if (!empty($user_fcm_ids[0]['fcm_id'])) {
            // Step 1: Group by platform
            $groupedByPlatform = [];
            foreach ($user_fcm_ids as $item) {
                $platform = $item['platform_type'];
                $groupedByPlatform[$platform][] = $item['fcm_id'];
            }

            // Step 2: Chunk each platform group into arrays of 1000
            $fcm_ids = [];
            foreach ($groupedByPlatform as $platform => $fcmIds) {
                $fcm_ids[$platform] = array_chunk($fcmIds, 1000);
            }
            $fcm_ids[0][] = $fcm_ids;
        }

        if ($consignment_data > 0) {
            $customer_msg = (!empty($custom_notification)) ? $message : 'Hello Dear ' . $user_res[0]['username'] . 'Order status updated to' . $_POST['val'] . ' for order ID #' . $order_id . ' please take note of it! Thank you. Regards ' . $app_name . '';

            $fcmMsg = array(
                'title' => (!empty($custom_notification)) ? $custom_notification[0]['title'] : "Order status updated",
                'body' => $customer_msg,
                'type' => "order",
                'order_id' => $order_id,
            );
            send_notification($fcmMsg, $fcm_ids, $fcmMsg);
        }

        $t->Order_model->update_order(['status' => $status], ['id' => $row["id"]], true, 'order_items');
        $t->Order_model->update_order(['active_status' => $status], ['id' => $row["id"]], false, 'order_items');
        update_details([
            "delivered_quantity" => (int) $row["quantity"]
        ], ["id" => $row["id"]], "order_items");
    }
    $t->db->insert_batch('consignment_items', $consignment_data);
    return [
        "error" => false,
        "message" => 'Consignment Created Successfully.',
        "data" => $response_data
    ];
}

function delete_consignment($consignment_id)
{
    $t = &get_instance();
    $t->load->model('Order_model');
    $consignment_items = fetch_details('consignment_items', ['consignment_id' => $consignment_id], 'order_item_id,quantity');
    if (isset($consignment_items) && empty($consignment_items)) {
        return [
            "error" => true,
            "message" => 'Consignment Not Found',
        ];
    }
    $consignment = fetch_details('consignments', ['id' => $consignment_id], 'active_status');
    $priority_status = [
        'received' => 0,
        'processed' => 1,
        'shipped' => 2,
        'delivered' => 3,
        'return_request_pending' => 4,
        'return_request_decline' => 5,
        'cancelled' => 6,
        'returned' => 7,
    ];
    if (!empty($consignment)) {
        if ($priority_status[$consignment[0]['active_status']] >= $priority_status['shipped']) {
            return [
                "error" => true,
                "message" => 'Cannot delete consignment after it has been Shipped',
            ];
        }
    }

    if (is_exist(['consignment_id' => $consignment_id, 'is_canceled' => 0, 'shiprocket_order_id !=', 0, 'shiprocket_order_id !=' => ''], 'order_tracking')) {
        return [
            "error" => true,
            "message" => 'The consignment cannot be deleted as a Shiprocket order has been created. Please cancel the Shiprocket order first.',
        ];
    }
    $order_item_id = [];
    foreach ($consignment_items as $item) {
        $order_item = fetch_details('order_items', ['id' => $item['order_item_id']], 'delivered_quantity');
        foreach ($order_item as $data) {
            $quantity = $item['quantity'];
            $delivered_quantity = $data['delivered_quantity'];
            $updated_delivered_quantity = (int) $delivered_quantity - (int) $quantity;

            update_details([
                "delivered_quantity" => $updated_delivered_quantity
            ], ["id" => $item['order_item_id']], "order_items");
        }
        array_push($order_item_id, $item['order_item_id']);
        $t->Order_model->update_order(['status' => json_encode([["received", date("d-m-y") . " " . date("h:i:sa")]])], ['id' => $item['order_item_id']], false, 'order_items', is_escape_array: false);
        $t->Order_model->update_order(['active_status' => 'received'], ['id' => $item['order_item_id']], false, 'order_items');
    }
    delete_details(['id' => $consignment_id], 'consignments');
    delete_details(['consignment_id' => $consignment_id], 'consignment_items');

    $response_data = [];
    foreach ($order_item_id as $val) {
        $order_items = fetch_details('order_items', ['id' => $val], 'id,product_variant_id,quantity,delivered_quantity,price');
        foreach ($order_items as $order_item_data) {
            $unit_price = $order_item_data['price'];
            $response_data[] = [
                "id" => $order_item_data["id"],
                "delivered_quantity" => (int) $order_item_data['delivered_quantity'],
                "quantity" => (int) $order_item_data["quantity"],
                "product_variant_id" => $order_item_data["product_variant_id"],
                "unit_price" => $unit_price
            ];
        }
    }
    return [
        "error" => false,
        "message" => 'Consignment Deleted Successfully.',
        "data" => $response_data
    ];
}

function view_all_consignments($order_id = null, $consignment_id = null, $seller_id = null, $offset = null, $limit = null, $order = "DESC", $in_detail = 1, $delivery_boy_id = null, $multiple_status = null, bool $show_otp = false)
{
    $t = &get_instance();

    $count_res = $t->db->select('COUNT(DISTINCT(c.id)) as total')
        ->join('consignment_items ci', 'ci.consignment_id = c.id', 'left')
        ->join('orders o', 'c.order_id = o.id', 'left')
        ->join('order_items oi', 'oi.id = ci.order_item_id', 'left')
        ->join('users u', 'u.id = o.user_id', 'left');

    if (isset($order_id)) {
        $count_res->where("o.id", $order_id);
    }
    if (isset($seller_id)) {
        $count_res->where("oi.seller_id", $seller_id);
    }
    if (isset($consignment_id)) {
        $count_res->where("c.id", $consignment_id);
    }
    if (isset($delivery_boy_id)) {
        $count_res->where("c.delivery_boy_id", $delivery_boy_id);
    }
    // if (isset($multiple_status) && !empty($multiple_status)) {
    //     if (is_array($multiple_status) && !empty($multiple_status)) {
    //         $count_res->where_in("c.active_status", $multiple_status);
    //     } else {
    //         $count_res->where("c.active_status", $multiple_status);
    //     }
    // }
    $consignment = $count_res->get('consignments c')->result_array();

    if (isset($order_id)) {
        $count_res->where("o.id", $order_id);
    }

    foreach ($consignment as $row) {
        $total = $row['total'];
    }

    $search_res = $t->db->select('u.username,u.email,u.mobile,u.latitude,u.longitude,u.image as user_profile,c.id, c.order_id,c.delivery_boy_id,c.otp, c.name, c.status,c.active_status, o.date_added as created_date, ' . (($show_otp) ? "c.otp," : "") . '  oi.seller_id as seller_id,o.payment_method,o.address as user_address,o.delivery_charge,o.wallet_balance,o.discount,o.promo_discount,o.total_payable,o.notes,o.delivery_date,o.delivery_time,o.is_cod_collected,o.is_shiprocket_order,o.final_total,o.total,o.address_id as user_address_id, o.mobile as mobile,o.mobile as order_mobile_number')
        ->from('consignments c')
        ->join('consignment_items ci', 'ci.consignment_id = c.id', 'left')
        ->join('orders o', 'c.order_id = o.id', 'left')
        ->join('order_items oi', 'oi.id = ci.order_item_id', 'left')
        ->join('users u', 'u.id = o.user_id', 'left');

    if (isset($order_id)) {
        $search_res->where("o.id", $order_id);
    }

    if (isset($seller_id)) {
        $search_res->where("oi.seller_id", $seller_id);
    }
    if (isset($consignment_id)) {
        $search_res->where("c.id", $consignment_id);
    }
    if (isset($delivery_boy_id)) {
        $search_res->where("c.delivery_boy_id", $delivery_boy_id);
    }
    if (isset($multiple_status) && !empty($multiple_status)) {
        if (is_array($multiple_status) && !empty($multiple_status)) {
            $search_res->where_in("c.active_status", $multiple_status);
        } else {
            $search_res->where("c.active_status", $multiple_status);
        }
    }
    $result = $search_res->group_by("c.id")->limit($limit, $offset)->order_by("c.id", $order)->get()->result_array();
    $consignment_list = [];

    foreach ($result as $row) {
        $c_id = $row['id'];
        if ($row['is_cod_collected'] == 1 && $row['payment_method'] == "COD" || $row['payment_method'] != "COD") {
            $row['total_payable'] = "0";
        }
        $seller_details = $t->db->select('sd.store_name,u.username as seller_name,u.address,u.mobile,sd.logo as store_image,u.latitude,u.longitude')
            ->from('seller_data sd')
            ->join('users u', 'u.id = sd.user_id')
            ->where('sd.user_id', $row['seller_id'])
            ->get()->result_array();

        $delivery_boy_details = $t->db->select('u.id,u.username,u.address,u.mobile,u.email,u.image')
            ->from('users u')
            ->where('u.id', $row['delivery_boy_id'])
            ->get()->result_array();

        $user_address = $t->db->select('a.id, a.name, a.latitude, a.longitude')
            ->from('addresses a')->where('a.id', $row['user_address_id'])->get()->result_array();

        if (isset($delivery_boy_details[0]['image']) && !empty($delivery_boy_details[0]['image'])) {
            $delivery_boy_details[0]['image'] = base_url($delivery_boy_details[0]['image']);
        } elseif (isset($delivery_boy_details[0]['image']) && empty($delivery_boy_details[0]['image'])) {
            $delivery_boy_details[0]['image'] = "";
        }
        if (isset($seller_details[0]['store_image']) && !empty($seller_details[0]['store_image'])) {
            $seller_details[0]['store_image'] = base_url($seller_details[0]['store_image']);
        }
        $tracking_details = $t->db->select('ot.*')
            ->from('order_tracking ot')
            ->where('ot.consignment_id', $row['id'])
            ->where('ot.is_canceled', 0)
            ->get()->result_array();

        $row['is_shiprocket_order_created'] = 0;
        if ($row['is_shiprocket_order'] == 1) {
            if (isset($tracking_details) && !empty($tracking_details))
                $row['is_shiprocket_order_created'] = 1;
        }

        $cancelled_tracking_details = $t->db->select('ot.*')
            ->from('order_tracking ot')
            ->where('ot.consignment_id', $row['id'])
            ->where('ot.is_canceled', 1)
            ->get()->result_array();

        $data = [
            'id' => $row['id'] ?? "",
            'username' => $row['username'] ?? "",
            'email' => $row['email'] ?? "",
            'mobile' => isset($row['mobile']) && !empty($row['mobile']) ? $row['mobile'] : (isset($row['order_mobile_number']) && !empty($row['order_mobile_number']) ? $row['order_mobile_number'] : ''),
            'order_id' => $row['order_id'] ?? "",
            'name' => $row['name'] ?? "",
            'longitude' => $user_address[0]['longitude'] ?? "",
            'latitude' => $user_address[0]['latitude'] ?? "",
            'created_date' => $row['created_date'] ?? "",
            'otp' => $row['otp'] ?? "",
            'seller_id' => $row['seller_id'] ?? "",
            'payment_method' => $row['payment_method'] ?? "",
            'user_address' => $row['user_address'] ?? "",
            'user_profile' => base_url(USER_IMG_PATH . $row['user_profile']) ?? "",
            'total' => $row['total'] ?? "",
            'delivery_charge' => 0,
            'delivery_boy_id' => $row['delivery_boy_id'] ?? "",
            'wallet_balance' => $row['wallet_balance'],
            'discount' => $row['discount'],
            'tax_percent' => "",
            'tax_amount' => "",
            'promo_discount' => $row['promo_discount'],
            'total_payable' => $row['total_payable'],
            'final_total' => $row['final_total'],
            'notes' => $row['notes'] ?? "",
            'delivery_date' => $row['delivery_date'] ?? "",
            'delivery_time' => $row['delivery_time'] ?? "",
            'is_cod_collected' => $row['is_cod_collected'],
            'is_shiprocket_order' => $row['is_shiprocket_order'],
            'is_shiprocket_order_created' => $row['is_shiprocket_order_created'],
            'active_status' => $row['active_status'],
            'status' => json_decode($row['status'], true),
            'consignment_items' => [],
            'seller_details' => $seller_details[0] ?? null,
            'tracking_details' => $tracking_details[0] ?? null,
            'cancelled_tracking_details' => $cancelled_tracking_details[0] ?? null,
            'delivery_boy_details' => $delivery_boy_details[0] ?? null
        ];
        // Use item_id as key to prevent duplicates
        $consignment_items = fetch_details("consignment_items", ['consignment_id' => $c_id]);


        $items = [];
        $total_tax_amount = 0;
        $total_tax_percent = 0;
        $subtotal = 0;
        foreach ($consignment_items as $item) {

            $order_item_details = [];
            if ($in_detail == 1) {
                $product_details = fetch_order_items(order_item_id: $item['order_item_id']);

                if (isset($product_details) && !empty($product_details)) {
                    $total_tax_amount += $product_details['order_data'][0]['tax_amount'];
                    $total_tax_percent += $product_details['order_data'][0]['tax_percent'];
                    $subtotal += $product_details['order_data'][0]['sub_total'];
                    $keys_to_unset = [
                        'id',
                        'delivery_boy_id',
                        'seller_id',
                        'user_id',
                        'order_id',
                        'username',
                        'name',
                        'email',
                        'mobile',
                        'otp',
                        'payment_method',
                        'user_address',
                        'product_variant_id',
                        'status',
                        'admin_commission_amount',
                        'seller_commission_amount',
                        'active_status',
                        'hash_link',
                        'product_id',
                        'store_name',
                        'order_cancel_counter',
                        'order_return_counter',
                        'order_counter',
                        'delivery_charge',
                        'wallet_balance',
                        'promo_discount',
                        'total_payable'
                    ];
                    foreach ($keys_to_unset as $key) {
                        unset($product_details['order_data'][0][$key]);
                    }
                    $order_item_details = $product_details['order_data'][0];
                }
            }
            $data['tax_amount'] = (string) $total_tax_amount;
            $data['tax_percent'] = (string) $total_tax_percent;


            if (!empty($item)) {

                $order_item = [
                    'id' => $item['id'],
                    'product_variant_id' => $item['product_variant_id'],
                    'order_item_id' => $item['order_item_id'],
                    'unit_price' => $item['unit_price'],
                    'quantity' => $item['quantity'],
                ] + ($order_item_details ?? []);
            }

            array_push($items, $order_item);
        }
        $total_order_items = $t->db->select('COUNT(DISTINCT(order_items.id)) as total')->from('order_items')->where('order_id', $row['order_id'])->get()->result_array();
        $total_order_items = $total_order_items[0]['total'] > 0 ? $total_order_items[0]['total'] : 1;
        $delivery_charges = $row['delivery_charge'];
        $order_item_delivery_charges = $delivery_charges / $total_order_items * count($consignment_items);
        $data['delivery_charge'] = (string) $order_item_delivery_charges;

        if ($subtotal > 0 && $row['total'] > 0) {
            $total_discount_percentage = calculatePercentage(part: $subtotal, total: $row['total']);
        }

        $promo_discount = $row['promo_discount'] ?? 0;
        $wallet_balance = $row['wallet_balance'] ?? 0;
        if ($promo_discount != 0) {
            $promo_discount = calculatePrice($total_discount_percentage, $promo_discount);
        }
        if ($wallet_balance != 0) {
            $wallet_balance = calculatePrice($total_discount_percentage, $wallet_balance);
        }
        $data['promo_discount'] = (string) intval($promo_discount);
        $data['wallet_balance'] = (string) intval($wallet_balance);
        $data['total'] = (string) intval($subtotal - $promo_discount - $wallet_balance);
        $data['total_payable'] = (string) intval($subtotal + $order_item_delivery_charges - $promo_discount - $wallet_balance);
        $data['final_total'] = (string) intval($subtotal + $order_item_delivery_charges - $promo_discount - $wallet_balance);
        $data['consignment_items'] = $items;
        array_push($consignment_list, $data);
    }

    return [
        "error" => false,
        "message" => 'Consignment Retrieve Successfully.',
        "total" => $total,
        "data" => $consignment_list
    ];
}

function validate_order_status($order_ids, $status, $table = 'order_items', $user_id = null, $fromuser = false)
{
    $t = &get_instance();
    $error = 0;
    $cancelable_till = '';
    $returnable_till = '';
    $is_already_returned = 0;
    $is_already_cancelled = 0;
    $is_returnable = 0;
    $is_cancelable = 0;
    $returnable_count = 0;
    $cancelable_count = 0;
    $return_request = 0;
    $check_status = ['received', 'processed', 'shipped', 'delivered', 'cancelled', 'return_pickedup', 'returned'];
    $group = array('admin', 'delivery_boy');
    if (in_array(strtolower(trim($status)), $check_status)) {
        if ($table == 'order_items') {
            $t->db->select('active_status');
            $t->db->where_in('id', explode(',', $order_ids));
            $active_status = $t->db->get('order_items')->result_array();
            $active_status = array_column($active_status, 'active_status');
            if (in_array("cancelled", $active_status) || in_array("returned", $active_status)) {
                $response['error'] = true;
                $response['message'] = "You can't update status once item cancelled / returned";
                $response['data'] = array();
                return $response;
            }
        }
        if ($table == 'consignments') {
            $t->db->select('c.active_status, ci.order_item_id');
            $t->db->join('consignment_items ci', 'ci.consignment_id = c.id', 'left');
            $t->db->where_in('c.id', explode(',', $order_ids));
            $result = $t->db->get('consignments c')->result_array();
            $order_item_ids = array_column($result, 'order_item_id');
            $active_status = array_column($result, 'active_status');
            if (in_array("cancelled", $active_status) || in_array("returned", $active_status)) {
                $response['error'] = true;
                $response['message'] = "You can't update status once item cancelled / returned";
                $response['data'] = array();
                return $response;
            }
            if (isset($order_item_ids) && empty($order_item_ids)) {
                $response['error'] = true;
                $response['message'] = " You can't update status Something Went Wrong!";
                $response['data'] = array();
                return $response;
            }
        }
        $t->db->select('p.*, pv.*, oi.id as order_item_id, oi.user_id as user_id, oi.product_variant_id as product_variant_id, oi.order_id as order_id');

        if ($table == "consignments") {
            $t->db->select('c.active_status, c.status as order_item_status');
        } else {
            $t->db->select('oi.active_status, oi.status as order_item_status');
        }

        $t->db
            ->join('product_variants pv', 'pv.id = oi.product_variant_id', 'left')
            ->join('products p', 'pv.product_id = p.id', 'left')
            ->join('consignment_items ci', 'ci.order_item_id = oi.id', 'left')
            ->join('consignments c', 'c.id = ci.consignment_id', 'left');
        if ($table == 'orders') {
            $t->db->where('oi.order_id', $order_ids);
        } else if ($table == 'consignments') {
            $t->db->where_in('oi.id', $order_item_ids);
            $t->db->group_by('oi.id');
        } else {
            $t->db->where_in('oi.id', explode(',', $order_ids));
        }
        $product_data = $t->db->get('order_items oi')->result_array();

        $priority_status = [
            'received' => 0,
            'processed' => 1,
            'shipped' => 2,
            'delivered' => 3,
            'return_request_pending' => 4,
            'return_request_approved' => 5,
            'return_pickedup' => 8,
            'cancelled' => 6,
            'returned' => 7,
        ];
        $is_posted_status_set = $canceling_delivered_item = $returning_non_delivered_item = false;
        $is_posted_status_set_count = 0;
        for ($i = 0; $i < count($product_data); $i++) {
            /* check if there are any products returnable or cancellable products available in the list or not */
            if ($product_data[$i]['is_returnable'] == 1) {
                $returnable_count += 1;
            }
            if ($product_data[$i]['is_cancelable'] == 1) {
                $cancelable_count += 1;
            }

            /* check if the posted status is present in any of the variants */
            $product_data[$i]['order_item_status'] = json_decode($product_data[$i]['order_item_status'], true);
            $order_item_status = array_column($product_data[$i]['order_item_status'], '0');
            /* check if posted status is already present in how many of the order items */
            if (in_array($status, $order_item_status)) {
                $is_posted_status_set_count++;
            }
            /* if all are marked as same as posted status set the flag */
            if ($is_posted_status_set_count == count($product_data)) {
                $is_posted_status_set = true;
            }

            /* check if user is cancelling the order after it is delivered */
            if (($status == "cancelled") && (in_array("delivered", $order_item_status) || in_array("returned", $order_item_status))) {
                $canceling_delivered_item = true;
            }

            /* check if user is returning non delivered item */
            if (($status == "returned") && !in_array("delivered", $order_item_status)) {
                $returning_non_delivered_item = true;
            }

            if (($status == "cancelled") && in_array("processed", $order_item_status)) {
                $response['error'] = true;
                $response['message'] = "This item has already been processed and cannot be canceled.";
                $response['data'] = array();
                return $response;
            }
        }
        if ($table == 'consignments' && $status == 'returned') {
            $response['error'] = true;
            $response['message'] = "You cannot return Consignment Order!";
            $response['data'] = array();
            return $response;
        }

        if ($is_posted_status_set == true) {
            /* status posted is already present in any of the order item */
            $response['error'] = true;
            $response['message'] = " Order is already marked as $status. You cannot set it again!";
            $response['data'] = array();
            return $response;
        }

        if ($canceling_delivered_item == true) {
            /* when user is trying cancel delivered order / item */
            $response['error'] = true;
            $response['message'] = "You cannot cancel delivered or returned order / item. You can only return that!";
            $response['data'] = array();
            return $response;
        }
        if ($returning_non_delivered_item == true) {
            /* when user is trying return non delivered order / item */
            $response['error'] = true;
            $response['message'] = "You cannot return a non-delivered order / item. First it has to be marked as delivered and then you can return it!";
            $response['data'] = array();
            return $response;
        }

        $is_returnable = ($returnable_count >= 1) ? 1 : 0;
        $is_cancelable = ($cancelable_count >= 1) ? 1 : 0;

        for ($i = 0; $i < count($product_data); $i++) {
            if ($product_data[$i]['active_status'] == 'returned') {
                $error = 1;
                $is_already_returned = 1;
                break;
            }

            if ($product_data[$i]['active_status'] == 'cancelled') {
                $error = 1;
                $is_already_cancelled = 1;
                break;
            }

            if ($status == 'returned' && $product_data[$i]['is_returnable'] == 0) {
                $error = 1;
                break;
            }

            if ($status == 'returned' && $product_data[$i]['is_returnable'] == 1 && $priority_status[$product_data[$i]['active_status']] < 3) {
                $error = 1;
                $returnable_till = 'delivery';
                break;
            }

            if ($status == 'cancelled' && $product_data[$i]['is_cancelable'] == 1) {
                $max = $priority_status[$product_data[$i]['cancelable_till']];
                $min = $priority_status[$product_data[$i]['active_status']];

                if ($min > $max) {
                    $error = 1;
                    $cancelable_till = $product_data[$i]['cancelable_till'];
                    break;
                }
            }

            if ($status == 'cancelled' && $product_data[$i]['is_cancelable'] == 0) {
                $error = 1;
                break;
            }
        }

        if ($status == 'returned' && $error == 1 && !empty($returnable_till)) {
            $response['error'] = true;
            $response['message'] = (count($product_data) > 1) ? "One of the order item is not delivered yet !" : "The order item is not delivered yet !";
            $response['data'] = array();
            return $response;
        }
        if ($status == 'returned' && $error == 1) {
            $response['error'] = true;
            $response['message'] = (count($product_data) > 1) ? "One of the order item can't be returned !" : "The order item can't be returned !";
            $response['data'] = $product_data;
            return $response;
        }

        if ($status == 'cancelled' && $error == 1 && !empty($cancelable_till)) {
            $response['error'] = true;
            $response['message'] = (count($product_data) > 1) ? " One of the order item can be cancelled till " . $cancelable_till . " only " : "The order item can be cancelled till " . $cancelable_till . " only";
            $response['data'] = array();
            return $response;
        }

        if ($status == 'cancelled' && $error == 1) {
            $response['error'] = true;
            $response['message'] = (count($product_data) > 1) ? "One of the order item can't be cancelled !" : "The order item can't be cancelled !";
            $response['data'] = array();
            return $response;
        }
        for ($i = 0; $i < count($product_data); $i++) {

            if ($status == 'returned' && $product_data[$i]['is_returnable'] == 1 && $error == 0) {
                $error = 1;
                $return_request_flag = 1;

                $return_status = [
                    'is_already_returned' => $is_already_returned,
                    'is_already_cancelled' => $is_already_cancelled,
                    'return_request_submitted' => $return_request,
                    'is_returnable' => $is_returnable,
                    'is_cancelable' => $is_cancelable,
                ];

                if ($fromuser == true || $fromuser == 1) {


                    if ($table == 'order_items') {

                        if (is_exist(['user_id' => $product_data[$i]['user_id'], 'order_item_id' => $product_data[$i]['order_item_id'], 'order_id' => $product_data[$i]['order_id']], 'return_requests')) {

                            $response['error'] = true;
                            $response['message'] = "Return request already submitted !";
                            $response['data'] = array();
                            $response['return_status'] = $return_status;
                            return $response;
                        }
                        $request_data_item_data = $product_data[$i];
                        set_user_return_request($request_data_item_data, $table);
                    } else {

                        for ($j = 0; $j < count($product_data); $j++) {
                            if (is_exist(['user_id' => $product_data[$i]['user_id'], 'order_item_id' => $product_data[$i]['order_item_id'], 'order_id' => $product_data[$i]['order_id']], 'return_requests')) {

                                $response['error'] = true;
                                $response['message'] = "Return request already submitted !";
                                $response['data'] = array();
                                $response['return_status'] = $return_status;
                                return $response;
                            }
                        }
                        $request_data_overall_item_data = $product_data;
                        set_user_return_request($request_data_overall_item_data, $table);
                    }
                }

                $response['error'] = false;
                $response['message'] = "Return request submitted successfully !";
                $response['return_request_flag'] = 1;
                $response['data'] = array();
                return $response;
            }
        }
        $response['error'] = false;
        $response['message'] = " ";
        $response['data'] = array();
        return $response;
    } else {
        $response['error'] = true;
        $response['message'] = "Invalid Status Passed";
        $response['data'] = array();
        return $response;
    }
}

function is_exist($where, $table, $update_id = null)
{
    $t = &get_instance();
    $where_tmp = [];
    foreach ($where as $key => $val) {
        $where_tmp[$key] = $val;
    }

    if (($update_id == null) ? $t->db->where($where_tmp)->get($table)->num_rows() > 0 : $t->db->where($where_tmp)->where_not_in('id', $update_id)->get($table)->num_rows() > 0) {
        return true;
    } else {
        return false;
    }
}

function set_user_return_request($data, $table = 'orders')
{

    $data = escape_array($data);

    $t = &get_instance();

    if ($table == 'orders') {
        for ($i = 0; $i < count($data); $i++) {
            $request_data = [
                'user_id' => $data[$i]['user_id'],
                'product_id' => $data[$i]['product_id'],
                'product_variant_id' => $data[$i]['product_variant_id'],
                'order_id' => $data[$i]['order_id'],
                'order_item_id' => $data[$i]['order_item_id']
            ];
            $t->db->insert('return_requests', $request_data);
        }
    } else {
        $request_data = [
            'user_id' => $data['user_id'],
            'product_id' => $data['product_id'],
            'product_variant_id' => $data['product_variant_id'],
            'order_id' => $data['order_id'],
            'order_item_id' => $data['order_item_id']
        ];
        $t->db->insert('return_requests', $request_data);
    }
}

function get_categories_option_html($categories, $selected_vals = [], $used_ids = [])
{
    $html = "";
    foreach ($categories as $category) {
        $selected = (!empty($selected_vals) && in_array($category['id'], $selected_vals)) ? "selected" : "";
        $disabled = (in_array($category['id'], $used_ids) && !$selected) ? "disabled" : "";

        // Add indent based on level (optional)
        $indent = str_repeat('&nbsp;&nbsp;&nbsp;', $category['level']);

        $html .= '<option value="' . $category['id'] . '" class="l' . $category['level'] . '" ' . $selected . ' ' . $disabled . '>' . $indent . output_escaping($category['name']) . '</option>';

        if (!empty($category['children'])) {
            $html .= get_categories_option_html($category['children'], $selected_vals, $used_ids);
        }
    }

    return $html;
}


function get_subcategory_option_html($subcategories, $selected_vals, $used_ids = [])
{
    $html = "";
    foreach ($subcategories as $subcategory) {
        $pre_selected = (!empty($selected_vals) && in_array($subcategory['id'], $selected_vals)) ? "selected" : "";
        $disabled = (in_array($subcategory['id'], $used_ids) && !$pre_selected) ? "disabled" : "";

        $html .= '<option value="' . $subcategory['id'] . '" class="l' . $subcategory['level'] . '" ' . $pre_selected . ' ' . $disabled . '>' . output_escaping($subcategory['name']) . '</option>';

        if (!empty($subcategory['children'])) {
            $html .= get_subcategory_option_html($subcategory['children'], $selected_vals, $used_ids);
        }
    }
    return $html;
}

function get_cart_total($user_id, $product_variant_id = false, $is_saved_for_later = '0', $address_id = '', $delivery_method = '', $token = '')
{
    $t = &get_instance();
    $t->db->select('(select sum(c.qty)  from cart c
    join product_variants pv on c.product_variant_id=pv.id
    join products p on p.id=pv.product_id
    join seller_data sd on sd.user_id=p.seller_id  where c.user_id="' . $user_id . '"
    and qty >= 0  and  is_saved_for_later = "' . $is_saved_for_later . '"
    and p.status=1 AND pv.status=1 AND sd.status=1) as total_items,
    (select count(c.id) from cart c
    join product_variants pv on c.product_variant_id=pv.id
    join products p on p.id=pv.product_id
    join seller_data sd on sd.user_id=p.seller_id where c.user_id="' . $user_id . '" and qty>=0 and  is_saved_for_later = "' . $is_saved_for_later . '"
    and p.status=1 AND pv.status=1 AND sd.status=1) as cart_count,
    c.qty,c.is_saved_for_later,p.is_prices_inclusive_tax,p.cod_allowed,p.type,p.download_allowed,p.minimum_order_quantity,p.slug,p.quantity_step_size,
    p.total_allowed_quantity, p.name, p.image, p.stock as product_stock,p.seller_id as product_seller_id, p.is_attachment_required, p.availability as product_availability,
    p.short_description,p.pickup_location,p.is_prices_inclusive_tax,p.category_id,sd.commission as seller_globle_commission,pv.weight,c.user_id,pv.*,
    (SELECT GROUP_CONCAT(tax.percentage) FROM taxes as tax WHERE FIND_IN_SET(tax.id, p.tax)) as tax_percentage,
    (SELECT GROUP_CONCAT(tax.id) FROM taxes as tax WHERE FIND_IN_SET(tax.id, p.tax)) as tax_ids,
    (SELECT GROUP_CONCAT(tax_title.title) FROM taxes as tax_title WHERE FIND_IN_SET(tax_title.id, p.tax)) as tax_title');

    if ($product_variant_id == true) {
        $t->db->where(['c.product_variant_id' => $product_variant_id, 'c.user_id' => $user_id, 'c.qty !=' => '0']);
    } else {
        $t->db->where(['c.user_id' => $user_id, 'c.qty >=' => '0']);
    }

    if ($is_saved_for_later == 0) {
        $t->db->where('is_saved_for_later', 0);
    } else {
        $t->db->where('is_saved_for_later', 1);
    }

    $t->db->join('product_variants pv', 'pv.id=c.product_variant_id', 'LEFT');
    $t->db->join('products p ', 'pv.product_id=p.id', 'LEFT');
    $t->db->join('seller_data sd ', 'sd.user_id=p.seller_id', 'LEFT');
    $t->db->join('seller_commission sc ', 'sc.seller_id=p.seller_id', 'LEFT');
    $t->db->join('taxes tax', 'FIND_IN_SET(tax.id, p.tax) > 0', 'LEFT');
    $t->db->join('categories ctg', 'p.category_id = ctg.id', 'left');
    $t->db->where(['p.status' => '1', 'pv.status' => 1, 'sd.status' => 1]);
    $t->db->group_by('c.id')->order_by('c.id', "DESC");
    $data = $t->db->get('cart c')->result_array();

    // print_R($token);
    // die;
    // Decode affiliate_ref cookie: Format should be {"product_id": "token"}
    if (isset($token) && !empty($token)) {

        $affiliate_cookie = isset($token) ? json_decode($token, true) : [];
    } else {

        $affiliate_cookie = isset($_COOKIE['affiliate_ref']) ? json_decode($_COOKIE['affiliate_ref'], true) : [];
    }
    // echo "<pre>";

    // print_r($affiliate_cookie);

    $total = array();
    $variant_id = array();
    $quantity = array();
    $percentage = array();
    $amount = array();
    $product_ids = [];


    if (!empty($affiliate_cookie) && is_array($affiliate_cookie)) {
        $affiliate_data = [];
        $product_variant_ids = array_keys($affiliate_cookie);

        // Step 1: Fetch product IDs from product_variants table
        $product_ids = array_values($t->db->select('product_id,id')
            ->from('product_variants')
            ->where_in('id', $product_variant_ids)
            ->get()
            ->result_array());
        // $product_ids = array_column($product_ids, 'product_id');

        foreach ($product_ids as $item) {
            $variant_id = $item['id'];         // 297 or 315
            $product_id = $item['product_id']; // 140 or 138

            if (isset($affiliate_cookie[$variant_id])) {
                $affiliate_data[$product_id] = $affiliate_cookie[$variant_id];
            }
        }

        // print_r($affiliate_data);

        $t->db->select('at.product_id, at.token, at.affiliate_id, at.category_commission');
        $t->db->from('affiliate_tracking at');

        // ✅ Join affiliates table to check status
        $t->db->join('affiliates a', 'a.user_id = at.affiliate_id');

        // ✅ Add status check
        $t->db->where('a.status', 1);
        $t->db->where('a.user_id !=', $user_id);

        // Build where conditions dynamically
        $where_conditions = [];
        foreach ($affiliate_data as $product_id => $token) {
            $where_conditions[] = "(at.product_id = " . $t->db->escape($product_id) . " AND at.token = " . $t->db->escape($token) . ")";
        }

        // Apply combined condition
        if (!empty($where_conditions)) {
            $t->db->where('(' . implode(' OR ', $where_conditions) . ')');
        }

        $query = $t->db->get();
        $affiliate_commission_data = $query->result_array();
    }

    $cod_allowed = 1;
    $download_allowed = array();
    for ($i = 0; $i < count($data); $i++) {


        $tax_title = (isset($data[$i]['tax_title']) && !empty($data[$i]['tax_title'])) ? $data[$i]['tax_title'] : '';
        $is_attachment_required = (isset($data[$i]['is_attachment_required']) && !empty($data[$i]['is_attachment_required'])) ? $data[$i]['is_attachment_required'] : '0';
        $prctg = (isset($data[$i]['tax_percentage']) && intval($data[$i]['tax_percentage']) > 0 && $data[$i]['tax_percentage'] != null) ? $data[$i]['tax_percentage'] : '0';

        //calculate multiple tax
        $tax_percentage = explode(',', $prctg);
        $total_tax = array_sum($tax_percentage);

        $data[$i]['item_tax_percentage'] = $prctg;
        $data[$i]['tax_title'] = $tax_title;
        $data[$i]['price_without_tax'] = $data[$i]['price'];
        $data[$i]['special_price_without_tax'] = $data[$i]['special_price'];
        if ((isset($data[$i]['is_prices_inclusive_tax']) && $data[$i]['is_prices_inclusive_tax'] == 0) || (!isset($data[$i]['is_prices_inclusive_tax'])) && $prctg > 0) {

            $price_tax_amount = $data[$i]['price'] * ($total_tax / 100);
            $special_price_tax_amount = $data[$i]['special_price'] * ($total_tax / 100);
        } else {
            $price_tax_amount = 0;
            $special_price_tax_amount = 0;
        }
        $data[$i]['image_sm'] = get_image_url($data[$i]['image'], 'thumb', 'sm');
        $data[$i]['image_md'] = get_image_url($data[$i]['image'], 'thumb', 'md');
        $data[$i]['image'] = get_image_url($data[$i]['image']);
        if ($data[$i]['cod_allowed'] == 0) {
            $cod_allowed = 0;
        }
        $variant_id[$i] = $data[$i]['id'];
        $quantity[$i] = intval($data[$i]['qty']);

        if (floatval($data[$i]['special_price']) > 0) {
            $total[$i] = floatval($data[$i]['special_price'] + $special_price_tax_amount) * $data[$i]['qty'];
        } else {
            $total[$i] = floatval($data[$i]['price'] + $price_tax_amount) * $data[$i]['qty'];
        }
        $data[$i]['special_price'] = $data[$i]['special_price'] + $special_price_tax_amount;
        $data[$i]['price'] = $data[$i]['price'] + $price_tax_amount;

        $price = isset($data[$i]['special_price']) && !empty($data[$i]['special_price']) && $data[$i]['special_price'] > 0 ? $data[$i]['special_price'] : $data[$i]['price'];

        if (isset($data[$i]['is_prices_inclusive_tax']) && $data[$i]['is_prices_inclusive_tax'] == 1) {
            $tax_amount = $price - ($price * (100 / (100 + $total_tax)));
        } else {
            $tax_amount = $price * ($total_tax / 100);
        }
        $data[$i]['tax_amount'] = number_format($tax_amount, 2);
        $data[$i]['total_subtotal_amount'] = array_sum($total);

        $percentage[$i] = (isset($data[$i]['tax_percentage']) && floatval($data[$i]['tax_percentage']) > 0) ? $data[$i]['tax_percentage'] : '0';

        if ($percentage[$i] != NUll && $percentage[$i] > 0) {
            $amount[$i] = (!empty($special_price_tax_amount)) ? number_format($special_price_tax_amount, 2) : number_format($price_tax_amount, 2);
        } else {
            $amount[$i] = 0;
            $percentage[$i] = 0;
        }

        if (isset($affiliate_commission_data) && !empty($affiliate_commission_data)) {

            foreach ($affiliate_commission_data as $affiliate_commission_data_item) {

                $affiliate_commission_amount = ($data[$i]['total_subtotal_amount'] * $affiliate_commission_data_item['category_commission']) / 100;
                // $affiliate_commission_amount = (450 * 6) / 100 ;

                if ($data[$i]['product_id'] == $affiliate_commission_data_item['product_id']) {
                    $data[$i]['affiliate_id'] = $affiliate_commission_data_item['affiliate_id'];
                    $data[$i]['affiliate_token'] = $affiliate_commission_data_item['token'];
                    $data[$i]['category_commission'] = $affiliate_commission_data_item['category_commission'];
                    $data[$i]['affiliate_commission_amount'] = $affiliate_commission_amount;
                }
            }
        } else {
            $data[$i]['affiliate_token'] = '';
            $data[$i]['affiliate_id'] = '';
            $data[$i]['category_commission'] = '';
            $data[$i]['affiliate_commission_amount'] = '';
        }

        $data[$i]['product_variants'] = get_variants_values_by_id($data[$i]['id']);

        array_push($download_allowed, $data[$i]['download_allowed']);
    }

    $total = array_sum($total);

    $system_settings = get_settings('system_settings', true);
    $shipping_settings = get_settings('shipping_method', true);
    $address = fetch_details('addresses', ['id' => $address_id], ['area_id', 'area', 'pincode']);
    $delivery_charge = $system_settings['delivery_charge'];
    $zipcode_id = fetch_details('zipcodes', ['zipcode' => $address[0]['pincode']], 'id')[0];
    $zipcode_data = fetch_details('zipcodes', ['zipcode' => $address[0]['pincode']], 'id,delivery_charges,minimum_free_delivery_order_amount')[0];

    $city_id = fetch_details('cities', ['id' => $address[0]['city_id']], 'id');

    if ((isset($system_settings['area_wise_delivery_charge']) && !empty($system_settings['area_wise_delivery_charge']))) {
        $delivery_charge = isset($zipcode_data[0]['delivery_charges']) && !empty($zipcode_data[0]['delivery_charges']) ? $zipcode_data[0]['delivery_charges'] : '0';
    }


    if (!empty($address_id)) {
        if ($city_id > 0) {
            $tmpRow['is_deliverable'] = (!empty($city_id) && $city_id > 0) ?
                is_product_delivarable('city', $city_id, $data[0]['product_id'])
                : false;
        } else {
            if (isset($shipping_settings['local_shipping_method']) && $shipping_settings['local_shipping_method'] == 1) {
                $tmpRow['is_deliverable'] = (!empty($zipcode_id) && $zipcode_id > 0) ?
                    is_product_delivarable('zipcode', $zipcode_id, $data[0]['product_id'])
                    : false;
            }
        }

        $tmpRow['delivery_by'] = ($tmpRow['is_deliverable']) ? "local" : "standard_shipping";
        if (isset($shipping_settings['shiprocket_shipping_method']) && $shipping_settings['shiprocket_shipping_method'] == 1) {
            if (!$tmpRow['is_deliverable'] && $data[0]['pickup_location'] != "") {

                if (isset($tmpRow['delivery_by']) && $tmpRow['delivery_by'] == 'standard_shipping') {


                    $parcels = make_shipping_parcels($data);
                    $parcels_details = check_parcels_deliveriblity($parcels, $address[0]['pincode']);
                    if (isset($shipping_settings['standard_shipping_free_delivery']) && isset($shipping_settings['minimum_free_delivery_order_amount'])) {
                        if ($total >= $shipping_settings['minimum_free_delivery_order_amount'] && $shipping_settings['standard_shipping_free_delivery'] == 1) {
                            $delivery_charge = 0;
                        } else {
                            $delivery_charge = $parcels_details['delivery_charge_without_cod'];
                        }
                    } else {
                        $delivery_charge = $parcels_details['delivery_charge_without_cod'];
                    }
                }
            }
        } else if (isset($shipping_settings['local_shipping_method']) && $shipping_settings['local_shipping_method'] == 1) {
            $delivery_charge = get_delivery_charge($address_id, $total);
        }
    }

    $delivery_charge = isset($data[0]['type']) && $data[0]['type'] == 'digital_product' ? 0 : $delivery_charge;
    $delivery_charge = str_replace(",", "", $delivery_charge);
    $overall_amt = 0;
    $tax_amount = array_sum($amount);
    $overall_amt = $total + $delivery_charge;
    $data[0]['is_cod_allowed'] = $cod_allowed;
    $data['sub_total'] = strval($total);
    $data['quantity'] = strval(array_sum($quantity));
    $data['tax_percentage'] = strval(array_sum($percentage));
    $data['tax_amount'] = strval(array_sum($amount));
    $data['total_arr'] = $total;
    $data['variant_id'] = $variant_id;
    $data['delivery_charge'] = $delivery_charge;
    $data['overall_amount'] = strval($overall_amt);
    $data['amount_inclusive_tax'] = strval($overall_amt + $tax_amount);
    $data['is_attachment_required'] = $is_attachment_required;
    $data['download_allowed'] = $download_allowed;
    return $data;
}
function get_frontend_categories_html()
{
    $t = &get_instance();
    $t->load->model('category_model');

    $limit = 8;
    $offset = 0;
    $sort = 'row_order';
    $order = 'ASC';
    $has_child_or_item = 'false';


    $categories = $t->category_model->get_categories('', $limit, $offset, $sort, $order, trim($has_child_or_item));
    $nav = '<div class="cd-morph-dropdown"><a href="#0" class="nav-trigger">Open Nav<span aria-hidden="true"></span></a><nav class="main-nav"><ul>';
    $html = "<div class='morph-dropdown-wrapper'><div class='dropdown-list'><ul>";

    for ($i = 0; $i < count($categories); $i++) {
        $nav .= '<li class="has-dropdown" data-content="' . str_replace(' ', '', str_replace('&', '-', trim(strtolower(strip_tags(str_replace('\'', '', $categories[$i]['name'])))))) . '">';
        $nav .= '<a href="' . base_url('products/category/' . $categories[$i]['slug']) . '">' . Ucfirst($categories[$i]['name']) . '</a></li>';
        $html .= "<li id='" . str_replace(' ', '', str_replace('&', '-', trim(strtolower(strip_tags($categories[$i]['name']))))) . "' class='dropdown'> <a href='#0' class='label'>" . $categories[$i]['name'] . "</a><div class='content'><ul>";

        if (!empty($categories[$i]['children'])) {
            $html .= get_frontend_subcategories_html($categories[$i]['children']);
        }
        $html .= "</ul></div>";
    }
    $nav .= '<li><a href="' . base_url('home/categories') . '">See All</a></li>';
    $html .= "</ul><div class='bg-layer' aria-hidden='true'></div></div></div></div>";
    $nav .= '</ul></nav>';
    return $nav . $html;
}

function get_frontend_subcategories_html($subcategories)
{
    $html = "";

    for ($i = 0; $i < count($subcategories); $i++) {
        $html .= "<li><a href='#0'>" . $subcategories[$i]['name'] . "</a>";
        if (!empty($subcategories[$i]['children'])) {
            $html .= '<ul>' . get_frontend_subcategories_html($subcategories[$i]['children']) . '</ul>';
        }
        $html .= "</li>";
    }

    return $html;
}

function resize_image($image_data, $source_path, $id = false)
{
    if ($image_data['is_image']) {

        $t = &get_instance();

        $image_type = ['thumb', 'cropped'];
        $image_size = ['md' => array('width' => 800, 'height' => 800), 'sm' => array('width' => 350, 'height' => 350)];
        $target_path = $source_path; // Target path will be under source path
        $image_name = $image_data['file_name']; // original image's name
        $w = $image_data['image_width']; // original image's width
        $h = $image_data['image_height']; // original images's height

        $t->load->library('image_lib');

        for ($i = 0; $i < count($image_type); $i++) {

            if (file_exists($source_path . $image_name)) {

                //check if the image file exist
                foreach ($image_size as $image_size_key => $image_size_value) {
                    if (!file_exists($target_path . $image_type[$i] . '-' . $image_size_key)) {
                        mkdir($target_path . $image_type[$i] . '-' . $image_size_key, 0777);
                    }

                    $n_w = $image_size_value['width']; // destination image's width //800
                    $n_h = $image_size_value['height']; // destination image's height //800
                    $config['image_library'] = 'gd2';
                    $config['create_thumb'] = FALSE;
                    $config['source_image'] = $source_path . $image_name;
                    $config['new_image'] = $target_path . $image_type[$i] . '-' . $image_size_key . '/' . $image_name;
                    if (($w >= $n_w || $h >= $n_h) && $image_type[$i] == 'cropped') {
                        $y = date('Y');
                        $thumb_type = ($image_size_key == 'sm') ? 'thumb-sm/' : 'thumb-md/';
                        $thumb_path = $source_path . $thumb_type . $image_name;

                        $data = getimagesize($thumb_path);
                        $width = $data[0];
                        $height = $data[1];
                        $config['source_image'] = (file_exists($thumb_path)) ? $thumb_path : $image_name;

                        /*  x-axis : (left)
                        width : (right)
                        y-axis : (top)
                        height : (bottom) */
                        $config['maintain_ratio'] = false;

                        if ($width > $height) {
                            $config['width'] = $height;
                            $config['height'] = round($height);
                            $config['x_axis'] = (($width / 4) - ($n_w / 4));
                        } else {
                            $config['width'] = $width;
                            $config['height'] = $width;
                            $config['y_axis'] = (($height / 4) - ($n_h / 4));
                        }

                        $t->image_lib->initialize($config);
                        $t->image_lib->crop();
                        $t->image_lib->clear();
                    }

                    if (($w >= $n_w || $h >= $n_h) && $image_type[$i] == 'thumb') {
                        $config['maintain_ratio'] = true;
                        $config['create_thumb'] = FALSE;
                        $config['width'] = $n_w;
                        $config['height'] = $n_h;
                        $t->image_lib->initialize($config);
                        if (!$t->image_lib->resize()) {
                            return $t->image_lib->display_errors();
                        }
                        $t->image_lib->clear();
                    }
                }
            }
        }
    }
}

function get_user_permissions($id)
{
    $userData = fetch_details('user_permissions', ['user_id' => $id]);
    return $userData;
}

function has_permissions($role, $module)
{
    $role = trim($role);
    $module = trim($module);

    if (!is_modification_allowed($module) && in_array($role, ['create', 'update', 'delete'])) {
        return false; //Modification not allowed
    }
    $t = &get_instance();
    $id = $t->session->userdata('user_id');
    $t->load->config('eshop');
    $general_system_permissions = $t->config->item('system_modules');
    $userData = get_user_permissions($id);
    if (!empty($userData)) {

        if (intval($userData[0]['role']) > 0) {
            $permissions = json_decode($userData[0]['permissions'], 1);
            if (array_key_exists($module, $general_system_permissions) && array_key_exists($module, $permissions)) {
                if (array_key_exists($module, $permissions)) {
                    if (in_array($role, $general_system_permissions[$module])) {
                        if (!array_key_exists($role, $permissions[$module])) {
                            return false; //User has no permission
                        }
                    }
                }
            } else {
                return false; //User has no permission
            }
        }
        return true; //User has permission
    }
}

function print_msg($error, $message, $module = false, $is_csrf_enabled = true)
{
    $t = &get_instance();
    if ($error) {

        $response['error'] = true;
        $response['message'] = (is_modification_allowed($module)) ? $message : DEMO_VERSION_MSG;
        if ($is_csrf_enabled) {
            $response['csrfName'] = $t->security->get_csrf_token_name();
            $response['csrfHash'] = $t->security->get_csrf_hash();
        }
        print_r(json_encode($response));
        return true;
    }
}

function get_system_update_info()
{
    $t = &get_instance();
    $db_version_data = $t->db->from('updates')->order_by("id", "desc")->get()->result_array();
    if (!empty($db_version_data) && isset($db_version_data[0]['version'])) {
        $db_current_version = $db_version_data[0]['version'];
    }
    if ($t->db->table_exists('updates') && !empty($db_current_version)) {
        $data['db_current_version'] = $db_current_version;
    } else {
        $data['db_current_version'] = $db_current_version = 1.0;
    }

    if (file_exists(UPDATE_PATH . "update/updater.txt") || file_exists(UPDATE_PATH . "updater.txt")) {
        $sub_directory = (file_exists(UPDATE_PATH . "update/folders.json")) ? "update/" : "";
        $lines_array = file(UPDATE_PATH . $sub_directory . "updater.txt");

        $search_string = "version";

        foreach ($lines_array as $line) {
            if (strpos($line, $search_string) !== false) {
                list(, $new_str) = explode(":", $line);
                // If you don't want the space before the word bong, uncomment the following line.
                $new_str = trim($new_str);
            }
        }
        $data['file_current_version'] = $file_current_version = $new_str;
    } else {
        $data['file_current_version'] = $file_current_version = false;
    }

    if ($file_current_version != false && version_compare($file_current_version, $db_current_version, '>')) {

        $data['is_updatable'] = true;
    } else {
        $data['is_updatable'] = false;
    }

    return $data;
}

function send_mail($to, $subject, $message)
{
    // print_r($message);
    // die();
    $t = &get_instance();
    $settings = get_settings('system_settings', true);
    $t->load->library('email');
    $config = $t->config->item('email_config');
    $t->email->initialize($config);
    $t->email->set_newline("\r\n");

    $t->email->from($config['smtp_user'], $settings['app_name']);
    $t->email->to($to);
    $t->email->subject($subject);
    $t->email->message($message);
    if ($t->email->send()) {
        $response['error'] = false;
        $response['config'] = $config;
        $response['message'] = 'Email Sent';
    } else {
        $response['error'] = true;
        $response['config'] = $config;
        $response['message'] = $t->email->print_debugger();
    }

    return $response;
}
function send_digital_product_mail($to, $subject, $message, $attachment)
{
    $t = &get_instance();
    $settings = get_settings('system_settings', true);
    $t->load->library('email');
    $config = $t->config->item('email_config');
    $config['mailtype'] = 'html';
    $t->email->initialize($config);
    $t->email->set_newline("\r\n");

    $t->email->from($config['smtp_user'], $settings['app_name']);
    $t->email->to($to);
    $t->email->subject($subject);
    $t->email->message($message);
    $t->email->attach($attachment);
    if ($t->email->send()) {
        $response['error'] = false;
        $response['config'] = $config;
        $response['message'] = 'Email Sent';
    } else {
        $response['error'] = true;
        $response['config'] = $config;
        $response['message'] = $t->email->print_debugger();
    }

    return $response;
}

function fetch_orders($order_id = NULL, $user_id = NULL, $status = NULL, $delivery_boy_id = NULL, $limit = NULL, $offset = NULL, $sort = NULL, $order = NULL, $download_invoice = false, $start_date = null, $end_date = null, $search = null, $city_id = null, $area_id = null, $seller_id = null, $order_type = '', $from_seller = false, $draftFilter = 1)
{

    $t = &get_instance();
    $where = [];

    $count_res = $t->db->select(' COUNT(distinct o.id) as total')
        ->join(' users u', 'u.id= o.user_id', 'left')
        ->join(' order_items oi', 'o.id= oi.order_id', 'left')
        ->join('product_variants pv', 'pv.id=oi.product_variant_id', 'left')
        ->join('products p', 'pv.product_id=p.id', 'left')
        ->join('order_tracking ot ', ' ot.order_item_id = oi.id', 'left')
        ->join('addresses a', 'a.id=o.address_id', 'left');
    if (isset($order_id) && $order_id != null) {
        $where['o.id'] = $order_id;
    }

    if (isset($delivery_boy_id) && $delivery_boy_id != NULL) {
        $where['oi.delivery_boy_id'] = $delivery_boy_id;
    }

    if (isset($user_id) && $user_id != null) {
        $where['o.user_id'] = $user_id;
    }
    if (isset($city_id) && $city_id != null) {
        $where['a.city_id'] = $city_id;
    }
    if (isset($area_id) && $area_id != null) {
        $where['a.area_id'] = $area_id;
    }
    if (isset($seller_id) && $seller_id != null) {
        $where['oi.seller_id'] = $seller_id;
    }
    if (isset($order_type) && $order_type != '' && $order_type == 'digital') {
        $where['p.type'] = 'digital_product';
    }
    if (isset($order_type) && $order_type != '' && $order_type == 'simple') {
        $where['p.type !='] = 'digital_product';
    }


    if (isset($status) && is_array($status) && count($status) > 0) {
        $status = array_map('trim', $status);
        $count_res->where_in('oi.active_status', $status);
    }

    if (isset($start_date) && $start_date != null && isset($end_date) && $end_date != null) {
        $count_res->where(" DATE(o.date_added) >= DATE('" . $start_date . "') ");
        $count_res->where(" DATE(o.date_added) <= DATE('" . $end_date . "') ");
    }

    if (isset($search) and $search != null) {

        $filters = [
            'u.username' => $search,
            'u.email' => $search,
            'o.id' => $search,
            'o.mobile' => $search,
            'o.address' => $search,
            'o.payment_method' => $search,
            'o.delivery_time' => $search,
            'o.date_added' => $search,
            'p.name' => $search,
            'oi.active_status' => $search,
        ];
    }
    if (isset($filters) && !empty($filters)) {
        $count_res->group_Start();
        $count_res->or_like($filters);
        $count_res->group_End();
    }


    $count_res->where($where);


    if ($sort == 'date_added') {
        $sort = 'o.date_added';
    }

    if ($sort != null && $order != null) {
        $count_res->order_by($sort, $order);
    }

    $order_count = $count_res->get('orders o')->result_array();

    $total = "0";
    foreach ($order_count as $row) {
        $total = $row['total'];
    }

    $search_res = $t->db->select(' o.*, u.username,u.image as user_profile,u.country_code, u.email as email, p.name,p.type,p.download_allowed,p.pickup_location,oi.return_reason, oi.return_item_image, a.name as order_recipient_person,pv.special_price,pv.price, oi.deliveryboy_otp_setting_on, oi.product_is_cancelable, oi.product_is_returnable, oi.return_reason, oi.return_item_image')
        ->join(' users u', 'u.id= o.user_id', 'left')
        ->join(' order_items oi', 'o.id= oi.order_id', 'left')
        ->join('product_variants pv', 'pv.id=oi.product_variant_id', 'left')
        ->join('addresses a', 'a.id=o.address_id', 'left')
        ->join('products p', 'pv.product_id=p.id', 'left');
    if ($draftFilter == 1) {
        $t->db->where("oi.active_status != 'draft'");
    }

    if (isset($order_id) && $order_id != null) {
        $search_res->where("o.id", $order_id);
    }
    if (isset($user_id) && $user_id != null) {
        $search_res->where("o.user_id", $user_id);
    }
    if (isset($delivery_boy_id) && $delivery_boy_id != NULL) {
        $search_res->where('oi.delivery_boy_id', $delivery_boy_id);
    }
    if (isset($seller_id) && $seller_id != null) {
        $search_res->where("oi.seller_id", $seller_id);
    }


    if (isset($start_date) && $start_date != null && isset($end_date) && $end_date != null) {
        $search_res->where(" DATE(o.date_added) >= DATE('" . $start_date . "') ");
        $search_res->where(" DATE(o.date_added) <= DATE('" . $end_date . "') ");
    }
    if (isset($order_type) && $order_type != '' && $order_type == 'digital') {
        $search_res->where("p.type = 'digital_product'");
    }
    if (isset($order_type) && $order_type != '' && $order_type == 'simple') {
        $search_res->where("p.type != 'digital_product'");
    }
    if (isset($status) && is_array($status) && count($status) > 0) {
        $status = array_map('trim', $status);
        $count_res->where_in('oi.active_status', $status);
    }

    if (isset($filters) && !empty($filters)) {
        $search_res->group_Start();
        $search_res->or_like($filters);
        $search_res->group_End();
    }

    if (empty($sort)) {
        $sort = 'o.date_added';
    }
    $search_res->group_by('o.id');
    if ($sort != null && $order != null) {
        $search_res->order_by($sort, $order);
    }

    if ($limit != null || $offset != null) {
        $search_res->limit($limit, $offset);
    }

    $order_details = $search_res->get('orders o')->result_array();

    $seller_where = [];
    if ($seller_id != null && $seller_id != "") {
        $seller_where["seller_id"] = $seller_id;
    }
    $orders_id = array_map(fn($row) => $row["id"], $order_details);
    $tmp = fetch_details(table: "order_charges", where_in_key: "order_id", where_in_value: $orders_id, where: $seller_where);

    foreach ($order_details as $key => $order) {
        foreach ($tmp as $oc) {
            if ($order_details[$key]['id'] == $oc["order_id"]) {

                $order_details[$key]["seller_delivery_charge"] = $oc['delivery_charge'];
                $order_details[$key]["seller_promo_dicount"] = $oc['promo_discount'];
            }
        }
    }




    for ($i = 0; $i < count($order_details); $i++) {


        $pr_condition = ($user_id != NULL && !empty(trim($user_id)) && is_numeric($user_id)) ? " and pr.user_id = $user_id " : "";
        // Create the subquery first - only select order_id
        $subquery = null;
        if (!empty($status)) {
            $subquery = $t->db->select('order_items.order_id', false) // false to prevent escaping
                ->from('order_items')
                ->where_in('active_status', $status)
                ->get_compiled_select();
        }
        // Reset Query Builder state
        $t->db->reset_query();


        // Main query
        $t->db->select('oi.*,p.id as product_id,p.is_cancelable,p.is_prices_inclusive_tax,p.cancelable_till,p.type,p.slug,
            p.download_allowed,p.download_link,sd.store_name,u.longitude as seller_longitude,u.mobile as seller_mobile,
            u.address as seller_address,u.latitude as seller_latitude,
            (select username from users where id=oi.delivery_boy_id) as delivery_boy_name, oi.return_reason, oi.return_item_image,
            sd.store_description,sd.rating as seller_rating,sd.logo as seller_profile,ot.courier_agency,
            ot.tracking_id,ot.awb_code,ot.url,u.username as seller_name,p.is_returnable,
            pv.special_price,pv.price as main_price,p.image,p.name,p.pickup_location,pv.weight,
            sd.no_of_ratings as seller_no_of_ratings,p.rating as product_rating,p.type,
            pr.rating as user_rating, pr.images as user_rating_images, pr.comment as user_rating_comment,
            oi.status as status,
            (Select count(id) from order_items where order_id = oi.order_id ) as order_counter,
            (Select count(active_status) from order_items where active_status ="cancelled" and order_id = oi.order_id ) as order_cancel_counter,
            (Select count(active_status) from order_items where active_status ="returned" and order_id = oi.order_id ) as order_return_counter,
            c.otp as otp,ci.id as consignment_item_id')
            ->from('order_items oi')
            ->join('product_variants pv', 'pv.id=oi.product_variant_id', 'left')
            ->join('products p', 'pv.product_id=p.id', 'left')
            ->join('product_rating pr', 'pv.product_id=pr.product_id ' . $pr_condition, 'left')
            ->join('seller_data sd', 'sd.user_id=oi.seller_id', 'left')
            ->join('order_tracking ot', 'ot.order_item_id = oi.id', 'left')
            ->join('consignment_items ci', 'ci.order_item_id = oi.id', 'left')
            ->join('consignments c', 'c.id = ci.consignment_id', 'left')
            ->join('users u', 'u.id=oi.seller_id', 'left');

        // Check if subquery is not null and add where_in condition
        if ($subquery) {
            $t->db->group_start()
                ->where_in('oi.order_id', $subquery, FALSE); // Add the subquery condition
        }

        // Check if order_details[$i]['id'] is set and add where_in condition for order_id

        if (isset($order_details[$i]['id'])) {
            $t->db->where_in('oi.order_id', $order_details[$i]['id']);
        }

        if ($subquery) {
            $t->db->group_end(); // Close the group for the subquery condition
        }

        // Check if seller_id is provided and add where condition
        if ($seller_id) {
            $t->db->where('oi.seller_id', $seller_id);
        }

        // Add the group_by condition
        $t->db->group_by('oi.id');

        $order_item_data = $t->db->get()->result_array();


        $return_request = fetch_details('return_requests', ['user_id' => $user_id]);
        if ($order_details[$i]['payment_method'] == "bank_transfer") {
            $bank_transfer = fetch_details('order_bank_transfer', ['order_id' => $order_details[$i]['id']], 'attachments,id,status');
            if (!empty($bank_transfer)) {
                $bank_transfer = array_map(function ($attachment) {
                    $temp['id'] = $attachment['id'];
                    $temp['attachment'] = base_url($attachment['attachments']);
                    $temp['banktransfer_status'] = $attachment['status'];
                    return $temp;
                }, $bank_transfer);
            }
        }
        if (isset($order_details[$i]['attachments']) && !empty($order_details[$i]['attachments'])) {
            $order_attachments = json_decode($order_details[$i]['attachments'], true);
            $k = 0;
            foreach ($order_attachments as $order_attachment) {
                $order_attachments[$k] = get_image_url($order_attachment);
                $k++;
            }
            $order_attachments = (array) $order_attachments;
            $order_attachments = array_values($order_attachments);
        }
        if (isset($order_details[$i]['return_item_image']) && !empty($order_details[$i]['return_item_image'])) {
            $return_item_images = explode(',', $order_details[$i]['return_item_image']);
            $k = 0;
            foreach ($return_item_images as $return_item_image) {
                $return_item_images[$k] = base_url($return_item_image);
                $k++;
            }

            $return_item_images = (array) $return_item_images;
            $return_item_images = array_values($return_item_images);
        }
        $order_details[$i]['user_profile'] = (isset($order_details[$i]['user_profile']) && !empty($order_details[$i]['user_profile'])) ? base_url($order_details[$i]['user_profile']) : "";
        $order_details[$i]['user_mobile'] = (isset($order_details[$i]['mobile']) && !empty($order_details[$i]['mobile'])) ? base_url($order_details[$i]['mobile']) : "";
        $order_details[$i]['latitude'] = (isset($order_details[$i]['latitude']) && !empty($order_details[$i]['latitude'])) ? $order_details[$i]['latitude'] : "";
        $order_details[$i]['longitude'] = (isset($order_details[$i]['longitude']) && !empty($order_details[$i]['longitude'])) ? $order_details[$i]['longitude'] : "";
        $order_details[$i]['order_recipient_person'] = (isset($order_details[$i]['order_recipient_person']) && !empty($order_details[$i]['order_recipient_person'])) ? $order_details[$i]['order_recipient_person'] : "";
        $order_details[$i]['attachments'] = (isset($bank_transfer) && !empty($bank_transfer)) ? $bank_transfer : [];



        $order_details[$i]['order_prescription_attachments'] = (isset($order_details[$i]['attachments']) && !empty($order_details[$i]['attachments'])) ? $order_attachments : [];
        $order_details[$i]['return_item_image'] = (isset($order_details[$i]['return_item_image']) && !empty($order_details[$i]['return_item_image'])) ? $return_item_images : [];
        $order_details[$i]['notes'] = (isset($order_details[$i]['notes']) && !empty($order_details[$i]['notes'])) ? $order_details[$i]['notes'] : "";
        $order_details[$i]['payment_method'] = isset($order_details[$i]['payment_method']) ? ucwords(str_replace('_', " ", $order_details[$i]['payment_method'])) : $order_details[$i]['payment_method'];
        $order_details[$i]['courier_agency'] = "";
        $order_details[$i]['tracking_id'] = "";
        $order_details[$i]['url'] = "";

        $city_id = fetch_details('addresses', ['id' => $order_details[$i]['address_id']], 'city_id')[0]['city_id'];


        if (isset($seller_id) && !empty($seller_id)) {
            if (isset($order_details[$i]['seller_delivery_charge'])) {
                $order_details[$i]['delivery_charge'] = $order_details[$i]['seller_delivery_charge'];
            } else {
                $order_details[$i]['delivery_charge'] = $order_details[$i]['delivery_charge'];
            }
        } else {
            $order_details[$i]['delivery_charge'] = $order_details[$i]['delivery_charge'];
        }
        if (isset($order_details[$i]['seller_promo_dicount'])) {
            $order_details[$i]['promo_discount'] = $order_details[$i]['seller_promo_dicount'];
        } else {
            $order_details[$i]['promo_discount'] = $order_details[$i]['promo_discount'];
        }

        $returnable_count = 0;
        $cancelable_count = 0;
        $already_returned_count = 0;
        $already_cancelled_count = 0;
        $return_request_submitted_count = 0;
        $total_tax_percent = $total_tax_amount = $item_subtotal = 0;
        $download_allowed = array();

        for ($k = 0; $k < count($order_item_data); $k++) {
            array_push($download_allowed, $order_item_data[$k]['download_allowed']);
            if (isset($order_item_data[$k]['quantity']) && $order_item_data[$k]['quantity'] != 0) {
                $price = $order_item_data[$k]['special_price'] != '' && $order_item_data[$k]['special_price'] != null && $order_item_data[$k]['special_price'] > 0 && $order_item_data[$k]['special_price'] < $order_item_data[$k]['main_price'] ? $order_item_data[$k]['special_price'] : $order_item_data[$k]['main_price'];
                $amount = $order_item_data[$k]['quantity'] * $price;
            }
            if (!empty($order_item_data)) {
                $user_rating_images = $order_item_data[$k]['user_rating_images'];
                $user_rating_images = json_decode($user_rating_images != null || !empty($user_rating_images) ? $user_rating_images : "[]", true);
                $order_item_data[$k]['user_rating_images'] = array();
                if (!empty($user_rating_images)) {
                    for ($f = 0; $f < count($user_rating_images); $f++) {
                        $order_item_data[$k]['user_rating_images'][] = base_url($user_rating_images[$f]);
                    }
                }

                if (isset($order_item_data[$k]['is_prices_inclusive_tax']) && $order_item_data[$k]['is_prices_inclusive_tax'] == 1) {
                    $price_tax_amount = $price - ($price * (100 / (100 + $order_item_data[$k]['tax_percent'])));
                } else {
                    $price_tax_amount = $price * ($order_item_data[$k]['tax_percent'] / 100);
                }
                if (isset($order_item_data[$k]['return_item_image']) && !empty($order_item_data[$k]['return_item_image'])) {
                    $return_item_images = explode(',', $order_item_data[$k]['return_item_image']);
                    $x = 0;
                    foreach ($return_item_images as $return_item_image) {
                        $return_item_images[$x] = base_url($return_item_image);
                        $x++;
                    }
                    $return_item_images = (array) $return_item_images;
                    $return_item_images = array_values($return_item_images);
                }
                $order_item_data[$k]['return_item_image'] = (isset($order_item_data[$k]['return_item_image']) && !empty($order_item_data[$k]['return_item_image'])) ? $return_item_images : [];


                $order_item_data[$k]['tax_amount'] = isset($price_tax_amount) && !empty($price_tax_amount) ? (float) $price_tax_amount : 0.00;
                $order_item_data[$k]['net_amount'] = $order_item_data[$k]['price'] - $order_item_data[$k]['tax_amount'];
                $item_subtotal += $order_item_data[$k]['sub_total'];
                $order_item_data[$k]['seller_name'] = (!empty($order_item_data[$k]['seller_name'])) ? $order_item_data[$k]['seller_name'] : '';
                $order_item_data[$k]['store_description'] = (!empty($order_item_data[$k]['store_description'])) ? $order_item_data[$k]['store_description'] : '';
                $order_item_data[$k]['seller_rating'] = (!empty($order_item_data[$k]['seller_rating'])) ? number_format($order_item_data[$k]['seller_rating'], 1) : "0";
                $order_item_data[$k]['seller_profile'] = (!empty($order_item_data[$k]['seller_profile'])) ? base_url() . $order_item_data[$k]['seller_profile'] : '';
                $order_item_data[$k]['seller_latitude'] = (isset($order_item_data[$k]['seller_latitude']) && !empty($order_item_data[$k]['seller_latitude'])) ? $order_item_data[$k]['seller_latitude'] : '';
                $order_item_data[$k]['seller_longitude'] = (isset($order_item_data[$k]['seller_longitude']) && !empty($order_item_data[$k]['seller_longitude'])) ? $order_item_data[$k]['seller_longitude'] : '';
                $order_item_data[$k]['seller_address'] = (isset($order_item_data[$k]['seller_address']) && !empty($order_item_data[$k]['seller_address'])) ? $order_item_data[$k]['seller_address'] : '';
                $order_item_data[$k]['seller_mobile'] = (isset($order_item_data[$k]['seller_mobile']) && !empty($order_item_data[$k]['seller_mobile'])) ? $order_item_data[$k]['seller_mobile'] : '';

                // if (isset($seller_id) && $seller_id != null) {
                $order_item_data[$k]['otp'] = (get_seller_permission($order_item_data[$k]['seller_id'], "view_order_otp")) ? $order_item_data[$k]['otp'] : "0";
                // }
                $order_item_data[$k]['pickup_location'] = isset($order_item_data[$k]['pickup_location']) && !empty($order_item_data[$k]['pickup_location']) && $order_item_data[$k]['pickup_location'] != 'NULL' ? $order_item_data[$k]['pickup_location'] : '';
                $varaint_data = get_variants_values_by_id($order_item_data[$k]['product_variant_id']);
                $order_item_data[$k]['varaint_ids'] = (!empty($varaint_data)) ? $varaint_data[0]['varaint_ids'] : '';
                $order_item_data[$k]['variant_values'] = (!empty($varaint_data)) ? $varaint_data[0]['variant_values'] : '';
                $order_item_data[$k]['attr_name'] = (!empty($varaint_data)) ? $varaint_data[0]['attr_name'] : '';
                $order_item_data[$k]['product_rating'] = (!empty($order_item_data[$k]['product_rating'])) ? number_format($order_item_data[$k]['product_rating'], 1) : "0";
                $order_item_data[$k]['name'] = (!empty($order_item_data[$k]['name'])) ? $order_item_data[$k]['name'] : $order_item_data[$k]['product_name'];
                $order_item_data[$k]['variant_values'] = (!empty($order_item_data[$k]['variant_values'])) ? $order_item_data[$k]['variant_values'] : $order_item_data[$k]['variant_name'];
                $order_item_data[$k]['user_rating'] = (!empty($order_item_data[$k]['user_rating'])) ? $order_item_data[$k]['user_rating'] : '0';
                $order_item_data[$k]['user_rating_comment'] = (!empty($order_item_data[$k]['user_rating_comment'])) ? $order_item_data[$k]['user_rating_comment'] : '';
                $order_item_data[$k]['status'] = json_decode($order_item_data[$k]['status']);
                if (!in_array($order_item_data[$k]['active_status'], ['returned', 'cancelled'])) {
                    $total_tax_percent = $total_tax_percent + $order_item_data[$k]['tax_percent'];
                    $total_tax_amount = $total_tax_amount + $order_item_data[$k]['tax_amount'] * $order_item_data[$k]['quantity'];
                }
                $order_item_data[$k]['image_sm'] = (empty($order_item_data[$k]['image']) || file_exists(FCPATH . $order_item_data[$k]['image']) == FALSE) ? base_url(NO_IMAGE) : get_image_url($order_item_data[$k]['image'], 'thumb', 'sm');
                $order_item_data[$k]['image_md'] = (empty($order_item_data[$k]['image']) || file_exists(FCPATH . $order_item_data[$k]['image']) == FALSE) ? base_url(NO_IMAGE) : get_image_url($order_item_data[$k]['image'], 'thumb', 'md');
                $order_item_data[$k]['image'] = (empty($order_item_data[$k]['image']) || file_exists(FCPATH . $order_item_data[$k]['image']) == FALSE) ? base_url(NO_IMAGE) : get_image_url($order_item_data[$k]['image']);
                $order_item_data[$k]['is_already_returned'] = ($order_item_data[$k]['active_status'] == 'returned') ? '1' : '0';
                $order_item_data[$k]['is_already_cancelled'] = ($order_item_data[$k]['active_status'] == 'cancelled') ? '1' : '0';
                $return_request_key = array_search($order_item_data[$k]['id'], array_column($return_request, 'order_item_id'));
                if ($return_request_key !== false) {
                    $order_item_data[$k]['return_request_submitted'] = $return_request[$return_request_key]['status'];
                    $order_item_data[$k]['return_request_remarks'] = $return_request[$return_request_key]['remarks'] ?? '';
                    if ($order_item_data[$k]['return_request_submitted'] == '1') {
                        $return_request_submitted_count += $order_item_data[$k]['return_request_submitted'];
                    }
                } else {
                    $order_item_data[$k]['return_request_submitted'] = '';
                    $return_request_submitted_count = null;
                }
                $order_item_data[$k]['courier_agency'] = (isset($order_item_data[$k]['courier_agency']) && !empty($order_item_data[$k]['courier_agency'])) ? $order_item_data[$k]['courier_agency'] : "";
                $order_item_data[$k]['tracking_id'] = (isset($order_item_data[$k]['tracking_id']) && !empty($order_item_data[$k]['tracking_id'])) ? $order_item_data[$k]['tracking_id'] : "";
                $order_item_data[$k]['url'] = (isset($order_item_data[$k]['url']) && !empty($order_item_data[$k]['url'])) ? $order_item_data[$k]['url'] : "";
                $order_item_data[$k]['shiprocket_order_tracking_url'] = (isset($order_item_data[$k]['awb_code']) && !empty($order_item_data[$k]['awb_code']) && $order_item_data[$k]['awb_code'] != '' && $order_item_data[$k]['awb_code'] != null) ? "https://shiprocket.co/tracking/" . $order_item_data[$k]['awb_code'] : "";
                $order_item_data[$k]['deliver_by'] = (isset($order_item_data[$k]['delivery_boy_name']) && !empty($order_item_data[$k]['delivery_boy_name'])) ? $order_item_data[$k]['delivery_boy_name'] : "";
                $order_item_data[$k]['delivery_boy_id'] = (isset($order_item_data[$k]['delivery_boy_id']) && !empty($order_item_data[$k]['delivery_boy_id'])) ? $order_item_data[$k]['delivery_boy_id'] : "";
                $order_item_data[$k]['discounted_price'] = (isset($order_item_data[$k]['discounted_price']) && !empty($order_item_data[$k]['discounted_price'])) ? $order_item_data[$k]['discounted_price'] : "";
                $order_item_data[$k]['delivery_boy_name'] = (isset($order_item_data[$k]['delivery_boy_name']) && !empty($order_item_data[$k]['delivery_boy_name'])) ? $order_item_data[$k]['delivery_boy_name'] : "";
                if (($order_details[$i]['type'] == 'digital_product' && in_array(0, $download_allowed)) || ($order_details[$i]['type'] != 'digital_product' && in_array(0, $download_allowed))) {
                    $order_details[$i]['download_allowed'] = '0';
                    $order_item_data[$k]['download_link'] = '';
                } else {
                    $order_details[$i]['download_allowed'] = '1';
                    $order_item_data[$k]['download_link'] = $order_item_data[$k]['download_link'];
                }
                $order_item_data[$k]['email'] = (isset($order_details[$i]['email']) && !empty($order_details[$i]['email']) ? $order_details[$i]['email'] : '');

                $returnable_count += $order_item_data[$k]['product_is_returnable'];
                $cancelable_count += $order_item_data[$k]['product_is_cancelable'];
                $already_returned_count += $order_item_data[$k]['is_already_returned'];
                $already_cancelled_count += $order_item_data[$k]['is_already_cancelled'];

                $delivery_date = null;

                foreach ($order_item_data[$k]['status'] as $status) {

                    if ($status[0] == 'delivered') {
                        $delivery_date = $status[1];
                    }
                }
                //                log_message("error", "orderitemData: ".$delivery_date);
                $settings = get_settings('system_settings', true);

                $today = date('Y-m-d');

                $return_till = date('Y-m-d', strtotime($delivery_date . ' + ' . $settings['max_product_return_days'] . ' days'));
                $order_item_data[$k]['is_returnable'] = (($delivery_date != null) && ($today < $return_till))  ? '1' : '0';
                $order_item_data[$k]['is_consignment_created'] = (isset($order_item_data[$k]['consignment_item_id']) && !empty($order_item_data[$k]['consignment_item_id'])) ? 1 : 0;
                unset($order_item_data[$k]['consignment_item_id']);
            }
        }

        $order_details[$i]['delivery_time'] = (isset($order_details[$i]['delivery_time']) && !empty($order_details[$i]['delivery_time'])) ? $order_details[$i]['delivery_time'] : "";
        $order_details[$i]['delivery_date'] = (isset($order_details[$i]['delivery_date']) && !empty($order_details[$i]['delivery_date'])) ? $order_details[$i]['delivery_date'] : "";
        $order_details[$i]['otp'] = (isset($order_details[$i]['otp']) && !empty($order_details[$i]['otp'])) ? $order_details[$i]['otp'] : "";
        $order_details[$i]['is_returnable'] = ($returnable_count >= 1 && isset($delivery_date) && !empty($delivery_date) && $today < $return_till) ? '1' : '0';
        $order_details[$i]['is_cancelable'] = ($cancelable_count >= 1) ? '1' : '0';
        $order_details[$i]['is_already_returned'] = ($already_returned_count == count($order_item_data)) ? '1' : '0';
        $order_details[$i]['is_already_cancelled'] = ($already_cancelled_count == count($order_item_data)) ? '1' : '0';
        if ($return_request_submitted_count == null) {
            $order_details[$i]['return_request_submitted'] = '';
        } else {
            $order_details[$i]['return_request_submitted'] = ($return_request_submitted_count == count($order_item_data)) ? '1' : '0';
        }

        if ((isset($delivery_boy_id) && $delivery_boy_id != null) || (isset($seller_id) && $seller_id != null)) {

            $order_details[$i]['total'] = strval($item_subtotal);

            $order_details[$i]['final_total'] = strval($item_subtotal - $total_tax_amount + $order_details[$i]['delivery_charge']);
            $order_details[$i]['total_payable'] = strval($item_subtotal + $order_details[$i]['delivery_charge'] - $order_details[$i]['promo_discount'] - $order_details[$i]['wallet_balance']);
        } else {
            $order_details[$i]['total'] = strval($order_details[$i]['total']);
        }

        $order_details[$i]['address'] = (isset($order_details[$i]['address']) && !empty($order_details[$i]['address'])) ? output_escaping($order_details[$i]['address']) : "";
        $order_details[$i]['username'] = output_escaping($order_details[$i]['username']) ?? "User Not Found";
        $order_details[$i]['email'] = output_escaping($order_details[$i]['email']) ?? "User Not Found";
        $order_details[$i]['country_code'] = (isset($order_details[$i]['country_code']) && !empty($order_details[$i]['country_code'])) ? $order_details[$i]['country_code'] : '';
        $order_details[$i]['total_tax_percent'] = strval($total_tax_percent);
        $order_details[$i]['total_tax_amount'] = strval($total_tax_amount);
        if (isset($seller_id) && $seller_id != null) {
            if ($download_invoice == true || $download_invoice == 1) {
                $order_details[$i]['invoice_html'] = get_seller_invoice_html($order_details[$i]['id'], $seller_id);
            }
        } else {
            if ($download_invoice == true || $download_invoice == 1) {
                $order_details[$i]['invoice_html'] = get_invoice_html($order_details[$i]['id']);
            }
        }

        if (!empty($order_item_data)) {
            $order_details[$i]['order_items'] = $order_item_data;
        } else {
            $order_details[$i]['order_items'] = [];
        }
    }

    $order_data['total'] = $total;
    $order_data['order_data'] = array_values($order_details);
    return $order_data;
}
function fetch_order_items($order_item_id = NULL, $user_id = NULL, $status = NULL, $delivery_boy_id = NULL, $limit = NULL, $offset = NULL, $sort = NULL, $order = NULL, $start_date = null, $end_date = null, $search = null, $seller_id = null, $order_id = null)
{

    $t = &get_instance();
    $where = [];

    $count_res = $t->db->select(' COUNT(o.id) as total ')
        ->join(' users u', 'u.id= oi.delivery_boy_id', 'left')
        ->join('users us ', ' us.id = oi.seller_id', 'left')
        ->join(' orders o', 'o.id= oi.order_id', 'left')
        ->join('users un ', ' un.id = o.user_id', 'left')
        ->join('product_variants pv', 'pv.id=oi.product_variant_id', 'left')
        ->join('products p', 'pv.product_id=p.id', 'left')
        ->join('seller_data sd', 'sd.user_id=p.seller_id', 'left');
    if (isset($order_item_id) && $order_item_id != null) {
        if (is_array($order_item_id) && !empty($order_item_id)) {
            $count_res->where_in('oi.id', $order_item_id);
        } else {
            $where['oi.id'] = $order_item_id;
        }
    }
    if (isset($order_id) && $order_id != null) {
        $where['oi.order_id'] = $order_id;
    }

    if (isset($delivery_boy_id) && $delivery_boy_id != null) {
        $where['oi.delivery_boy_id'] = $delivery_boy_id;
    }
    if (isset($seller_id) && $seller_id != null) {
        $where['oi.seller_id'] = $seller_id;
    }

    if (isset($start_date) && $start_date != null && isset($end_date) && $end_date != null) {
        $count_res->where(" DATE(oi.date_added) >= DATE('" . $start_date . "') ");
        $count_res->where(" DATE(oi.date_added) <= DATE('" . $end_date . "') ");
    }

    if (isset($search) and $search != null) {

        $filters = [
            'u.username' => $search,
            'u.email' => $search,
            'oi.id' => $search,
            'p.name' => $search
        ];
    }
    if (isset($filters) && !empty($filters)) {
        $count_res->group_Start();
        $count_res->or_like($filters);
        $count_res->group_End();
    }

    $count_res->where($where);
    if ($sort == 'date_added') {
        $sort = 'oi.date_added';
    }
    if ($sort != null && $order != null) {
        $count_res->order_by($sort, $order);
    }

    $order_count = $count_res->get('order_items oi')->result_array();

    $total = "0";
    foreach ($order_count as $row) {
        $total = $row['total'];
    }

    $search_res = $t->db->select('u.username,u.email,u.mobile, oi.*,p.id as product_id,p.is_cancelable,sd.store_name,p.is_returnable,p.image,p.name,p.type,oi.status as status,(Select count(id) from order_items where order_id = oi.order_id ) as order_counter ,(Select count(active_status) from order_items where active_status ="cancelled" and order_id = oi.order_id ) as order_cancel_counter , (Select count(active_status) from order_items where active_status ="returned" and order_id = oi.order_id ) as order_return_counter, o.payment_method,o.address as user_address,o.total as subtotal_of_order_items,o.delivery_charge,o.wallet_balance,o.discount,o.promo_discount,o.total_payable,o.notes,o.delivery_date,o.delivery_time,o.is_cod_collected,o.is_shiprocket_order,p.pickup_location,p.sku,p.slug as product_slug')
        ->join('users u', 'u.id= oi.delivery_boy_id', 'left')
        ->join('users us ', ' us.id = oi.seller_id', 'left')
        ->join('orders o', 'o.id= oi.order_id', 'left')
        ->join('users un ', ' un.id = o.user_id', 'left')
        ->join('product_variants pv', 'pv.id=oi.product_variant_id', 'left')
        ->join('products p', 'pv.product_id=p.id', 'left')
        ->join('seller_data sd', 'sd.user_id=p.seller_id', 'left');
    $search_res->where($where);
    if (is_array($order_item_id) && !empty($order_item_id)) {
        $search_res->where_in('oi.id', $order_item_id);
    }
    if (isset($start_date) && $start_date != null && isset($end_date) && $end_date != null) {
        $search_res->where(" DATE(oi.date_added) >= DATE('" . $start_date . "') ");
        $search_res->where(" DATE(oi.date_added) <= DATE('" . $end_date . "') ");
    }
    if (isset($filters) && !empty($filters)) {
        $search_res->group_Start();
        $search_res->or_like($filters);
        $search_res->group_End();
    }
    if (empty($sort)) {
        $sort = 'oi.date_added';
    }
    $search_res->group_by('oi.id');
    $search_res->order_by($sort, $order);
    if ($limit != null || $offset != null) {
        $search_res->limit($limit, $offset);
    }

    $order_item_data = $search_res->get('order_items oi')->result_array();
    for ($k = 0; $k < count($order_item_data); $k++) {

        $multipleWhere = ['seller_id' => $order_item_data[$k]['seller_id'], 'order_id' => $order_item_data[$k]['order_id']];
        $order_charge_data = $t->db->where($multipleWhere)->get('order_charges')->result_array();
        $return_request = fetch_details('return_requests', ['user_id' => $user_id]);
        $order_item_data[$k]['status'] = json_decode($order_item_data[$k]['status']);
        $order_item_data[$k]['delivery_boy_id'] = (isset($order_item_data[$k]['delivery_boy_id']) && !empty($order_item_data[$k]['delivery_boy_id'])) ? $order_item_data[$k]['delivery_boy_id'] : '';
        $order_item_data[$k]['discounted_price'] = (isset($order_item_data[$k]['discounted_price']) && !empty($order_item_data[$k]['discounted_price'])) ? $order_item_data[$k]['discounted_price'] : '';
        $order_item_data[$k]['deliver_by'] = (isset($order_item_data[$k]['deliver_by']) && !empty($order_item_data[$k]['deliver_by'])) ? $order_item_data[$k]['deliver_by'] : '';
        if ($order_item_data[$k]['otp'] != 0) {
            $order_item_data[$k]['otp'] = $order_item_data[$k]['otp'];
        } else if ($order_charge_data[0]['otp'] != 0) {
            $order_item_data[$k]['otp'] = $order_charge_data[0]['otp'];
        } else {
            $order_item_data[$k]['otp'] = '';
        }

        for ($j = 0; $j < count($order_item_data[$k]['status']); $j++) {
            $order_item_data[$k]['status'][$j][1] = date('d-m-Y h:i:sa', strtotime($order_item_data[$k]['status'][$j][1]));
        }

        $returnable_count = 0;
        $cancelable_count = 0;
        $already_returned_count = 0;
        $already_cancelled_count = 0;
        $return_request_submitted_count = 0;
        $total_tax_percent = $total_tax_amount = 0;

        $varaint_data = get_variants_values_by_id($order_item_data[$k]['product_variant_id']);
        // varient ids
        $order_item_data[$k]['varaint_ids'] = (!empty($varaint_data)) ? $varaint_data[0]['varaint_ids'] : '';
        $order_item_data[$k]['variant_values'] = (!empty($varaint_data)) ? $varaint_data[0]['variant_values'] : '';
        $order_item_data[$k]['attr_name'] = (!empty($varaint_data)) ? $varaint_data[0]['attr_name'] : '';

        $order_item_data[$k]['name'] = (!empty($order_item_data[$k]['name'])) ? $order_item_data[$k]['name'] : $order_item_data[$k]['product_name'];
        $order_item_data[$k]['variant_values'] = (!empty($order_item_data[$k]['variant_values'])) ? $order_item_data[$k]['variant_values'] : $order_item_data[$k]['variant_name'];

        if (!in_array($order_item_data[$k]['active_status'], ['returned', 'cancelled'])) {
            $total_tax_percent = $total_tax_percent + $order_item_data[$k]['tax_percent'];
            $total_tax_amount = $total_tax_amount + $order_item_data[$k]['tax_amount'];
        }

        for ($j = 0; $j < count($order_item_data[$k]['status']); $j++) {
            $order_item_data[$k]['status'][$j][1] = date('d-m-Y h:i:sa', strtotime($order_item_data[$k]['status'][$j][1]));
        }

        $order_item_data[$k]['image_sm'] = (empty($order_item_data[$k]['image']) || file_exists(FCPATH . $order_item_data[$k]['image']) == FALSE) ? base_url(NO_IMAGE) : get_image_url($order_item_data[$k]['image'], 'thumb', 'sm');
        $order_item_data[$k]['image_md'] = (empty($order_item_data[$k]['image']) || file_exists(FCPATH . $order_item_data[$k]['image']) == FALSE) ? base_url(NO_IMAGE) : get_image_url($order_item_data[$k]['image'], 'thumb', 'md');
        $order_item_data[$k]['image'] = (empty($order_item_data[$k]['image']) || file_exists(FCPATH . $order_item_data[$k]['image']) == FALSE) ? base_url(NO_IMAGE) : get_image_url($order_item_data[$k]['image']);
        $order_item_data[$k]['is_already_returned'] = ($order_item_data[$k]['active_status'] == 'returned') ? '1' : '0';
        $order_item_data[$k]['is_already_cancelled'] = ($order_item_data[$k]['active_status'] == 'cancelled') ? '1' : '0';
        $return_request_key = array_search($order_item_data[$k]['id'], array_column($return_request, 'order_item_id'));
        if ($return_request_key !== false) {
            $order_item_data[$k]['return_request_submitted'] = $return_request[$return_request_key]['status'];
            if ($order_item_data[$k]['return_request_submitted'] == '1') {
                $return_request_submitted_count += $order_item_data[$k]['return_request_submitted'];
            }
        } else {
            $order_item_data[$k]['return_request_submitted'] = '';
            $return_request_submitted_count = null;
        }

        $returnable_count += $order_item_data[$k]['is_returnable'];
        $cancelable_count += $order_item_data[$k]['is_cancelable'];
        $already_returned_count += $order_item_data[$k]['is_already_returned'];
        $already_cancelled_count += $order_item_data[$k]['is_already_cancelled'];

        $order_details[$k]['is_returnable'] = ($returnable_count >= 1) ? '1' : '0';
        $order_details[$k]['is_cancelable'] = ($cancelable_count >= 1) ? '1' : '0';
        $order_details[$k]['is_already_returned'] = ($already_returned_count == count($order_item_data)) ? '1' : '0';
        $order_details[$k]['is_already_cancelled'] = ($already_cancelled_count == count($order_item_data)) ? '1' : '0';
        if ($return_request_submitted_count == null) {
            $order_details[$k]['return_request_submitted'] = null;
        } else {
            $order_details[$k]['return_request_submitted'] = ($return_request_submitted_count == count($order_item_data)) ? '1' : '0';
        }
        $order_details[$k]['username'] = output_escaping($order_details[$k]['username']);
        $order_details[$k]['total_tax_percent'] = strval($total_tax_percent);
        $order_details[$k]['total_tax_amount'] = strval($total_tax_amount);
    }
    $order_data['total'] = $total;
    $order_data['order_data'] = (!empty($order_item_data)) ? array_values($order_item_data) : [];
    return $order_data;
}

function find_media_type($extenstion)
{
    $t = &get_instance();
    $t->config->load('eshop');
    $type = $t->config->item('type');
    foreach ($type as $main_type => $extenstions) {
        foreach ($extenstions['types'] as $k => $v) {
            if ($v === strtolower($extenstion)) {
                return array($main_type, $extenstions['icon']);
            }
        }
    }
    return false;
}

function formatBytes($size, $precision = 2)
{
    $base = log($size, 1024);
    $suffixes = array('', 'KB', 'MB', 'GB', 'TB');

    return round(pow(1024, $base - floor($base)), $precision) . ' ' . $suffixes[floor($base)];
}

function delete_images($subdirectory, $image_name)
{
    $image_types = ['thumb-md/', 'thumb-sm/', 'cropped-md/', 'cropped-sm/'];
    $main_dir = FCPATH . $subdirectory;

    foreach ($image_types as $types) {
        $path = $main_dir . $types . $image_name;
        if (file_exists($path)) {
            unlink($path);
        }
    }

    if (file_exists($main_dir . $image_name)) {
        unlink($main_dir . $image_name);
    }
}

function get_image_url($path, $image_type = '', $image_size = '', $file_type = 'image')
{
    if (filter_var($path, FILTER_VALIDATE_URL)) {
        return $path;
    }
    $path = explode('/', (string) $path);
    $subdirectory = '';
    for ($i = 0; $i < count($path) - 1; $i++) {
        $subdirectory .= $path[$i] . '/';
    }
    $image_name = end($path);

    $file_main_dir = FCPATH . $subdirectory;
    $image_main_dir = base_url() . $subdirectory;
    if ($file_type == 'image') {
        $types = ['thumb', 'cropped'];
        $sizes = ['md', 'sm'];
        if (in_array(trim(strtolower($image_type)), $types) && in_array(trim(strtolower($image_size)), $sizes)) {
            $filepath = $file_main_dir . $image_type . '-' . $image_size . '/' . $image_name;
            $imagepath = $image_main_dir . $image_type . '-' . $image_size . '/' . $image_name;
            if (file_exists($filepath)) {
                return $imagepath;
            } else if (file_exists($file_main_dir . $image_name)) {
                return $image_main_dir . $image_name;
            } else {
                return base_url() . NO_IMAGE;
            }
        } else {
            if (file_exists($file_main_dir . $image_name)) {
                return $image_main_dir . $image_name;
            } else {
                return base_url() . NO_IMAGE;
            }
        }
    } else {
        $file = new SplFileInfo($file_main_dir . $image_name);
        $ext = $file->getExtension();

        $media_data = find_media_type($ext);
        $image_placeholder = $media_data[1];
        $filepath = FCPATH . $image_placeholder;
        $extensionpath = base_url() . $image_placeholder;
        if (file_exists($filepath)) {
            return $extensionpath;
        } else {
            return base_url() . NO_IMAGE;
        }
    }
}
function fetch_users($id)
{
    $t = &get_instance();
    $user_details = $t->db->select('u.id,username,country_code,email,mobile,balance,dob, image, referral_code, friends_code, c.name as cities,a.name as area,street,pincode, address')
        ->join('areas a', 'u.area = a.name', 'left')
        ->join('cities c', 'u.city = c.name', 'left')
        ->where('u.id', $id)->get('users u')
        ->result_array();
    return $user_details;
}

function escape_array($array)
{
    $t = &get_instance();
    $posts = array();
    if (!empty($array)) {
        if (is_array($array)) {
            foreach ($array as $key => $value) {
                $posts[$key] = $t->db->escape_str($value ?? '');
            }
        } else {
            return $t->db->escape_str($array);
        }
    }
    return $posts;
}

function allowed_media_types()
{
    $t = &get_instance();
    $t->config->load('eshop');
    $type = $t->config->item('type');
    $general = [];
    foreach ($type as $main_type => $extenstions) {
        $general = array_merge_recursive($general, $extenstions['types']);
    }
    return $general;
}

function get_current_version()
{
    $t = &get_instance();
    $version = $t->db->select('*')->order_by('id', 'DESC')->get('updates')->result_array();
    return $version[0]['version'];
}

function resize_review_images($image_data, $source_path, $id = false)
{
    if ($image_data['is_image']) {

        $t = &get_instance();

        $target_path = $source_path; // Target path will be under source path
        $image_name = $image_data['file_name']; // original image's name
        $w = $image_data['image_width']; // original image's width
        $h = $image_data['image_height']; // original images's height

        $t->load->library('image_lib');

        if (file_exists($source_path . $image_name)) {  //check if the image file exist

            if (!file_exists($target_path)) {
                mkdir($target_path, 0777);
            }

            $n_w = 800;
            $n_h = 800;
            $config['image_library'] = 'gd2';
            $config['create_thumb'] = FALSE;
            $config['maintain_ratio'] = TRUE;
            $config['quality'] = '90%';
            $config['source_image'] = $source_path . $image_name;
            $config['new_image'] = $target_path . $image_name;
            $config['width'] = $n_w;
            $config['height'] = $n_h;
            $t->image_lib->clear();
            $t->image_lib->initialize($config);
            if (!$t->image_lib->resize()) {
                return $t->image_lib->display_errors();
            }
        }
    }
}

function get_invoice_html($order_id)
{
    $t = &get_instance();
    $invoice_generated_html = '';
    $t->data['main_page'] = VIEW . 'api-order-invoice';
    $settings = get_settings('system_settings', true);
    $t->data['title'] = 'Invoice Management |' . $settings['app_name'];
    $t->data['meta_description'] = 'Ekart | Invoice Management';
    if (isset($order_id) && !empty($order_id)) {
        $res = $t->Order_model->get_order_details(['o.id' => $order_id], true);
        if (!empty($res)) {
            $items = [];
            $promo_code = [];
            if (!empty($res[0]['promo_code'])) {
                $promo_code = fetch_details('promo_codes', ['promo_code' => trim($res[0]['promo_code'])]);
            }
            foreach ($res as $row) {
                $row = output_escaping($row);
                $temp['product_id'] = $row['product_id'];
                $temp['seller_id'] = $row['seller_id'];
                $temp['product_variant_id'] = $row['product_variant_id'];
                $temp['pname'] = $row['pname'];
                $temp['quantity'] = $row['quantity'];
                $temp['discounted_price'] = $row['discounted_price'];
                $temp['tax_percent'] = $row['tax_percent'];
                $temp['tax_amount'] = $row['tax_amount'];
                $temp['price'] = $row['price'];
                $temp['delivery_boy'] = $row['delivery_boy'];
                $temp['active_status'] = $row['oi_active_status'];
                $temp['is_prices_inclusive_tax'] = $row['is_prices_inclusive_tax'];
                array_push($items, $temp);
            }
            $t->data['order_detls'] = $res;
            $t->data['items'] = $items;
            $t->data['promo_code'] = $promo_code;
            $t->data['settings'] = $settings;
            $invoice_generated_html = $t->load->view('admin/invoice-template', $t->data, TRUE);
        } else {
            $invoice_generated_html = '';
        }
    } else {
        $invoice_generated_html = '';
    }
    return $invoice_generated_html;
}

function get_seller_invoice_html($order_id, $seller_id)
{
    $t = &get_instance();
    $invoice_generated_html = '';
    $t->data['main_page'] = VIEW . 'api-order-invoice';
    $settings = get_settings('system_settings', true);
    $t->data['title'] = 'Invoice Management |' . $settings['app_name'];
    $t->data['meta_description'] = 'Ekart | Invoice Management';
    if (isset($order_id) && !empty($order_id) && isset($seller_id) && !empty($seller_id)) {
        $s_user_data = fetch_details('users', ['id' => $seller_id], 'email,mobile,address,country_code');
        $seller_data = fetch_details('seller_data', ['user_id' => $seller_id], 'store_name,pan_number,tax_name,tax_number');
        $res = $t->order_model->get_order_details(['o.id' => $order_id, 'oi.seller_id' => $seller_id], true);
        if (!empty($res)) {
            $items = [];
            $promo_code = [];
            if (!empty($res[0]['promo_code'])) {
                $promo_code = fetch_details('promo_codes', ['promo_code' => trim($res[0]['promo_code'])]);
            }
            foreach ($res as $row) {
                $row = output_escaping($row);
                $temp['product_id'] = $row['product_id'];
                $temp['product_variant_id'] = $row['product_variant_id'];
                $temp['pname'] = $row['pname'];
                $temp['quantity'] = $row['quantity'];
                $temp['discounted_price'] = $row['discounted_price'];
                $temp['tax_percent'] = $row['tax_percent'];
                $temp['tax_amount'] = $row['tax_amount'];
                $temp['price'] = $row['price'];
                $temp['delivery_boy'] = $row['delivery_boy'];
                $temp['active_status'] = $row['oi_active_status'];
                array_push($items, $temp);
            }
            $t->data['order_detls'] = $res;
            $t->data['items'] = $items;
            $t->data['s_user_data'] = $s_user_data;
            $t->data['seller_data'] = $seller_data;
            $t->data['promo_code'] = $promo_code;
            $t->data['settings'] = $settings;
            $invoice_generated_html = $t->load->view('seller/invoice-template', $t->data, TRUE);
        } else {
            $invoice_generated_html = '';
        }
    } else {
        $invoice_generated_html = '';
    }
    return $invoice_generated_html;
}

function is_modification_allowed($module)
{
    $allow_modification = (get_instance()->session->userdata('mobile') == '9638527410') ? 1 : IS_ALLOWED_MODIFICATION;

    $allow_modification = ($allow_modification == 0) ? 0 : 1;
    $excluded_modules = ['orders'];
    if (isset($allow_modification) && $allow_modification == 0) {
        if (!in_array(strtolower($module), $excluded_modules)) {
            return false;
        }
    }
    return true;
}
function output_escaping($array)
{
    $exclude_fields = ["images", "other_images"];
    $t = &get_instance();

    if (!empty($array)) {
        if (is_array($array)) {
            $data = array();
            foreach ($array as $key => $value) {
                if (!in_array($key, $exclude_fields)) {
                    $data[$key] = stripcslashes((string) $value);
                } else {
                    $data[$key] = $value;
                }
            }
            return $data;
        } else if (is_object($array)) {
            $data = new stdClass();
            foreach ($array as $key => $value) {
                if (!in_array($key, $exclude_fields)) {
                    $data->$key = stripcslashes($value);
                } else {
                    $data[$key] = $value;
                }
            }
            return $data;
        } else {
            return stripcslashes($array);
        }
    }
}
function get_min_max_price_of_product($product_id = '')
{

    $t = &get_instance();
    $t->db->join('product_variants pv', 'p.id = pv.product_id')->join('taxes tax', 'FIND_IN_SET(tax.id, p.tax) > 0', 'LEFT');
    if (!empty($product_id)) {
        $t->db->where('p.id', $product_id);
    }
    $response = $t->db->select('is_prices_inclusive_tax,price,special_price,GROUP_CONCAT(tax.percentage) as tax_percentage')->group_by('p.id')->get('products p')->result_array();
    $percentage = (isset($response[0]['tax_percentage']) && intval($response[0]['tax_percentage']) > 0 && $response[0]['tax_percentage'] != null) ? $response[0]['tax_percentage'] : '0';

    if (isset($response[0]['tax_percentage']) && !empty($response[0]['tax_percentage']) && ((isset($response[0]['is_prices_inclusive_tax']) && $response[0]['is_prices_inclusive_tax'] == 0) || (!isset($response[0]['is_prices_inclusive_tax'])) && $percentage > 0)) {

        // Calculate multi tax amount
        $tax_percentage = explode(',', $percentage);
        $total_tax = array_sum($tax_percentage);

        $price_tax_amount = $response[0]['price'] * ($total_tax / 100);
        $special_price_tax_amount = $response[0]['special_price'] * ($total_tax / 100);
    } else {
        $price_tax_amount = 0;
        $special_price_tax_amount = 0;
    }
    $data['min_price'] = (isset($response) && !empty($response)) ? min(array_column($response, 'price')) + $price_tax_amount : '0';
    $data['max_price'] = (isset($response) && !empty($response)) ? max(array_column($response, 'price')) + $price_tax_amount : '0';
    $data['special_price'] = (isset($response) && !empty($response)) ? min(array_column($response, 'special_price')) + $special_price_tax_amount : '0';
    $data['max_special_price'] = (isset($response) && !empty($response)) ? max(array_column($response, 'special_price')) + $special_price_tax_amount : '0';
    $data['discount_in_percentage'] = find_discount_in_percentage($data['special_price'] + $special_price_tax_amount, $data['min_price'] + $price_tax_amount);
    return $data;
}
function get_price_range_of_product($product_id = '')
{
    $system_settings = get_settings('system_settings', true);
    $currency = (isset($system_settings['currency']) && !empty($system_settings['currency'])) ? $system_settings['currency'] : '';
    $t = &get_instance();
    $t->db->join('product_variants pv', 'p.id = pv.product_id')->join('taxes tax', 'tax.id = p.tax', 'LEFT');
    if (!empty($product_id)) {
        $t->db->where('p.id', $product_id);
    }
    $response = $t->db->select('is_prices_inclusive_tax,price,special_price,tax.percentage as tax_percentage')->get('products p')->result_array();

    if (count($response) == 1) {
        $percentage = (isset($response[0]['tax_percentage']) && intval($response[0]['tax_percentage']) > 0 && $response[0]['tax_percentage'] != null) ? $response[0]['tax_percentage'] : '0';
        if ((isset($response[0]['is_prices_inclusive_tax']) && $response[0]['is_prices_inclusive_tax'] == 0) || (!isset($response[0]['is_prices_inclusive_tax'])) && $percentage > 0) {
            $price_tax_amount = $response[0]['price'] * ($percentage / 100);
            $special_price_tax_amount = $response[0]['special_price'] * ($percentage / 100);
        } else {
            $price_tax_amount = 0;
            $special_price_tax_amount = 0;
        }
        $price_tax_amount = $price_tax_amount;
        $special_price_tax_amount = $special_price_tax_amount;
        $price = $response[0]['special_price'] == 0 ? $response[0]['price'] + $price_tax_amount : $response[0]['special_price'] + $special_price_tax_amount;
        $data['range'] = $currency . "<small style='font-size: 20px;'>" . number_format($price, 2) . "</small>";
    } else {
        for ($i = 0; $i < count($response); $i++) {
            $is_all_specical_price_zero = 1;
            if ($response[$i]['special_price'] != 0) {
                $is_all_specical_price_zero = 0;
            }

            if ($is_all_specical_price_zero == 1) {
                $min = min(array_column($response, 'price'));
                $max = max(array_column($response, 'price'));
                $percentage = (isset($response[$i]['tax_percentage']) && intval($response[$i]['tax_percentage']) > 0 && $response[$i]['tax_percentage'] != null) ? $response[$i]['tax_percentage'] : '0';
                if ((isset($response[$i]['is_prices_inclusive_tax']) && $response[$i]['is_prices_inclusive_tax'] == 0) || (!isset($response[$i]['is_prices_inclusive_tax'])) && $percentage > 0) {
                    $min_price_tax_amount = $min * ($percentage / 100);
                    $min = $min + $min_price_tax_amount;

                    $max_price_tax_amount = $max * ($percentage / 100);
                    $max = $max + $max_price_tax_amount;
                }

                $data['range'] = $currency . "<small style='font-size: 20px;'>" . number_format($min, 2) . "</small>" . ' - ' . $currency . "<small style='font-size: 20px;'>" . number_format($max, 2) . "</small>";
            } else {

                $min_special_price = array_column($response, 'special_price');
                for ($j = 0; $j < count($min_special_price); $j++) {
                    if ($min_special_price[$j] == 0) {
                        unset($min_special_price[$j]);
                    }
                }
                $min_special_price = min($min_special_price);
                $max = max(array_column($response, 'price'));
                $percentage = (isset($response[$i]['tax_percentage']) && intval($response[$i]['tax_percentage']) > 0 && $response[$i]['tax_percentage'] != null) ? $response[$i]['tax_percentage'] : '0';
                if ((isset($response[$i]['is_prices_inclusive_tax']) && $response[$i]['is_prices_inclusive_tax'] == 0) || (!isset($response[$i]['is_prices_inclusive_tax'])) && $percentage > 0) {
                    $min_price_tax_amount = $min_special_price * ($percentage / 100);
                    $min_special_price = $min_special_price + $min_price_tax_amount;
                    $max_price_tax_amount = $max * ($percentage / 100);
                    $max = $max + $max_price_tax_amount;
                }
                $data['range'] = $currency . "<small style='font-size: 20px;'>" . number_format($min_special_price, 2) . "</small>" . ' - ' . $currency . "<small style='font-size: 20px;'>" . number_format($max, 2) . "</small>";
            }
        }
    }

    return $data;
}
function find_discount_in_percentage($special_price, $price)
{
    $diff_amount = $price - $special_price;
    if ($diff_amount > 0) {
        return intval(($diff_amount * 100) / $price);
    }
}
function get_attribute_ids_by_value($values, $names)
{
    $t = &get_instance();
    $names = str_replace('-', ' ', $names);
    $attribute_ids = $t->db->select("av.id")
        ->join('attributes a ', 'av.attribute_id = a.id ')
        ->where_in('av.value', $values)
        ->where_in('a.name', $names)
        ->get('attribute_values av')->result_array();
    return array_column($attribute_ids, 'id');
}

function insert_details($data, $table)
{
    $t = &get_instance();
    return $t->db->insert($table, $data);
}

function get_category_id_by_slug($slug)
{
    $t = &get_instance();
    $slug = urldecode($slug);
    return $t->db->select("id")
        ->where('slug', $slug)
        ->get('categories')->row_array()['id'];
}

function get_variant_attributes($product_id)
{
    $product = fetch_product(NULL, NULL, $product_id);
    if (!empty($product['product'][0]['variants']) && isset($product['product'][0]['variants'])) {
        $attributes_array = explode(',', $product['product'][0]['variants'][0]['attr_name']);
        $variant_attributes = [];
        foreach ($attributes_array as $attribute) {
            $attribute = trim($attribute);

            $key = array_search($attribute, array_column($product['product'][0]['attributes'], 'name'), false);
            if ($key === 0 || !empty(strval($key))) {
                $variant_attributes[$key]['ids'] = $product['product'][0]['attributes'][$key]['ids'];
                $variant_attributes[$key]['values'] = $product['product'][0]['attributes'][$key]['value'];
                $variant_attributes[$key]['attr_name'] = $attribute;
            }
        }
        return $variant_attributes;
    }
}

function get_product_variant_details($product_variant_id)
{
    $CI = &get_instance();
    $res = $CI->db->join('products p', 'p.id=pv.product_id')
        ->where('pv.id', $product_variant_id)
        ->select('p.name,p.id,p.image,p.short_description,pv.*')->get('product_variants pv')->result_array();

    if (!empty($res)) {
        $res = array_map(function ($d) {
            $d['image_sm'] = get_image_url($d['image'], 'sm');
            $d['image_md'] = get_image_url($d['image'], 'md');
            $d['image'] = get_image_url($d['image']);
            return $d;
        }, $res);
    } else {
        return null;
    }
    return $res[0];
}

function get_cities($id = NULL, $limit = NULL, $offset = NULL)
{
    $CI = &get_instance();
    if (!empty($limit) || !empty($offset)) {
        $CI->db->limit($limit, $offset);
    }
    return $CI->db->get('cities')->result_array();
}

function get_favorites($user_id, $limit = NULL, $offset = NULL)
{
    $CI = &get_instance();
    if (!empty($limit) || !empty($offset)) {
        $CI->db->limit($limit, $offset);
    }
    $res = $CI->db->join('products p', 'p.id=f.product_id')
        ->where('f.user_id', $user_id)
        ->select('p.*')
        ->order_by('f.id', "DESC")
        ->get('favorites f')->result_array();

    $res = array_map(function ($d) {
        $d['image_md'] = get_image_url($d['image'], 'thumb', 'md');
        $d['image_sm'] = get_image_url($d['image'], 'thumb', 'sm');
        $d['relative_path'] = $d['image'];
        $d['image'] = get_image_url($d['image']);
        $d['variants'] = get_variants_values_by_pid($d['id']);
        $d['min_max_price'] = get_min_max_price_of_product($d['id']);
        $d['is_favorite'] = '1';
        return $d;
    }, $res);
    return $res;
}
function current_theme($id = '', $name = '', $slug = '', $is_default = 1, $status = '')
{
    //If don't pass any params then this function will return the current theme.
    $CI = &get_instance();
    if (!empty($id)) {
        $CI->db->where('id', $id);
    }
    if (!empty($name)) {
        $CI->db->where('name', $name);
    }
    if (!empty($slug)) {
        $CI->db->where('slug', $slug);
    }
    if (!empty($is_default)) {
        $CI->db->where('is_default', $is_default);
    }
    if (!empty($status)) {
        $CI->db->where('status', $status);
    }
    $res = $CI->db->get('themes')->result_array();
    $res = array_map(function ($d) {
        $d['image'] = base_url('assets/front_end/theme-images/' . $d['image']);
        return $d;
    }, $res);
    return $res;
}
function get_languages($id = '', $language_name = '', $code = '', $is_rtl = '', $is_default = '')
{
    $CI = &get_instance();
    if (!empty($id)) {
        $CI->db->where('id', $id);
    }
    if (!empty($language_name)) {
        $CI->db->where('language', $language_name);
    }
    if (!empty($code)) {
        $CI->db->where('code', $code);
    }
    if (!empty($is_rtl)) {
        $CI->db->where('is_rtl', $is_rtl);
    }
    if (!empty($is_default)) {
        $CI->db->where('is_default', $is_default);
    }
    $res = $CI->db->get('languages')->result_array();
    return $res;
}

function verify_payment_transaction($txn_id, $payment_method, $additional_data = [])
{
    if (empty(trim($txn_id))) {
        $response['error'] = true;
        $response['message'] = "Transaction ID is required";
        return $response;
    }

    $CI = &get_instance();
    $CI->config->load('eshop');
    $supported_methods = $CI->config->item('supported_payment_methods');

    if (empty(trim($payment_method)) || !in_array($payment_method, $supported_methods)) {
        $response['error'] = true;
        $response['message'] = "Invalid payment method supplied";
        return $response;
    }
    switch ($payment_method) {
        case 'razorpay':
            $CI->load->library("razorpay");
            $payment = $CI->razorpay->fetch_payments($txn_id);
            if (!empty($payment) && isset($payment['status'])) {
                if ($payment['status'] == 'authorized') {

                    /* if the payment is authorized try to capture it using the API */
                    $capture_response = $CI->razorpay->capture_payment($payment['amount'], $txn_id, $payment['currency']);
                    if ($capture_response['status'] == 'captured') {
                        $response['error'] = false;
                        $response['message'] = "Payment captured successfully";
                        $response['amount'] = $capture_response['amount'] / 100;
                        $response['data'] = $capture_response;
                        return $response;
                    } else if ($capture_response['status'] == 'refunded') {
                        $response['error'] = true;
                        $response['message'] = "Payment is refunded.";
                        $response['amount'] = $capture_response['amount'] / 100;
                        $response['data'] = $capture_response;
                        return $response;
                    } else {
                        $response['error'] = true;
                        $response['message'] = "Payment could not be captured.";
                        $response['amount'] = (isset($capture_response['amount'])) ? $capture_response['amount'] / 100 : 0;
                        $response['data'] = $capture_response;
                        return $response;
                    }
                } else if ($payment['status'] == 'captured') {
                    $response['error'] = false;
                    $response['message'] = "Payment captured successfully";
                    $response['amount'] = $payment['amount'] / 100;
                    $response['data'] = $payment;
                    return $response;
                } else if ($payment['status'] == 'created') {
                    $response['error'] = true;
                    $response['message'] = "Payment is just created and yet not authorized / captured!";
                    $response['amount'] = $payment['amount'] / 100;
                    $response['data'] = $payment;
                    return $response;
                } else {
                    $response['error'] = true;
                    $response['message'] = "Payment is " . ucwords($payment['status']) . "! ";
                    $response['amount'] = (isset($payment['amount'])) ? $payment['amount'] / 100 : 0;
                    $response['data'] = $payment;
                    return $response;
                }
            } else {
                $response['error'] = true;
                $response['message'] = "Payment not found by the transaction ID!";
                $response['amount'] = 0;
                $response['data'] = [];
                return $response;
            }
            break;
        case 'paystack':
            $CI->load->library("paystack");
            $payment = $CI->paystack->verify_transation($txn_id);
            if (!empty($payment)) {
                $payment = json_decode($payment, true);
                if (isset($payment['data']['status']) && $payment['data']['status'] == 'success') {
                    $response['error'] = false;
                    $response['message'] = "Payment is successful";
                    $response['amount'] = (isset($payment['data']['amount'])) ? $payment['data']['amount'] / 100 : 0;
                    $response['data'] = $payment;
                    return $response;
                } elseif (isset($payment['data']['status']) && $payment['data']['status'] != 'success') {
                    $response['error'] = true;
                    $response['message'] = "Payment is " . ucwords($payment['data']['status']) . "! ";
                    $response['amount'] = (isset($payment['data']['amount'])) ? $payment['data']['amount'] / 100 : 0;
                    $response['data'] = $payment;
                    return $response;
                } else {
                    $response['error'] = true;
                    $response['message'] = "Payment is unsuccessful! ";
                    $response['amount'] = (isset($payment['data']['amount'])) ? $payment['data']['amount'] / 100 : 0;
                    $response['data'] = $payment;
                    return $response;
                }
            } else {
                $response['error'] = true;
                $response['message'] = "Payment not found by the transaction ID!";
                $response['amount'] = 0;
                $response['data'] = [];
                return $response;
            }
            break;

        case 'instamojo':
            $CI->load->library("instamojo");
            $payment = $CI->instamojo->payment_requests_detail($txn_id);
            if (!empty($payment)) {
                $payment = json_decode($payment['body'], true);

                if (isset($payment['status']) && ($payment['status'] == 'Completed' || $payment['status'] == 'completed')) {
                    $response['error'] = false;
                    $response['message'] = "Payment is successful";
                    $response['amount'] = (isset($payment['amount'])) ? $payment['amount'] : 0;
                    $response['data'] = $payment;
                    return $response;
                } elseif (isset($payment['status']) && $payment['status'] != 'success') {
                    $response['error'] = true;
                    $response['message'] = "Payment is " . ucwords($payment['status']) . "! ";
                    $response['amount'] = (isset($payment['amount'])) ? $payment['amount'] : 0;
                    $response['data'] = $payment;
                    return $response;
                } else {
                    $response['error'] = true;
                    $response['message'] = "Payment is unsuccessful! ";
                    $response['amount'] = (isset($payment['amount'])) ? $payment['amount'] : 0;
                    $response['data'] = $payment;
                    return $response;
                }
            } else {
                $response['error'] = true;
                $response['message'] = "Payment not found by the transaction ID!";
                $response['amount'] = 0;
                $response['data'] = [];
                return $response;
            }
            break;
        case 'flutterwave':
            $CI->load->library("flutterwave");
            $transaction = $CI->flutterwave->verify_transaction($txn_id);
            if (!empty($transaction)) {
                $transaction = json_decode($transaction, true);
                if ($transaction['status'] == 'error') {
                    $response['error'] = true;
                    $response['message'] = $transaction['message'];
                    $response['amount'] = (isset($transaction['data']['amount'])) ? $transaction['data']['amount'] : 0;
                    $response['data'] = $transaction;
                    return $response;
                }

                if ($transaction['status'] == 'success' && $transaction['data']['status'] == 'successful') {
                    $response['error'] = false;
                    $response['message'] = "Payment has been completed successfully";
                    $response['amount'] = $transaction['data']['amount'];
                    $response['data'] = $transaction;
                    return $response;
                } else if ($transaction['status'] == 'success' && $transaction['data']['status'] != 'successful') {
                    $response['error'] = true;
                    $response['message'] = "Payment is " . $transaction['data']['status'];
                    $response['amount'] = $transaction['data']['amount'];
                    $response['data'] = $transaction;
                    return $response;
                }
            } else {
                $response['error'] = true;
                $response['message'] = "Payment not found by the transaction ID!";
                $response['amount'] = 0;
                $response['data'] = [];
                return $response;
            }
            break;

        case 'stripe':
            $response['error'] = false;
            $response['message'] = "stripe is supplied";
            return $response;
            break;

        case 'phonepe':
            $response['error'] = false;
            $response['message'] = "Payment captured successfully";
            return $response;
            break;

        case 'paytm':
            $CI->load->library('paytm');
            $payment = $CI->paytm->transaction_status($txn_id); /* We are using order_id created during the generation of txn token */
            if (!empty($payment)) {
                $payment = json_decode($payment, true);
                if (
                    isset($payment['body']['resultInfo']['resultCode'])
                    && ($payment['body']['resultInfo']['resultCode'] == '01' && $payment['body']['resultInfo']['resultStatus'] == 'TXN_SUCCESS')
                ) {
                    $response['error'] = false;
                    $response['message'] = "Payment is successful";
                    $response['amount'] = (isset($payment['body']['txnAmount'])) ? $payment['body']['txnAmount'] : 0;
                    $response['data'] = $payment;
                    return $response;
                } elseif (
                    isset($payment['body']['resultInfo']['resultCode'])
                    && ($payment['body']['resultInfo']['resultStatus'] == 'TXN_FAILURE')
                ) {
                    $response['error'] = true;
                    $response['message'] = $payment['body']['resultInfo']['resultMsg'];
                    $response['amount'] = (isset($payment['body']['txnAmount'])) ? $payment['body']['txnAmount'] : 0;
                    $response['data'] = $payment;
                    return $response;
                } else if (
                    isset($payment['body']['resultInfo']['resultCode'])
                    && ($payment['body']['resultInfo']['resultStatus'] == 'PENDING')
                ) {
                    $response['error'] = true;
                    $response['message'] = $payment['body']['resultInfo']['resultMsg'];
                    $response['amount'] = (isset($payment['body']['txnAmount'])) ? $payment['body']['txnAmount'] : 0;
                    $response['data'] = $payment;
                    return $response;
                } else {
                    $response['error'] = true;
                    $response['message'] = "Payment is unsuccessful!";
                    $response['amount'] = (isset($payment['body']['txnAmount'])) ? $payment['body']['txnAmount'] : 0;
                    $response['data'] = $payment;
                    return $response;
                }
            } else {
                $response['error'] = true;
                $response['message'] = "Payment not found by the Order ID!";
                $response['amount'] = 0;
                $response['data'] = [];
                return $response;
            }
            break;

        case 'paypal':
            $response['error'] = false;
            $response['message'] = "paypal is supplied";
            return $response;
            break;
        case 'midtrans':
            $response['error'] = false;
            $response['message'] = "midtrans is supplied";
            return $response;
            break;

        default:
            $response['error'] = true;
            $response['message'] = "Could not validate the transaction with the supplied payment method";
            return $response;
            break;
    }
}

function process_refund($id, $status, $type = 'order_items')
{
    $possible_status = array("cancelled", "returned");
    if (!in_array($status, $possible_status)) {
        $response['error'] = true;
        $response['message'] = 'Refund cannot be processed. Invalid status';
        $response['data'] = array();
        return $response;
    }

    if ($type == 'order_items') {

        /* fetch order_id */
        $order_item_details = fetch_details('order_items', ['id' => $id], 'order_id,id,seller_id,sub_total,quantity,status');

        /* fetch order and its complete details with order_items */
        $order_id = $order_item_details[0]['order_id'];
        $seller_id = $order_item_details[0]['seller_id'];

        $order_item_data = fetch_details('order_charges', ['order_id' => $order_id, 'seller_id' => $seller_id], 'sub_total');
        $order_total = 0.00;
        if (isset($order_item_data) && !empty($order_item_data)) {
            $order_total = floatval($order_item_data[0]['sub_total']);
        }

        $order_item_total = $order_item_details[0]['sub_total'];

        $order_details = fetch_orders($order_id);
        $order_details = $order_details['order_data'];

        $order_items_details = $order_details[0]['order_items'];

        $key = array_search($id, array_column($order_items_details, 'id'));
        $current_price = $order_items_details[$key]['sub_total'];
        $order_item_id = $order_items_details[$key]['id'];
        $currency = (isset($system_settings['currency']) && !empty($system_settings['currency'])) ? $system_settings['currency'] : '';
        $payment_method = $order_details[0]['payment_method'];


        //check for order active status
        $active_status = json_decode($order_item_details[0]['status'], true);

        if (trim(strtolower($payment_method)) != 'wallet') {
            if ($active_status[1][0] == 'cancelled' && $active_status[0][0] == 'awaiting') {
                $response['error'] = true;
                $response['message'] = 'Refund cannot be processed.';
                $response['data'] = array();
                return $response;
            }
        }

        $total = $order_details[0]['total'];
        $is_delivery_charge_returnable = isset($order_details[0]['is_delivery_charge_returnable']) && $order_details[0]['is_delivery_charge_returnable'] == 1 ? '1' : '0';
        $delivery_charge = (isset($order_details[0]['delivery_charge']) && !empty($order_details[0]['delivery_charge'])) ? $order_details[0]['delivery_charge'] : 0;

        $promo_code = $order_details[0]['promo_code'];
        $promo_discount = $order_details[0]['promo_discount'];
        $final_total = $order_details[0]['final_total'];
        $wallet_balance = $order_details[0]['wallet_balance'];
        $total_payable = $order_details[0]['total_payable'];
        $user_id = $order_details[0]['user_id'];

        $order_items_count = $order_details[0]['order_items'][0]['order_counter'];
        $cancelled_items_count = $order_details[0]['order_items'][0]['order_cancel_counter'];
        $returned_items_count = $order_details[0]['order_items'][0]['order_return_counter'];
        $last_item = 0;

        $user_res = fetch_details('users', ['id' => $user_id], 'fcm_id,mobile,email,username,platform_type');
        $fcm_ids = array();
        if (!empty($user_res[0]['fcm_id'])) {
            // Step 1: Group by platform
            $groupedByPlatform = [];
            foreach ($user_res as $item) {
                $platform = $item['platform_type'];
                $groupedByPlatform[$platform][] = $item['fcm_id'];
            }

            // Step 2: Chunk each platform group into arrays of 1000
            $fcm_ids = [];
            foreach ($groupedByPlatform as $platform => $fcmIds) {
                $fcm_ids[$platform] = array_chunk($fcmIds, 1000);
            }
            $fcm_ids[0][] = $fcm_ids;
        }

        if (($cancelled_items_count + $returned_items_count) == $order_items_count) {
            $last_item = 1;
        }
        $new_total = $total - $current_price;

        /* recalculate delivery charge */
        $new_delivery_charge = ($new_total > 0) ? recalulate_delivery_charge($order_details[0]['address_id'], $new_total, $delivery_charge) : 0;
        /* recalculate promo discount */
        $new_promo_discount = recalculate_promo_discount($promo_code, $promo_discount, $user_id, $new_total, $payment_method, $new_delivery_charge, $wallet_balance);

        $new_final_total = $new_total + $new_delivery_charge - $new_promo_discount;
        $bank_receipt = fetch_details('order_bank_transfer', ['order_id' => $order_item_details[0]['order_id']]);
        $bank_receipt_status = (isset($bank_receipt[0]['status'])) ? $bank_receipt[0]['status'] : "";

        /* find returnable_amount, new_wallet_balance
        condition : 1
        */
        if (trim(strtolower($payment_method)) == 'cod' || $payment_method == 'Bank Transfer') {
            /* when payment method is COD or Bank Transfer and payment is not yet done */
            if (trim(strtolower($payment_method)) == 'cod' || ($payment_method == 'Bank Transfer' && (empty($bank_receipt_status) || $bank_receipt_status == "0" || $bank_receipt_status == "1"))) {

                $returnable_amount = ($order_item_total <= $current_price) ? $order_item_total : (($order_item_total > 0) ? $current_price : 0);

                $returnable_amount = ($promo_discount != $new_promo_discount && $last_item == 0) ? $returnable_amount - $promo_discount + $new_promo_discount : $returnable_amount; /* if the new promo discount changed then adjust that here */
                $returnable_amount = ($returnable_amount < 0) ? 0 : $returnable_amount;

                /* if returnable_amount is 0 then don't change he wallet_balance */
                $new_wallet_balance = ($returnable_amount > 0) ? (($wallet_balance <= $current_price) ? 0 : (($wallet_balance - $current_price > 0) ? $wallet_balance - $current_price : 0)) : $wallet_balance;
            }
            /* if it is bank transfer and payment is already done by bank transfer
            same as condition : 2
            */
        }

        /* if it is any other payment method or bank transfer with accepted receipts then payment is already done
        condition : 2
        */
        if ((trim(strtolower($payment_method)) != 'cod' && $payment_method != 'Bank Transfer') || ($payment_method == 'Bank Transfer' && $bank_receipt_status == 2)) {
            $returnable_amount = $current_price;
            $returnable_amount = ($promo_discount != $new_promo_discount) ? $returnable_amount - $promo_discount + $new_promo_discount : $returnable_amount;
            $returnable_amount = ($last_item == 1 && $is_delivery_charge_returnable == 1) ? $returnable_amount + $delivery_charge : $returnable_amount;  /* if its the last item getting cancelled then check if we have to return delivery charge or not */
            $returnable_amount = ($returnable_amount < 0) ? 0 : $returnable_amount;
            $new_wallet_balance = ($last_item == 1) ? 0 : (($wallet_balance - $returnable_amount < 0) ? 0 : $wallet_balance - $returnable_amount);
        }

        /* find new_total_payable */
        if (trim(strtolower($payment_method)) != 'cod' && $payment_method != 'Bank Transfer') {
            /* online payment or any other payment method is used. and payment is already done */
            $new_total_payable = 0;
        } else {
            if ($bank_receipt_status == 2) {
                $new_total_payable = 0;
            } else {
                $new_total_payable = $new_final_total - $new_wallet_balance;
            }
        }

        if ($new_total == 0) {
            $new_total = $new_wallet_balance = $new_delivery_charge = $new_final_total = $new_total_payable = 0;
        }

        //custom message
        $custom_notification = fetch_details('custom_notifications', ['type' => "wallet_transaction"], '');
        $hashtag_currency = '< currency >';
        $hashtag_returnable_amount = '< returnable_amount >';
        $string = json_encode($custom_notification[0]['message'], JSON_UNESCAPED_UNICODE);
        $hashtag = html_entity_decode($string);
        $data = str_replace(array($hashtag_currency, $hashtag_returnable_amount), array($currency, $returnable_amount), $hashtag);
        $message = output_escaping(trim($data, '"'));

        if ($returnable_amount > 0) {

            $fcmMsg = array(
                'title' => (!empty($custom_notification)) ? $custom_notification[0]['title'] : "Amount Credited To Wallet",
                'body' => (!empty($custom_notification)) ? $message : $currency . ' ' . $returnable_amount,
                'type' => "wallet",
            );
            send_notification($fcmMsg, $fcm_ids, $fcmMsg);
            (notify_event(
                "wallet_transaction",
                ["customer" => [$user_res[0]['email']]],
                ["customer" => [$user_res[0]['mobile']]],
                ["users.id" => $user_id]
            ));
            if ($order_details[0]['payment_method'] == 'RazorPay' || $order_details[0]['payment_method'] == 'razorpay' || $order_details[0]['payment_method'] == 'Razorpay') {
                update_wallet_balance('refund', $user_id, $returnable_amount, 'Amount Refund for Order Item ID  : ' . $id, $order_item_id, '', 'razorpay');
            } else {
                update_wallet_balance('credit', $user_id, $returnable_amount, 'Refund Amount Credited for Order Item ID  : ' . $id, $order_item_id);
            }
        }

        // recalculate delivery charge and promocode for each seller

        $order_delivery_charge = fetch_details('order_charges', ['order_id' => $order_id, 'seller_id' => $seller_id], 'delivery_charge');
        $order_charges_data = fetch_details('order_charges', ['order_id' => $order_id, 'seller_id !=' => $seller_id], '*');

        if (isset($order_delivery_charge) && !empty($order_delivery_charge)) {
            $parcel_total = floatval($order_total) - floatval($order_item_total);
            if ($parcel_total != 0) {
                if ($new_total != 0) {
                    $seller_promocode_discount_percentage = ($parcel_total * 100) / $new_total;
                } else {
                    $seller_promocode_discount_percentage = ($parcel_total * 100);
                }
            }
            $seller_promocode_discount = ($new_promo_discount * $seller_promocode_discount_percentage) / 100;
            $seller_delivery_charge = ($new_delivery_charge * $seller_promocode_discount_percentage) / 100;

            $parcel_final_total = $parcel_total + $seller_delivery_charge - $seller_promocode_discount;
            $set = [
                'promo_discount' => round($seller_promocode_discount, 2),
                'delivery_charge' => round($seller_delivery_charge, 2),
                'sub_total' => round($parcel_total, 2),
                'total' => round($parcel_final_total, 2)
            ];
            update_details($set, ['order_id' => $order_id, 'seller_id' => $seller_id], 'order_charges');
        }
        if (isset($order_charges_data) && !empty($order_charges_data)) {
            foreach ($order_charges_data as $data) {

                $total = $data['sub_total'] + $data['promo_discount'] - $data['delivery_charge'];
                if ($new_total != 0) {
                    $promocode_discount_percentage = ($data['sub_total'] * 100) / $new_total;
                } else {
                    $promocode_discount_percentage = ($data['sub_total'] * 100);
                }
                $promocode_discount = ($new_promo_discount * $promocode_discount_percentage) / 100;
                $delivery_charge = ($new_delivery_charge * $promocode_discount_percentage) / 100;
                $final_total = $data['sub_total'] + $delivery_charge - $promocode_discount;
                $value = [
                    'promo_discount' => round($promocode_discount, 2),
                    'delivery_charge' => round($delivery_charge, 2),
                    'sub_total' => $data['sub_total'],
                    'total' => round($final_total, 2)
                ];
                update_details($value, ['order_id' => $order_id, 'seller_id' => $data['seller_id']], 'order_charges');
            }
        }
        // end

        $set = [
            'total' => $new_total,
            'final_total' => $new_final_total,
            'total_payable' => $new_total_payable,
            'promo_discount' => (!empty($new_promo_discount) && $new_promo_discount > 0) ? $new_promo_discount : 0,
            'delivery_charge' => $new_delivery_charge,
            'wallet_balance' => $new_wallet_balance
        ];
        update_details($set, ['id' => $order_id], 'orders');
        $response['error'] = false;
        $response['message'] = 'Status Updated Successfully';
        $response['data'] = array();
        return $response;
    } elseif ($type == 'orders') {

        /* if complete order is getting cancelled */
        $order_details = fetch_orders($id);
        $order_item_details = fetch_details('order_items', ['order_id' => $order_details['order_data'][0]['id']], 'sum(tax_amount) as total_tax,status');
        $order_details = $order_details['order_data'];
        $payment_method = $order_details[0]['payment_method'];

        $active_status = json_decode($order_item_details[0]['status'], true);
        if (trim(strtolower($payment_method)) != 'wallet') {
            if ($active_status[1][0] == 'cancelled' && $active_status[0][0] == 'awaiting') {
                $response['error'] = true;
                $response['message'] = 'Refund cannot be processed.';
                $response['data'] = array();
                return $response;
            }
        }

        $wallet_refund = true;
        $bank_receipt = fetch_details('order_bank_transfer', ['order_id' => $id]);

        $is_transfer_accepted = 0;

        if ($payment_method == 'Bank Transfer') {
            if (!empty($bank_receipt)) {
                foreach ($bank_receipt as $receipt) {
                    if ($receipt['status'] == 2) {
                        $is_transfer_accepted = 1;
                        break;
                    }
                }
            }
        }
        if ($order_details[0]['wallet_balance'] == 0 && $status == 'cancelled' && $payment_method == 'Bank Transfer' && (!$is_transfer_accepted || empty($bank_receipt))) {
            $wallet_refund = false;
        } else {
            $wallet_refund = true;
        }

        $promo_discount = $order_details[0]['promo_discount'];
        $final_total = $order_details[0]['final_total'];
        $is_delivery_charge_returnable = isset($order_details[0]['is_delivery_charge_returnable']) && $order_details[0]['is_delivery_charge_returnable'] == 1 ? '1' : '0';
        $payment_method = trim(strtolower($payment_method));
        $total_tax_amount = $order_item_details[0]['total_tax'];
        $wallet_balance = $order_details[0]['wallet_balance'];
        $currency = (isset($system_settings['currency']) && !empty($system_settings['currency'])) ? $system_settings['currency'] : '';
        $user_id = $order_details[0]['user_id'];
        $fcmMsg = array(
            'title' => "Amount Credited To Wallet",
        );
        $user_res = fetch_details('users', ['id' => $user_id], 'fcm_id,mobile,email,platform_type');
        $fcm_ids = array();
        if (!empty($user_res[0]['fcm_id'])) {
            // Step 1: Group by platform
            $groupedByPlatform = [];
            foreach ($user_res as $item) {
                $platform = $item['platform_type'];
                $groupedByPlatform[$platform][] = $item['fcm_id'];
            }

            // Step 2: Chunk each platform group into arrays of 1000
            $fcm_ids = [];
            foreach ($groupedByPlatform as $platform => $fcmIds) {
                $fcm_ids[$platform] = array_chunk($fcmIds, 1000);
            }
            $fcm_ids[0][] = $fcm_ids;
        }
        if ($wallet_refund == true) {
            if ($payment_method != 'cod') {
                /* update user's wallet */
                if ($is_delivery_charge_returnable == 1) {
                    $returnable_amount = $order_details[0]['total'] + $order_details[0]['delivery_charge'];
                } else {
                    $returnable_amount = $order_details[0]['total'];
                }

                if ($payment_method == 'bank transfer' && !$is_transfer_accepted) {
                    $returnable_amount = $returnable_amount - $order_details[0]['total_payable'];
                }
                //send custom notifications
                $custom_notification = fetch_details('custom_notifications', ['type' => "wallet_transaction"], '');
                $hashtag_currency = '< currency >';
                $hashtag_returnable_amount = '< returnable_amount >';
                $string = json_encode($custom_notification[0]['message'], JSON_UNESCAPED_UNICODE);
                $hashtag = html_entity_decode($string);
                $data = str_replace(array($hashtag_currency, $hashtag_returnable_amount), array($currency, $returnable_amount), $hashtag);
                $message = output_escaping(trim($data, '"'));
                $fcmMsg = array(
                    'title' => (!empty($custom_notification)) ? $custom_notification[0]['title'] : "Amount Credited To Wallet",
                    'body' => (!empty($custom_notification)) ? $message : $currency . ' ' . $returnable_amount,
                    'type' => "wallet",
                );
                send_notification($fcmMsg, $fcm_ids, $fcmMsg);
                (notify_event(
                    "wallet_transaction",
                    ["customer" => [$user_res[0]['email']]],
                    ["customer" => [$user_res[0]['mobile']]],
                    ["users.id" => $user_id]
                ));

                update_wallet_balance('credit', $user_id, $returnable_amount, 'Wallet Amount Credited for Order Item ID  : ' . $id);
            } else {
                if ($wallet_balance != 0) {
                    /* update user's wallet */
                    $returnable_amount = $wallet_balance;
                    //send custom notifications
                    $custom_notification = fetch_details('custom_notifications', ['type' => "wallet_transaction"], '');
                    $hashtag_currency = '< currency >';
                    $hashtag_returnable_amount = '< returnable_amount >';
                    $string = json_encode($custom_notification[0]['message'], JSON_UNESCAPED_UNICODE);
                    $hashtag = html_entity_decode($string);
                    $data = str_replace(array($hashtag_currency, $hashtag_returnable_amount), array($currency, $returnable_amount), $hashtag);
                    $message = output_escaping(trim($data, '"'));
                    $fcmMsg = array(
                        'title' => (!empty($custom_notification)) ? $custom_notification[0]['title'] : "Amount Credited To Wallet",
                        'body' => (!empty($custom_notification)) ? $message : $currency . ' ' . $returnable_amount,
                        'type' => "wallet",
                    );
                    send_notification($fcmMsg, $fcm_ids, $fcmMsg);
                    (notify_event(
                        "wallet_transaction",
                        ["customer" => [$user_res[0]['email']]],
                        ["customer" => [$user_res[0]['mobile']]],
                        ["users.id" => $user_id]
                    ));

                    update_wallet_balance('credit', $user_id, $returnable_amount, 'Wallet Amount Credited for Order Item ID  : ' . $id);
                }
            }
        }
    }
}


function recalulate_delivery_charge($address_id, $total, $old_delivery_charge)
{
    $t = &get_instance();
    $system_settings = get_settings('system_settings', true);
    $min_amount = $system_settings['min_amount'];
    $d_charge = $old_delivery_charge;

    if ((isset($system_settings['area_wise_delivery_charge']) && !empty($system_settings['area_wise_delivery_charge']))) {
        if (isset($address_id) && !empty($address_id)) {
            $address = fetch_details('addresses', ['id' => $address_id], 'area_id,pincode,city_id');
            if ((isset($address[0]['area_id']) && !empty($address[0]['area_id'])) || (isset($address[0]['pincode']) && !empty($address[0]['pincode']))) {
                $area = fetch_details('areas', ['id' => $address[0]['area_id']], 'minimum_free_delivery_order_amount');
                if ($t->db->field_exists('delivery_charges', 'zipcodes') && $t->db->field_exists('minimum_free_delivery_order_amount', 'zipcodes')) {
                    $zipcode = fetch_details('zipcodes', ['zipcode' => $address[0]['pincode'], 'city_id' => $address[0]['city_id']], 'delivery_charges,minimum_free_delivery_order_amount');
                }
                if (isset($area[0]['minimum_free_delivery_order_amount']) || isset($zipcode[0]['minimum_free_delivery_order_amount'])) {
                    $min_amount = isset($area[0]['minimum_free_delivery_order_amount']) && !empty($area[0]['minimum_free_delivery_order_amount']) ? $area[0]['minimum_free_delivery_order_amount'] : $zipcode[0]['minimum_free_delivery_order_amount'];
                }
            }
        }
    }
    if ($total < $min_amount) {
        if ($old_delivery_charge == 0) {
            if (isset($address_id) && !empty($address_id)) {
                $d_charge = get_delivery_charge($address_id);
            } else {
                $d_charge = $system_settings['delivery_charge'];
            }
        }
    }

    return $d_charge;
}

function recalculate_promo_discount($promo_code, $promo_discount, $user_id, $total, $payment_method, $delivery_charge, $wallet_balance)
{
    /* recalculate promocode discount if the status of the order_items is cancelled or returned */
    $promo_code_discount = $promo_discount;
    if (isset($promo_code) && !empty($promo_code)) {
        $promo_code = validate_promo_code($promo_code, $user_id, $total, true);
        if ($promo_code['error'] == false) {

            if ($promo_code['data'][0]['discount_type'] == 'percentage') {
                $promo_code_discount = floatval($total * $promo_code['data'][0]['discount'] / 100);
            } else {
                $promo_code_discount = $promo_code['data'][0]['discount'];
            }
            if (trim(strtolower($payment_method)) != 'cod' && $payment_method != 'Bank Transfer') {
                /* If any other payment methods are used like razorpay, paytm, flutterwave or stripe then
                    obviously customer would have paid complete amount so making total_payable = 0*/
                $total_payable = 0;
                if ($promo_code_discount > $promo_code['data'][0]['max_discount_amount']) {
                    $promo_code_discount = $promo_code['data'][0]['max_discount_amount'];
                }
            } else {
                /* also check if the previous discount and recalculated discount are
                    different or not, then only modify total_payable*/
                if ($promo_code_discount <= $promo_code['data'][0]['max_discount_amount'] && $promo_discount != $promo_code_discount) {
                    $total_payable = floatval($total) + $delivery_charge - $promo_code_discount - $wallet_balance;
                } else if ($promo_discount != $promo_code_discount) {
                    $total_payable = floatval($total) + $delivery_charge - $promo_code['data'][0]['max_discount_amount'] - $wallet_balance;
                    $promo_code_discount = $promo_code['data'][0]['max_discount_amount'];
                }
            }
        } else {
            $promo_code_discount = 0;
        }
    }
    return $promo_code_discount;
}

function get_sliders($id = '', $type = '', $type_id = '')
{
    $ci = &get_instance();
    if (!empty($id)) {
        $ci->db->where('id', $id);
    }
    if (!empty($type)) {
        $ci->db->where('type', $type);
    }
    if (!empty($type_id)) {
        $ci->db->where('type_id', $type_id);
    }
    $res = $ci->db->get('sliders')->result_array();
    $res = array_map(function ($d) {
        $ci = &get_instance();
        if ($d['type'] != "slider_url") {
            $d['link'] = '';
        }
        if (!empty($d['type'])) {
            if ($d['type'] == "categories") {
                $type_details = $ci->db->where('id', $d['type_id'])->select('slug')->get('categories')->row_array();
                if (!empty($type_details)) {
                    $d['link'] = base_url('products/category/' . $type_details['slug']);
                }
            } elseif ($d['type'] == "products") {
                $type_details = $ci->db->where('id', $d['type_id'])->select('slug')->get('products')->row_array();
                if (!empty($type_details)) {
                    $d['link'] = base_url('products/details/' . $type_details['slug']);
                }
            }
        }
        return $d;
    }, $res);
    return $res;
}

function get_offers($id = '', $type = '', $type_id = '')
{
    $ci = &get_instance();
    if (!empty($id)) {
        $ci->db->where('id', $id);
    }
    if (!empty($type)) {
        $ci->db->where('type', $type);
    }
    if (!empty($type_id)) {
        $ci->db->where('type_id', $type_id);
    }
    $res = $ci->db->get('offers')->result_array();
    $res = array_map(function ($d) {
        $ci = &get_instance();
        $d['link'] = '';
        if (!empty($d['type'])) {
            if ($d['type'] == "categories") {
                $type_details = $ci->db->where('id', $d['type_id'])->select('slug')->get('categories')->row_array();
                if (!empty($type_details)) {
                    $d['link'] = base_url('products/category/' . $type_details['slug']);
                }
            } elseif ($d['type'] == "products") {
                $type_details = $ci->db->where('id', $d['type_id'])->select('slug')->get('products')->row_array();
                if (!empty($type_details)) {
                    $d['link'] = base_url('products/details/' . $type_details['slug']);
                }
            }
        }
        return $d;
    }, $res);
    return $res;
}
function get_cart_count($user_id)
{
    $ci = &get_instance();
    if (!empty($user_id)) {
        $ci->db->where('user_id', $user_id);
    }
    $ci->db->where('qty !=', 0);
    $ci->db->where('is_saved_for_later =', 0);
    $ci->db->distinct();
    $ci->db->select('count(id) as total');
    $res = $ci->db->get('cart')->result_array();

    return $res;
}
function is_variant_available_in_cart($product_variant_id, $user_id)
{
    $ci = &get_instance();
    $ci->db->where('product_variant_id', $product_variant_id);
    $ci->db->where('user_id', $user_id);
    $ci->db->where('qty !=', 0);
    $ci->db->where('is_saved_for_later =', 0);
    $ci->db->select('id');
    $res = $ci->db->get('cart')->result_array();
    if (!empty($res[0]['id'])) {
        return true;
    } else {
        return false;
    }
}
function get_user_balance($user_id)
{
    $ci = &get_instance();
    $ci->db->where('id', $user_id);
    $ci->db->select('balance');
    $res = $ci->db->get('users')->result_array();
    if (!empty($res[0]['balance'])) {
        return $res[0]['balance'];
    } else {
        return "0";
    }
}

function get_stock($id, $type)
{
    $t = &get_instance();
    $t->db->where('id', $id);
    if ($type == 'variant') {
        $response = $t->db->select('stock')->get('product_variants')->result_array();
    } else {
        $response = $t->db->select('stock')->get('products')->result_array();
    }
    $stock = isset($response[0]['stock']) ? $response[0]['stock'] : null;
    return $stock;
}
function get_delivery_charge($address_id, $total = 0, $user_id = '')
{
    $t = &get_instance();
    $total = str_replace(',', '', $total);
    $system_settings = get_settings('system_settings', true);
    $shipping_settings = get_settings('shipping_method', true);
    $address = fetch_details('addresses', ['id' => $address_id], 'area_id,pincode,city_id');
    $min_amount = $system_settings['min_amount'];
    $delivery_charge = $system_settings['delivery_charge'];
    $default_delivery_charge = $shipping_settings['default_delivery_charge'];


    if ((isset($system_settings['area_wise_delivery_charge']) && !empty($system_settings['area_wise_delivery_charge']))) {

        $cart_user_data = $t->cart_model->get_user_cart($user_id);
        $seller_ids = [];

        if (!empty($cart_user_data)) {
            foreach ($cart_user_data as $product) {
                $seller_ids[] = $product['seller_id']; // Collect seller IDs
            }
            $seller_ids = array_unique($seller_ids);

            $t->db->select('user_id,serviceable_zipcodes,serviceable_cities,deliverable_zipcode_type,deliverable_city_type');
            $t->db->where_in("user_id", $seller_ids);

            $fetched_records = $t->db->get('seller_data');
            $sellers_data = $fetched_records->result_array();
        }

        if (isset($system_settings['update_seller_flow']) && $system_settings['update_seller_flow'] == '1') {

            if (isset($shipping_settings['pincode_wise_deliverability']) && $shipping_settings['pincode_wise_deliverability'] == 1) {
                $pincode_serviceable_count = 0;
                $pincode = (isset($address[0]['pincode']) && ($address[0]['pincode']) != 0) ? $address[0]['pincode'] : "";
                $minimum_free_delivery_order_amount = fetch_details('zipcodes', ['zipcode' => $pincode], '*');
                $total_delivery_charges = 0;
                $address_zipcode_id = $minimum_free_delivery_order_amount[0]['id'];
                $amount = $minimum_free_delivery_order_amount[0]['minimum_free_delivery_order_amount'];
                $delivery_charges = $minimum_free_delivery_order_amount[0]['delivery_charges'];

                foreach ($sellers_data as $seller_data) {
                    $serviceable_zipcodes = explode(',', $seller_data['serviceable_zipcodes']);
                    if (in_array($address_zipcode_id, $serviceable_zipcodes)) {
                        $pincode_serviceable_count++;
                        $total_delivery_charges += $pincode_serviceable_count * $delivery_charges;
                    } else {
                        $total_delivery_charges += $delivery_charges + $default_delivery_charge;
                    }
                }
                $delivery_charge_total = (string) $total_delivery_charges;
            } else if (isset($shipping_settings['city_wise_deliverability']) && $shipping_settings['city_wise_deliverability'] == 1) {
                $city_serviceable_count = 0;

                $city = (isset($address[0]['city']) && ($address[0]['city']) != '') ? $address[0]['city'] : "";
                $minimum_free_delivery_order_amount = fetch_details('cities', ['name' => $city], '*');

                $amount = $minimum_free_delivery_order_amount[0]['minimum_free_delivery_order_amount'];
                $delivery_charges = $minimum_free_delivery_order_amount[0]['delivery_charges'];
                $address_city_id = $minimum_free_delivery_order_amount[0]['id'];

                foreach ($sellers_data as $seller_data) {
                    $serviceable_cities = explode(',', $seller_data['serviceable_cities']);
                    if (in_array($address_city_id, $serviceable_cities)) {
                        $city_serviceable_count++;
                        $total_delivery_charges = $city_serviceable_count * $delivery_charges;
                    } else {
                        $total_delivery_charges = $delivery_charges + $default_delivery_charge;
                    }
                }
                $delivery_charge_total = (string) $total_delivery_charges;
            }
        } else {

            if (isset($shipping_settings['pincode_wise_deliverability']) && $shipping_settings['pincode_wise_deliverability'] == 1) {
                if ((isset($address[0]['area_id']) && !empty($address[0]['area_id'])) || (isset($address[0]['pincode']) && !empty($address[0]['pincode']))) {
                    $area = fetch_details('areas', ['id' => $address[0]['area_id']], 'delivery_charges,minimum_free_delivery_order_amount');

                    if ($t->db->field_exists('delivery_charges', 'zipcodes') && $t->db->field_exists('minimum_free_delivery_order_amount', 'zipcodes')) {
                        $zipcode = fetch_details('zipcodes', ['zipcode' => $address[0]['pincode'], 'city_id' => $address[0]['city_id']], 'delivery_charges,minimum_free_delivery_order_amount');
                    }
                    if (isset($area[0]['minimum_free_delivery_order_amount']) || isset($zipcode[0]['minimum_free_delivery_order_amount'])) {
                        $min_amount = isset($area[0]['minimum_free_delivery_order_amount']) && !empty($area[0]['minimum_free_delivery_order_amount']) ? $area[0]['minimum_free_delivery_order_amount'] : $zipcode[0]['minimum_free_delivery_order_amount'];
                        $delivery_charge_total = isset($area[0]['delivery_charges']) && !empty($area[0]['delivery_charges']) ? $area[0]['delivery_charges'] : $zipcode[0]['delivery_charges'];
                    }
                }
            } elseif (isset($shipping_settings['city_wise_deliverability']) && $shipping_settings['city_wise_deliverability'] == 1) {

                $zipcode = fetch_details('cities', ['id' => $address[0]['city_id']], 'delivery_charges,minimum_free_delivery_order_amount');


                if (isset($address[0]['city_id'])) {
                    $min_amount = isset($area[0]['minimum_free_delivery_order_amount']) && !empty($area[0]['minimum_free_delivery_order_amount']) ? $area[0]['minimum_free_delivery_order_amount'] : $zipcode[0]['minimum_free_delivery_order_amount'];
                    $delivery_charge_total = $zipcode[0]['delivery_charges'];
                }
            }
        }
        $d_charge = $delivery_charge_total;
        if (((float)$total < (float)$min_amount) || $total == 0) {
            $d_charge = $d_charge;
        } else {
            $d_charge = 0;
        }
    } else {
        $delivery_charge_total = $delivery_charge;
        if (((float)$total < (float)$min_amount) || $total == 0) {
            $d_charge = $delivery_charge_total;
        } else {
            $d_charge = 0;
        }
    }

    return number_format($d_charge, 2);
}


function validate_otp($otp, $order_item_id = NULL, $order_id = NULL, $seller_id = NULL, $consignment_id = null)
{
    $res = fetch_details('order_items', ['id' => $order_item_id], 'otp');
    $consignment_res = fetch_details('consignments', ['id' => $consignment_id], 'otp');
    $order_res = fetch_details('order_charges', ['order_id' => $order_id, 'seller_id' => $seller_id], 'otp');
    if (($res[0]['otp'] != 0 && $res[0]['otp'] == $otp) || ($order_res[0]['otp'] != 0 && $order_res[0]['otp'] == $otp) || ($consignment_res[0]['otp'] != 0 && $consignment_res[0]['otp'] == $otp)) {
        return true;
    } else {
        return false;
    }
}

// function is_product_delivarable($type, $type_id, $product_id)
// {
//     $ci = &get_instance();

//     $city_exists = '';

//     if ($type == 'zipcode') {
//         $zipcode_id = $type_id;
//     } else if ($type == 'area') {
//         $res = fetch_details('areas', ['id' => $type_id], 'zipcode_id');
//         $zipcode_id = $res[0]['zipcode_id'];
//     } else if ($type == 'city') {
//         $city_id = $type_id;
//     } else {
//         return false;
//     }

//     $product_data = fetch_product(id: $product_id);
//     $seller_id = $product_data['product'][0]['seller_id'];
//     $seller_data = fetch_details('seller_data', ['user_id' => $seller_id], 'serviceable_zipcodes,serviceable_cities,deliverable_city_type,deliverable_zipcode_type');

//     $seller_zipcode_type = $seller_data[0]['deliverable_zipcode_type'];
//     $seller_cities = explode(',', $seller_data[0]['serviceable_cities']);
//     $seller_city_type = $seller_data[0]['deliverable_city_type'];

//     if (!empty($zipcode_id) && $zipcode_id != 0) {

//         if ($seller_zipcode_type == 3) {
//             $seller_zipcodes = explode(',', $seller_data[0]['serviceable_zipcodes']);
//             $zipcode_exists = in_array($zipcode_id, $seller_zipcodes);
//         }

//         if ($seller_zipcode_type == 1 || !$zipcode_exists) {
//             return true;
//         } else {
//             return false;
//         }

//         $ci->db->select('id');
//         $ci->db->group_Start();
//         $where = "((deliverable_type='2' and FIND_IN_SET('$zipcode_id', deliverable_zipcodes)) or deliverable_type = '1') OR (deliverable_type='3' and NOT FIND_IN_SET('$zipcode_id', deliverable_zipcodes)) ";
//         $ci->db->where($where);
//         $ci->db->group_End();
//         $ci->db->where("id = $product_id");
//         $product = $ci->db->get('products')->num_rows();

//         if ($product > 0) {
//             return true;
//         } else {
//             return false;
//         }
//     } else if (!empty($city_id) && $city_id != 0) {

//         if ($seller_city_type == 3) {
//             $seller_zipcodes = explode(',', $seller_data[0]['serviceable_cities']);
//             $city_exists = in_array($city_id, $seller_cities);
//         }
//         if ($seller_city_type == 1 || !$city_exists) {
//             return true;
//         } else {
//             return false;
//         }

//         $ci->db->select('id');
//         $ci->db->group_Start();
//         $where = "(((deliverable_city_type = '2' AND FIND_IN_SET('" . $city_id . "',deliverable_cities))OR deliverable_city_type = '1') OR (deliverable_city_type = '3' AND NOT FIND_IN_SET('" . $city_id . "',deliverable_cities))) ";
//         $ci->db->where($where);
//         $ci->db->group_End();
//         $ci->db->where("id = $product_id");
//         $product = $ci->db->get('products')->num_rows();

//         if ($product > 0) {
//             return true;
//         } else {
//             return false;
//         }
//     } else {
//         return false;
//     }
// }

function is_product_delivarable($type, $type_id, $product_id)
{
    $ci = &get_instance();

    $zipcode_id = 0;
    $city_id = 0;

    if ($type == 'zipcode') {
        $zipcode_id = $type_id;
    } elseif ($type == 'area') {
        $res = fetch_details('areas', ['id' => $type_id], 'zipcode_id');
        $zipcode_id = $res[0]['zipcode_id'] ?? 0;
    } elseif ($type == 'city') {
        $city_id = $type_id;
    } else {
        return false;
    }

    $product_data = fetch_product(id: $product_id);
    $product = $product_data['product'][0];
    $seller_id = $product['seller_id'];

    $seller_data = fetch_details('seller_data', ['user_id' => $seller_id], 'serviceable_zipcodes,serviceable_cities,deliverable_city_type,deliverable_zipcode_type');
    $seller = $seller_data[0];

    $seller_zipcode_type = (int)$seller['deliverable_zipcode_type'];
    $seller_city_type = (int)$seller['deliverable_city_type'];
    $seller_zipcodes = explode(',', $seller['serviceable_zipcodes']);
    $seller_cities = explode(',', $seller['serviceable_cities']);

    // ----- ZIPCODE BASED CHECK -----
    if ($zipcode_id) {
        $zipcode_exists = in_array($zipcode_id, $seller_zipcodes);

        // Seller-level check
        // if (
        //     $seller_zipcode_type === 0 || // None
        //     ($seller_zipcode_type === 2 && !$zipcode_exists) || // Included but not present
        //     ($seller_zipcode_type === 3 && $zipcode_exists) // Excluded but present
        // ) {
        //     return false;
        // }

        // Product-level check
        $ci->db->select('id');
        $ci->db->group_Start();
        $where = "(
            (deliverable_type = '2' AND FIND_IN_SET('$zipcode_id', deliverable_zipcodes)) OR
            deliverable_type = '1' OR
            (deliverable_type = '3' AND NOT FIND_IN_SET('$zipcode_id', deliverable_zipcodes))
        )";
        $ci->db->where($where);
        $ci->db->group_End();
        $ci->db->where("id", $product_id);
        return $ci->db->get('products')->num_rows() > 0;
    }

    // ----- CITY BASED CHECK -----
    if ($city_id) {
        $city_exists = in_array($city_id, $seller_cities);

        // Seller-level check
        if (
            $seller_city_type === 0 || // None
            ($seller_city_type === 2 && !$city_exists) || // Included but not present
            ($seller_city_type === 3 && $city_exists) // Excluded but present
        ) {
            return false;
        }

        // Product-level check
        $ci->db->select('id');
        $ci->db->group_Start();
        $where = "(
            (deliverable_city_type = '2' AND FIND_IN_SET('$city_id', deliverable_cities)) OR
            deliverable_city_type = '1' OR
            (deliverable_city_type = '3' AND NOT FIND_IN_SET('$city_id', deliverable_cities))
        )";
        $ci->db->where($where);
        $ci->db->group_End();
        $ci->db->where("id", $product_id);
        return $ci->db->get('products')->num_rows() > 0;
    }

    return false;
}
function check_cart_products_delivarable($user_id, $area_id = 0, $zipcode = "", $zipcode_id = "", $city = "", $city_id = "")
{
    $t = &get_instance();
    $products = $tmpRow = array();
    $cart = get_cart_total($user_id);
    $settings = get_settings('shipping_method', true);

    if (!empty($cart)) {
        $product_weight = 0;
        for ($i = 0; $i < $cart[0]['cart_count']; $i++) {
            $tmpRow = [];
            /* check in local shipping first */
            if ($city_id > 0) {
                $tmpRow['is_deliverable'] = (!empty($city_id) && $city_id > 0) ?
                    is_product_delivarable('city', $city_id, $cart[$i]['product_id'])
                    : false;
            } else {
                if (isset($settings['local_shipping_method']) && $settings['local_shipping_method'] == 1) {

                    $tmpRow['is_deliverable'] = (!empty($zipcode_id) && $zipcode_id > 0) ?
                        is_product_delivarable('zipcode', $zipcode_id, $cart[$i]['product_id'])
                        : false;
                }
            }
            $tmpRow['delivery_by'] = (isset($tmpRow['is_deliverable']) && $tmpRow['is_deliverable']) ? "local" : "";
            $tmpRow['product_seller_id'] = (isset($cart[$i]['product_seller_id']) && !empty($cart[$i]['product_seller_id'])) ? $cart[$i]['product_seller_id'] : "";

            /* check in standard shipping then */
            if (isset($settings['shiprocket_shipping_method']) && $settings['shiprocket_shipping_method'] == 1) {
                if (!$tmpRow['is_deliverable'] && $cart[$i]['pickup_location'] != "") {

                    $t->load->library(['Shiprocket']);
                    $pickup_pincode = fetch_details('pickup_locations', ['pickup_location' => $cart[$i]['pickup_location']], 'pin_code');
                    $product_weight += $cart[$i]['weight'] * $cart[$i]['qty'];

                    if (isset($zipcode) && !empty($zipcode)) {

                        if ($product_weight > 15) {
                            $tmpRow['is_deliverable'] = false;
                            $tmpRow['is_valid_wight'] = 0;
                            $tmpRow['message'] = "You cannot ship weight more then 15 KG";
                        } else {
                            $availibility_data = [
                                'pickup_postcode' => (isset($pickup_pincode[0]['pin_code']) && !empty($pickup_pincode[0]['pin_code'])) ? $pickup_pincode[0]['pin_code'] : "",
                                'delivery_postcode' => $zipcode,
                                'cod' => 0,
                                'weight' => $product_weight,
                            ];

                            $check_deliveribility = $t->shiprocket->check_serviceability($availibility_data);

                            if (isset($check_deliveribility['status_code']) && $check_deliveribility['status_code'] == 422) {
                                $tmpRow['is_deliverable'] = false;
                                $tmpRow['message'] = "Invalid zipcode supplied!";
                            } else {
                                if (isset($check_deliveribility['status']) && $check_deliveribility['status'] == 200 && !empty($check_deliveribility['data']['available_courier_companies'])) {
                                    $tmpRow['is_deliverable'] = true;
                                    $tmpRow['delivery_by'] = "standard_shipping";
                                    $estimate_date = $check_deliveribility['data']['available_courier_companies'][0]['etd'];
                                    $tmpRow['estimate_date'] = $estimate_date;
                                    $_SESSION['valid_zipcode'] = $zipcode;
                                    $tmpRow['message'] = 'Product is deliverable by ' . $estimate_date;
                                } else {
                                    $tmpRow['is_deliverable'] = false;
                                    $tmpRow['message'] = $check_deliveribility['message'];
                                }
                            }
                        }
                    } else {
                        $tmpRow['is_deliverable'] = false;
                        $tmpRow['message'] = 'Please select zipcode to check the deliveribility of item.';
                    }
                }
            }
            $tmpRow['product_id'] = $cart[$i]['product_id'];
            $tmpRow['variant_id'] = $cart[$i]['id'];
            $tmpRow['name'] = $cart[$i]['name'];
            $products[] = $tmpRow;
        }

        if (!empty($products)) {
            return $products;
        } else {
            return false;
        }
    } else {
        return false;
    }
}

function orders_count($status = "", $seller_id = "", $order_type = "")
{

    $t = &get_instance();
    $where = [];
    // $count_res = $t->db->select(' COUNT(distinct oi.order_id) as total')
    //     ->join(' orders o', 'o.id= oi.order_id', 'left')
    //     ->join('product_variants pv', 'pv.id=oi.product_variant_id', 'left')
    //     ->join('products p', 'pv.product_id=p.id', 'left');
    $count_res = $t->db->select(' COUNT(distinct o.id) as total')
        ->join(' order_items oi', 'oi.order_id= o.id', 'left')
        ->join('product_variants pv', 'pv.id=oi.product_variant_id', 'left')
        ->join('products p', 'pv.product_id=p.id', 'left');

    if (isset($order_type) && $order_type != '' && $order_type == 'digital') {
        $where['p.type'] = 'digital_product';
        $where['oi.active_status'] = $status;
    }
    if (isset($order_type) && $order_type != '' && $order_type == 'simple') {
        $where['p.type!='] = 'digital_product';
        $where['oi.active_status'] = $status;
    }
    if ($order_type == '' && !empty($status)) {
        $where['oi.active_status'] = $status;
    }

    if (!empty($seller_id)) {
        $where['oi.seller_id'] = $seller_id;
        $where['oi.active_status'] != 'awaiting';
    }

    $count_res->where($where);
    $result = $count_res->get('orders o')->result_array();

    return $result[0]['total'];
}

function delivery_boy_orders_count($status = "", $delivery_boy_id = "", $table = "")
{
    $t = &get_instance();
    if ($table == "consignments") {
        $t->db->select('count(DISTINCT id) total');
        if (!empty($status)) {
            $t->db->where('active_status', $status);
        }
        if (!empty($delivery_boy_id)) {
            $t->db->where('delivery_boy_id', $delivery_boy_id);
        }
        $result = $t->db->from("consignments")->get()->result_array();
        return $result[0]['total'];
    } else {
        $t->db->select('count(DISTINCT order_id) total');
        if (!empty($status)) {
            $t->db->where('active_status', $status);
        }
        if (!empty($delivery_boy_id)) {
            $t->db->where('delivery_boy_id', $delivery_boy_id);
        }
        $result = $t->db->from("order_items")->get()->result_array();
        return $result[0]['total'];
    }
}




function curl($url, $method = 'GET', $data = [], $authorization = "")
{
    $ch = curl_init();
    $curl_options = array(
        CURLOPT_URL => $url,
        CURLOPT_RETURNTRANSFER => 1,
        CURLOPT_HEADER => 0,
        CURLOPT_HTTPHEADER => array(
            'Content-Type: application/x-www-form-urlencoded',
        )
    );

    if (!empty($authorization)) {
        $curl_options['CURLOPT_HTTPHEADER'][] = $authorization;
    }

    if (strtolower($method) == 'post') {
        $curl_options[CURLOPT_POST] = 1;
        $curl_options[CURLOPT_POSTFIELDS] = http_build_query($data);
    } else {
        $curl_options[CURLOPT_CUSTOMREQUEST] = 'GET';
    }
    curl_setopt_array($ch, $curl_options);

    $result = array(
        'body' => json_decode(curl_exec($ch), true),
        'http_code' => curl_getinfo($ch, CURLINFO_HTTP_CODE),
    );
    return $result;
}

function get_seller_permission($seller_id, $permit = NULL)
{
    $t = &get_instance();
    $seller_id = (isset($seller_id) && !empty($seller_id)) ? $seller_id : $t->session->userdata('user_id');
    $permits = fetch_details('seller_data', ['user_id' => $seller_id], 'permissions');
    if (!empty($permit)) {
        $s_permits = json_decode($permits[0]['permissions'], true);
        return $s_permits[$permit];
    } else {
        return json_decode($permits[0]['permissions']);
    }
}

function get_price()
{
    $t = &get_instance();
    $t->db->select('IF( pv.special_price > 0, pv.special_price, pv.price ) as pr_price')
        ->join(" categories c", "p.category_id=c.id ", 'LEFT')
        ->join(" seller_data sd", "p.seller_id=sd.user_id ")
        ->join('product_variants pv', 'p.id = pv.product_id', 'LEFT')
        ->join('product_attributes pa', ' pa.product_id = p.id ', 'LEFT');
    $t->db->where(" p.status = '1' AND pv.status = 1 AND sd.status = 1 AND (c.status = '1' OR c.status = '0')");
    $result = $t->db->from("products p ")->get()->result_array();
    if (isset($result) && !empty($result)) {
        $pr_price = array_column($result, 'pr_price');
        return [
            'min' => min($pr_price),
            'max' => max($pr_price)
        ];
    } else {
        return [
            'min' => 0,
            'max' => 0
        ];
    }
}


function check_for_parent_id($category_id)
{
    $t = &get_instance();
    $t->db->select('id,parent_id,name,slug');
    $t->db->where('id', $category_id);
    $result = $t->db->from("categories")->get()->result_array();
    if (!empty($result)) {
        return $result;
    } else {
        return false;
    }
}

function update_balance($amount, $delivery_boy_id, $action)
{
    $t = &get_instance();

    if ($action == "add") {
        $t->db->set('balance', 'balance+' . $amount, FALSE);
    } elseif ($action == "deduct") {
        $t->db->set('balance', 'balance-' . $amount, FALSE);
    }
    return $t->db->where('id', $delivery_boy_id)->update('users');
}

function update_cash_received($amount, $delivery_boy_id, $action)
{
    $t = &get_instance();
    if ($action == "add") {
        $t->db->set('cash_received', 'cash_received+' . $amount, FALSE);
    } elseif ($action == "deduct") {
        $t->db->set('cash_received', 'cash_received-' . $amount, FALSE);
    }
    return $t->db->where('id', $delivery_boy_id)->update('users');
}

function word_limit($string, $length = WORD_LIMIT, $dots = "...")
{
    $split = explode(" ", $string);
    $newLength = 0;
    $words = [];
    foreach ($split as $word) {
        $newLength += strlen($word);
        array_push($words, $word);
        if ($newLength >= $length) {
            break;
        }
    }
    $newstr = implode(" ", $words);
    if (strlen($newstr) < $string) {
        $newstr .= $dots;
    }
    return $newstr;
}
function short_description_word_limit($string, $length = SHORT_DESCRIPTION_WORD_LIMIT, $dots = "...")
{
    $split = explode(" ", $string);
    $newLength = 0;
    $words = [];
    foreach ($split as $word) {
        $newLength += strlen($word);
        array_push($words, $word);
        if ($newLength >= $length) {
            break;
        }
    }
    $newstr = implode(" ", $words);
    if (strlen($newstr) < $string) {
        $newstr .= $dots;
    }
    return $newstr;
}
function description_word_limit($string, $length = DESCRIPTION_WORD_LIMIT, $dots = "...")
{
    $split = explode(" ", $string);
    $newLength = 0;
    $words = [];
    foreach ($split as $word) {
        $newLength += strlen($word);
        array_push($words, $word);
        if ($newLength >= $length) {
            break;
        }
    }
    $newstr = implode(" ", $words);
    if (strlen($newstr) < $string) {
        $newstr .= $dots;
    }
    return $newstr;
}
function calculate_tax_inclusive($original_cost, $tax)
{
    $tax_amount = ($original_cost * (100 / (100 + $tax)));
    $Net_price = $original_cost - $tax_amount;
    return $Net_price;
}
function labels($label, $alt = '')
{
    $label = trim($label);
    if (lang('Text.' . $label) != 'Text.' . $label) {
        if (lang('Text.' . $label) == '') {
            return $alt;
        }
        return trim(lang('Text.' . $label));
    } else {
        return trim($alt);
    }
}


function is_single_seller($product_variant_id, $user_id)
{
    $t = &get_instance();
    if (isset($product_variant_id) && !empty($product_variant_id) && $product_variant_id != "" && isset($user_id) && !empty($user_id) && $user_id != "") {
        $pv_id = (strpos((string) $product_variant_id, ",")) ? explode(",", $product_variant_id) : $product_variant_id;

        // get exist data from cart if any
        $exist_data = $t->db->select('c.product_variant_id,p.seller_id')
            ->join('product_variants pv ', 'pv.id=c.product_variant_id')
            ->join('products p ', 'pv.product_id=p.id')
            ->where(['user_id' => $user_id, 'is_saved_for_later' => 0])->group_by('p.seller_id')->get('cart c')->result_array();
        if (!empty($exist_data)) {
            $seller_id = array_values(array_unique(array_column($exist_data, "seller_id")));
        } else {
            // clear to add cart
            return true;
        }
        // get seller ids of varients
        $new_data = $t->db->select('p.seller_id')
            ->join('products p ', 'pv.product_id=p.id')
            ->where_in('pv.id', $pv_id)->get('product_variants pv')->result_array();
        $new_seller_id = $new_data[0]["seller_id"];
        if (!empty($seller_id) && !empty($new_seller_id)) {
            if (in_array($new_seller_id, $seller_id)) {
                // clear to add to cart
                return true;
            } else {
                // another seller id verient, give single seller error
                return false;
            }
        } else {
            return false;
        }
    } else {
        return false;
    }
}

function is_single_product_type($product_variant_id, $user_id)
{
    $t = &get_instance();
    if (isset($product_variant_id) && !empty($product_variant_id) && $product_variant_id != "" && isset($user_id) && !empty($user_id) && $user_id != "") {
        $pv_id = (strpos($product_variant_id, ",")) ? explode(",", $product_variant_id) : $product_variant_id;

        // get exist data from cart if any
        $exist_data = $t->db->select('c.product_variant_id,p.type')
            ->join('product_variants pv ', 'pv.id=c.product_variant_id')
            ->join('products p ', 'pv.product_id=p.id')
            ->where(['user_id' => $user_id, 'is_saved_for_later' => 0, 'p.status' => '1', 'pv.status' => '1'])->group_by('p.type')->get('cart c')->result_array();
        if (!empty($exist_data)) {
            $product_type = array_values(array_unique(array_column($exist_data, "type")));
        } else {
            // clear to add cart
            return true;
        }
        // get product types of varients
        $new_data = $t->db->select('p.type')
            ->join('products p ', 'pv.product_id=p.id')
            ->where_in('pv.id', $pv_id)->get('product_variants pv')->result_array();
        $new_product_type = $new_data[0]["type"];
        if (!empty($product_type) && !empty($new_product_type)) {
            if (in_array($new_product_type, $product_type)) {
                // clear to add to cart
                return true;
            } else {
                if (!in_array("digital_product", $product_type) && ($new_product_type == "variable_product" || $new_product_type == "simple_product")) {
                    return true;
                } else {
                    // another product type, give single product type
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

function label($label = "", $alt = "")
{
    $t = &get_instance();
    return !empty($t->lang->line($label)) ? $t->lang->line($label) : $alt;
}

function shiprocket_recomended_data($shiprocket_data)
{
    $result = array();
    if (isset($shiprocket_data['data']['recommended_courier_company_id'])) {
        foreach ($shiprocket_data['data']['available_courier_companies'] as $rd) {
            if ($shiprocket_data['data']['recommended_courier_company_id'] == $rd['courier_company_id']) {
                $result = $rd;
                break;
            }
        }
    } else {
        foreach ($shiprocket_data['data']['available_courier_companies'] as $rd) {
            if ($rd['courier_company_id']) {
                $result = $rd;
                break;
            }
        }
    }
    return $result;
}
function get_shipment_id($item_id, $order_id)
{

    $t = &get_instance();
    $t->db->select('*');
    $t->db->from('order_tracking');
    $t->db->where('order_id', $order_id);
    $t->db->where('is_canceled', '0');
    $t->db->where('find_in_set("' . $item_id . '", order_item_id) <> 0');
    $query = $t->db->get()->result_array();
    if (!empty($query)) {
        return $query;
    } else {
        return false;
    }
}
function make_shipping_parcels($data)
{
    /**
     *
     */
    $parcels = array();
    foreach ($data as $product) {

        if (!empty($product['pickup_location'])) {
            $parcels[$product['seller_id']][$product['pickup_location']]['weight'] += (isset($parcels[$product['seller_id']][$product['pickup_location']][$product['weight']]) && !empty($product['weight'])) ? $parcels[$product['seller_id']][$product['pickup_location']] : $product['weight'] * $product['qty'];
        }
    }
    return $parcels;
}

function check_parcels_deliveriblity($parcels, $user_pincode)
{
    $t = &get_instance();
    $t->load->library(['shiprocket']);
    $min_days = $max_days = $delivery_charge_with_cod = $delivery_charge_without_cod = 0;

    foreach ($parcels as $seller_id => $parcel) {
        foreach ($parcel as $pickup_location => $parcel_weight) {


            $pickup_postcode = fetch_details('pickup_locations', ['pickup_location' => $pickup_location], 'pin_code');
            if (isset($parcel[$pickup_location]['weight']) && $parcel[$pickup_location]['weight'] > 15) {
                $data = "More than 15kg weight is not allow";
            } else {
                $availibility_data = [
                    'pickup_postcode' => $pickup_postcode[0]['pin_code'],
                    'delivery_postcode' => $user_pincode,
                    'cod' => 0,
                    'weight' => $parcel_weight['weight'],
                ];


                $check_deliveribility = $t->shiprocket->check_serviceability($availibility_data);
                $shiprocket_data = shiprocket_recomended_data($check_deliveribility);


                $availibility_data_with_cod = [
                    'pickup_postcode' => $pickup_postcode[0]['pin_code'],
                    'delivery_postcode' => $user_pincode,
                    'cod' => 1,
                    'weight' => $parcel_weight['weight'],
                ];

                $check_deliveribility_with_cod = $t->shiprocket->check_serviceability($availibility_data_with_cod);
                $shiprocket_data_with_cod = shiprocket_recomended_data($check_deliveribility_with_cod);
                $data = [];
                $data[$seller_id][$pickup_location]['parcel_weight'] = $parcel_weight['weight'];
                $data[$seller_id][$pickup_location]['pickup_availability'] = $shiprocket_data['pickup_availability'];
                $data[$seller_id][$pickup_location]['courier_name'] = $shiprocket_data['courier_name'];
                $data[$seller_id][$pickup_location]['delivery_charge_with_cod'] = $shiprocket_data_with_cod['rate'];
                $data[$seller_id][$pickup_location]['delivery_charge_without_cod'] = $shiprocket_data['rate'];
                $data[$seller_id][$pickup_location]['estimate_date'] = $shiprocket_data['etd'];
                $data[$seller_id][$pickup_location]['estimate_days'] = $shiprocket_data['estimated_delivery_days'];
                $min_days = (empty($min_days) || $shiprocket_data['estimated_delivery_days'] < $min_days) ? $shiprocket_data['estimated_delivery_days'] : $min_days;
                $max_days = (empty($max_days) || $shiprocket_data['estimated_delivery_days'] > $max_days) ? $shiprocket_data['estimated_delivery_days'] : $max_days;
                $delivery_charge_with_cod += $data[$seller_id][$pickup_location]['delivery_charge_with_cod'];
                $delivery_charge_without_cod += $data[$seller_id][$pickup_location]['delivery_charge_without_cod'];
            }
        }
    }

    $delivery_day = ($min_days == $max_days) ? $min_days : $min_days . '-' . $max_days;
    $shipping_parcels = [
        'error' => false,
        'estimated_delivery_days' => $delivery_day,
        'estimate_date' => $shiprocket_data['etd'],
        'delivery_charge' => 0,
        'delivery_charge_with_cod' => round($delivery_charge_with_cod),
        'delivery_charge_without_cod' => round($delivery_charge_without_cod),
        'data' => $data
    ];
    return $shipping_parcels;
}
function get_shiprocket_order($shiprocket_order_id)
{
    $t = &get_instance();
    $t->load->library(['shiprocket']);
    $res = $t->shiprocket->get_specific_order($shiprocket_order_id);
    return $res;
}

function generate_awb($shipment_id)
{
    $t = &get_instance();
    $order_tracking = fetch_details('order_tracking', ['shipment_id' => $shipment_id], 'courier_company_id');
    $courier_company_id = $order_tracking[0]['courier_company_id'];

    $t->load->library(['Shiprocket']);
    $res = $t->shiprocket->generate_awb($shipment_id);

    if (isset($res["status_code"]) && $res["status_code"] == 500) {
        return [
            "error" => true,
            "message" => $res["message"]
        ];
    }
    if (isset($res["status_code"]) && $res["status_code"] == 400) {
        return [
            "error" => true,
            "message" => $res["message"]
        ];
    }
    if (isset($res["status_code"]) && $res["status_code"] != 200) {
        return [
            "error" => true,
            "message" => $res["message"],
        ];
    }

    if (isset($res['awb_assign_status']) && $res['awb_assign_status'] == 1) {
        $order_tracking_data = [
            'awb_code' => $res['response']['data']['awb_code'],
        ];
        $res_shippment_data = $t->shiprocket->get_order($shipment_id);
        $t->db->set($order_tracking_data)->where('shipment_id', $shipment_id)->update('order_tracking');
    } else {
        $res = $t->shiprocket->generate_awb($shipment_id);
        $order_tracking_data = [
            'awb_code' => $res['response']['data']['awb_code'],
        ];
        $res_shippment_data = $t->shiprocket->get_order($shipment_id);
        $t->db->set($order_tracking_data)->where('shipment_id', $shipment_id)->update('order_tracking');
    }
    return $res;
}

function send_pickup_request($shipment_id)
{
    $t = &get_instance();
    $t->load->library(['Shiprocket']);
    $res = $t->shiprocket->request_for_pickup($shipment_id);

    if (isset($res['pickup_status']) && $res['pickup_status'] == 1) {

        $order_tracking_data = [
            'pickup_status' => $res['pickup_status'],
            'pickup_scheduled_date' => $res['response']['pickup_scheduled_date'],
            'pickup_token_number' => $res['response']['pickup_token_number'],
            'status' => $res['response']['status'],
            'pickup_generated_date' => json_encode(array($res['response']['pickup_generated_date'])),
            'data' => $res['response']['data'],
        ];
        $t->db->set($order_tracking_data)->where('shipment_id', $shipment_id)->update('order_tracking');
    }
    return $res;
}

function generate_label($shipment_id)
{

    $t = &get_instance();
    $t->load->library(['Shiprocket']);
    $res = $t->shiprocket->generate_label($shipment_id);

    if (isset($res['label_created']) && $res['label_created'] == 1) {
        $label_data = [
            'label_url' => $res['label_url'],
        ];
        $t->db->set($label_data)->where('shipment_id', $shipment_id)->update('order_tracking');
    }
    return $res;
}

function generate_invoice($shiprocket_order_id)
{
    $t = &get_instance();
    $t->load->library(['Shiprocket']);
    $res = $t->shiprocket->generate_invoice($shiprocket_order_id);

    if (isset($res['is_invoice_created']) && $res['is_invoice_created'] == 1) {
        $invoice_data = [
            'invoice_url' => $res['invoice_url'],
        ];
        $t->db->set($invoice_data)->where('shiprocket_order_id', $shiprocket_order_id)->update('order_tracking');
    }
    return $res;
}
function cancel_shiprocket_order($shiprocket_order_id)
{
    $t = &get_instance();
    $t->load->library(['Shiprocket']);
    $t->load->model('Order_model');
    $res = $t->shiprocket->cancel_order($shiprocket_order_id);
    if ($res['status'] == 200 || $res['status_code'] == 200) {
        $is_canceled = [
            'is_canceled' => 1,
        ];
        $t->db->set($is_canceled)->where('shiprocket_order_id', $shiprocket_order_id)->update('order_tracking');
        $order_tracking = $t->db->where('shiprocket_order_id', $shiprocket_order_id)->get('order_tracking')->row_array();
        $consignment_id = $order_tracking['consignment_id'];
        $uniqueStatus = ["processed"];
        $active_status = "cancelled";
        $status = json_encode($uniqueStatus);
        $old_active_status = fetch_details('consignments', ['id' => $consignment_id], 'active_status');
        $old_active_status = $old_active_status[0]['active_status'] ?? "";
        if ($old_active_status != "processed" && $old_active_status != "cancelled") {
            if ($t->Order_model->update_order(['status' => $status], ['id' => $consignment_id], false, 'consignments', is_escape_array: false)) {
                $t->Order_model->update_order(['active_status' => $active_status], ['id' => $consignment_id], false, 'consignments');
                $consignment_item_details = fetch_details('consignment_items', ['consignment_id' => $consignment_id]);

                foreach ($consignment_item_details as $item) {
                    $t->Order_model->update_order(['status' => $status], ['id' => $item['order_item_id']], false, 'order_items', is_escape_array: false);
                    $t->Order_model->update_order(['active_status' => $active_status], ['id' => $item['order_item_id']], false, 'order_items');
                }
            }
        }
        $consignment_details = view_all_consignments(consignment_id: $consignment_id, in_detail: 1);
        $res['data'] = $consignment_details['data'][0];
    }
    return $res;
}

function parse_sms(string $string = "", string $mobile = "", string $sms = "", string $country_code = "")
{
    $parsedString = str_replace("{only_mobile_number}", $mobile, $string);
    $parsedString = str_replace("{message}", $sms, $parsedString); // Use $parsedString as the third argument
    $parsedString = str_replace("{mobile_number_with_country_code}", $country_code . $mobile, $parsedString);

    return $parsedString;
}


function expoxable_settings()
{
    $settings = get_settings('system_settings', true);
    $settings_data = [];
    $settings_data['system.app_name'] = $settings['app_name'];
    $settings_data['system.support_number'] = $settings['support_number'];
    $settings_data['system.support_email'] = $settings['support_email'];
    $settings_data['system.company_name'] = $settings['company_name'];
    $settings_data['system.currency'] = $settings['currency'];
    return $settings_data;
}

function get_order_data($where = [], $first = false)
{
    $t = &get_instance();

    $settings_data = expoxable_settings();
    $t->db->from('orders')->select("orders.id AS 'order.id',
                orders.user_id AS 'order.user_id',
                orders.address_id AS 'order.address_id',
                orders.mobile AS 'order.mobile',
                orders.total AS 'order.total',
                orders.delivery_charge AS 'order.delivery_charge',
                orders.is_delivery_charge_returnable AS 'order.is_delivery_charge_returnable',
                orders.wallet_balance AS 'order.wallet_balance',
                orders.promo_code AS 'order.promo_code',
                orders.promo_discount AS 'order.promo_discount',
                orders.discount AS 'order.discount',
                orders.total_payable AS 'order.total_payable',
                orders.payment_method AS 'order.payment_method',
                orders.latitude AS 'order.latitude',
                orders.longitude AS 'order.longitude',
                orders.address AS 'order.address',
                orders.delivery_time AS 'order.delivery_time',
                orders.delivery_date AS 'order.delivery_date',
                orders.date_added AS 'order.date_added',
                orders.otp AS 'order.otp',
                orders.notes AS 'order.notes',
                orders.attachments AS 'order.attachments',
                orders.is_pos_order AS 'order.is_pos_order',
                users.id AS 'user.id',
                users.ip_address AS 'user.ip_address',
                users.username AS 'user.username',
                users.email AS 'user.email',
                users.mobile AS 'user.mobile',
                users.image AS 'user.image',
                users.balance AS 'user.balance',
                users.active AS 'user.active',
                users.company AS 'user.company',
                users.address AS 'user.address',
                users.bonus_type AS 'user.bonus_type',
                users.bonus AS 'user.bonus',
                users.cash_received AS 'user.cash_received',
                users.dob AS 'user.dob',
                users.city AS 'user.city',
                users.area AS 'user.area',
                users.street AS 'user.street',
                users.pincode AS 'user.pincode',
                users.serviceable_zipcodes AS 'user.serviceable_zipcodes',
                users.fcm_id AS 'user.fcm_id',
                users.latitude AS 'user.latitude',
                users.longitude AS 'user.longitude',
                users.type AS 'user.type',
                users.driving_license AS 'user.driving_license',
                users.status AS 'user.status',
                users.web_fcm AS 'user.web_fcm',
                users.created_on AS 'user.created_on',
                addresses.id AS 'addresses.id',
                addresses.user_id AS 'addresses.user_id',
                addresses.name AS 'addresses.name',
                addresses.type AS 'addresses.type',
                addresses.mobile AS 'addresses.mobile',
                addresses.alternate_mobile AS 'addresses.alternate_mobile',
                addresses.address AS 'addresses.address',
                addresses.landmark AS 'addresses.landmark',
                addresses.area_id AS 'addresses.area_id',
                addresses.city_id AS 'addresses.city_id',
                addresses.city AS 'addresses.city',
                addresses.area AS 'addresses.area',
                addresses.pincode AS 'addresses.pincode',
                addresses.country_code AS 'addresses.country_code',
                addresses.state AS 'addresses.state',
                addresses.country AS 'addresses.country',
                addresses.latitude AS 'addresses.latitude',
                addresses.longitude AS 'addresses.longitude',
                addresses.is_default AS 'addresses.is_default',
                transactions.id AS 'transactions.id',
                transactions.transaction_type AS 'transactions.transaction_type',
                transactions.user_id AS 'transactions.user_id',
                transactions.order_id AS 'transactions.order_id',
                transactions.order_item_id AS 'transactions.order_item_id',
                transactions.type AS 'transactions.type',
                transactions.txn_id AS 'transactions.txn_id',
                transactions.payu_txn_id AS 'transactions.payu_txn_id',
                transactions.amount AS 'transactions.amount',
                transactions.status AS 'transactions.status',
                transactions.currency_code AS 'transactions.currency_code',
                transactions.payer_email AS 'transactions.payer_email',
                transactions.message AS 'transactions.message',
                transactions.transaction_date AS 'transactions.transaction_date',
                transactions.date_created AS 'transactions.date_created',
                transactions.is_refund AS 'transactions.is_refund',
                return_requests.id 'return_requests.id',
                return_requests.user_id AS 'return_requests.user_id',
                return_requests.product_id AS 'return_requests.product_id',
                return_requests.product_variant_id AS 'return_requests.product_variant_id',
                return_requests.order_id AS 'return_requests.order_id',
                return_requests.order_item_id AS 'return_requests.order_item_id',
                return_requests.status AS 'return_requests.status',
                return_requests.remarks AS 'return_requests.remarks',
                return_requests.date_created AS 'return_requests.date_created'
                ")
        ->join("users", "orders.user_id = users.id", "LEFT")
        ->join("addresses", "orders.address_id = addresses.id", "LEFT")
        ->join("transactions", "orders.id = transactions.order_id", "LEFT")
        ->join("return_requests", "orders.id = return_requests.order_id", "LEFT");




    foreach ($where as $key => $val) {
        $t->db->where($key, $val);
    }

    $data = $t->db->get()->result_array();
    if (count($data) == 0) {
        return [
            "error" => true,
            "message" => "Data not found.",
            "data" => $data
        ];
    }
    $data = array_merge($data[0], $settings_data);
    return [
        "error" => false,
        "message" => "order data received successfully,",
        "data" => $data
    ];
}
function get_notification_variables()
{
    $t = &get_instance();
    $tags = [];
    $keys = $t->config->item('order_keys');
    foreach (expoxable_settings() as $key => $val) {
        $keys[] = $key;
    }

    foreach ($keys as $val) {
        $tags[] = "{" . $val . "}";
    }

    return $tags;
}

function set_user_otp($mobile, $otp, $country_code = "+91")
{
    $t = &get_instance();
    $dateString = date('Y-m-d H:i:s');
    $time = strtotime($dateString);

    $identity_column = $t->config->item('identity', 'ion_auth');

    $otps = fetch_details('otps', ['mobile' => $mobile]);
    $data['otp'] = $otp;
    $data['created_at'] = $time;

    foreach ($otps as $user) {

        if (isset($user['mobile']) && !empty($user['mobile'])) {
            send_sms($mobile, "please don't share with anyone $otp", $country_code);
            $t->db->where('id', $user['id']);
            $t->db->update('otps', $data);
            return [
                "error" => false,
                "message" => "OTP send successfully.",
                "data" => $data
            ];
        }
        return [
            "error" => true,
            "message" => "Something went wrong."
        ];
    }
}


function checkOTPExpiration($otpTime)
{

    $time = date('Y-m-d H:i:s');
    $currentTime = strtotime($time);
    $timeDifference = $currentTime - $otpTime;


    if ($timeDifference <= 60) {
        return [
            "error" => false,
            "message" => "Success: OTP is valid."
        ];
    } else {
        return [
            "error" => true,
            "message" => "OTP has expired."
        ];
    }
}

function get_statistics($product_varient_id)
{

    $t = &get_instance();
    $dateString = date('Y-m-d H:i:s');

    $query = $t->db->query('
    SELECT
        (SELECT COUNT(id) FROM order_items
         WHERE product_variant_id = ?
         AND DATE(date_added) >= DATE(NOW()) - INTERVAL 31 DAY) AS total_ordered,
        (SELECT COUNT(f.id) FROM favorites f
         LEFT JOIN product_variants pv ON f.product_id = pv.product_id
         WHERE pv.id = ?) AS total_favorites,
        (SELECT COUNT(id) FROM cart
         WHERE product_variant_id = ?) AS total_in_cart
', [$product_varient_id, $product_varient_id, $product_varient_id]);

    $result = $query->row_array();

    // Round to the nearest multiple of 100
    $totalOrdered = round($result['total_ordered'], -1);
    $totalFavorites = round($result['total_favorites'], -1);
    $totalInCart = round($result['total_in_cart'], -1);

    // Add a "+" sign if needed
    $totalOrdered = ($totalOrdered > 10) ? number_format($totalOrdered) . '+' : $totalOrdered;
    $totalFavorites = ($totalFavorites > 10) ? number_format($totalFavorites) . '+' : $totalFavorites;
    $totalInCart = ($totalInCart > 10) ? number_format($totalInCart) . '+' : $totalInCart;
    $total = [
        "total_ordered" => $totalOrdered,
        "total_favorites" => $totalFavorites,
        "total_in_cart" => $totalInCart,
        'product_variant_id' => $product_varient_id
    ];

    return $total;
}

function time2str($ts)
{
    if (!ctype_digit($ts))
        $ts = strtotime($ts);

    $diff = time() - $ts;
    if ($diff == 0)
        return 'now';
    elseif ($diff > 0) {
        $day_diff = floor($diff / 86400);
        if ($day_diff == 0) {
            if ($diff < 60)
                return 'just now';
            if ($diff < 120)
                return '1 minute ago';
            if ($diff < 3600)
                return floor($diff / 60) . ' minutes ago';
            if ($diff < 7200)
                return '1 hour ago';
            if ($diff < 86400)
                return floor($diff / 3600) . ' hours ago';
        }
        if ($day_diff == 1)
            return 'Yesterday';
        if ($day_diff < 7)
            return $day_diff . ' days ago';
        if ($day_diff < 31)
            return ceil($day_diff / 7) . ' weeks ago';
        if ($day_diff < 60)
            return 'last month';
        return date('F Y', $ts);
    } else {
        $diff = abs($diff);
        $day_diff = floor($diff / 86400);
        if ($day_diff == 0) {
            if ($diff < 120)
                return 'in a minute';
            if ($diff < 3600)
                return 'in ' . floor($diff / 60) . ' minutes';
            if ($diff < 7200)
                return 'in an hour';
            if ($diff < 86400)
                return 'in ' . floor($diff / 3600) . ' hours';
        }
        if ($day_diff == 1)
            return 'Tomorrow';
        if ($day_diff < 4)
            return date('l', $ts);
        if ($day_diff < 7 + (7 - date('w')))
            return 'next week';
        if (ceil($day_diff / 7) < 4)
            return 'in ' . ceil($day_diff / 7) . ' weeks';
        if (date('n', $ts) == date('n') + 1)
            return 'next month';
        return date('F Y', $ts);
    }
}

function checkProductSellerIds(array $data): string
{
    $sellerIds = []; // Array to store unique product_seller_ids

    // Iterate through each element in the array
    foreach ($data as $item) {
        // Check if the item is an array and contains the 'product_seller_id' key
        if (is_array($item) && array_key_exists('product_seller_id', $item)) {
            $sellerIds[] = $item['product_seller_id']; // Add product_seller_id to the array
        }
    }

    // Remove duplicate values and reindex the array
    $uniqueSellerIds = array_values(array_unique($sellerIds));

    // If there is only one unique product_seller_id, they are all the same
    // if diffrent then return 0 , if same then return 1
    if (count($uniqueSellerIds) === 1) {
        return 1;
    } else {
        return 0;
    }
}

function verify_app_request()
{

    // to verify the token from application
    $t = &get_instance();
    $t->load->library(['jwt', 'key']);

    try {
        $token = $t->jwt->getBearerToken();
    } catch (\Exception $e) {
        return [
            "error" => true,
            "message" => $e->getMessage(),
            "status" => 401,
            "data" => []
        ];
    }


    if (empty($token)) {
        return [
            "error" => true,
            "message" => "Unauthorized access not allowed",
            "status" => 401,

            "status_code" => 101,
            "data" => []
        ];
    }
    $api_keys = JWT_SECRET_KEY;

    if (empty($api_keys)) {
        return [
            "error" => true,
            "message" => 'No API found !',
            "status" => 401,
            "data" => []
        ];
    }
    $flag = true;
    $error = true;

    $message = '';
    $status_code = 0;
    $user_token = "";

    try {
        $user_id = $t->jwt->decode($token, new Key($api_keys, 'HS256'))->user_id;
        $user_data = fetch_details('users', ['id' => $user_id]);
        $user_token = $user_data[0]['apikey'];
    } catch (\Exception $e) {
        $message = $e->getMessage();
    }
    if ($user_token == $token) {
        try {
            $payload = $t->jwt->decode($token, new Key($api_keys, 'HS256'));

            if (isset($payload->iss)) {
                $error = false;
                $flag = false;
            } else {
                $error = true;
                $flag = false;
                $message = 'Token Expired';
                $status_code = 403;
            }
        } catch (\Exception $e) {
            $message = $e->getMessage();
        }
    } else {
        if (isset($_POST['mobile']) && !empty($_POST['mobile'])) {
            $user_data = fetch_details('users', ['mobile' => $_POST['mobile']]);
        } elseif (isset($_POST['email']) && !empty($_POST['email'])) {
            $user_data = fetch_details('users', ['email' => $_POST['email']]);
        } else {
            $user_data = fetch_details('users', ['id' => $_POST['user_id']]);
        }

        $new_token = generate_token($user_data[0]['mobile'], $user_data[0]['email']);
        return [
            "error" => false,
            "message" => "Token expired. New token generated.",
            "status" => 200,
            "new_token" => $new_token,
            "data" => $user_data[0]
        ];
    }
    if ($flag) {
        return [
            "error" => true,
            "message" => $message,
            "status" => 401,
            "data" => []
        ];
    } else {
        if ($error == true) {
            return [
                "error" => true,
                "message" => $message,
                "status" => 401,
                "status_code" => 102,
                "data" => []
            ];
        } else {
            return [
                "error" => false,
                "message" => "Token verified !!",
                "status" => 200,
                "data" => $user_data[0]
            ];
        }
    }
}


function generate_token($identity, $email = null)
{

    $t = &get_instance();
    $t->load->library('jwt');
    if (!empty($identity)) {
        $user_id = fetch_details("users", ['mobile' => $identity], "id")[0]['id'];
    } else {
        $user_id = fetch_details("users", ['email' => $email], "id")[0]['id'];
    }
    $payload = [
        'iat' => time(), /* issued at time */
        'iss' => 'eshop',
        'exp' => time() + (60 * 60 * 24 * 365),
        'user_id' => $user_id
    ];
    $token = $t->jwt->encode($payload, JWT_SECRET_KEY);
    return $token;
}

function format_price($price, $decimal_point = 2)
{
    $settings = get_settings('system_settings', true);
    if (isset($settings['decimal_point'])) {
        return number_format($price, (int) $settings['decimal_point']);
    } else {
        return number_format($price, $decimal_point);
    }
}

function calculatePriceWithTax($percentage, $price)
{
    $tax_percentage = explode(',', $percentage);
    $total_tax = array_sum($tax_percentage);

    $price_tax_amount = $price * ($total_tax / 100);
    $price_with_tax_amount = $price + $price_tax_amount;

    return $price_with_tax_amount;
}

function getProductTaxPercentage($product_id)
{
    $t = &get_instance();
    $t->db->select('taxes.title, taxes.percentage');
    $t->db->from('taxes');
    $t->db->join('products', 'FIND_IN_SET(taxes.id, products.tax)', 'left');
    $t->db->where('products.id', $product_id);  // adding the where condition on product id
    $query = $t->db->get();

    $taxes = $query->result_array();

    return $taxes;
}

function getTtaxById($tax_id)
{
    $CI = &get_instance();
    $CI->db->where('id', $tax_id);
    $query = $CI->db->get('taxes'); // Assuming 'taxes' is your tax table
    return $query->row_array();
}

function convertAllWebpToPng()
{

    $t = &get_instance();
    $allFiles = $t->db->where([
        "extension" => "webp"
    ])->get('media')->result_array();


    $basePath = FCPATH;

    foreach ($allFiles as $row) {
        $target_path = $basePath . $row["sub_directory"];
        $rowFileName = $row["name"];

        $arr = explode(".", $rowFileName);
        if (in_array("webp", $arr)) {
            $title = $rowFileName;
            $arr[count($arr) - 1] = "png";
            $newName = $target_path . implode(".", $arr);
            $title = rtrim($title, ".webp");
            $im = imagecreatefromwebp($target_path . $rowFileName);

            if (file_exists($newName)) {
                $fileName = $arr[count($arr) - 2];
                $fileName1 = $arr[count($arr) - 2];
                $temp = explode("_", $fileName);

                // Check if the filename has any underscore separators
                if (count($temp) == 1) {
                    $temp = explode("_", $fileName . "_1");
                }

                if (count($temp) != 1) {
                    // Check if the last part of the filename is a number
                    if (is_numeric(end($temp))) {
                        // Check if a file with the same name and a numeric suffix exists
                        if (file_exists($target_path . $fileName1 . "_1.png")) {
                            $temp[count($temp) - 1] = (int) end($temp) + 1;
                        } else {
                            $temp[count($temp) - 1] = (int) end($temp);
                        }
                    }
                    $fileName = implode("_", $temp);
                    $title = $fileName;
                }

                $arr[count($arr) - 2] = $fileName;
                $newName = $target_path . implode(".", $arr);
            }

            $array = [
                'name' => $title . ".png",
                'title' => $title,
                "extension" => "png"
            ];

            update_details($array, ['name' => $rowFileName], "media");
            unlink($target_path . $rowFileName);
            imagepng($im, $newName);
            imagedestroy($im);
        }
    }
    return true;
}

function update_shiprocket_order_status($tracking_id)
{
    $t = &get_instance();
    $t->load->model('Order_model');

    $order_tracking_details = fetch_details("order_tracking", ['tracking_id' => $tracking_id, 'is_canceled' => 0], 'order_id,consignment_id,shiprocket_order_id');
    if (empty($order_tracking_details) && !isset($order_tracking_details[0]['consignment_id'])) {
        $response['error'] = true;
        $response['message'] = "Something Went Wrong. Order Not Found.";
        $response['data'] = [];
        return $response;
    }
    $consignment_id = $order_tracking_details[0]['consignment_id'];
    $order_id = $order_tracking_details[0]['order_id'];
    $shiprocket_order_id = $order_tracking_details[0]['shiprocket_order_id'];
    $order_item_status = fetch_details('order_items', ['order_id' => $order_id], 'status');

    $t->load->library(['Shiprocket']);
    $res = $t->shiprocket->tracking_order($tracking_id);
    // $res = $t->shiprocket->get_specific_order($shiprocket_order_id);


    if (isset($res[0]['tracking_data']) && !empty($res[0]['tracking_data'])) {
        $active_status = "";
        $status = [];
        $active_status_code = $res[0]['tracking_data']['shipment_status'];

        $awb_code = $res[0]['tracking_data']['shipment_track'][0]['awb_code'];
        $track_url = $res[0]['tracking_data']['track_url'];

        $data = [
            'url' => $track_url,
            'awb_code' => $awb_code
        ];
        if ($active_status_code != 8) {
            update_details($data, ['tracking_id' => $tracking_id], 'order_tracking');
        }
        $track_activities = $res[0]['tracking_data']['shipment_track'];

        foreach ($track_activities as $track_list) {
            $test = transforShiprocketStatus($track_list['current_status']);

            $data = [
                $test,
                $track_list['edd'],
            ];
            array_push($status, $data);

            $active_status = strtolower($status[0][0]);
        }


        if ($active_status == 'delivered' && !in_array('delivered', array_column($status, 0))) {
            $data = [
                $active_status,
                $res[0]['tracking_data']['shipment_track'][0]['delivered_date'] ?? date("Y-m-d") . " " . date("h:i:sa")
            ];
            array_push($status, $data);
        }

        if (empty($active_status) && empty($status)) {
            $response['error'] = true;
            $response['message'] = "Check Status Manually From Given Tracking Url!";
            $response['data'] = [
                'track_url' => $track_url
            ];
            return $response;
        }
        $consignment_item_details = fetch_details('consignment_items', ['consignment_id' => $consignment_id]);
        $consignment_details = fetch_details('consignments', ['id' => $consignment_id]);
        if (empty($consignment_details) || empty($consignment_item_details)) {
            $response['error'] = true;
            $response['message'] = "Something Went Wrong. Order Not Found.";
            $response['data'] = [
                'track_url' => $track_url
            ];
            return $response;
        }
        if (!empty($active_status) && empty($status)) {
            $status = [[$active_status, date("Y-m-d") . " " . date("h:i:sa")]];
        }
        if (empty($active_status) && !empty($status)) {
            $active_status = $consignment_details[0]['active_status'];
        }

        $status = array_reverse($status);
        $uniqueStatus = [];
        // remove duplicate status

        foreach ($status as $entry) {
            $status = $entry[0];
            if (!in_array($status, array_column($uniqueStatus, 0))) {
                $uniqueStatus[] = $entry;
            }
        }

        $response_data = [];
        $active_status = str_replace(" ", "_", $active_status);
        $status = json_decode($order_item_status[0]['status'], true);

        $last_status = end($status);

        // Merge arrays only if they are not the same
        $updated_array = array_merge($status, $uniqueStatus);

        $uniqueArray = array_intersect_key(
            $updated_array,
            array_unique(array_column($updated_array, 0))
        );

        // Reset array indexes
        $updated_array = array_values($uniqueArray);

        if ($active_status == "cancelled") {
            $data1 = [
                'is_canceled' => 1
            ];

            foreach ($status as $entry) {
                $result[] = $entry;
                if ($entry[0] === "processed") {
                    break; // Stop adding entries once "processed" is found
                }
            }

            $updated_array = $result;
            $active_status = "processed";
            update_details($data1, ['tracking_id' => $tracking_id], 'order_tracking');
        }

        $updated_status = json_encode($updated_array, true);

        if ($t->Order_model->update_order(['status' => $updated_status], ['id' => $consignment_id], false, 'consignments', is_escape_array: false)) {
            $t->Order_model->update_order(['active_status' => $active_status], ['id' => $consignment_id], false, 'consignments');

            foreach ($consignment_item_details as $item) {
                $t->Order_model->update_order(['status' => $updated_status], ['id' => $item['order_item_id']], false, 'order_items', is_escape_array: false);
                $t->Order_model->update_order(['active_status' => $active_status], ['id' => $item['order_item_id']], false, 'order_items');
                $data = [
                    'consignment_id' => $consignment_id,
                    'order_item_id' => $item['order_item_id'],
                    'status' => $active_status
                ];
                array_push($response_data, $data);
            }
        }
        if ($active_status == "cancelled") {
            $response['error'] = true;
            $response['message'] = "Shiprocket Order Is Cancelled!";
            $response['data'] = [
                'track_url' => $track_url
            ];
        } else {
            $response['error'] = false;
            $response['message'] = "Status Updated Successfully";
            $response['data'] = $response_data;
        }
        return $response;
    } else {
        $msg = $res[0][$tracking_id]['tracking_data']['error'] ?? "";
        $response['error'] = true;
        $response['message'] = ($msg == "") ? "Something Went Wrong!" : $msg;
        $response['data'] = [];
        return $response;
    }
}

function calculatePercentage($part, $total)
{
    if ($total == 0) {
        return "Error: Total cannot be zero.";
    }
    if ($part == 0) {
        return "Error: Part cannot be zero.";
    }
    $percentage = ($part / $total) * 100;
    return $percentage;
}

function calculatePrice($percentage, $total)
{
    if ($total == 0) {
        return "Error: Total cannot be zero.";
    }

    $price = ($percentage / 100) * $total;

    return $price;
}
function suggestionKeyword($input_keyword)
{

    $t = &get_instance();

    // Escape the input to prevent SQL injection
    $input_keyword = $t->db->escape_like_str($input_keyword);

    $query = $t->db->query("
    SELECT `suggestion_keyword`
    FROM (
        (
            SELECT TRIM(SUBSTRING_INDEX(SUBSTRING_INDEX(p.tags, ',', numbers.n), ',', -1)) AS suggestion_keyword,
                   p.date_added
            FROM (
                SELECT 1 n UNION ALL SELECT 2 UNION ALL SELECT 3 UNION ALL SELECT 4 UNION ALL SELECT 5
                UNION ALL SELECT 6 UNION ALL SELECT 7 UNION ALL SELECT 8 UNION ALL SELECT 9 UNION ALL SELECT 10
            ) numbers
            INNER JOIN products p
            ON CHAR_LENGTH(p.tags) - CHAR_LENGTH(REPLACE(p.tags, ',', '')) >= numbers.n - 1
            WHERE TRIM(SUBSTRING_INDEX(SUBSTRING_INDEX(p.tags, ',', numbers.n), ',', -1)) LIKE '%$input_keyword%'
        )
        UNION
        (
            SELECT c.name AS suggestion_keyword, p.date_added
            FROM products p
            JOIN categories c ON c.id = p.category_id
            WHERE c.name LIKE '%$input_keyword%'
        )
        UNION
        (
            SELECT p.brand AS suggestion_keyword, p.date_added
            FROM products p
                WHERE p.brand LIKE '%$input_keyword%'
            )
        ) AS suggestions
        GROUP BY `suggestion_keyword`
        ORDER BY `date_added` DESC, `suggestion_keyword` ASC
        LIMIT 8
    ");

    $result = $query->result_array();

    return $result;
}

function get_cod_limits()
{
    $payment_settings = get_settings("payment_method", true);
    dd($payment_settings);
}

function transforShiprocketStatus($shiprocketStatus)
{
    return match (strtolower($shiprocketStatus)) {
        "pickup error" => "received",

        "box packing", "ready to pack", "pickup generated", "packed", "packed exception", "pickup exception", "pickup error" => "processed",

        "shipped",
        "picked up",
        "pickup rescheduled",
        "out for delivery",
        "in transit",
        "Delayed",
        "rwached at destination hub",
        "misrouted",
        "reached warehouse",
        "custom cleared",
        "in flight",
        "handover to courier",
        "shipment booked",
        "in transit overseas",
        "connection aligned",
        "reached overseas warehouse",
        "custom cleared overseas",
        "processed at warehouse",
        "rider assigned",
        "rider unassigned",
        "roder reached at drop",
        "searching_for_rider",
        "picklist generated",
        "fc allocated",
        "fc manifest generated" => "shipped",

        "delivered",
        "partial_delivered",
        "fulfilled",
        "self fulfilled" => "delivered",

        "canceled",
        "cancellation requested",
        "lost",
        "untraceable" => "cancelled",

        "rto initiated",
        "rto delivered",
        "rto acknowledged",
        "rto_ndr",
        "rto_ofd",
        "damaged",
        "destroyed",
        "disposed off",
        "cancelled_before_dispatched",
        "rto in transit",
        "qc failed",
        "handover exception",
        "rto_lock",
        "issue_related_to_the_recipient",
        "undelivered" => "return_request_pending",

        "out for pickup",
        "pickup booked",
        "reached_back_at_seller_city" => "return_request_approved",

        default => "processed",
    };
}

function csvToJsonProduct($file_path, $type = 'upload')
{
    $data = [];
    if (($handle = fopen($file_path, "r")) !== FALSE) {
        $headers = fgetcsv($handle);

        // Create a mapping of header positions
        $headerPositions = [];
        foreach ($headers as $index => $header) {
            $header = str_replace(' ', '_', $header);
            if (!isset($headerPositions[$header])) {
                $headerPositions[$header] = [];
            }
            $headerPositions[$header][] = $index;
        }

        while (($row = fgetcsv($handle)) !== FALSE) {
            // Skip rows that are empty or contain only null/empty strings
            $filteredRow = array_filter($row, function ($value) {
                return $value !== null && trim($value) !== '';
            });
            if (empty($filteredRow)) {
                continue;
            }

            $rowData = [];
            foreach ($headerPositions as $header => $positions) {
                if (count($positions) > 1) {
                    $rowData[$header] = [];
                    foreach ($positions as $pos) {
                        $rowData[$header][] = $row[$pos] ?? '';
                    }
                } else {
                    $rowData[$header] = $row[$positions[0]] ?? '';
                }
            }
            $data[] = $rowData;
        }

        fclose($handle);
    }


    return transformData($data, $type);
}


function transformData($data, $type = 'upload')
{
    $result = [];
    $variantFields = [
        'attribute_value_ids',
        'variant_id',
        'price',
        'special_price',
        'sku',
        'stock',
        'images',
        'availability',
        'weight',
        'height',
        'breadth',
        'length',
    ];

    foreach ($data as $item) {
        $base = [];
        $variants = [];

        // Extract base fields
        foreach ($item as $key => $value) {
            if (!in_array($key, $variantFields)) {
                $base[$key] = is_array($value) ? $value[0] : $value;
            }
        }

        if ($item['type'] === 'variable_product') {
            // print_r($item);
            // print_r($item['attribute_value_ids']);
            // $variantCount_array = explode(',', $item['attribute_value_ids']);
            $variantCount = count($item['attribute_value_ids']);
            // $variantCount = count($variantCount_array);
            for ($i = 0; $i < $variantCount; $i++) {
                if (!empty($item['attribute_value_ids'][$i])) {
                    $variant = [];
                    if ($type == 'update') {
                        $variant['variant_id'] = $item['variant_id'][$i];
                    }
                    $variant['attribute_value_ids'] = $item['attribute_value_ids'][$i];
                    $variant['price'] = $item['price'][$i] ?? '';
                    $variant['special_price'] = $item['special_price'][$i] ?? '';
                    $variant['sku'] = $item['sku'][$i + 1] ?? '';
                    $variant['stock'] = $item['stock'][$i + 1] ?? '';
                    $variant['images'] = $item['images'][$i] ?? '';
                    $variant['availability'] = $item['availability'][$i + 1] ?? '';
                    $variant['weight'] = $item['weight'][$i] ?? '';
                    $variant['height'] = $item['height'][$i] ?? '';
                    $variant['breadth'] = $item['breadth'][$i] ?? '';
                    $variant['length'] = $item['length'][$i] ?? '';
                    $variants[] = $variant;
                }
            }
            $base['variants'] = $variants;
        } else {
            // For simple products
            $variant = [];
            foreach ($variantFields as $field) {
                $variant[$field] = isset($item[$field][0]) ? $item[$field][0] : '';
            }
            $base['variants'] = [$variant];

            // Add stock details to base level for simple products
            $base['sku'] = $variant['sku'];
            $base['stock'] = $variant['stock'];
            $base['availability'] = $variant['availability'];
        }

        $result[] = $base;
    }

    return $result;
}

function read_more_text($text, $limit = 10)
{
    $alpha_count = 0;
    $truncated = '';
    $reached_limit = false;

    for ($i = 0; $i < strlen($text); $i++) {
        $char = $text[$i];
        if (ctype_alpha($char)) {
            $alpha_count++;
        }

        if ($alpha_count > $limit) {
            $reached_limit = true;
            break;
        }

        $truncated .= $char;
    }

    return $reached_limit ? $truncated . '...' : $text;
}

// get categories assigned to products
function get_assigned_categories()
{
    $t = &get_instance();
    // Get unique category IDs from products
    $category_ids = $t->db->distinct()
        ->select('category_id')
        ->where('category_id !=', '')
        ->get('products')
        ->result_array();

    // Extract IDs into a simple array
    $ids = array_column($category_ids, 'category_id');
    if (empty($ids)) {
        return [];
    }

    // Fetch category data for these IDs
    $categories = $t->db->where_in('id', $ids)->get('categories')->result_array();
    return $categories;
}


// get brands assigned to products
function get_assigned_brands()
{
    $t = &get_instance();
    // Get unique brand IDs from products
    $brand_ids = $t->db->distinct()
        ->select('brand')
        ->where('brand !=', '')
        ->get('products')
        ->result_array();

    // Extract IDs into a simple array
    $ids = array_column($brand_ids, 'brand');
    if (empty($ids)) {
        return [];
    }

    // Fetch brand data for these IDs
    $brands = $t->db->where_in('id', $ids)->get('brands')->result_array();

    return $brands;
}

function generate_unique_affiliate_uuid($user_id = null)
{
    $t = &get_instance();
    $t->load->database();

    for ($i = 0; $i < 5; $i++) {
        // Generate purely numeric token: timestamp + random
        $timestamp = str_replace('.', '', microtime(true)); // microseconds without dot
        $random = rand(100, 999);
        $token = $timestamp . $random; // purely numbers

        // Check if token already exists
        $query = $t->db->get_where('affiliates', ['uuid' => $token]);

        if ($query->num_rows() === 0) {
            return $token; // unique
        } else {
            $existing = $query->row();
            if ($existing->user_id == $user_id) {
                return $token; // same user
            }
        }
    }

    // fallback to longer token
    return $token . rand(1000, 9999);
}


/**
 * Update affiliate wallet balance and log transaction
 *
 * @param string $type - "credit" or "debit"
 * @param int $user_id - Affiliate user ID
 * @param float $amount - Amount to update
 * @param int|null $product_id - Product ID (used for tracking updates)
 * @param string $message - Transaction message
 * @param string $reference_type - "order" (get commission) or "withdraw" (withdrawal)
 * @param float|null $sub_total - Order sub_total to update total_order_value (optional)
 * @param string|null $token - Token used (to update usage count)
 * @return array
 */
function update_affiliate_wallet_balance($type, $user_id, $amount, $product_id = null, $message = '', $reference_type = 'order', $sub_total = null, $token = null)
{
    $ci = &get_instance();
    $ci->load->model('affiliate_model');
    $ci->load->model('affiliate_transaction_model');

    // Validate inputs
    if (!in_array($type, ['credit', 'debit']) || $user_id <= 0 || $amount <= 0) {
        return ['error' => true, 'message' => 'Invalid input'];
    }

    if ($reference_type == 'order') {
        $affiliate = $ci->db->get_where('affiliates', ['user_id' => $user_id])->row_array();
        if (!$affiliate) {
            return ['error' => true, 'message' => 'Affiliate not found'];
        }

        $current_balance = (float)$affiliate['affiliate_wallet_balance'];
        $new_balance = ($type == 'credit') ? $current_balance + $amount : $current_balance - $amount;

        if ($new_balance < 0) {
            return ['error' => true, 'message' => 'Insufficient balance'];
        }

        // Start DB transaction
        // $ci->db->trans_start();

        // Update wallet balance
        $ci->db->where('user_id', $user_id)->update('affiliates', [
            'affiliate_wallet_balance' => $new_balance
        ]);

        // update affiliate tracking data
        if (!empty($product_id)) {
            $ci->db->set('commission_earned', 'commission_earned + ' . $ci->db->escape($amount), false);

            if (!empty($token)) {
                $ci->db->set('usage_count', 'usage_count + 1', false);
            }

            if (!is_null($sub_total)) {
                $ci->db->set('total_order_value', 'total_order_value + ' . $ci->db->escape($sub_total), false);
            }

            $ci->db->where('affiliate_id', $user_id)
                ->where('product_id', $product_id)
                ->where('token', $token)
                ->update('affiliate_tracking');
        }
        $data = [
            'user_id' => $user_id,
            'amount' => $amount,
            'transaction_type' => $type,
            'reference_type' => $reference_type,
            'message' => $message
        ];

        // Insert transaction log
        $ci->affiliate_transaction_model->add_affiliate_wallet_transactions($data);
        // $ci->db->insert('affiliate_wallet_transactions', [
        //     'user_id' => $user_id,
        //     'amount' => $amount,
        //     'type' => $type,
        //     'reference_type' => $reference_type,
        //     'message' => $message
        // ]);

        // $ci->db->trans_complete();

        if (!$ci->db->trans_status()) {
            return ['error' => true, 'message' => 'Transaction failed'];
        }
    }

    return ['error' => false, 'message' => 'Affiliate Wallet updated successfully'];
}


function process_phonepe_result($txn_id, $amount = null, $status = null)
{
    $t = &get_instance();
    $t->load->library(['Phonepe']);
    $t->load->model('customer_model');

    $transaction = fetch_details('transactions', ['txn_id' => $txn_id], '*');
    if (empty($transaction)) {
        $transaction = fetch_details('transactions', ['order_id' => $txn_id], '*');
    }
    if (empty($transaction)) {
        log_message('error', 'Transaction not found: ' . $txn_id);
        return;
    }

    $user_id = $transaction[0]['user_id'];
    $transaction_type = $transaction[0]['transaction_type'] ?? "";
    $order_id = $transaction[0]['order_id'] ?? "";

    // Always confirm status with PhonePe API for security
    $check_status = $t->phonepe->check_status_v2($txn_id);
    // echo "<pre>";
    //     print_R($check_status);
    // die;

    // Use API state over webhook/redirect state if available
    $final_status = $check_status['state'] ?? $status;
    $final_amount = $check_status['amount'] ?? $amount;

    $data = [];
    if ($final_status == 'COMPLETED') {
        $data['status'] = "success";
        if ($transaction_type == "wallet") {
            $data['message'] = "Wallet refill successful";
            $t->transaction_model->update_transaction($data, $txn_id);

            if (!$t->customer_model->update_balance($final_amount / 100, $user_id, 'add')) {
                log_message('error', 'Could not update wallet balance for txn ' . $txn_id);
            }
        } elseif ($transaction_type == "transaction") {
            $data['message'] = "Payment received successfully";
            update_details(['active_status' => 'received'], ['order_id' => $order_id], 'order_items');

            $order_status = json_encode([['received', date("d-m-Y h:i:sa")]]);
            update_details(['status' => $order_status], ['order_id' => $order_id], 'order_items', false);
            update_details(['status' => $order_status], ['id' => $order_id], 'orders', false);
            $t->transaction_model->update_transaction($data, $txn_id);
        }
    } elseif (in_array($final_status, ["BAD_REQUEST", "AUTHORIZATION_FAILED", "PAYMENT_ERROR", "TRANSACTION_NOT_FOUND", "PAYMENT_DECLINED", "TIMED_OUT", "FAILED"])) {
        $data['status'] = "failed";
        if ($transaction_type == "wallet") {
            $data['message'] = "Wallet could not be recharged!";
            $t->transaction_model->update_transaction($data, $txn_id);
        } elseif ($transaction_type == "transaction") {
            update_details(['active_status' => 'cancelled'], ['order_id' => $order_id], 'order_items');
            $order_status = json_encode([['cancelled', date("d-m-Y h:i:sa")]]);
            update_details(['status' => $order_status], ['order_id' => $order_id], 'order_items', false);
            update_details(['status' => 'cancelled'], ['id' => $order_id], 'orders', false);
            $data['message'] = "Payment couldn't be processed!";
            $t->transaction_model->update_transaction($data, $txn_id);
        }
    } else {
        log_message('error', 'Unhandled PhonePe status: ' . $final_status);
    }
    return $data;
}
