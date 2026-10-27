{{-- Company selector for Super Admin — auto-included in POS Master forms --}}
@if(auth()->user()?->isSuperAdmin())
<div class="col-md-6 mb-3">
    <label class="nexora-input-label">Perusahaan / Tenant <span class="text-danger">*</span></label>
    <div class="nexora-input-wrap">
        <i class="fas fa-building input-icon"></i>
        <select name="company_id" id="company_id" class="form-control select2" required>
            <option value="">-- Pilih Company --</option>
            @foreach(\App\Models\Company::orderBy('name')->get() as $company)
                <option value="{{ $company->id }}" @selected(old('company_id', $selectedCompanyId ?? '') == $company->id)>
                    {{ $company->name }} — {{ $company->company_code }}
                </option>
            @endforeach
        </select>
    </div>
    @error('company_id')<div class="nexora-field-error"><i class="fa fa-circle-exclamation mr-1"></i>{{ $message }}</div>@enderror
</div>
@endif
