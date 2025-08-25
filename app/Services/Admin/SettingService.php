<?php
namespace App\Services\Admin;
use App\Models\Setting;
use Illuminate\Http\Request;
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

            // Get all existing settings as key => value
            $existingSettings = Setting::pluck('value', 'key')->toArray();

            foreach ($inputs as $key => $value) {
                if ($key === '_token') continue;

                // Only update if user entered a value, otherwise keep existing
                if ($value !== null && $value !== '') {
                    Setting::updateOrCreate(
                        ['key' => $key],
                        ['value' => $value]
                    );
                }
                // If value is empty and key exists, do nothing (keep existing)
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
           $setting->key=$request->key;
           $setting->value=$request->value;
           $setting->save();
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
                $setting->delete();
                return redirect()->bacK()->with('success', 'Setting Deleted Successfully');
            }
        }catch(\Exception $e)
        {
            return redirect()->back()->with('failed', $e->getmessage());
        }
    }
}
