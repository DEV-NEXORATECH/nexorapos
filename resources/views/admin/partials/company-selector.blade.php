{{-- Company selector for Super Admin — auto-included in POS Master forms --}}
@if(auth()->user()?->isSuperAdmin())
<div class="col-md-6">
    <div class="form-group">
        <label for="company_id">Company *</label>
        <select name="company_id" id="company_id" class="form-control select2" required>
            <option value="">-- Pilih Company --</option>
            @foreach(\App\Models\Company::orderBy('name')->get() as $company)
                <option value="{{ $company->id }}" @selected(old('company_id', $selectedCompanyId ?? '') == $company->id)>
                    {{ $company->name }} — {{ $company->company_code }}
                </option>
            @endforeach
        </select>
        @error('company_id')<div class="nexora-field-error">{{ $message }}</div>@enderror
    </div>
</div>
@endif
