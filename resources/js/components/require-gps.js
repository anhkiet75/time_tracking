export default function requireJsFormComponent({ state }) {
    return {
        state,
        handleOnClick: function () {
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
                console.log("Unable to retrieve your location.");
            }
        },
    };
}
