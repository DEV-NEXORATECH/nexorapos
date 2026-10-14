<?php
namespace App\Services\Admin;
use App\Models\Company;
use App\Models\CompanyFeature;
use App\Models\Feature;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use App\Support\AuditLogger;
use App\Validations\Admin\Setting\SettingStoreValidation;
use App\Validations\Admin\Setting\SettingUpdateValidation;
class SettingService{

    public $settingStore;
    public $settingUpdate;
    public function __construct(){
        $this->settingStore=new SettingStoreValidation();
        $this->settingUpdate=new SettingUpdateValidation();
    }

public function index()
    {
        if (request()->ajax()) {
            $data = Setting::with([]);
            return \Yajra\DataTables\Facades\DataTables::of($data)
                ->addColumn('id', function($row) { return $row->id; })
                ->addColumn('key', function($row) { return $row->key; })
                ->addColumn('value', function($row) { return $row->value; })
                ->addColumn('company', function($row) { return $row->company ? $row->company->name : "-"; })
                ->addColumn('created_by', function($row) { return "-"; })
                ->addColumn('created_at', function($row) { return $row->created_at ? $row->created_at->format("d M Y H:i") : "-"; })
                ->addColumn('action', function($row) { return '<a href="'.url('admin-panel/settings/edit/'.$row->id).'" class="nexora-action-btn is-warning"><i class="fas fa-edit"></i></a><a href="'.url('admin-panel/settings/delete/'.$row->id).'" class="nexora-action-btn is-danger no-ajax" onclick="return confirm(\'Delete?\')"><i class="fas fa-trash"></i></a>'; })
                
                
                
                
                
                
                
                
                
                
                
                
                
                
                
                
                
                
                ->rawColumns(['action'])
                ->make(true);
        }
        return view('admin.setting.listing');
    }

    public function license()
    {
        $user = auth()->user();
        // License status must remain viewable even when an older deployment
        // has not run the feature/license migrations yet.
        $hasFeatureSchema = Schema::hasTable('features')
            && Schema::hasColumns('features', ['key', 'name', 'display_order']);
        $hasCompanyFeatureSchema = Schema::hasTable('company_features')
            && Schema::hasColumns('company_features', ['company_id', 'feature_key', 'is_enabled']);

        $features = $hasFeatureSchema
            ? Feature::orderBy('display_order')->orderBy('name')->get()
            : collect();
        $companies = $user?->isSuperAdmin()
            ? Company::orderBy('name')->get()
            : Company::where('id', $user?->company_id)->get();
        $companyFeatures = $hasCompanyFeatureSchema
            ? CompanyFeature::all()
            ->groupBy('company_id')
            ->map(fn ($items) => $items->keyBy('feature_key'))
            : collect();

        return view('admin.setting.license', compact('companies', 'features', 'companyFeatures'));
    }

    public function add()
    {
        $settings=Setting::all();
        return view('admin.setting.add', compact('settings'));
    }

    public function store(Request $request)
    {
        try {
            $inputs = $request->all();
            $errorMessage = $this->settingStore->validate($inputs);
            if ($errorMessage) {
                return redirect()->back()->with('failed', $errorMessage);
            }

            $user = auth()->user();
            $companyId = $user?->isSuperAdmin() ? null : $user?->company_id;

            foreach ($inputs as $key => $value) {
                if ($key === '_token') continue;

                if ($value !== null && $value !== '') {
                    $setting = Setting::updateOrCreate(
                        [
                            'key' => $key,
                            'company_id' => $companyId,
                        ],
                        [
                            'value' => $value,
                        ]
                    );
                    AuditLogger::log('upsert', 'settings', $setting, [], $setting->only(['id', 'key', 'company_id']));
                    Setting::forgetCachedValue($key);
                }
            }

            return redirect()->back()->with('success', 'Settings Added/Updated Successfully');
        } catch (\Exception $e) {
            return redirect()->back()->with('failed', $e->getMessage());
        }
    }

    public function edit(Request $request,$id){
        $setting=Setting::find($id);
        if($setting){
            return view('admin.setting.update', compact('setting'));
        }else{
            return redirect()->back()->with('failed', "Record Not Found");
        }
    }

    //for updating the record

    public function update(Request $request,$id)
    {
        try{
           $inputs=$request->all();
           $errorMessage=$this->settingUpdate->validate($inputs, $id);
           if($errorMessage){
            return redirect()->back()->with('failed', $errorMessage);
           }

           $setting=Setting::find($id);
           if (! $setting) {
            return redirect()->back()->with('failed', 'Setting not found.');
           }
           $oldKey = $setting->key;
           $oldValue = $setting->only(['id', 'key', 'value', 'company_id']);
           $setting->key=$request->key;
           $setting->value=$request->value;
           $setting->save();
           Setting::forgetCachedValue($oldKey);
           Setting::forgetCachedValue($setting->key);
           AuditLogger::log('update', 'settings', $setting, $oldValue, $setting->only(['id', 'key', 'value', 'company_id']));
           if($setting){
            return redirect()->to('admin-panel/settings/listing')->with('success', 'Setting Updated Successfully');
           }
        }catch(\Exception $e){
            return redirect()->back()->with('failed', $e->getMessage());
        }

    }

    //function for deleting
    public function destroy(Request $request,$id)
    {
        try{
            $setting=Setting::find($id);
            if($setting)
            {
                Setting::forgetCachedValue($setting->key);
                AuditLogger::log('delete', 'settings', $setting, $setting->only(['id', 'key', 'company_id']), []);
                $setting->delete();
                return redirect()->bacK()->with('success', 'Setting Deleted Successfully');
            }
        }catch(\Exception $e)
        {
            return redirect()->back()->with('failed', $e->getmessage());
        }
    }
}
