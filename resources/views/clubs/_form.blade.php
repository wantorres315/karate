<div class="grid grid-cols-1 md:grid-cols-2 gap-4">

    {{-- Nome do Clube --}}
    <div>
        <label class="block text-sm font-medium text-gray-700">Nome do Clube</label>
        <input type="text" name="name" value="{{ old('name', $club->name ?? '') }}"
               class="w-full border rounded-md px-3 py-2">
    </div>

    {{-- Sigla / Acronym --}}
    <div>
        <label class="block text-sm font-medium text-gray-700">Sigla</label>
        <input type="text" name="acronym" value="{{ old('acronym', $club->acronym ?? '') }}"
               class="w-full border rounded-md px-3 py-2">
    </div>

    {{-- Logo --}}
    <div class="md:col-span-2">
        <label class="block text-sm font-medium text-gray-700">Logo</label>
        <input type="file" name="logo" class="w-full border rounded-md px-3 py-2">
        @if(!empty($club->logo))
            <img src="{{ asset('storage/' . $club->logo) }}" alt="Logo do Clube" class="mt-2 h-20">
        @endif
    </div>

    {{-- Username FNKP --}}
    <div>
        <label class="block text-sm font-medium text-gray-700">Username FNKP</label>
        <input type="text" name="username_fnkp" value="{{ old('username_fnkp', $club->username_fnkp ?? '') }}"
               class="w-full border rounded-md px-3 py-2">
    </div>

    {{-- Username Password FNKP --}}
    <div>
        <label class="block text-sm font-medium text-gray-700">Senha FNKP</label>
        <input type="text" name="username_password_fnkp" value="{{ old('username_password_fnkp', $club->username_password_fnkp ?? '') }}"
               class="w-full border rounded-md px-3 py-2">
    </div>

    {{-- Certificate FNKP --}}
    <div>
        <label class="block text-sm font-medium text-gray-700">Certificado FNKP</label>
        <input type="text" name="certificate_fnkp" value="{{ old('certificate_fnkp', $club->certificate_fnkp ?? '') }}"
               class="w-full border rounded-md px-3 py-2">
    </div>

    {{-- Status Ano / status_year --}}
    <div>
        <label class="block text-sm font-medium text-gray-700">Ano de Status</label>
        <input type="text" name="status_year"  value="{{ old('status_year', $club->status_year ?? '') }}"
               class="w-full border rounded-md px-3 py-2">
    </div>

    {{-- Status --}}
    <div>
        <label class="block text-sm font-medium text-gray-700">Status</label>
        <select name="status" class="w-full border rounded-md px-3 py-2">
            <option value="active" selected {{ (old('status', $club->status ?? '') == 'active') ? 'selected' : '' }}>Ativo</option>
            <option value="inactive" {{ (old('status', $club->status ?? '') == 'inactive') ? 'selected' : '' }}>Inativo</option>
        </select>
    </div>

    {{-- Endereço --}}
    <div class="md:col-span-2">
        <label class="block text-sm font-medium text-gray-700">Endereço</label>
        <input type="text" name="address" value="{{ old('address', $club->address ?? '') }}"
               class="w-full border rounded-md px-3 py-2">
    </div>

    {{-- Postal Code --}}
    <div>
        <label class="block text-sm font-medium text-gray-700">Código Postal</label>
        <input type="text" name="postal_code" value="{{ old('postal_code', $club->postal_code ?? '') }}"
               class="w-full border rounded-md px-3 py-2">
    </div>

    {{-- Cidade --}}
    <div>
        <label class="block text-sm font-medium text-gray-700">Cidade</label>
        <input type="text" name="city" value="{{ old('city', $club->city ?? '') }}"
               class="w-full border rounded-md px-3 py-2">
    </div>

    {{-- Distrito --}}
    <div>
        <label class="block text-sm font-medium text-gray-700">Distrito</label>
        <input type="text" name="district" value="{{ old('district', $club->district ?? '') }}"
               class="w-full border rounded-md px-3 py-2">
    </div>

    {{-- Telemóvel --}}
    <div>
        <label class="block text-sm font-medium text-gray-700">Telemóvel</label>
        <input type="text" name="cell_number" value="{{ old('cell_number', $club->cell_number ?? '') }}"
               class="w-full border rounded-md px-3 py-2">
    </div>

    {{-- Telefone --}}
    <div>
        <label class="block text-sm font-medium text-gray-700">Telefone</label>
        <input type="text" name="phone_number" value="{{ old('phone_number', $club->phone_number ?? '') }}"
               class="w-full border rounded-md px-3 py-2">
    </div>

    {{-- Email --}}
    <div>
        <label class="block text-sm font-medium text-gray-700">Email</label>
        <input type="email" name="email" value="{{ old('email', $club->email ?? '') }}"
               class="w-full border rounded-md px-3 py-2">
    </div>

    {{-- Website --}}
    <div>
        <label class="block text-sm font-medium text-gray-700">Website</label>
        <input type="text" name="website" value="{{ old('website', $club->website ?? '') }}"
               class="w-full border rounded-md px-3 py-2">
    </div>

    {{-- Nome do Responsável --}}
    <div>
        <label class="block text-sm font-medium text-gray-700">Nome do Responsável</label>
        <input type="text" name="responsible_name" value="{{ old('responsible_name', $club->responsible_name ?? '') }}"
               class="w-full border rounded-md px-3 py-2">
    </div>

    {{-- Telemóvel do Responsável --}}
    <div>
        <label class="block text-sm font-medium text-gray-700">Telemóvel do Responsável</label>
        <input type="text" name="responsible_cell_number" value="{{ old('responsible_cell_number', $club->responsible_cell_number ?? '') }}"
               class="w-full border rounded-md px-3 py-2">
    </div>

    {{-- Telefone do Responsável --}}
    <div>
        <label class="block text-sm font-medium text-gray-700">Telefone do Responsável</label>
        <input type="text" name="responsible_telephone_number" value="{{ old('responsible_telephone_number', $club->responsible_telephone_number ?? '') }}"
               class="w-full border rounded-md px-3 py-2">
    </div>

    {{-- Cargo do Responsável --}}
    <div>
        <label class="block text-sm font-medium text-gray-700">Cargo do Responsável</label>
        <input type="text" name="responsible_position" value="{{ old('responsible_position', $club->responsible_position ?? '') }}"
               class="w-full border rounded-md px-3 py-2">
    </div>
</div>
