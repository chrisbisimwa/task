@php $editing = isset($task) @endphp

<div class="row">
    <x-inputs.group class="col-sm-12">
        <x-inputs.select name="employee_id" label="Employee" required>
            @php $selected = old('employee_id', ($editing ? $task->employee_id : '')) @endphp
            <option disabled {{ empty($selected) ? 'selected' : '' }}>Please select the Employee</option>
            @foreach($employees as $value => $label)
            <option value="{{ $value }}" {{ $selected == $value ? 'selected' : '' }} >{{ $label }}</option>
            @endforeach
        </x-inputs.select>
    </x-inputs.group>

    <x-inputs.group class="col-sm-12">
        <x-inputs.text
            name="name"
            label="Name"
            :value="old('name', ($editing ? $task->name : ''))"
            maxlength="255"
            placeholder="Name"
            required
        ></x-inputs.text>
    </x-inputs.group>

    <x-inputs.group class="col-sm-12">
        <x-inputs.textarea
            name="description"
            label="Description"
            maxlength="255"
            >{{ old('description', ($editing ? $task->description : ''))
            }}</x-inputs.textarea
        >
    </x-inputs.group>

    <x-inputs.group class="col-sm-12">
        <x-inputs.select name="status" label="Status">
            @php $selected = old('status', ($editing ? $task->status : 'pending')) @endphp
            <option value="pending" {{ $selected == 'pending' ? 'selected' : '' }} >Pending</option>
            <option value="in_progress" {{ $selected == 'in_progress' ? 'selected' : '' }} >In progress</option>
            <option value="done" {{ $selected == 'done' ? 'selected' : '' }} >Done</option>
        </x-inputs.select>
    </x-inputs.group>

    <x-inputs.group class="col-sm-12">
        <x-inputs.text
            name="due_week"
            label="Due Week"
            :value="old('due_week', ($editing ? $task->due_week : ''))"
            maxlength="255"
            placeholder="Due Week"
            required
        ></x-inputs.text>
    </x-inputs.group>
</div>
