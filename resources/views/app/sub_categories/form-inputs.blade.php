@php $editing = isset($subCategory) @endphp

<div class="card mb-4">
    <div class="card-header">
        <h5 class="card-title mb-0">Form Sub Kategori</h5>
    </div>
    <div class="card-body">
        <div class="row gy-4">
            {{-- Category Select --}}
            <x-inputs.group class="col-sm-12">
                <x-inputs.select name="category_id" label="Category" class="form-select select2" required>
                    @php 
                        $selected = old('category_id', $editing ? $subCategory->category_id : $categoryId) 
                    @endphp
                    <option disabled {{ empty($selected) ? 'selected' : '' }}>
                        Please select the Category
                    </option>
                    @foreach($categories as $value => $label)
                        <option value="{{ $value }}" {{ $selected == $value ? 'selected' : '' }}>
                            {{ $label }}
                        </option>
                    @endforeach
                </x-inputs.select>
            </x-inputs.group>

            {{-- Name Input --}}
            <x-inputs.group class="col-sm-12">
                <x-inputs.text
                    name="name"
                    label="Name"
                    :value="old('name', $editing ? $subCategory->name : '')"
                    maxlength="255"
                    placeholder="Name"
                    required>
                </x-inputs.text>
            </x-inputs.group>
        </div>
    </div>
</div>
