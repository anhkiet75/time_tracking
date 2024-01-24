// resources/js/components/require-gps.js
function requireJsFormComponent({ state }) {
  return {
    state,
    handleOnClick: function() {
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
    }
  };
}
export {
  requireJsFormComponent as default
};
//# sourceMappingURL=data:application/json;base64,ewogICJ2ZXJzaW9uIjogMywKICAic291cmNlcyI6IFsiLi4vLi4vY29tcG9uZW50cy9yZXF1aXJlLWdwcy5qcyJdLAogICJzb3VyY2VzQ29udGVudCI6IFsiZXhwb3J0IGRlZmF1bHQgZnVuY3Rpb24gcmVxdWlyZUpzRm9ybUNvbXBvbmVudCh7IHN0YXRlIH0pIHtcclxuICAgIHJldHVybiB7XHJcbiAgICAgICAgc3RhdGUsXHJcbiAgICAgICAgaGFuZGxlT25DbGljazogZnVuY3Rpb24gKCkge1xyXG4gICAgICAgICAgICBpZiAobmF2aWdhdG9yLmdlb2xvY2F0aW9uKSB7XHJcbiAgICAgICAgICAgICAgICBuYXZpZ2F0b3IuZ2VvbG9jYXRpb24uZ2V0Q3VycmVudFBvc2l0aW9uKFxyXG4gICAgICAgICAgICAgICAgICAgIHN1Y2Nlc3NGdW5jdGlvbixcclxuICAgICAgICAgICAgICAgICAgICBlcnJvckZ1bmN0aW9uXHJcbiAgICAgICAgICAgICAgICApO1xyXG4gICAgICAgICAgICB9IGVsc2Uge1xyXG4gICAgICAgICAgICAgICAgY29uc29sZS5sb2coXCJHZW9sb2NhdGlvbiBpcyBub3Qgc3VwcG9ydGVkIGJ5IHRoaXMgYnJvd3Nlci5cIik7XHJcbiAgICAgICAgICAgIH1cclxuXHJcbiAgICAgICAgICAgIGZ1bmN0aW9uIHN1Y2Nlc3NGdW5jdGlvbihwb3NpdGlvbikge1xyXG4gICAgICAgICAgICAgICAgY29uc29sZS5sb2cocG9zaXRpb24pO1xyXG4gICAgICAgICAgICB9XHJcblxyXG4gICAgICAgICAgICBmdW5jdGlvbiBlcnJvckZ1bmN0aW9uKCkge1xyXG4gICAgICAgICAgICAgICAgY29uc29sZS5sb2coXCJVbmFibGUgdG8gcmV0cmlldmUgeW91ciBsb2NhdGlvbi5cIik7XHJcbiAgICAgICAgICAgIH1cclxuICAgICAgICB9LFxyXG4gICAgfTtcclxufVxyXG4iXSwKICAibWFwcGluZ3MiOiAiO0FBQWUsU0FBUix1QkFBd0MsRUFBRSxNQUFNLEdBQUc7QUFDdEQsU0FBTztBQUFBLElBQ0g7QUFBQSxJQUNBLGVBQWUsV0FBWTtBQUN2QixVQUFJLFVBQVUsYUFBYTtBQUN2QixrQkFBVSxZQUFZO0FBQUEsVUFDbEI7QUFBQSxVQUNBO0FBQUEsUUFDSjtBQUFBLE1BQ0osT0FBTztBQUNILGdCQUFRLElBQUksK0NBQStDO0FBQUEsTUFDL0Q7QUFFQSxlQUFTLGdCQUFnQixVQUFVO0FBQy9CLGdCQUFRLElBQUksUUFBUTtBQUFBLE1BQ3hCO0FBRUEsZUFBUyxnQkFBZ0I7QUFDckIsZ0JBQVEsSUFBSSxtQ0FBbUM7QUFBQSxNQUNuRDtBQUFBLElBQ0o7QUFBQSxFQUNKO0FBQ0o7IiwKICAibmFtZXMiOiBbXQp9Cg==
