@php
    // Quebra a cor em até 2 partes (ex: "yellow_orange")
    $colors = isset($graduation) ? explode('_', $graduation->color) : [];
    $color1 = $colors[0] ?? '';
    $color2 = $colors[1] ?? '';
@endphp

@csrf

<div class="mb-4">
    <label class="block text-sm font-medium text-gray-700">Nome</label>
    <input type="text" name="name" value="{{ old('name', $graduation->name ?? '') }}" 
           class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm px-3 py-2 focus:outline-none focus:ring-red-500 focus:border-red-500 sm:text-sm">
    @error('name')
        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
    @enderror
</div>

<div class="mb-4">
    <label class="block text-sm font-medium text-gray-700">Cor da Faixa</label>
    <div class="flex items-center gap-3">
        <!-- Cor 1 -->
        <select id="color1" name="color1" onchange="updatePreview()" 
                class="border rounded-md px-4 py-3 text-base w-40">
            <option value="">Selecione...</option>
            <option value="white"  {{ old('color1', $color1) == 'white' ? 'selected' : '' }}>Branco</option>
            <option value="yellow" {{ old('color1', $color1) == 'yellow' ? 'selected' : '' }}>Amarelo</option>
            <option value="orange" {{ old('color1', $color1) == 'orange' ? 'selected' : '' }}>Laranja</option>
            <option value="green"  {{ old('color1', $color1) == 'green' ? 'selected' : '' }}>Verde</option>
            <option value="blue"   {{ old('color1', $color1) == 'blue' ? 'selected' : '' }}>Azul</option>
            <option value="brown"  {{ old('color1', $color1) == 'brown' ? 'selected' : '' }}>Marrom</option>
            <option value="black"  {{ old('color1', $color1) == 'black' ? 'selected' : '' }}>Preto</option>
            <option value="red"  {{ old('color1', $color1) == 'red' ? 'selected' : '' }}>Vermelho</option>
        </select>

        <!-- Cor 2 (opcional) -->
        <select id="color2" name="color2" onchange="updatePreview()" 
                class="border rounded-md px-4 py-3 text-base w-40">
            <option value="">Nenhuma</option>
            <option value="white"  {{ old('color2', $color2) == 'white' ? 'selected' : '' }}>Branco</option>
            <option value="yellow" {{ old('color2', $color2) == 'yellow' ? 'selected' : '' }}>Amarelo</option>
            <option value="orange" {{ old('color2', $color2) == 'orange' ? 'selected' : '' }}>Laranja</option>
            <option value="green"  {{ old('color2', $color2) == 'green' ? 'selected' : '' }}>Verde</option>
            <option value="blue"   {{ old('color2', $color2) == 'blue' ? 'selected' : '' }}>Azul</option>
            <option value="brown"  {{ old('color2', $color2) == 'brown' ? 'selected' : '' }}>Marrom</option>
            <option value="black"  {{ old('color2', $color2) == 'black' ? 'selected' : '' }}>Preto</option>
            <option value="red"  {{ old('color2', $color2) == 'red' ? 'selected' : '' }}>Vermelho</option>
        </select>

        <!-- preview -->
        <span id="preview" 
              style="display:inline-block; width:26px; height:26px; border-radius:50%; border:1px solid #000;"></span>
    </div>

    <small class="text-gray-500 block mt-1">
        Se quiser faixa mista, selecione duas cores (ex: Amarelo + Laranja).
    </small>
</div>

<div class="flex justify-end gap-2">
    <a href="{{ route('graduations.index') }}" 
       class="px-4 py-2 bg-gray-300 text-gray-800 rounded-md hover:bg-gray-400">Cancelar</a>
    <button type="submit" 
            class="px-4 py-2 text-white rounded-md hover:bg-red-700" style="background-color: #E62111;">
        Salvar
    </button>
</div>

<script>
function updatePreview() {
    let c1 = document.getElementById('color1').value;
    let c2 = document.getElementById('color2').value;
    let preview = document.getElementById('preview');

    if(c1 && c2 && c1 !== c2){
        preview.style.background = `linear-gradient(to right, ${c1} 50%, ${c2} 50%)`;
    } else {
        preview.style.background = c1 || c2 || 'transparent';
    }
}
updatePreview();
</script>
