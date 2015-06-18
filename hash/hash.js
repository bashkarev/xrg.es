var app = angular.module("appHash", []);


app.controller("HashController", function($scope) {
	$scope.plainText = window.location.hash.substr(1);
	$scope.hashes = [];
})


app.directive("plainText", function($http) {
	return {
		restrict: 'A',
		require: 'ngModel',
		link: function(scope, elem, attr, ctrl) {
			scope.$watch(attr.ngModel, function(value) {
				window.location.hash = value.substr(0, 1024*2);
				
				$http.get("hash.php?plain=" + encodeURIComponent(value.substr(0, 1024*2))).success(function(data) {
					scope.hashes = data;
				});
			})
		}
	}
});