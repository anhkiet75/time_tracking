<script>
    function handleOnClick() {
        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(
                successFunction,
                errorFunction
            );
        } else {
            console.log("Geolocation is not supported by this browser.");
        }

        function successFunction(position) {
            console.log(position);
        }

        function errorFunction() {
            alert('Please reset your Location permission')
        }
    }
</script>

<x-dynamic-component :component="$getFieldWrapperView()">
    <div class="w-30 ms-auto">
        <x-filament::button x-on:click="handleOnClick()" icon="heroicon-o-map-pin">
            Allow GPS
        </x-filament::button>
    </div>

</x-dynamic-component>
