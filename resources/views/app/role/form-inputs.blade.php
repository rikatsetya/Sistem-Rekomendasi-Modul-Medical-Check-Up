@php $editing = isset($role) @endphp

<div class="row">
    <x-inputs.group class="col-sm-12">
        <x-inputs.text
            name="name"
            label="Name"
            :value="old('name', ($editing ? $role->name : ''))"
            placeholder="Role Name"
            icon="icon-base ti tabler-users-group"
            required
        ></x-inputs.text>
    </x-inputs.group>

    <div class="form-group col-sm-12 mt-4">
        <h4>Assign @lang('crud.permissions.name')</h4>
        <div class="row">
            @foreach ($permissions as $permission)
                <div class="col-md-4 mb-4">
                    <div class="form-check custom-option custom-option-basic">
                        <label class="form-check-label custom-option-content w-100"
                            for="permission{{ $permission->id }}">
                            <input class="form-check-input" type="checkbox" id="permission{{ $permission->id }}"
                                name="permissions[]" value="{{ $permission->id }}"
                                {{ isset($role) && $role->hasPermissionTo($permission) ? 'checked' : '' }} />
                            <span class="custom-option-header">
                                <span class="h6 mb-0">{{ ucfirst($permission->name) }}</span>
                                <small class="text-body-secondary">ID: {{ $permission->id }}</small>
                            </span>
                        </label>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>
