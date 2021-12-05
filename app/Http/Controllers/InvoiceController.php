<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class InvoiceController extends Controller
{

    protected $user;


    public function __construct()
    {
        $this->user = Auth::user();

    }//end __construct()


    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $validator = Validator::make(
            $request->all(),
            [
                'filter_date_1' => 'required|date',
                'filter_date_2' => 'required|date'
            ]
        );

        if($validator->fails()){
            return response()->json(
                [
                    'status' => false,
                    'errors' => $validator->errors()
                ],
                400
            );
        }

        $invoces = Invoice::whereBetween('date', [$request->filter_date_1, $request->filter_date_2]) 
        ->where(function($query){
            $query->where('added_by', '=', Auth::user()->id)
            ->orWhere('send_to', '=', Auth::user()->id) ;
        })
        ->get();

        return response()->json($invoces->toArray());
    }

   

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        
        $validator = Validator::make(
            $request->all(),
            [
                'description' => 'required|string',
                'date' => 'required|date',
                'send_to' => 'int|exists:users,id',
                'currency' => 'required|string',
                'total' => 'required|numeric',
                'tax_rate' => 'required|numeric',
            ]
        );

        if($validator->fails()){
            return response()->json(
                [
                    'status' => false,
                    'errors' => $validator->errors()
                ],
                400
            );
        }

        // check taxt amount and grand total
        $validator2 = Validator::make(
            $request->all(),
            [
                'tax_amount' => 'required|numeric|in:'.($request->total*$request->tax_rate/100),
                'grand_total' => 'required|numeric|in:'.($request->total*$request->tax_rate/100 + $request->total)
            ]
        );

        if($validator2->fails()){
            return response()->json(
                [
                    'status' => false,
                    'errors' => 'Tax amount or grand total is not correct!'
                ],
                400
            );
        }




        $invoice = new Invoice();
        $invoice->description = $request->description;
        $invoice->date = $request->date;
        $invoice->send_to = $request->send_to;
        $invoice->currency = $request->currency;
        $invoice->total = $request->total;
        $invoice->tax_rate = $request->tax_rate;
        $invoice->tax_amount = $request->tax_amount;
        $invoice->grand_total = $request->grand_total;
   
        if (Auth::user()->invoices('added_by')->save($invoice)) {
            return response()->json(
                [
                    'status' => true,
                    'invoice'   => $invoice,
                ]
            );
        } else {
            return response()->json(
                [
                    'status'  => false,
                    'message' => 'Oops, the invoice could not be saved.',
                ]
            );
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Invoice  $invoice
     * @return \Illuminate\Http\Response
     */
    public function show(Invoice $invoice)
    {
        if($invoice->send_to==Auth::user()->id || $invoice->added_by==Auth::user()->id){
            return response()->json([
                'status'=>true,
                'invoice'=>$invoice
            ]);
        }else{
            return response()->json(
                [
                    'status' => false,
                    'errors' => "Oops, you do not have the authority!"
                ],
                400
            );
        }

    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Invoice  $invoice
     * @return \Illuminate\Http\Response
     */
    public function send(Invoice $invoice, Request $request)
    {
       
        
        $validator = Validator::make(
            $request->all(),
            [
                'send_to' => [                                                                  
                    'required',                                                            
                    Rule::exists('users', 'id')                     
                    ->where('type','<>', Auth::user()->type),                                                                    
                ],

            ]
        );

        if($validator->fails()){
            return response()->json(
                [
                    'status' => false,
                    'errors' => [
                        "send_to"=>"The user not found or type of the user is not different!"
                    ]
                ],
                400
            );
        }

     

        $invoice->send_to = $request->send_to;
      
        $invoice->save();
        if ($invoice->save()) {
            return response()->json(
                [
                    'status' => true,
                    'invoice'   => $invoice,
                ]
            );
        } else {
            return response()->json(
                [
                    'status'  => false,
                    'message' => 'Oops, the invoice could not be saved.',
                ]
            );
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Invoice  $invoice
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Invoice $invoice)
    {
        $validator = Validator::make(
            $request->all(),
            [
                'description' => 'required|string',
                'date' => 'required|date',
                'send_to' => [                                                                  
                    'required',                                                            
                    Rule::exists('users', 'id')                     
                    ->where('type','<>', Auth::user()->type),                                                                    
                ],
                'send_to' => 'int|exists:users,id,type,',
                'currency' => 'required|string',
                'total' => 'required|numeric',
                'tax_rate' => 'required|numeric',
            ]
        );

        if($validator->fails()){
            return response()->json(
                [
                    'status' => false,
                    'errors' => $validator->errors()
                ],
                400
            );
        }

        // check taxt amount and grand total
        $validator2 = Validator::make(
            $request->all(),
            [
                'tax_amount' => 'required|numeric|in:'.($request->total*$request->tax_rate/100),
                'grand_total' => 'required|numeric|in:'.($request->total*$request->tax_rate/100 + $request->total)
            ]
        );

        if($validator2->fails()){
            return response()->json(
                [
                    'status' => false,
                    'errors' => 'Tax amount or grand total is not correct!'
                ],
                400
            );
        }

        $invoice->description = $request->description;
        $invoice->date = $request->date;
        $invoice->send_to = $request->send_to;
        $invoice->currency = $request->currency;
        $invoice->total = $request->total;
        $invoice->tax_rate = $request->tax_rate;
        $invoice->tax_amount = $request->tax_amount;
        $invoice->grand_total = $request->grand_total;
        $invoice->save();
        if ($invoice->save()) {
            return response()->json(
                [
                    'status' => true,
                    'invoice'   => $invoice,
                ]
            );
        } else {
            return response()->json(
                [
                    'status'  => false,
                    'message' => 'Oops, the invoice could not be saved.',
                ]
            );
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Invoice  $invoice
     * @return \Illuminate\Http\Response
     */
    public function destroy(Invoice $invoice)
    {
        if ($invoice->delete($invoice)) {
            return response()->json(
                [
                    'status' => true,
                    'invoice'   => $invoice,
                ]
            );
        } else {
            return response()->json(
                [
                    'status'  => false,
                    'message' => 'Oops, the appointment could not be deleted.',
                ]
            );
        }
    }
}
