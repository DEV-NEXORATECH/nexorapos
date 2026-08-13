<?php
namespace App\Services\Pos;
use Illuminate\Http\Request;
use App\Models\Pos\Payment;
use App\Models\Pos\Sale;
use App\Models\Pos\Method;
use App\Pos\Payment\PaymentStoreValidation;
use App\Support\AuditLogger;
use Illuminate\Support\Facades\Schema;
class PaymentService{
    public $payment;
    public function __construct()
    {
        return $this->payment=new PaymentStoreValidation();
    }
    public function index()
    {
        $payments = Payment::with('sale', 'method')
            ->where('company_id', auth()->user()?->company_id)
            ->latest()
            ->get();
        return view('admin.pos.payment.listing', compact('payments'));
    }

    public function add()
    {
        $companyId = auth()->user()?->company_id;
        $sales = Sale::where('company_id', $companyId)->get();
        $methods = Method::where('company_id', $companyId)->get();
        return view('admin.pos.payment.add', compact('sales', 'methods'));
    }

    public function store(Request $request)
    {
        try{
            $companyId = auth()->user()?->company_id;
            $inputs=$request->all();
            $errorMessage=$this->payment->validate($inputs);
            if($errorMessage){
                return redirect()->back()->with('failed', $errorMessage);
            }
            if (! Sale::whereKey($request->sale_id)->where('company_id', $companyId)->exists()) {
                return redirect()->back()->with('failed', 'Sale tidak valid untuk company ini.')->withInput();
            }
            if (! Method::whereKey($request->method_id)->where('company_id', $companyId)->exists()) {
                return redirect()->back()->with('failed', 'Payment method tidak valid untuk company ini.')->withInput();
            }

            $payment=new Payment();
            $payment->company_id = $companyId;
            $payment->sale_id=$request->sale_id;
            $payment->method_id=$request->method_id;
            $payment->amount=$request->amount;
            $payment->reference=$request->reference;
            if (Schema::hasColumn('payments', 'status')) {
                $payment->status='active';
            }
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
        $companyId = auth()->user()?->company_id;
        $sales = Sale::where('company_id', $companyId)->get();
        $methods = Method::where('company_id', $companyId)->get();
        $payment = Payment::where('company_id', $companyId)->find($id);
        if($payment)
        {
            return view('admin.pos.payment.update', compact('payment', 'sales', 'methods'));
        }

        return redirect()->back()->with('failed', 'Record Not Found or Unauthorized');
    }

    public function update(Request $request,$id)
    {
        try{
            $companyId = auth()->user()?->company_id;
            $payment = Payment::where('company_id', $companyId)->find($id);
            if(!$payment)
            {
                return redirect()->back()->with('failed', 'Record Not Found or Unauthorized');
            }

            $inputs=$request->all();
            $errorMessage=$this->payment->validate($inputs);
            if($errorMessage){
                return redirect()->back()->with('failed', $errorMessage);
            }
            if (! Sale::whereKey($request->sale_id)->where('company_id', $companyId)->exists()) {
                return redirect()->back()->with('failed', 'Sale tidak valid untuk company ini.')->withInput();
            }
            if (! Method::whereKey($request->method_id)->where('company_id', $companyId)->exists()) {
                return redirect()->back()->with('failed', 'Payment method tidak valid untuk company ini.')->withInput();
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
        $companyId = auth()->user()?->company_id;
        $payment = Payment::where('company_id', $companyId)->find($id);
        if($payment){
            $oldValue = $payment->only(['id', 'sale_id', 'method_id', 'amount', 'status']);
            if (Schema::hasColumn('payments', 'status')) {
                $payment->status='void';
            }
            if (Schema::hasColumn('payments', 'voided_at')) {
                $payment->voided_at=now();
            }
            $payment->save();
            AuditLogger::log('void', 'payments', $payment, $oldValue, $payment->only(['id', 'sale_id', 'method_id', 'amount', 'status']));
            return redirect()->back()->with('success', 'Payment voided successfully');
        }

        return redirect()->back()->with('failed', 'Record Not Found or Unauthorized');
    }
}
