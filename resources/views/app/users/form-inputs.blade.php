@php $editing = isset($user) @endphp

<div class="row">
    {{-- LEFT SIDE --}}
    <div class="col-12 col-lg-8">
        {{-- Card 1: Informasi Utama --}}
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="card-title mb-0">Informasi Utama</h5>
            </div>
            <div class="card-body">
                {{-- Name --}}
                <div class="mb-4">
                    <x-inputs.text name="name" label="Name" 
                        :value="old('name', $editing ? $user->name : '')" 
                        maxlength="255"
                        placeholder="Enter your name"
                        icon="icon-base ti tabler-user" 
                        required />
                </div>

                {{-- Email --}}
                <div class="mb-4">
                    <x-inputs.email name="email" label="Email" 
                        :value="old('email', $editing ? $user->email : '')" 
                        maxlength="255"
                        placeholder="Enter email" />
                </div>

                {{-- Row for Peran and DOB --}}
                <div class="row">
                    <div class="col-md-6 mb-4">
                        <x-inputs.text name="peran" label="Peran" 
                            :value="old('peran', $editing ? $user->peran : '')" 
                            maxlength="255"
                            placeholder="Peran" />
                    </div>

                    <div class="col-md-6 mb-4">
                        <x-inputs.date name="dob" label="Date of Birth" 
                            :value="old('dob', $editing ? optional($user->dob)->format('Y-m-d') : '')" 
                            max="255" />
                    </div>
                </div>

                {{-- Alamat --}}
                <div class="mb-4">
                    <x-inputs.text name="alamat" label="Alamat" 
                        :value="old('alamat', $editing ? $user->alamat : '')" 
                        maxlength="255"
                        placeholder="Alamat" />
                </div>

                {{-- Row for Kopeg, Devisi, Gender --}}
                <div class="row">
                    <div class="col-md-4 mb-4">
                        <x-inputs.text name="kopeg" label="Kopeg" 
                            :value="old('kopeg', $editing ? $user->kopeg : '')" 
                            maxlength="255"
                            placeholder="Kopeg" />
                    </div>

                    <div class="col-md-4 mb-4">
                        <x-inputs.text name="devisi" label="Devisi" 
                            :value="old('devisi', $editing ? $user->devisi : '')" 
                            maxlength="255"
                            placeholder="Devisi" />
                    </div>

                    <div class="col-md-4 mb-4">
                        <x-inputs.text name="gender" label="Gender" 
                            :value="old('gender', $editing ? $user->gender : '')" 
                            maxlength="255"
                            placeholder="Gender" />
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- RIGHT SIDE: Assign Roles --}}
    <div class="col-12 col-lg-4">
        <div class="card mb-4 h-100">
            <div class="card-header">
                <h5 class="card-title mb-0">Assign Roles</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    @foreach ($roles as $role)
                        <div class="col-12 mb-3">
                            <div class="form-check custom-option custom-option-basic">
                                <label class="form-check-label custom-option-content w-100"
                                    for="role{{ $role->id }}">
                                    <input type="checkbox" class="form-check-input" name="roles[]"
                                        id="role{{ $role->id }}" value="{{ $role->id }}"
                                        {{ isset($user) && $user->hasRole($role) ? 'checked' : '' }} />
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
    </div>
</div>