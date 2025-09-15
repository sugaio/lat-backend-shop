<?php

namespace App\Http\Controllers\Api;

use App\Models\Cart;
use App\Models\Invoice;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Midtrans\Snap;

class CheckoutController extends Controller
{
    protected $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
        // set config midtrans
        \Midtrans\Config::$serverKey = config('services.midtrans.serverKey');
        \Midtrans\Config::$isProduction = config('services.midtrans.isProduction');
        \Midtrans\Config::$isSanitized = config('services.midtrans.isSanitized');
        \Midtrans\Config::$is3ds = config('services.midtrans.is3ds');
    }

    public function store()
    {
        $snapToken = DB::transaction(function () {
            
            // buat nomor invoice
            $lenght = 10;
            $random = '';
            for($i = 0; $i < $lenght; $i++) {
                $random .= rand(0, 1) ? rand(0, 9) : chr(rand(ord('a'), ord('z')));
            }
            $no_invoice = 'INV-' . Str::upper($random); // INV-A92K8V23WE

            $total_weight = Cart::where('customer_id', Auth::user()->id)->sum('weight');
            $total_cart = Cart::where('customer_id', Auth::user()->id)->sum('price');

            $invoice = Invoice::create([
                'invoice' => $no_invoice,
                'customer_id' => Auth::user()->id,
                'courier' => $this->request->courier,
                'services' => $this->request->service,
                'cost_courier' => $this->request->cost_courier,
                'weight' => $total_weight,
                'name' => $this->request->name,
                'phone' => $this->request->phone,
                'address' => $this->request->address,
                'grand_total' => $total_cart + $this->request->cost_courier,
                'status' => 'pending',
            ]);

            // insert data cart ke order
            $carts = Cart::with('product')->where('customer_id', Auth::user()->id)->get();
            $orders = [];
            foreach ($carts as $cart) {
                $orders[] = [
                    'invoice_id' => $invoice->id,
                    'invoice' => $invoice->invoice,
                    'product_id' => $cart->product_id,
                    'product_name' => $cart->product->title,
                    'image' => $cart->product->image,
                    'qty' => $cart->quantity,
                    'price' => $cart->price
                ];

                $cart->product->decrement('stock', $cart->quantity);
            }

            $invoice->orders()->createMany($orders);

            Cart::where('customer_id', Auth::user()->id)->delete();

            // menyimpan data untuk membuat transaksi ke midtrans
            $payload = [
                'transaction_details' => [
                'order_id' => $invoice->invoice,
                'gross_amount' => $invoice->grand_total,
                ],
                'customer_details' => [
                    'firts_name' => $invoice->name,
                    'email' => Auth::user()->email,
                    'phone' => $invoice->phone,
                    'shipping_address' => $invoice->address,
                ]
            ];
            
            // send request snap token ke midtrans
            $snapTokenResult = Snap::getSnapToken($payload);
            $invoice->snap_token = $snapTokenResult;
            $invoice->save();

            return $snapTokenResult;
        });

        return response()->json([
            'success' => true,
            'message' => 'Checkout Successfully!',
            'snap_token' => $snapToken,
        ], 200);
    }

    public function notificationHandler(Request $request)
    {
        $payload = $request->getContent(); // midtrans mengirimkan data dalam format JSON
        $notification = json_decode($payload); 

        $validSignatureKey = hash("sha512", $notification->order_id, $notification->status_code . $notification->gross_amount . config('services.midtrans.serverKey'));

        if ($notification->signature_key != $validSignatureKey) {
            return response(['message' => 'Invalid Signature'], 403);
        }

        $transaction = $notification->transaction_status;
        $type = $notification->payment_type;
        $orderId = $notification->order_id;
        $fraud = $notification->fraud_status;

        $data_transaction = Invoice::where('invoice', $orderId)->first();

        if($transaction == 'capture') {
            if($type == 'credit_card') {
                if($fraud == 'challenge') {
                    // sistem midtrans mendeteksi penipuan
                    $data_transaction->update([
                        'status' => 'pending'
                    ]);
                } else {
                    $data_transaction->update([
                        'status' => 'success',
                    ]);
                }
            }
        } elseif ($transaction == 'settlement') {
            // untuk metode pembayran melalui tf, va, ewallet
            $data_transaction->update([
                'status' => 'success'
            ]);
        } elseif ($transaction = 'pending') {
            $data_transaction->update([
                'status' => 'pending'
            ]);
        } elseif ($transaction = 'deny') {
            $data_transaction->update([
                'status' => 'failed'
            ]);
        } elseif ($transaction = 'expire') {
            $data_transaction->update([
                'status' => 'failed'
            ]);
        } elseif ($transaction = 'cancel') {
            $data_transaction->update([
                'status' => 'failed'
            ]);
        }
    }
}
