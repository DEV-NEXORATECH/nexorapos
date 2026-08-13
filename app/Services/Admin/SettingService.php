<?php
namespace App\Services\Admin;
use App\Models\Company;
use App\Models\CompanyFeature;
use App\Models\Feature;
use App\Models\Setting;
use Illuminate\Http\Request;
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
        $settings=Setting::all();
        return view('admin.setting.listing', compact('settings'));
    }

    public function license()
    {
        $user = auth()->user();
        $features = Feature::orderBy('display_order')->orderBy('name')->get();
        $companies = $user?->isSuperAdmin()
            ? Company::orderBy('name')->get()
            : Company::where('id', $user?->company_id)->get();
        $companyFeatures = CompanyFeature::all()
            ->groupBy('company_id')
            ->map(fn ($items) => $items->keyBy('feature_key'));

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
