﻿<?php

namespace App\Service;

class Omipay
{
    // Địa chỉ thanh toán hoá đơn của Omipay.vn
    //public $omipay_url = 'https://checkout.omipay.vn/checkout.php';
    public $omipay_url = 'https://checkout-sandbox.omidev.com/checkout.php';
    // Mã website của bạn đăng ký trong chức năng tích hợp thanh toán của Omipay.vn.
    public $merchant_site_code = '275'; //100001 chỉ là ví dụ, bạn hãy thay bằng mã của bạn
    // Mật khẩu giao tiếp giữa website của bạn và Omipay.vn.
    public $secure_pass = '123456'; //d685739bf1 chỉ là ví dụ, bạn hãy thay bằng mật khẩu của bạn
    // Nếu bạn thay đổi mật khẩu giao tiếp trong quản trị website của chức năng tích hợp thanh toán trên Omipay.vn, vui lòng update lại mật khẩu này trên website của bạn
    public $affiliate_code = ''; //Mã đối tác tham gia chương trình liên kết của Omipay.vn

    public $create_order_params = [];

    /**
     * HÀM TẠO ĐƯỜNG LINK THANH TOÁN QUA Omipay.vn VỚI THAM SỐ MỞ RỘNG
     *
     * @param string $return_url : Đường link dùng để cập nhật tình trạng hoá đơn tại website của bạn khi người mua thanh toán thành công tại Omipay.vn
     * @param string $receiver : Địa chỉ Email chính của tài khoản Omipay.vn của người bán dùng nhận tiền bán hàng
     * @param string $transaction_info : Tham số bổ sung, bạn có thể dùng để lưu các tham số tuỳ ý để cập nhật thông tin khi Omipay.vn trả kết quả về
     * @param string $order_code : Mã hoá đơn hoặc tên sản phẩm
     * @param int $price : Tổng tiền hoá đơn/sản phẩm, chưa kể phí vận chuyển, giảm giá, thuế.
     * @param string $currency : Loại tiền tệ, nhận một trong các giá trị 'vnd', 'usd'. Mặc định đồng tiền thanh toán là 'vnd'
     * @param int $quantity : Số lượng sản phẩm
     * @param int $tax : Thuế
     * @param int $discount : Giảm giá
     * @param int $fee_cal : Nhận giá trị 0 hoặc 1. Do trên hệ thống Omipay.vn cho phép chủ tài khoản cấu hình cho nhập/thay đổi phí lúc thanh toán hay không. Nếu website của bạn đã có phí vận chuyển và không cho sửa thì đặt tham số này = 0
     * @param int $fee_shipping : Phí vận chuyển
     * @param string $order_description : Mô tả về sản phẩm, đơn hàng
     * @param string $buyer_info : Thông tin người mua
     * @param string $affiliate_code : Mã đối tác tham gia chương trình liên kết của Omipay.vn
     * @return string
     */
    public function buildCheckoutUrlExpand($return_url, $receiver, $transaction_info, $order_code, $price, $currency = 'vnd', $quantity = 1, $tax = 0, $discount = 0, $fee_cal = 0, $fee_shipping = 0, $order_description = '', $buyer_info = '', $affiliate_code = '')
    {
        if ($affiliate_code == "") $affiliate_code = $this->affiliate_code;
        $this->create_order_params = array(
            'merchant_site_code' => strval($this->merchant_site_code),
            'return_url' => strval(strtolower($return_url)),
            'receiver' => strval($receiver),
            'transaction_info' => strval($transaction_info),
            'order_code' => strval($order_code),
            'price' => strval($price),
            'currency' => strval($currency),
            'quantity' => strval($quantity),
            'tax' => strval($tax),
            'discount' => strval($discount),
            'fee_cal' => strval($fee_cal),
            'fee_shipping' => strval($fee_shipping),
            'order_description' => strval($order_description),
            'buyer_info' => strval(''), //"Họ tên người mua *|* Địa chỉ Email *|* Điện thoại *|* Địa chỉ"
            'affiliate_code' => strval($affiliate_code),
        );

        $secure_code = implode(' ', $this->create_order_params) . ' ' . $this->secure_pass;
        $this->create_order_params['secure_code'] = md5($secure_code);

        /* */
        $redirect_url = $this->omipay_url;
        if (strpos($redirect_url, '?') === false) {
            $redirect_url .= '?';
        } else if (substr($redirect_url, strlen($redirect_url) - 1, 1) != '?' && strpos($redirect_url, '&') === false) {
            $redirect_url .= '&';
        }

        /* */
        $url = '';
        foreach ($this->create_order_params as $key => $value) {
            $value = urlencode($value);
            if ($url == '') {
                $url .= $key . '=' . $value;
            } else {
                $url .= '&' . $key . '=' . $value;
            }
        }
//		$url .= '&time_limit=' . urlencode(date('d/m/Y,H:i', time() + 60));
//		$url .= '&time_limit=' . '30/04/2014,16:30';
        //echo $url;
        // die;
        return $redirect_url . $url;
    }

