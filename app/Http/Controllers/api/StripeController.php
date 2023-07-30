<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Stripe\Exception\ApiErrorException;

use Stripe\Customer;
use Stripe\Stripe;
use Stripe\Token;

use Stripe\Invoice; 
use Stripe\InvoiceItem;
use Stripe\PaymentIntent;
use Stripe\PaymentMethod;

use Stripe\Charge;

class StripeController extends Controller
{
    public function createCustomer()
    {
        // Get the Stripe Secret Key from your .env file
        // $stripeSecretKey = config('services.stripe.secret');

        // Set the Stripe API key
        // Stripe::setApiKey($stripeSecretKey);
        Stripe::setApiKey(env('STRIPE_SECRET'));

        $customerName = 'milan sarvaiya';
        $addressLine1 = 'addressLine1';
        $addressLine2 = 'addressLine2';
        $city = 'rajkot';
        $state = 'gujrat';
        $postalCode = '340001';
        $country = 'india';

        // Create the customer in Stripe
        /*$customer = Customer::create([
            'email' => 'milansarvaiya702@gmail.com', // Replace with the customer's email
            // Add any other relevant customer data
        ]);*/

        $customer = Customer::create([
                    'name' => $customerName,
                    'address' => [
                        'line1' => $addressLine1,
                        'line2' => $addressLine2,
                        'city' => $city,
                        'state' => $state,
                        'postal_code' => $postalCode,
                        'country' => $country,
                    ],
                ]);


        // Save the customer_id in your database for future reference
        $customer_id = $customer->id;

        var_dump($customer_id);

        // You can now use $customer_id to associate the customer with other data in your application
        // For example, you can save it in the users table if you have a relationship between users and customers.



        // cus_OIaPIThvXKSme1


        // cus_OIb25mm18C3NLM
    }

    public function generateCardToken(Request $request)
    {
        

         Stripe::setApiKey(env('STRIPE_KEY'));

        try {
            // Create a card token
             $token = Token::create([
                'card' => [
                    'number' => $request->number,
                    'exp_month' => $request->exp_month,
                    'exp_year' => $request->exp_year,
                    'cvc' => $request->cvc,
                ],
            ]);

            var_dump($token);

            /*card ['id'] */


            // The card token is now available as $cardToken->id
            // return response()->json(['card_token' => $cardToken->id]);

        } catch (\Exception $e) {
            // Handle any exceptions or errors that occurred during the process
            // Log the error, return an error message, etc.
              var_dump($e->getMessage());
        }

        
        // tok_1NVzeSSD2IR8ArWU0SnkIQWD
        // card_1NVzeRSD2IR8ArWU5gPWVowr
    }

    public function createInvoiceAndPayWithDebitCard()
    {
        $customer_id = 'cus_OIaPIThvXKSme1';
        $amount = 1100;
        $card_id = 'card_1NVzkeSD2IR8ArWUGAWfRiUs';

        

        try {

            \Stripe\Stripe::setApiKey(env('STRIPE_SECRET'));

            // $customer = Customer::retrieve($customer_id);

               // Get the default payment method ID
              /* $defaultPaymentMethodId = $customer->invoice_settings->default_payment_method;

            Customer::update($customer_id, [
                'invoice_settings' => [
                    'default_payment_method' => $defaultPaymentMethodId,
                ],
            ]);*/

            \Stripe\Customer::update($customer_id, [
                       'invoice_settings' => [
                           'default_payment_method' => $card_id,
                       ],
                   ]);

            // Create an Invoice Item
            \Stripe\InvoiceItem::create([
                'customer' => $customer_id,
                'amount' => $amount,
                'currency' => 'usd',
                'description' => 'description',
            ]);

            // Create the Invoice
            $invoice = \Stripe\Invoice::create([
                'customer' => $customer_id,
            ]);

            // Pay the Invoice using the specified card
            $invoice->pay();

            echo "<pre>";
            print_r($invoice);
            die('st');


        } catch (\Exception $e) {
                    // Handle any exceptions or errors that occurred during the process
                    // Log the error, return an error message, etc.
                      var_dump($e->getMessage());
                }
    }

