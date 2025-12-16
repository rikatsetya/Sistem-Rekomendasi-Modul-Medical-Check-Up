@php $editing = isset($permission) @endphp

<div class="row">
    <x-inputs.group class="col-sm-12">
        <x-inputs.text
            name="name"
            label="Name"
            :value="old('name', ($editing ? $permission->name : ''))"
            placeholder="Permission Name"
            icon="icon-base ti tabler-id-badge"
            required
        ></x-inputs.text>
    </x-inputs.group>

    <div class="form-group col-sm-12 mt-4">
        <h4>Assign @lang('crud.roles.name')</h4>

        <div class="row">
            @foreach ($roles as $role)
                <div class="col-md-4 mb-4">
                    <div class="form-check custom-option custom-option-basic">
                        <label class="form-check-label custom-option-content w-100" for="role{{ $role->id }}">
                            <input
                                type="checkbox"
                                class="form-check-input"
                                name="roles[]"
                                id="role{{ $role->id }}"
                                value="{{ $role->id }}"
                                {{ isset($permission) && $role->hasPermissionTo($permission) ? 'checked' : '' }}
                            />
                            <span class="custom-option-header">
                                <span class="h6 mb-0">{{ ucfirst($role->name) }}</span>
                                <small class="text-body-secondary">ID: {{ $role->id }}</small>
                            </span>
                        </label>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>
