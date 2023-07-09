<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Invoice;

class GstVatServiceController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $invoice = Invoice::all();
        return view('admin.pages.gstvatservice', compact('invoice'));
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
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function storegstvate(Request $request)
    {
        // return $request;
        $request->validate([
            'type' => 'bail|required',
            'gst' => 'bail|required',
            'vat' => 'bail|required',
            'services_charges' => 'bail|required',
           
        ]);
        $invoice = new Invoice();
        $invoice->type = $request->type;
        $invoice->gst = $request->gst;
        $invoice->vat = $request->vat;
        $invoice->services_charges = $request->services_charges;
        $invoice->save();
        return redirect('admin/gst');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function showgstvat()
    {
        
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    // public function edit($id)
    // {
    //     $invoice = Invoice::find($id);
    //     return response()->json(['success' => true,'data' => $invoice], 200);
    // }
    public function edit($id)
    {
        $invoice = Invoice::find($id);
        // return view('admin.gstvatservices.edit',compact('invoice'));
        return response()->json(['success' => true,'data' => $invoice], 200);
        
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $invoice = Invoice::find($id);
        $invoice->type = $request->type;
        $invoice->gst = $request->gst;
        $invoice->vat = $request->vat;
        $invoice->services_charges = $request->services_charges;
        $invoice->save();
        //  return redirect('admin/gst');
        return response()->json(['success' => true,'data' => $invoice, 'msg' => 'gstvatservices edit'], 200);
    
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $invoice = Invoice::find($id);
        $invoice->delete();
        return redirect('admin/gst');
    }
}