    public function card_chk(){
        // Verify if the card exists for the customer

          $customer_id = 'cus_OIaPIThvXKSme1';
        $card_id = 'card_1NVzkeSD2IR8ArWUGAWfRiUs';

         \Stripe\Stripe::setApiKey(env('STRIPE_SECRET'));

        try {
            $card = \Stripe\Customer::retrieveSource(
                $customer_id,
                $card_id,
                []
            );
        } catch (\Stripe\Exception\CardException $e) {
            // Handle card-related exceptions here
            echo 'Card Error: ' . $e->getMessage();
            return;
        }

        // Proceed to pay the invoice if the card exists
        try {
            $invoice->pay(['source' => $card_id]);
            var_dump($invoice);
        } catch (\Stripe\Exception\InvalidRequestException $e) {
            // Handle invalid request exceptions here
            echo 'Invalid Request Error: ' . $e->getMessage();
        } catch (\Stripe\Exception\AuthenticationException $e) {
            // Handle authentication exceptions here
            echo 'Authentication Error: ' . $e->getMessage();
        } catch (\Stripe\Exception\ApiConnectionException $e) {
            // Handle API connection exceptions here
            echo 'API Connection Error: ' . $e->getMessage();
        } catch (\Stripe\Exception\ApiErrorException $e) {
            // Handle generic API errors here
            echo 'API Error: ' . $e->getMessage();
        }
    }

   

    // tok_1NWiKoSD2IR8ArWUfAljq3xq

    // tok_1NWiNvSD2IR8ArWUcTAlJfPD
    public function Charge(Request $request)
    {
       /* $validator = Validator::make($request->all(), [
            'token' => 'required',
            'amount' => 'required|numeric',            
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }*/

        try { 
            Stripe::setApiKey(env('STRIPE_SECRET'));

            $charge = Charge::create([
                'amount' => (int) $request->amount * 100, // Amount in rs
                'currency' => 'usd',
                'source' => $request->token,
                "description" => "Test payment from MTDB"
            ]);

            echo "<pre>";
            print_r($charge);
            die('st');

            // return response()->json(['token' => $token->id, 'success' => true, 'msg' => 'Your payment was successfully paid'], 200);
        } catch (CardException $exception) {
            Log::error($exception->getMessage());
            return response()->json(['error' => 'Your payment is rejected'], 401);            
        }
        
    }

    public function createToken(Request $request)
    {
        /*$validator = Validator::make($request->all(), [
            'number' => 'required|numeric|digits:16',
            'exp_month' => 'required|numeric|between:1,12',
            'exp_year' => 'required|numeric|digits:4',
            'cvc' => 'required|numeric|digits_between:3,4',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }*/

        try {            
            Stripe::setApiKey(env('STRIPE_KEY'));
            // Create a token
            $token = Token::create([
                'card' => [
                    'number' => $request->number,
                    'exp_month' => $request->exp_month,
                    'exp_year' => $request->exp_year,
                    'cvc' => $request->cvc,
                ],
            ]);
            return response()->json(['token' => $token->id, 'success' => true, 'msg' => 'Token created successfully'], 200);
        } catch (CardException $exception) {
            Log::error($exception->getMessage());
            return response()->json(['error' => 'card is invalid'], 401);            
        }
        
    }
    

