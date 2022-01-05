<?php

namespace App\Http\Controllers;

use App\Service\DemoService;
use Illuminate\Http\Request;
use App\Service\Omipay;

class OmipayController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        return view('omipay.index');
    }

    public function submitForm(Request $request)
    {

        $input = $request->all();
        //dd($input);
        $omipayService = new DemoService();

        $omipayService->merchant_site_code = $request->input('key');
        $omipayService->secure_pass = $request->input('code');

        //Basic Info
        $notify_url = 'https://saigon360hn.com/notice';
        $txt_name = $request->input('fullname');
        $txt_email = $request->input('email');
        $txt_phone = $request->input('phone');
        $txt_price = $request->input('price');
        $order_description = $request->input('description');

        $tax_amount = $request->input('txt_tax');
        $fee_shipping = $request->input('txt_shipping_fee');
        $return_url = $request->input('txt_return_url');
        $cancel_url = $request->input('txt_cancel_url');
        $txt_buyer_info = $txt_name . '*|*' . $txt_email . '*|*' . $txt_phone . '*|*' . 'HN';
        //Adv Info
        $txt_merchant_id = $request->input('key');
        $txt_merchant_pass = $request->input('code');
        $txt_receiver = $request->input('txt_receiver');
        $txt_return_url = $request->input('txt_return_url', 'https://saigon360hn.com/return');
        $txt_receiver = 'dungpx@htpgroup.com.vn';
        $txt_transaction_info = $request->input('txt_transaction_info', 'thanhtoan');
        $txt_order_code = 'OM_122112';
        $txt_currency = $request->input('txt_currency', 'vnd');
        $txt_quantity = $request->input('txt_quantity', 1);
        $txt_tax = $request->input('txt_tax', 0);
        $txt_discount = $request->input('txt_discount', 0);
        $txt_cal_fee = $request->input('txt_cal_fee', 0);
        $txt_shipping_fee = $request->input('txt_shipping_fee', 0);
        $txt_order_description = $request->input('txt_order_description', 'Hoá đơn');

        $url = $omipayService->buildCheckoutUrlExpand($txt_return_url, $txt_receiver, $txt_transaction_info, $txt_order_code, $txt_price,
            $txt_currency, $txt_quantity, $txt_tax, $txt_discount, $txt_cal_fee, $txt_shipping_fee,
            $txt_order_description, $txt_buyer_info, '');
        $url .= '&notify_url=' . $notify_url;


        //dd($url);
        return redirect($url);
        //return redirect('add-blog-post-form')->with('status', 'Blog Post Form Data Has Been inserted');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
