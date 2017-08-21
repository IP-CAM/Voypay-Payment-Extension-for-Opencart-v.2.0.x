<?php

class Controllerpaymentvoypay extends Controller
{
    //URL网址，放在php的header中
    private $http_referer;
    private $debug = false;
    const  CONNECT_TIMEOUT = 300;
    const  PROCESS_TIMEOUT = 300;
    //表单提交地址
    private $formAction="index.php?route=payment/voypay/paymentSend";
    private $voypayConfig;
    public function index()
    {
        $this->voypayConfig = $this->getvoypayConfig();
        $this->load->model('checkout/order');
        $this->language->load('payment/voypay');
        $data['entry_website_url'] = HTTP_SERVER;
        $data['text_credit_card'] = $this->voypayConfig['messages']['cardType'];
        $data['card_pay_wait'] = $this->language->get('card_pay_wait');
        $data['entry_cc_type'] = $this->language->get('Newcard_entry_cc_type');
        $data['entry_cc_number'] = $this->voypayConfig['messages']['cardNumber'];
        if($this->canReadHtml5()){
            $data['numberType']='tel';
        }else{
            $data['numberType']='text';
        }
        $data['entry_cc_expire_date'] =$this->voypayConfig['messages']['expirationDate'];;
        $data['entry_cc_cvv2'] =  $this->voypayConfig['messages']['cvv'];
        $data['cvvNote'] = str_replace("<strong>","",$this->voypayConfig['messages']['cvvNote']);
        $data['cvvNote'] = str_replace("</strong>","",$data['cvvNote']);
        $data['whatIsThis'] = $this->voypayConfig['messages']['whatIsThis'];
        $data['entry_cc_expire_month'] = $this->voypayConfig['messages']['month'];
        $data['entry_cc_expire_year'] = $this->voypayConfig['messages']['year'];
        $data['button_confirm'] = $this->voypayConfig['messages']['submit'];
        $data['button_back'] = $this->language->get('Newcard_button_back');
        $data['entry_cc_number_check'] = $this->voypayConfig['messages']['cardNoError'];
        $data['entry_cc_expire_month_check'] =  $this->voypayConfig['messages']['monthError'];
        $data['entry_cc_expire_year_check'] = $this->voypayConfig['messages']['yearError'];
        $data['entry_cc_cvv2_check'] = $this->voypayConfig['messages']['cvvError'];
        $data['text_whatIsThis'] = HTTP_SERVER. $this->language->get('text_whatIsThis');
        $data['text_brand'] =  HTTP_SERVER. $this->language->get('text_brand');
        $data['path_global'] =  HTTP_SERVER. $this->language->get('path_global');
        $data['button_back'] = $this->language->get('button_back');
        $this->load->library('encryption');
        $products = $this->cart->getProducts();
        $sum = count($products);
        foreach ($products as $product) {
            for ($i = 0; $i < $sum; $i++) {
                $_SESSION["PName" . $i] = $product["name"];
                $_SESSION["PModel" . $i] = $product["model"];
            }
        }
        $MonthOp = "";
        for ($i = 1; $i < 13; $i++) {
            $MonthOp .= "<option value='" . sprintf('%02d', $i) . "'>" . sprintf('%02d', $i) . "</option>";
        }
        $YearOp = "";
        $today = getdate();
        for ($i = $today['year']; $i < $today['year'] + 20; $i++) {
            $YearOp .= "<option value='" . strftime('%Y', mktime(0, 0, 0, 1, 1, $i)) . "'>" . strftime('%Y', mktime(0, 0, 0, 1, 1, $i)) . "</option>";
        }
        $data['entry_cc_month_select'] = $MonthOp;
        $data['entry_cc_year_select'] = $YearOp;


        if ($this->request->get ['route'] != 'checkout/guest_step_3') {
            $data ['back'] = HTTPS_SERVER . 'index.php?route=checkout/payment';
        } else {
            $data ['back'] = HTTPS_SERVER . 'index.php?route=checkout/guest_step_2';
        }
        $this->id = 'payment';
        $order_info = $this->model_checkout_order->getOrder($this->session->data ['order_id']);
        $data ['out_trade_no'] = date('Ymd') . $order_info ['order_id'];
        $data ['mer_no'] = $this->config->get('voypay_mer_no');
        $data['Framework'] = "OpenCart";
        $data['formAction'] = $this->formAction;
        $this->http_referer=HTTP_SERVER."index.php?route=payment/voypay";
        $_SESSION['monidata']=$data;
        //print_r($_SERVER);
        if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/payment/voypay.tpl')) {
            return $this->load->view($this->config->get('config_template') .'/template/payment/voypay.tpl', $data);
        } else {
            return $this->load->view('default/template/payment/voypay.tpl', $data);
        }
    }

    public function paymentSend()
    {

        $this->voypayConfig=$this->getvoypayConfig();
        $this->load->model('checkout/order');
        $order_info = $this->model_checkout_order->getOrder($this->session->data ['order_id']);
        $this->load->library('encryption');
        $products = $this->cart->getProducts();


        $cardholder = $order_info['payment_firstname']. ' '.$order_info['payment_lastname'];

        $goodsInfo = array();
        foreach ($products as $i=>$product) {
            $item = array(
                'goods_name'=>$product['name'],
                'quantity'=>$product['quantity'],
                'price'=>$product['price'],
            );
            $goodsInfo[] = $item;
        }

        $billingAddr = array(
            'first_name'=>$order_info['payment_firstname'],
            'last_name'=>$order_info['payment_lastname'],
            'address1'=>$order_info['payment_address_1'],
            'address2'=>$order_info['payment_address_2'],
            'zip_code'=>$order_info['payment_postcode'],
            'city'=>$order_info['payment_city'],
            'state'=>$order_info['payment_zone_code'],
            'country'=>$order_info['payment_iso_code_2']
        );

        $shippingAddr = array(
            'first_name'=>$order_info['shipping_firstname'],
            'last_name'=>$order_info['shipping_lastname'],
            'address1'=>$order_info['shipping_address_1'],
            'address2'=>$order_info['shipping_address_2'],
            'zip_code'=>$order_info['shipping_postcode'],
            'city'=>$order_info['shipping_city'],
            'state'=>$order_info['shipping_zone_code'],
            'country'=>$order_info['shipping_iso_code_2']
        );

        $card_no = str_replace(' ', '',$this->request->post['Newcard_cardNo']);//卡号
        $expire_month = $this->request->post['Newcard_cardExpireMonth'];//有效期的月
        $expire_year = $this->request->post['Newcard_cardExpireYear'];//有效期的年
        $csc = $this->request->post['Newcard_cardSecurityCode'];
        $this->load->model ( 'localisation/currency' );

        $param = array(
            'card_no'=>$card_no,
            'cvv' =>$csc,
            'exp_year'=>$expire_year,
            'exp_month'=>$expire_month,

            'merchant_trade_no'=>$order_info ['order_id'],
            'currency'=>$order_info['currency_code'],
            'amount'=>number_format($order_info['total'], 2, '.', ''),
            'card_holder'=>$cardholder,
            'buyer_email'=>$order_info ['email'],
            'buyer_phone'=>$order_info ['telephone'],
            'goods_info'=>$goodsInfo,
            'billing_address'=>$billingAddr,
            'shipping_address'=>$shippingAddr,
            'return_url'=>$this->url->link('payment/voypay/callback', '', 'SSL'),
            'notify_url'=>$this->url->link('payment/voypay/callback', '', 'SSL'),
            'langugage'=>'en',
            'buyer_ip'=> $this->get_client_ip(),
            'user_agent'=> $order_info['user_agent'],
            'remark'=>'',

        );
        $object_result = $this->callRemoteMethod('trade.credit.submit',$param);

        if($object_result["status"]=="02"){
            $this->voypay_success($object_result);
        }else if(($object_result["status"]=="01") || ($object_result["status"]=="00")){
            $this->voypay_pending($object_result);
        }else{
            $this->voypay_failure($object_result);
        }
    }
    public function voypay_success($json_result){//成功的交易，有E3默认pending
        $v_tempdate =$_SESSION['order_id'];
        $message = $this->voypayConfig['messages']['paySuccess']."\n";
        if (isset ( $json_result["merchant_trade_no"] )) {
            $data ['text_billno'] = '<font color="green">' . $v_tempdate . '</font>';
            $message .=  $this->voypayConfig['messages']['lblOrderNumber'] . $v_tempdate . "\n";
        }

        if (isset ($json_result["amount"])) {
            $amount = $json_result["amount"];
            $message .=$this->voypayConfig['messages']['lblpayment'] .$amount . "\n";
        }
        if (isset ($json_result["currency"])) {
            $currency = $json_result["currency"];
            $message .=$this->voypayConfig['messages']['lblpayment'] .$currency ."\n";
        }
        if (isset ( $json_result["status"] )) {
            $status =$json_result["status"];
            $message .= 'status: ' . $status . "\n";
        }

        if (isset ( $json_result["message"])) {
            $RespMsg = $json_result["message"];
            $data ['text_result'] = '<font color="green">' . $RespMsg . '</font>';
            $message .= 'failure_reason: ' . $RespMsg. "\n";
        }
        if (isset ( $json_result["mer_no"] )) {
            $mer_no=$json_result["mer_no"];
            $message .= 'mer_no: ' . $mer_no. "\n";
        }
        if (isset ( $json_result["voypay_trade_no"])) {
            $ref_no =$json_result["voypay_trade_no"];
            $message .= 'ref_no: ' . $ref_no. "\n";
        }
        if (isset ( $json_result["sign"])) {
            $sign =$json_result["sign"];
            $message .= 'sign: ' . $sign. "\n";
        }
        $this->load->model('checkout/order');
        $this->model_checkout_order->addOrderHistory($this->session->data['order_id'], $this->config->get('voypay_new_success_order_status_id'), $message, FALSE);
        $this->response->redirect($this->url->link('checkout/success'));


    }
    public function voypay_pending($json_result){//超时未成功订单,默认pending
        $this->voypayConfig=$this->getvoypayConfig();
        $this->language->load ( 'payment/voypay' );
        $data ['heading_title'] = sprintf ( $this->language->get ( 'heading_title' ), $this->config->get ( 'config_name' ) );
        $data ['text_response'] = $this->language->get ( 'text_response' );
        if ($this->request->get ['route'] != 'checkout/guest_step_3') {
            $data ['text_failure_wait'] = sprintf ( $this->language->get ( 'text_failure_wait' ), HTTPS_SERVER . 'index.php?route=checkout/checkout' );
        } else {
            $data ['text_failure_wait'] = sprintf ( $this->language->get ( 'text_failure_wait' ), HTTPS_SERVER . 'index.php?route=checkout/guest_step_2' );
        }

        if(!empty($json_result['3d_forward_url'])){
            $url = $json_result['3d_forward_url'];
            $this->response->redirect($url);
        }

        $message='';
        $this->load->model('checkout/order');

        $this->model_checkout_order->addOrderHistory($this->session->data['order_id'], $this->config->get('voypay_order_status_id'), $message, FALSE);

        $this->response->redirect($this->url->link('checkout/success'));
    }
    public function voypay_failure($json_result){//处理失败订单,其中有些是处理中的交易
        $this->voypayConfig=$this->getvoypayConfig();
        $this->language->load ( 'payment/voypay' );
        $data ['heading_title'] = sprintf ( $this->language->get ( 'heading_title' ), $this->config->get ( 'config_name' ) );
        $data ['text_response'] = $this->language->get ( 'text_response' );
        if ($this->request->get ['route'] != 'checkout/guest_step_3') {
            $data ['text_failure_wait'] = sprintf ( $this->language->get ( 'text_failure_wait' ), HTTPS_SERVER . 'index.php?route=checkout/checkout' );
        } else {
            $data ['text_failure_wait'] = sprintf ( $this->language->get ( 'text_failure_wait' ), HTTPS_SERVER . 'index.php?route=checkout/guest_step_2' );
        }
        $v_tempdate =$_SESSION['order_id'];
        $message='';
        $this->load->model('checkout/order');
        $message .= $this->voypayConfig['messages']['lblOrderNumber'] . $v_tempdate . "\n";
        $Recode = trim($json_result["status"]);
        $recode_padding=array('00','01');//处理中的回调code
        if(!in_array($Recode,$recode_padding)){
            $message.=str_replace('@@@',$json_result['failure_reason'],$this->voypayConfig['messages']['payFailure'])."\n";
            $data ['text_result'] = '<font color="red">' . str_replace('@@@',$json_result['failure_reason'],$this->voypayConfig['messages']['payFailure']) . '</font>';
            $data ['text_failure'] = $data ['text_result'];
            $this->model_checkout_order->addOrderHistory($this->session->data['order_id'], $this->config->get('voypay_new_fail_order_status_id'), $message, FALSE);
        }else{
            $message.= $this->voypayConfig['messages']['payPending'] . "\n";
            $data ['text_result'] = '<font color="red">' .  $this->voypayConfig['messages']['payPending']  . '</font>';
            $data ['text_failure'] =$this->voypayConfig['messages']['payPending'];
            // $this->model_checkout_order->addOrderHistory($this->session->data['order_id'], $this->config->get('voypay_order_status_id'), $message, FALSE);
            $this->model_checkout_order->addOrderHistory($this->session->data['order_id'], $this->config->get('voypay_new_fail_order_status_id'), $message, FALSE);
        }
        $data['continue'] = HTTPS_SERVER . 'index.php?route=checkout/cart';
        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['column_right'] = $this->load->controller('common/column_right');
        $data['footer'] = $this->load->controller('common/footer');
        if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/payment/voypay_failure.tpl')) {
            $this->response->setOutput($this->load->view($this->config->get('config_template') .'/template/payment/voypay_failure.tpl', $data));
        } else {
            $this->response->setOutput( $this->load->view('default/template/payment/voypay_failure.tpl', $data));
        }
    }
    public function callback(){
        $this->voypayConfig=$this->getvoypayConfig();
        //var_dump($_REQUEST);
        $mer_no = $_REQUEST['mer_no'];
        $out_trade_no = $_REQUEST['merchant_trade_no'];
        $ref_no = $_REQUEST['voypay_trade_no'];
        $status = $_REQUEST['status'];
        $failure_reason = $_REQUEST['message'];
        $amount = $_REQUEST['amount'];
        $currency = $_REQUEST['currency'];

        $message = $this->voypayConfig['messages']['paySuccess']."\n";
        $message .=  $this->voypayConfig['messages']['lblOrderNumber'] . $out_trade_no . "\n";
        $message .=$this->voypayConfig['messages']['lblpayment'] .$amount .$currency. "\n";
        $message .= 'status: ' . $status . "\n";
        $message .= 'failure_reason: ' . $failure_reason. "\n";
        $message .= 'mer_no: ' . $mer_no. "\n";
        $message .= 'ref_no: ' . $ref_no. "\n";
        $this->load->model('checkout/order');
        if($status=='02'){
            $data ['text_billno'] = '<font color="green">' . $out_trade_no . '</font>';
            $data ['text_result'] = '<font color="green">' . $failure_reason . '</font>';
            $this->model_checkout_order->addOrderHistory($this->session->data['order_id'], $this->config->get('voypay_new_success_order_status_id'), $message, FALSE);
            $this->response->redirect($this->url->link('checkout/success'));
        }else{
            $this->language->load ( 'payment/voypay' );
            $data ['heading_title'] = sprintf ( $this->language->get ( 'heading_title' ), $this->config->get ( 'config_name' ) );
            $data ['text_response'] = $this->language->get ( 'text_response' );
            if ($this->request->get ['route'] != 'checkout/guest_step_3') {
                $data ['text_failure_wait'] = sprintf ( $this->language->get ( 'text_failure_wait' ), HTTPS_SERVER . 'index.php?route=checkout/checkout' );
            } else {
                $data ['text_failure_wait'] = sprintf ( $this->language->get ( 'text_failure_wait' ), HTTPS_SERVER . 'index.php?route=checkout/guest_step_2' );
            }
            $data ['text_result'] = '<font color="red">' .str_replace('@@@',$json_result['failure_reason'],$this->voypayConfig['messages']['payFailure']). '</font>';
            $data ['text_failure'] = $data ['text_result'];
            $this->model_checkout_order->addOrderHistory($this->session->data['order_id'], $this->config->get('voypay_new_fail_order_status_id'), $message, FALSE);
            $data['continue'] = HTTPS_SERVER . 'index.php?route=checkout/cart';
            $data['header'] = $this->load->controller('common/header');
            $data['column_left'] = $this->load->controller('common/column_left');
            $data['column_right'] = $this->load->controller('common/column_right');
            $data['footer'] = $this->load->controller('common/footer');
            if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/payment/voypay_failure.tpl')) {
                $this->response->setOutput($this->load->view($this->config->get('config_template') .'/template/payment/voypay_failure.tpl', $data));
            } else {
                $this->response->setOutput( $this->load->view('default/template/payment/voypay_failure.tpl', $data));
            }
        }
    }


    private function buildSign($assesskey,$content){
        return hash('sha256', $assesskey.$content);
    }

    // 签名验证
    private function checkSign($signature, $assesskey, $content){
        $newSign =   $this->buildSign($assesskey,$content);
        return strtolower($signature) == strtolower($newSign);
    }


    public function callRemoteMethod($appId, $param){
        $merId = $this->config->get('voypay_mer_no');
        $accesskey  = $this->config->get('voypay_sign');
        $mode = $this->config->get('voypay_mode');

        if($mode == 'live'){
            $url = 'https://gateway.voypay.net/process/';
        }
        else{
            $url = 'https://test.voypay.net/gateway/process/';
        }

        $content = trim(json_encode($param,JSON_UNESCAPED_UNICODE|JSON_PRETTY_PRINT));

        $headers =  array();
        $headers[] = "MerId: ".$merId;
        $headers[] = "AppId: ".$appId;
        $headers[] = "Signature: " . $this->buildSign($accesskey, $content);
        $headers[] = "Expect: ";

        if($this->debug){
            echo  'Voypay Request header';
            print_r($headers);
            echo 'Voypay Request body';
            print_r($content);
        }

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HEADER, true);

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, self::CONNECT_TIMEOUT);
        curl_setopt($ch, CURLOPT_TIMEOUT, self::PROCESS_TIMEOUT);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $content);
        curl_setopt($ch, CURLOPT_REFERER, $this->url->link('payment/voypay', '', 'SSL'));
        curl_setopt($ch, CURLOPT_NOSIGNAL, true);
        $res = curl_exec($ch);
        if($this->debug) {
            echo 'Voypay Response Info';
            print_r($res);
            echo  'curl_getinfo';
            print_r(curl_getinfo($ch));
        }
        curl_close($ch);

        list($header, $body) = explode( "\r\n\r\n",$res);
        $header = $this->parseHeader($header);

        if(!$this->checkSign($header['signature'], $accesskey, $body)){
            return false;
        }
        return json_decode($body, true, 512, JSON_BIGINT_AS_STRING);
    }
    private function parseHeader($header){
        $res = explode("\r\n",$header);
        $t = current($res);
        list($protocol,$status) = explode(' ', $t);
        $backheader['protocol'] = $protocol;
        $backheader['status'] = $status;
        foreach ((array)$res as $i => $item){
            if($i ==0) {
                continue;
            };
            list($key,$value) = explode(': ', $item);
            if($key){
                $key = strtolower($key);
                $backheader[$key] = trim($value);
            }
        }
        return $backheader;
    }

    public function get_client_ip()
    {
        if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $online_ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
        } elseif (isset($_SERVER['HTTP_CLIENT_IP'])) {
            $online_ip = $_SERVER['HTTP_CLIENT_IP'];
        } elseif (isset($_SERVER['HTTP_X_REAL_IP'])) {
            $online_ip = $_SERVER['HTTP_X_REAL_IP'];
        } else {
            $online_ip = $_SERVER['REMOTE_ADDR'];
        }
        $ips = explode(",", $online_ip);
        return $ips[0];
    }
    function getBrowserLang()
    {
        $acceptLan = '';
        if (isSet($_SERVER['HTTP_ACCEPT_LANGUAGE']) && !empty($_SERVER['HTTP_ACCEPT_LANGUAGE'])) {
            $acceptLan = explode(',', $_SERVER['HTTP_ACCEPT_LANGUAGE']);
            $acceptLan = $acceptLan[0];
        }
        return $acceptLan;
    }

    function isMobile(){
        $UserAgent = $_SERVER['HTTP_USER_AGENT'];
        $IsMobile=false;
        if(stristr($UserAgent,'mobile') && !stristr($UserAgent,'ipad')){
            $IsMobile=true;
        }
        return $IsMobile;
    }

    function canReadHtml5(){
        $UserAgent = strtolower($_SERVER['HTTP_USER_AGENT']);
        $CanReadHTML5 = false;
        if(strpos($UserAgent,'webkit') || strpos($UserAgent,'firefox') || strpos($UserAgent,'trident') || strpos($UserAgent,'safari')){
            $CanReadHTML5=true;
        }
        return $CanReadHTML5;
    }

    public function getvoypayConfig(){
        static $config;
        if(!empty($config)){
            return $config;
        }
        $lang = substr($_SESSION["language"],0,2);

        $messages["total"]=$this->language->get('total');
        $messages["expirationDate"]=$this->language->get('expirationDate');
        $messages["submit"]=$this->language->get('submit');
        $messages["cvvError"]=$this->language->get('cvvError');
        $messages["cardNoError"]=$this->language->get('cardNoError');
        $messages["cardType"]=$this->language->get('cardType');
        $messages["yearError"]=$this->language->get('yearError');
        $messages["lblOrderNumber"]=$this->language->get('lblOrderNumber');
        $messages["payFailure"]=$this->language->get('payFailure');
        $messages["payPending"]=$this->language->get('payPending');
        $messages["month"]=$this->language->get('month');
        $messages["cvv"]=$this->language->get('cvv');
        $messages["errorNote"]=$this->language->get('errorNote');
        $messages["year"]=$this->language->get('year');
        $messages["lblpayment"]=$this->language->get('lblpayment');
        $messages["whatIsThis"]=$this->language->get('whatIsThis');
        $messages["cvvNote"]=$this->language->get('cvvNote');
        $messages["monthError"]=$this->language->get('monthError');
        $messages["paySuccess"]=$this->language->get('paySuccess');
        $messages["cardNumber"]=$this->language->get('cardNumber');

        $_SESSION['lang'] = $lang;

        $config = $_SESSION['voypayConfig'] = array(
            'lang'=>$lang,
            'messages'=>$messages,
        );
        return $config;
    }

}