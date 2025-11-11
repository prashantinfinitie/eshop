<?php
/* 
    1. get_credentials()
    2. create_order($amount,$receipt='')
    3. fetch_payments($id ='')
    4. capture_payment($amount, $id, $currency = "INR")
    5. verify_payment($order_id, $razorpay_payment_id, $razorpay_signature)

    0. curl($url, $method = 'GET', $data = [])
*/
class Phonepe
{
    private $client_id = "";
    private $client_secret = "";
    private $url = "";
    private $environment = "";
    private $marchant_id = "";

    public $client_version = 1;
    public $grant_type = "Generated";

    function __construct()
    {
        $settings = get_settings('payment_method', true);
        $system_settings = get_settings('system_settings', true);

        $this->client_id = (isset($settings['phonepe_client_id'])) ? $settings['phonepe_client_id'] : " ";
        $this->client_secret = (isset($settings['phonepe_client_secret'])) ? $settings['phonepe_client_secret'] : " ";
        $this->marchant_id = (isset($settings['phonepe_marchant_id'])) ? $settings['phonepe_marchant_id'] : " ";

        $this->url = (isset($settings['phonepe_payment_mode']) && $settings['phonepe_payment_mode'] == "PRODUCTION") ? "https://api.phonepe.com/apis/hermes" : "https://api-preprod.phonepe.com/apis/pg-sandbox";

        if (isset($settings['phonepe_payment_mode'])) {
            if ($settings['phonepe_payment_mode'] == 'PRODUCTION') {
                $this->environment = 'PRODUCTION';
            } elseif ($settings['phonepe_payment_mode'] == 'UAT') {
                $this->environment = 'UAT';
            } else {
                $this->environment = 'SANDBOX';
            }
        }
    }

    public function get_access_token()
    {
        $client_id = $this->client_id;
        $client_version = 1;
        $client_secret = $this->client_secret;
        $grant_type = "Generated";

        $postData = http_build_query([
            'client_id' => $client_id,
            'client_version' => $client_version,
            'client_secret' => $client_secret,
            'grant_type' => 'Generated',
        ]);

        $url = $this->url . '/v1/oauth/token';

        $curl = curl_init();


        curl_setopt_array($curl, array(
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => $postData,
            CURLOPT_HTTPHEADER => array(
                'Content-Type: application/x-www-form-urlencoded'
            ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);

        $getToken = json_decode($response, true);

        //echo $getToken['access_token'];
        if (isset($getToken['access_token']) && $getToken['access_token'] != '') {
            $accessToken = $getToken['access_token'];
            $expires_at = $getToken['expires_at'];
            // Save this details in the database to use access token and check expiry

        } else {
            $accessToken = '';
            $expires_at = '';
        }

        return $accessToken;
    }

    public function pay_v2($data, $type)
    {
        $accessToken = $this->get_access_token();
        if (!$accessToken) {
            return ['error' => 'Could not fetch access token'];
        }
        if ($type == 'web') {
            $url = $this->url . "/checkout/v2/pay";
        } else {
            $url = $this->url . "/checkout/v2/sdk/order";
        }

        $payload = [
            "merchantOrderId" => $data['merchantTransactionId'],
            "amount" => intval($data['amount']), // Amount in paise
            // "expireAfter" => $accessToken['expires_at'],
            "metaInfo" => $data,
            "paymentFlow" => [
                "type" => "PG_CHECKOUT",
                "message" => "Payment message used for collect requests",
                "merchantUrls" => [
                    "redirectUrl" => $data['redirectUrl'] . "?TransactionID=" . $data['merchantTransactionId'],
                ]
            ]
        ];
        $headers = [
            'Content-Type: application/json',
            'Authorization: O-Bearer ' . $accessToken
        ];

        $response = $this->curl($url, 'POST', json_encode($payload), $headers);
        $response_data = json_decode($response['body'], true);


        if (isset($data['redirectUrl'])) {
            return [
                'orderId' => $response_data['orderId'] ?? '',
                'redirectUrl' => $response_data['redirectUrl'],
                'merchantOrderId' => $data['merchantTransactionId']
            ];
        } else {
            return $response_data;
        }
    }

    public function check_status_v2($id = '')
    {
        // $data['merchantId'] = $this->merchant_id;
        $accessToken = $this->get_access_token();

        $data['paymentInstrument'] = array(
            'type' => 'PAY_PAGE',
        );
        $endpoint = '/checkout/v2/order/' . $id . '/status';
        $url = $this->url . $endpoint;
        $method = 'GET';

        $header = [
            'Content-Type: application/json',
            'Authorization: O-Bearer ' . $accessToken
        ];
        $response = $this->curl($url, $method, [], $header);
        $res = json_decode($response['body'], true);
        return $res;
    }

    public function phonepe_checksum_v2($data)
    {

        $orderId = 'TX' . time(); // unique order ID

        $expireAfter = 1200; // in seconds (20 mins)
        $token = $this->get_access_token();

        $order = $this->pay_v2($data, 'app');

        $requestPayload = [
            // "orderId" => $data['merchantTransactionId'],
            "state"  => "PENDING",
            "merchantOrderId" => $order['orderId'],
            "amount" => $data['amount'], // marchant id
            "merchantId" => $this->client_id,
            "expireAT" => $expireAfter,
            "token" => $order['token'],
            "paymentMode" => [
                "type" => "PAY_PAGE"
            ]
        ];

        // Convert to JSON string as required by Flutter SDK
        $requestString = json_encode($requestPayload);

        return [
            "environment" => $this->environment, // or "PRODUCTION"
            "merchantOrderId" => $order['orderId'],
            "flowId" => $orderId,
            "enableLogging" => true, // false in production
            "request" => $requestPayload,
            "token" => $token,
        ];
    }

    public function curl($url, $method = 'POST', $data = [], $header = [])
    {
        $ch = curl_init();
        $curl_options = array(
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_HEADER => 0,
            CURLOPT_HTTPHEADER => $header
        );
        if (strtolower($method) == 'post') {
            $curl_options[CURLOPT_POST] = 1;
            if (!empty($data)) {
                $curl_options[CURLOPT_POSTFIELDS] = $data;
            }
        } else {
            $curl_options[CURLOPT_CUSTOMREQUEST] = 'GET';
        }
        curl_setopt_array($ch, $curl_options);
        $result = array(
            'body' => curl_exec($ch),
            'http_code' => curl_getinfo($ch, CURLINFO_HTTP_CODE),
        );
        return $result;
    }
}