    /**
     * HÀM TẠO ĐƯỜNG LINK THANH TOÁN QUA Omipay.vn VỚI THAM SỐ CƠ BẢN
     *
     * @param string $return_url : Đường link dùng để cập nhật tình trạng hoá đơn tại website của bạn khi người mua thanh toán thành công tại Omipay.vn
     * @param string $receiver : Địa chỉ Email chính của tài khoản Omipay.vn của người bán dùng nhận tiền bán hàng
     * @param string $transaction_info : Tham số bổ sung, bạn có thể dùng để lưu các tham số tuỳ ý để cập nhật thông tin khi Omipay.vn trả kết quả về
     * @param string $order_code : Mã hoá đơn/Tên sản phẩm
     * @param int $price : Tổng tiền phải thanh toán
     * @return string
     */
    public function buildCheckoutUrl($return_url, $receiver, $transaction_info, $order_code, $price)
    {

        // Bước 1. Mảng các tham số chuyển tới nganluong.vn
        $arr_param = array(
            'merchant_site_code' => strval($this->merchant_site_code),
            'return_url' => strtolower(urlencode($return_url)),
            'receiver' => strval($receiver),
            'transaction_info' => strval($transaction_info),
            'order_code' => strval($order_code),
            'price' => strval($price)
        );

        $secure_code = implode(' ', $arr_param) . ' ' . $this->secure_pass;
        $arr_param['secure_code'] = md5($secure_code);

        /* Bước 2. Kiểm tra  biến $redirect_url xem có '?' không, nếu không có thì bổ sung vào */
        $redirect_url = $this->omipay_url;
        if (strpos($redirect_url, '?') === false) {
            $redirect_url .= '?';
        } else if (substr($redirect_url, strlen($redirect_url) - 1, 1) != '?' && strpos($redirect_url, '&') === false) {
            // Nếu biến $redirect_url có '?' nhưng không kết thúc bằng '?' và có chứa dấu '&' thì bổ sung vào cuối
            $redirect_url .= '&';
        }

        /* Bước 3. Tạo url */
        $url = '';
        foreach ($arr_param as $key => $value) {
            if ($key != 'return_url') $value = urlencode($value);

            if ($url == '')
                $url .= $key . '=' . $value;
            else
                $url .= '&' . $key . '=' . $value;
        }
        return $redirect_url . $url;
    }

    /**
     * HÀM KIỂM TRA TÍNH ĐÚNG ĐẮN CỦA ĐƯỜNG LINK KẾT QUẢ TRẢ VỀ TỪ Omipay.vn
     *
     * @param string $transaction_info : Thông tin về giao dịch, Giá trị do website gửi sang
     * @param string $order_code : Mã hoá đơn/tên sản phẩm
     * @param string $price : Tổng tiền đã thanh toán
     * @param string $payment_id : Mã giao dịch tại Omipay.vn
     * @param int $payment_type : Hình thức thanh toán: 1 - Thanh toán ngay (tiền đã chuyển vào tài khoản Omipay.vn của người bán); 2 - Thanh toán Tạm giữ (tiền người mua đã thanh toán nhưng Omipay.vn đang giữ hộ)
     * @param string $error_text : Giao dịch thanh toán có bị lỗi hay không. $error_text == "" là không có lỗi. Nếu có lỗi, mô tả lỗi được chứa trong $error_text
     * @param string $secure_code : Mã checksum (mã kiểm tra)
     * @return unknown
     */
    public function verifyPaymentUrl($transaction_info, $order_code, $price, $payment_id, $payment_type, $error_text, $secure_code)
    {
        // Tạo mã xác thực từ chủ web
        $str = '';
        $str .= ' ' . urldecode(strval($transaction_info));
        $str .= ' ' . strval($order_code);
        $str .= ' ' . strval($price);
        $str .= ' ' . strval($payment_id);
        $str .= ' ' . strval($payment_type);
        $str .= ' ' . strval($error_text);
        $str .= ' ' . strval($this->merchant_site_code);
        $str .= ' ' . strval($this->secure_pass);

        // Mã hóa các tham số
        $verify_secure_code = '';
        $verify_secure_code = md5($str);

        // Xác thực mã của chủ web với mã trả về từ nganluong.vn
        if ($verify_secure_code === $secure_code) return true;
        else return false;
    }

    function GetTransactionDetail($token)
    {
        ###################### BEGIN #####################
        $params = array(
            'merchant_id' => $this->merchant_site_code,
            'merchant_password' => MD5($this->secure_pass),
            'version' => Config::$_VERSION,
            'function' => 'GetTransactionDetail',
            'token' => $token
        );
        $api_url = Config::$_URL_SERVICE;
        $post_field = '';
        foreach ($params as $key => $value) {
            if ($post_field != '') $post_field .= '&';
            $post_field .= $key . "=" . $value;
        }
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $api_url);
        curl_setopt($ch, CURLOPT_ENCODING, 'UTF-8');
        curl_setopt($ch, CURLOPT_VERBOSE, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post_field);
        $result = curl_exec($ch);
        $status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        if ($result != '' && $status == 200) {
            $nl_result = simplexml_load_string($result);
            return $nl_result;
        }
        return false;
    }
}
