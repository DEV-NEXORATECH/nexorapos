<?php
namespace App\Services\Pos;
use App\Models\Pos\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
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
        if (request()->ajax()) {
            $data = Customer::with(["company", "sales"]);
            return \Yajra\DataTables\Facades\DataTables::of($data)
                ->addColumn('code', function($row) { return $row->customer_code; })
                ->addColumn('name', function($row) { return $row->name; })
                ->addColumn('phone', function($row) { return $row->phone; })
                ->addColumn('email', function($row) { return $row->email; })
                ->addColumn('address', function($row) { return $row->address; })
                ->addColumn('transactions', function($row) { return $row->sales ? $row->sales->count() : 0; })
                ->addColumn('total_spend', function($row) { return "Rp " . number_format($row->sales ? $row->sales->sum("grand_total") : 0); })
                ->addColumn('company', function($row) { return $row->company ? $row->company->name : "-"; })
                ->addColumn('created_by', function($row) { return "-"; })
                ->addColumn('created_at', function($row) { return $row->created_at ? $row->created_at->format("d M Y H:i") : "-"; })
                ->addColumn('action', function($row) { return '<a href="'.url('admin-panel/customer/edit/'.$row->id).'" class="nexora-action-btn is-warning"><i class="fas fa-edit"></i></a><a href="'.url('admin-panel/customer/delete/'.$row->id).'" class="nexora-action-btn is-danger no-ajax" onclick="return confirm(\'Delete?\')"><i class="fas fa-trash"></i></a>'; })
                
                
                
                
                
                
                
                
                
                
                
                
                
                
                
                
                
                
                
                
                
                
                
                
                
                
                
                
                
                ->rawColumns(['action'])
                ->make(true);
        }
        return view('admin.pos.customer.listing');
    }

    public function add()
    {
        return view('admin.pos.customer.add');
    }

    public function apiStore(Request $request)
    {
        $companyId = auth()->user()?->company_id;

        if (! $companyId) {
            return response()->json([
                'success' => false,
                'message' => 'Akun belum terhubung ke company.',
            ], 422);
        }

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255', 'unique:customers,email'],
            'phone' => ['nullable', 'string', 'max:30'],
            'address' => ['nullable', 'string', 'max:500'],
        ]);

        $customer = Customer::create([
            'company_id' => $companyId,
            'customer_code' => $this->nextCustomerCode(),
            'name' => $data['name'],
            'email' => $data['email'] ?? null,
            'phone' => $data['phone'] ?? null,
            'address' => $data['address'] ?? null,
        ]);

        return response()->json([
            'success' => true,
            'customer' => $customer,
        ]);
    }

    private function nextCustomerCode(): string
    {
        do {
            $code = 'CUS-'.now()->format('YmdHis').'-'.Str::upper(Str::random(4));
        } while (Customer::where('customer_code', $code)->exists());

        return $code;
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
        $customer->company_id = (auth()->user()?->isSuperAdmin() && $request->company_id) ? $request->company_id : auth()->user()?->company_id;
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
        $customer = Customer::where('id', $id)->when(!auth()->user()?->isSuperAdmin(), fn($q) => $q->where('company_id', auth()->user()?->company_id))->first();
        if($customer)
        {
            return view('admin.pos.customer.update', compact('customer'));
        }else{
            return redirect()->back()->with('failed', 'Record Not Found or Unauthorized');
        }
    }

    public function show(Request $request, $id)
    {
        $customer = Customer::with('sales')->where('id', $id)->when(!auth()->user()?->isSuperAdmin(), fn($q) => $q->where('company_id', auth()->user()?->company_id))->first();
        if($customer)
        {
            return view('admin.pos.customer.show', compact('customer'));
        }else{
            return redirect()->back()->with('failed', 'Record Not Found or Unauthorized');
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
        $customer = Customer::where('id', $id)->when(!auth()->user()?->isSuperAdmin(), fn($q) => $q->where('company_id', auth()->user()?->company_id))->first();
        if (!$customer) {
            return redirect()->back()->with('failed', 'Record Not Found or Unauthorized');
        }
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
        $customer = Customer::where('id', $id)->when(!auth()->user()?->isSuperAdmin(), fn($q) => $q->where('company_id', auth()->user()?->company_id))->first();
        if($customer)
        {
            $customer->delete();
            return redirect()->back()->with('success', 'Record Deleted Successfully');
        }else{
            return redirect()->back()->with('failed', 'Record Not Found or Unauthorized');
        }
    }
}
