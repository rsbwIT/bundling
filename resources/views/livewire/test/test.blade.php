<div>
    <button wire:click='generatePdfToBase64'>
        Test generate
    </button>
    <button wire:click="decodeBade64('{{$test}}')">
        Test decode
    </button>

    <br>
    {{$test}}
</div>
