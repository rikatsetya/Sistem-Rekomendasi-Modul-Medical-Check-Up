@php $editing = isset($category) @endphp

<div class="card mb-4">
    <div class="card-header">
        <h5 class="card-title mb-0">Form Kategori</h5>
    </div>
    <div class="card-body">
        <div class="row gy-4">
            <x-inputs.group class="col-sm-12">
                <x-inputs.text 
                    name="name" 
                    label="Name" 
                    :value="old('name', $editing ? $category->name : '')" 
                    maxlength="255" 
                    placeholder="Name"
                    required>
                </x-inputs.text>
            </x-inputs.group>

            <x-inputs.group class="col-sm-12">
                <x-inputs.text 
                    name="parent" 
                    label="Parent" 
                    :value="old('parent', $editing ? $category->parent : '')" 
                    maxlength="255" 
                    placeholder="Parent">
                </x-inputs.text>
            </x-inputs.group>
        </div>
    </div>
</div>
