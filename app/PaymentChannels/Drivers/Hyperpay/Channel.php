<?php

namespace App\PaymentChannels\Drivers\Hyperpay;

use App\Models\Order;
use App\Models\PaymentChannel;
use App\PaymentChannels\BasePaymentChannel;
use App\PaymentChannels\IChannel;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request as Psr7Request;
use Illuminate\Http\Request;


class Channel extends BasePaymentChannel implements IChannel
{
    protected $currency;
    protected $order_session_key;
    protected $api;

    private $entityId, $endPointUrl, $authToken;

    public function __construct(PaymentChannel $paymentChannel)
    {
        $this->currency = currency();
        $this->endPointUrl = env("HYPERPAY_URL");
        $this->entityId = env("HYPERPAY_ENTITY_ID");
        $this->authToken = env("HYPERPAY_AUTH_KEY");
        $this->order_session_key = "hayperpay.payments.order_id";
    }


    public function paymentRequest(Order $order)
    {
        try {
            session()->put($this->order_session_key, $order->id);

            $client = new Client();
            $headers = [
                'Authorization' => "Bearer {$this->authToken}",
                'Content-Type' => 'application/x-www-form-urlencoded',
            ];
            $options = [
                'form_params' => [
                    'entityId' => $this->entityId,
                    'amount' => $order->total_amount,
                    'currency' => 'SAR',
                    'paymentType' => 'DB'
                ]
            ];
            $request = new Psr7Request('POST', "{$this->endPointUrl}/checkouts", $headers);
            $res = $client->sendAsync($request, $options)->wait();
            $responseJson = $res->getBody()->getContents();
            $responseArray = json_decode($responseJson, true);

            session()->put($this->order_session_key, $order->id);

            return view("web.default.cart.channels.hayperPay", ["checkoutId" => $responseArray["id"]]);
        } catch (\Exception $e) {
            print('Error: ' . $e->getMessage());
        }
    }


    public function verify(Request $request)
    {

        try {

            $id = request()->id;

            $client = new Client();
            $headers = [
                'Authorization' => "Bearer {$this->authToken}",
            ];
            $request = new Psr7Request('GET', "{$this->endPointUrl}/checkouts/{$id}/payment?entityId={$this->entityId}", $headers);
            $res = $client->sendAsync($request)->wait();
            $responseJson = $res->getBody()->getContents();
            $responseArray = json_decode($responseJson, true);

            $order_id = session()->get($this->order_session_key, null);
            session()->forget($this->order_session_key);

            $user = auth()->user();

            $order = Order::where('id', $order_id)
                ->where('user_id', $user->id)
                ->first();
            $pattern = "/^(000.000.|000.100.1|000.[36]|000.400.[1][12]0)/";

            (isset($responseArray["result"]) && preg_match($pattern, $responseArray["result"]["code"])) ? $order->update(['status' => Order::$paying]) : $order->update(['status' => Order::$fail]);

            return $order;

        } catch (\Exception $exception) {

        }
    }


}
