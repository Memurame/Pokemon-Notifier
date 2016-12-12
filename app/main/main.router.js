app.config(function($stateProvider, $urlRouterProvider) {

    $urlRouterProvider.otherwise('/welcome');

    $stateProvider
        .state('welcome', {
            url: '/welcome',
            templateUrl: 'template/welcome.html'
        })
        .state('about', {
            url: '/about',
            templateUrl: 'template/about.html'
        })
        .state('service', {
            url: '/service',
            templateUrl: 'template/service.html'
        });

});