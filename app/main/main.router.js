app.config(function($stateProvider, $urlRouterProvider) {

    $urlRouterProvider.otherwise('/welcome');

    $stateProvider
        .state('welcome', {
            url: '/welcome',
            templateUrl: 'app/template/welcome.html'
        })
        .state('about', {
            url: '/about',
            templateUrl: 'app/template/about.html'
        })
        .state('service', {
            url: '/service',
            templateUrl: 'app/template/service.html'
        });

});