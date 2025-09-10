<x-app-layout>
    <x-slot name="header">
        <h2 class="fw-bold">Dashboard</h2>
    </x-slot>

    <div class="row g-4 mb-4">
        {{-- Card Usuários --}}
        <div class="col-md-6 col-lg-3">
            <div class="card shadow-sm border-0">
                <div class="card-body">
                    <h6 class="text-muted">Usuários</h6>
                    <h3 class="fw-bold">120</h3>
                    <small class="text-success">
                        <i class="bi bi-arrow-up"></i> +12% este mês
                    </small>
                </div>
            </div>
        </div>

        {{-- Card Pedidos --}}
        <div class="col-md-6 col-lg-3">
            <div class="card shadow-sm border-0">
                <div class="card-body">
                    <h6 class="text-muted">Pedidos</h6>
                    <h3 class="fw-bold">56</h3>
                    <small class="text-danger">
                        <i class="bi bi-arrow-down"></i> -5% este mês
                    </small>
                </div>
            </div>
        </div>

        {{-- Card Receita --}}
        <div class="col-md-6 col-lg-3">
            <div class="card shadow-sm border-0">
                <div class="card-body">
                    <h6 class="text-muted">Receita</h6>
                    <h3 class="fw-bold">R$ 12.300</h3>
                    <small class="text-success">
                        <i class="bi bi-arrow-up"></i> +8% este mês
                    </small>
                </div>
            </div>
        </div>

        {{-- Card Visitas --}}
        <div class="col-md-6 col-lg-3">
            <div class="card shadow-sm border-0">
                <div class="card-body">
                    <h6 class="text-muted">Visitas</h6>
                    <h3 class="fw-bold">8.430</h3>
                    <small class="text-success">
                        <i class="bi bi-arrow-up"></i> +20% este mês
                    </small>
                </div>
            </div>
        </div>
    </div>

    
</x-app-layout>
