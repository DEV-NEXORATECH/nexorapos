<?php
namespace App\Services\Pos;
use App\Models\Pos\Customer;
use Illuminate\Http\Request;
use App\Pos\Customer\CustomerStoreValidation;
use App\Pos\Customer\CustomerUpdateValidation;
class CustomerService{

    public $customer;
    public $cUpdate;
    public function __construct()
    {
        $this->customer=new CustomerStoreValidation();
        $this->cUpdate=new CustomerUpdateValidation();
    }
    public function index()
    {
        $customers=Customer::all();
        return view('admin.pos.customer.listing', compact('customers'));
    }

    public function add()
    {
        return view('admin.pos.customer.add');
    }

   public function store(Request $request)
{
    try {
        $inputs = $request->all();
        $errorMessage = $this->customer->validate($inputs);

        if($errorMessage !== true) {
            return redirect()->back()->withErrors($errorMessage)->withInput();
        }
        $customer = new Customer();
        $customer->name = $request->name;
        $customer->email = $request->email;
        $customer->phone = $request->phone;
        $customer->address = $request->address;

        $customer->save();

        return redirect()->to('admin-panel/customer/listing')->with('success', 'Customer added successfully');

    } catch (\Exception $e) {
        return redirect()->back()->with('failed', $e->getMessage());
    }
}

    public function edit(Request $request, $id)
    {
        $customer=Customer::find($id);
        if($customer)
        {
            return view('admin.pos.customer.update', compact('customer'));
        }else{
            return redirect()->back()->with('failed', 'Record Not Found');
        }
    }

    public function update(Request $request,$id)
    {
        try {
        $inputs = $request->all();
        $errorMessage = $this->cUpdate->validate($inputs,$id);

        if($errorMessage !== true) {
            return redirect()->back()->withErrors($errorMessage)->withInput();
        }
        $customer=Customer::find($id);
        $customer->name = $request->name;
        $customer->email = $request->email;
        $customer->phone = $request->phone;
        $customer->address = $request->address;

        $customer->save();

        return redirect()->to('admin-panel/customer/listing')->with('success', 'Customer Updated successfully');

    } catch (\Exception $e) {
        return redirect()->back()->with('failed', $e->getMessage());
    }
}

    public function destroy($id)
    {
        $customer=Customer::find($id);
        if($customer)
        {
            $customer->delete();
            return redirect()->back()->with('success', 'Record Deleted Successfully');
        }else{
            return redirect()->back()->with('failed', "Record Not Found");
        }
    }
}
