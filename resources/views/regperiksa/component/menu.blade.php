<div>
    <ul aria-labelledby="dropdownSubMenu1{{ $key }}" class="dropdown-menu border-0 shadow">
        <li><a href="#" class="dropdown-item">Some other action</a></li>
        {{-- <li class="dropdown-divider"></li>
        <li data-toggle="modal" data-target="#BillingPasien">
            <button href="#" class="dropdown-item"
                wire:click="SetmodalBilling('{{ $key }}','{{ $item->no_rawat }}', '{{$item->status_lanjut}}')">Billing (Read Only)</button>
        </li> --}}
        <li class="dropdown-divider"></li>
        {{-- LAB --}}
        <li class="dropdown-submenu dropdown-hover">
            <a id="dropdownSubMenuLab2" href="#" role="button" data-toggle="dropdown" aria-haspopup="true"
                aria-expanded="false" class="dropdown-item dropdown-toggle">Lab</a>
            <ul aria-labelledby="dropdownSubMenuLab2" class="dropdown-menu border-0 shadow">
                <li>
                    <form action="{{ url('bridging-lis-lab') }}" method="">
                        @csrf
                        <input hidden type="text" name="no_rawat" value="{{ $item->no_rawat }}">
                        <input hidden type="text" name="status_lanjut" value="{{ $item->status_lanjut }}">
                        <input hidden type="text" name="nm_pasien" value="{{ $item->nm_pasien }}">
                        <button class="dropdown-item" type="submit">Bridging LIS</a>
                    </form>
                </li>
            </ul>
        </li>
    </ul>
</div>
