<?php

namespace App\Http\Controllers;

use App\Models\Coupon;
use App\Models\Invoice;
use App\Models\InvoicePayment;
use App\Models\Order;
use App\Models\Plan;
use App\Models\UserCoupon;
use App\Models\Utility;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Models\Client;
use App\Models\Project;



class PaystackPaymentController extends Controller
{
    public $secret_key;
    public $public_key;
    public $is_enabled;
    public $currancy;
    public $user;
    public $pay_amount;

    public function setPaymentDetail_client($invoice_id){

        $invoice = Invoice::find($invoice_id);
        if(Auth::user() != null){
            $this->user         = Auth::user();
        }else{
            $this->user         = Client::where('id',$invoice->client_id)->first();
        }   

        $payment_setting = Utility::getPaymentSetting($this->user->currentWorkspace->id);
        $this->currancy  = (isset($this->user->currentWorkspace->currency_code)) ? $this->user->currentWorkspace->currency_code : 'USD';

        $this->secret_key = isset($payment_setting['paystack_secret_key']) ? $payment_setting['paystack_secret_key'] : '';
        $this->public_key = isset($payment_setting['paystack_public_key']) ? $payment_setting['paystack_public_key'] : '';
        $this->is_enabled = isset($payment_setting['is_paystack_enabled']) ? $payment_setting['is_paystack_enabled'] : 'off';
    }


    public function invoicePayWithPaystack(Request $request, $slug, $invoice_id)
    {
        $this->setPaymentDetail_client($invoice_id);

        $validatorArray = [
            'amount' => 'required',
        ];
        $validator      = Validator::make(
            $request->all(), $validatorArray
        );

        if($validator->fails())
        {
            return response()->json(
                [
                    'message' => $validator->errors()->first(),
                ], 401
            );
        }

        // $currentWorkspace = Utility::getWorkspaceBySlug($slug);
        $invoice          = Invoice::find($invoice_id);
        if($invoice->getDueAmount() < $request->amount)
        {
            return response()->json(
                [
                    'message' => __('Invalid amount.'),
                ], 401
            );
        }

        $res_data['email']       = $this->user->email;
        $res_data['total_price'] = $request->amount;
        $res_data['currency']    = $this->currancy;
        $res_data['flag']        = 1;
        $res_data['invoice_id']  = $invoice->id;
        $request->session()->put('invoice_data', $res_data);
        $this->pay_amount = $request->amount;

        return $res_data;
    }