    public function createPaymentMethod(Request $request)
    {
        // Set your Stripe secret key
        Stripe::setApiKey(env('STRIPE_SECRET'));

        try {
            $paymentMethod = PaymentMethod::create([
                'type' => 'card',
               /* 'card' => [
                    'number' => '4000003560000008',
                    'exp_month' => 12,
                    'exp_year' => 2034,
                    'cvc' => '314',
                ],*/
                 'card' => [
                    'token' => 'tok_1NWjEqSD2IR8ArWU11iOUgsK',
                ],
            ]);

            return response()->json(['message' => 'Payment method created successfully', 'paymentMethod' => $paymentMethod]);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Error creating payment method', 'error' => $e->getMessage()], 500);
        }
    }

    // pm_1NWjBBSD2IR8ArWUG5n3EU7O

    public function PaymentIntent(Request $request){

          Stripe::setApiKey(env('STRIPE_SECRET'));

        /*$customer = Customer::retrieve('cus_OIb25mm18C3NLM');
        $defaultPaymentMethodId = $customer->invoice_settings->default_payment_method;

        var_dump($defaultPaymentMethodId);
        die('ss');
        */
        Stripe::setApiKey(env('STRIPE_SECRET'));
        $paymentIntent = PaymentIntent::create([
            'amount' => 1500, // Replace with the actual amount in cents (e.g., 1000 for $10.00)
            'currency' => 'inr',
            /*'customer_id' => 'cus_OIb25mm18C3NLM',*/
            /*'source' => $request->token,*/
            'payment_method' => 'pm_1NWjF3SD2IR8ArWU9wmZ6zA8',

        ]);


        var_dump($paymentIntent);

       
    }

    public function new_one(Request $request)
    {        
        try {            
            Stripe::setApiKey(env('STRIPE_KEY'));
            // Stripe::setApiKey(env('STRIPE_SECRET'));
       
            $token = Token::create([
                'card' => [
                    'number' => $request->number,
                    'exp_month' => $request->exp_month,
                    'exp_year' => $request->exp_year,
                    'cvc' => $request->cvc,
                ],
            ]);

            $paymentMethod = PaymentMethod::create([
                'type' => 'card',
                 'card' => [
                    'token' => $token->id,
                ],
            ]);
            var_dump($paymentMethod->id);

          /*  $paymentIntent = PaymentIntent::create([
                'amount' => 1600, 
                'currency' => 'inr',
                'payment_method' => $paymentMethod->id,
                
            ]);*/

            /*$paymentIntent = PaymentIntent::retrieve($paymentIntentId->id);

               $paymentIntent->confirm([
                   'payment_method' => 'pm_card_visa',
               ]);*/


            // var_dump($paymentIntent);


            // return response()->json(['token' => $token->id, 'success' => true, 'msg' => 'Token created successfully'], 200);
        } catch (CardException $exception) {
            Log::error($exception->getMessage());
            return response()->json(['error' => 'card is invalid'], 401);            
        }
        
    }

    public function second_one(Request $request)
    {       


        try {      
             Stripe::setApiKey(env('STRIPE_SECRET'));
             $paymentIntent = PaymentIntent::create([
                 'amount' => 1700, // Replace with the actual amount in cents (e.g., 1000 for $10.00)
                 'currency' => 'inr',
                 'payment_method' => $request->payment_method,
             ]);

             // pm_1NWtyMSD2IR8ArWU4sTEyxu3

             // $paymentIntent = PaymentIntent::retrieve($paymentIntent->id);

            // $paymentIntent->confirm();

            // $paymentIntent->confirm();

             // Retrieve the PaymentIntent using its ID
             // paymentIntent.requires_action

             $paymentIntentId = $paymentIntent->id;
             $paymentIntent2 = PaymentIntent::retrieve($paymentIntentId);

             // Confirm the PaymentIntent
             $paymentIntent2->confirm();



            /*[
                   'payment_method' => 'pm_card_visa',
               ]*/

               echo "<pre>";
             print_r($paymentIntent2);
             die('stop');
        }  catch (\Exception $e) {
                 return response()->json(['message' => 'Error creating payment method', 'error' => $e->getMessage()], 500);     
               }
        
    }

    
  



}
