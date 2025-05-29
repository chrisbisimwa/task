@php $editing = isset($accessToken) @endphp

<div class="row">
    <x-inputs.group class="col-sm-12">
        <x-inputs.select name="employee_id" label="Employee" required>
            @php $selected = old('employee_id', ($editing ? $accessToken->employee_id : '')) @endphp
            <option disabled {{ empty($selected) ? 'selected' : '' }}>Please select the Employee</option>
            @foreach($employees as $value => $label)
            <option value="{{ $value }}" {{ $selected == $value ? 'selected' : '' }} >{{ $label }}</option>
            @endforeach
        </x-inputs.select>
    </x-inputs.group>

    <x-inputs.group class="col-sm-12">
        <x-inputs.text
            name="token"
            label="Token"
            :value="old('token', ($editing ? $accessToken->token : ''))"
            maxlength="255"
            placeholder="Token"
            required
        ></x-inputs.text>
    </x-inputs.group>

    <x-inputs.group class="col-sm-12">
        <x-inputs.date
            name="expires_at"
            label="Expires At"
            value="{{ old('expires_at', ($editing ? optional($accessToken->expires_at)->format('Y-m-d') : '')) }}"
            max="255"
            required
        ></x-inputs.date>
    </x-inputs.group>
</div>