    public function getInvoicePaymentStatus($slug, $pay_id, $invoice_id, Request $request)
    {
        $this->setPaymentDetail_client($invoice_id);


        if(!empty($invoice_id) && !empty($pay_id))
        {
            $user             = $this->user;
            $currentWorkspace = $user ? Utility::getWorkspaceBySlug($slug) : Utility::getWorkspaceBySlug_copylink( 'invoice' , $invoice_id);

            $invoice_id   = decrypt($invoice_id);
            $invoice      = Invoice::find($invoice_id);
            $invoice_data = $request->session()->get('invoice_data');

            if($invoice && !empty($invoice_data))
            {
                $url = "https://api.paystack.co/transaction/verify/$pay_id";

                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, $url);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                curl_setopt(
                    $ch, CURLOPT_HTTPHEADER, [
                           'Authorization: Bearer ' . $this->secret_key,
                       ]
                );
                $result = curl_exec($ch);
                curl_close($ch);
                if($result)
                {
                    $result = json_decode($result, true);
                }

                if(isset($result['status']) && $result['status'] == true)
                {
                    $order_id = strtoupper(str_replace('.', '', uniqid('', true)));

                    $invoice_payment                 = new InvoicePayment();
                    $invoice_payment->order_id       = $order_id;
                    $invoice_payment->invoice_id     = $invoice->id;
                    $invoice_payment->currency       = $currentWorkspace->currency_code;
                    $invoice_payment->amount         = isset($invoice_data['total_price']) ? $invoice_data['total_price'] : 0;
                    $invoice_payment->payment_type   = 'Paystack';
                    $invoice_payment->receipt        = '';
                    $invoice_payment->client_id      = $this->$user->id;
                    $invoice_payment->user_id        = $this->$user->id;
                    $invoice_payment->txn_id         = $pay_id;
                    $invoice_payment->payment_status = 'succeeded';
                    $invoice_payment->save();

                    if(($invoice->getDueAmount() - $invoice_payment->amount) == 0)
                    {
                        $invoice->status = 'paid';
                        $invoice->save();
                    }else{
                        $invoice->status = 'partialy paid';
                        $invoice->save();
                    }

                    $user1 = $currentWorkspace->id;
                    $settings  = Utility::getPaymentSetting($user1);
                    $total_amount =  $invoice->getDueAmounts($invoice->id);
                    $client           = Client::find($invoice->client_id);
                    $project_name = Project::where('id', $invoice->project_id)->first();


                    $uArr = [
                        // 'user_name' => $user->name,
                        'project_name' => $project_name->name,
                        'company_name' => User::find($project_name->created_by)->name,
                        'invoice_id' => Utility::invoiceNumberFormat($invoice->id),
                        'client_name' => $client->name,
                        'total_amount' => "$total_amount",
                        'paid_amount' => $request->amount,
                    ];

                    if (isset($settings['invoicest_notificaation']) && $settings['invoicest_notificaation'] == 1) {

                        Utility::send_slack_msg('Invoice Status Updated', $user1 , $uArr);
                    }

                    if (isset($settings['telegram_invoicest_notificaation']) && $settings['telegram_invoicest_notificaation'] == 1) {
                        Utility::send_telegram_msg('Invoice Status Updated' ,$uArr, $user1);
                    }

                    //webhook
                    $module ='Invoice Status Updated';
                    $webhook=  Utility::webhookSetting($module , $user1);

                    // $webhook=  Utility::webhookSetting($module);
                    if($webhook)
                    {
                        $parameter = json_encode($invoice);
                        // 1 parameter is  URL , 2 parameter is data , 3 parameter is method
                        $status = Utility::WebhookCall($webhook['url'],$parameter,$webhook['method']);
                        // if($status == true)
                        // {
                        //     return redirect()->back()->with('success', __('Payment added Successfully!'));
                        // }
                        // else
                        // {
                        //     return redirect()->back()->with('error', __('Webhook call failed.'));
                        // }
                    }

                    $request->session()->forget('invoice_data');

                    return redirect()->back()->with('success', __('Payment added Successfully'));

                    if(\Auth::check())
                    {
                        return redirect()->route(
                            'client.invoices.show', [
                                                      $slug,
                                                      $invoice_id,
                                                  ]
                        )->with('success', __('Invoice paid Successfully!'));
                    }
                    else
                    {
                        return redirect()->route('pay.invoice',[$slug,\Illuminate\Support\Facades\Crypt::encrypt($invoice->id)])->with('success', __('Invoice paid Successfully!'));
                    }
                    
                }
                else
                {
                    if(\Auth::check())
                    {
                        return redirect()->route(
                            'client.invoices.show', [
                                                      $slug,
                                                      $invoice_id,
                                                  ]
                        )->with('error', __('Transaction has been failed.'));
                    }
                    else
                    {
                        return redirect()->route('pay.invoice',[$slug,\Illuminate\Support\Facades\Crypt::encrypt($invoice->id)])->with('error', __('Transaction has been failed.'));
                    }
                    
                }
            }
            else
            {
                if(\Auth::check())
                {
                    return redirect()->route(
                        'client.invoices.show', [
                                                  $slug,
                                                  $invoice_id,
                                              ]
                    )->with('error', __('Invoice not found.'));
                }
                else
                {
                    return redirect()->route('pay.invoice',[$slug,\Illuminate\Support\Facades\Crypt::encrypt($invoice->id)])->with('error', __('Invoice not found.'));
                }
                
            }
        }
        else
        {
            if(\Auth::check())
            {
                return redirect()->route('client.invoices.index', $slug)->with('error', __('Invoice not found.'));
            }
            else
            {
                return redirect()->route('pay.invoice',[$slug,\Illuminate\Support\Facades\Crypt::encrypt($invoice_id)])->with('error', __('Invoice not found.'));
            }
        }
    }
}
