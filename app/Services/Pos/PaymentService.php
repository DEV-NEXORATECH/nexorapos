<?php
namespace App\Services\Pos;
use Illuminate\Http\Request;
use App\Models\Pos\Payment;
use App\Models\Pos\Sale;
use App\Models\Pos\Method;
use App\Pos\Payment\PaymentStoreValidation;
class PaymentService{
    public $payment;
    public function __construct()
    {
        return $this->payment=new PaymentStoreValidation();
    }
    public function index()
    {
        $payments=Payment::with('sale', 'method')->get();
        return view('admin.pos.payment.listing', compact('payments'));
    }

    public function add()
    {
        $sales=Sale::all();
        $methods=Method::all();
        return view('admin.pos.payment.add', compact('sales', 'methods'));
    }

    public function store(Request $request)
    {
        try{
            $inputs=$request->all();
            $errorMessage=$this->payment->validate($inputs);
            if($errorMessage){
                return redirect()->back()->with('failed', $errorMessage);
            }

            $payment=new Payment();
            $payment->sale_id=$request->sale_id;
            $payment->method_id=$request->method_id;
            $payment->amount=$request->amount;
            $payment->reference=$request->reference;
            $payment->save();
            if($payment){
                return redirect()->to('admin-panel/payments/listing')->with('success', 'Payment Submitted Successfully');
            }
        }catch(\Exception $e)
        {
            return redirect()->back()->with('failed', $e->getMessage());
        }
    }

    public function edit($id)
    {
        $sales=Sale::all();
        $methods=Method::all();
        $payment=Payment::find($id);
        if($payment)
        {
            return view('admin.pos.payment.update', compact('payment', 'sales', 'methods'));
        }

        return redirect()->back()->with('failed', 'Record Not Found');
    }

    public function update(Request $request,$id)
    {
        try{
            $payment=Payment::find($id);
            if($payment)
            {
                $inputs=$request->all();
                $errorMessage=$this->payment->validate($inputs);
                if($errorMessage){
                    return redirect()->back()->with('failed', $errorMessage);
                }
            }

            $payment->sale_id=$request->sale_id;
            $payment->method_id=$request->method_id;
            $payment->amount=$request->amount;
            $payment->reference=$request->reference;
            $payment->save();
            if($payment){
                return redirect()->to('admin-panel/payments/listing')->with('success', 'Record Updated Successfully');
            }
        }catch(\Exception $e)
        {
            return redirect()->back()->with('failed', $e->getMessage());
        }
    }

    public function destroy($id)
    {
        $payment=Payment::find($id);
        if($payment){
             $payment->delete();
            return redirect()->back()->with('success', 'Record Deleted SuccessFuly');
        }

        return redirect()->back()->with('failed', 'Record Not FOund');
    }
}
