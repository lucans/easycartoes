var SERVER_PATH = 'server/';

var app = angular.module('cartonline', ['ui.router','ngMask','720kb.datepicker','ngSanitize'])


.config(function($stateProvider, $urlRouterProvider) {
    $urlRouterProvider.otherwise("app/Modelos");
    $stateProvider        
        .state('menu', {
          url: "/app",
          templateUrl: "partials/menu.html",
          controller: "userCtrl"
        })
        .state('menu.modelos', {
          url: "/Modelos",
          templateUrl: "views/modelos.html",
          controller: "cartaoCtrl"
        })           
        .state('menu.contatos', {
          url: "/Contatos",
          templateUrl: "views/contatos.html",
          controller: "contatoCtrl"
        })       
        .state('menu.feed', {
          url: "/Feed",
          templateUrl: "views/feed.html",
          controller: "feedCtrl"
        }) 
        .state('menu.login', {
          url: "/Login",
          templateUrl: "partials/login.html",
          controller: "userCtrl"
        })    
}) 

.service('DateProvider', function () {

    this.date = new Date();
    this.payment = new Date();
    
    this.getHoje = function () {       
        var today = (this.date.getDate() < 10 ? '0' + (this.date.getDate()) : (this.date.getDate()));
        var today = today + "/" + (this.date.getMonth()+1 < 10 ? '0' + (this.date.getMonth()+1) : (this.date.getMonth()+1));
        var today = today + "/" + this.date.getFullYear();

        return today;
    }

    this.getAno = function () {
        return this.date.getFullYear();
    }
   
    this.dayOfWeek = function () {
        var weekDay = this.date.getDay();   

        switch(weekDay){
            case 1:
                return 'Segunda-Feira';
                break;         
            case 2:
                return 'Terça-Feira';
                break;         
            case 3:
                return 'Quarta-Feira';
                break;         
            case 4:
                return 'Quinta-Feira';
                break;         
            case 5:
                return 'Sexta-Feira';
                break;         
            case 6:
                return 'Sábado';
                break;         
            case 7:
                return 'Domingo';
                break;
            default:         
                return 'Hoje';
        }
    }
})


app.controller("userCtrl", ['$scope', '$http', '$rootScope','DateProvider','$location', function ($s, $http, $rs, Date, $location) {
    
    $s.userLogin = function(oUser){
    
        var classe = 'Usuario';
        $s.func = 'userLogin';

        $http.post(SERVER_PATH + "redirect.php?func=" + $s.func + "&c=" + classe, {
            oUser: oUser
        }).success(function(result){    
        console.log(result);   
            if (result.email) {
                $rs.usuario = result;
                $s.goRota("/Feed");
                $s.showToast("Bem vindo!");                      
            } else {
                $s.showToast("Usuário ou senha inválidos!");                      
            }          
        });

    }

    $s.verificaSession = function(){
        var classe = 'Usuario';
        $s.func = 'verificaUserSession';
        oUser = $rs.usuario;

        $http.post(SERVER_PATH + "redirect.php?func=" + $s.func + "&c=" + classe, {
            oUser: oUser
        }).success(function(result){  

            if (result.email == '') {
                $rs.usuario = result;    
                $s.showToast("Sessão expirada!");
                $s.goRota("/Login");
            }

        });
    }

    $s.verificaSession();

    $s.goRota = function(rota){ 
        if (rota) {
            $location.path(rota);
        }
    };

    $s.teste = function(){
        console.log($rs.usuario);
    }

    $s.showToast = function(message){        
        Materialize.toast(message, 3000);
    };
}]);

app.controller("cartaoCtrl", ['$scope', '$http', '$rootScope','DateProvider', function ($s, $http, $rs, Date) {
  
    $(document).ready(function(){
        $('.collapsible').collapsible({
          accordion : false
        });
    });

    $(document).ready(function() {
        $('select').material_select();
    });
      
    $(document).ready(function(){
        // the "href" attribute of .modal-trigger must specify the modal ID that wants to be triggered
        $('.modal').modal();
    });
      
          

    $rs.dia_semana = Date.dayOfWeek();

    $s.hoje = Date.getHoje();
    console.log($s.hoje);

    var classe = 'Cartao';

    $s.showToast = function(message){        
        Materialize.toast(message, 3000);
    };

    $s.goRota = function(rota){ 
        if (rota) {
            $location.path(rota);
        }
    };

    $s.abreModal = function(cartao){
        $s.cartao = cartao;        
        $('#enviarCartao').modal('open');
    }

    $s.getCartoesBig = function(){
        $s.func = 'getCartoes';

        $http.get(SERVER_PATH + "redirect.php?func=" + $s.func + "&c=" + classe + "&q=big").success(function(result){
            $s.cartoes_big = result;                         
        });
    };       

    $s.getCartoesMedium = function(){
        $s.func = 'getCartoes';

        $http.get(SERVER_PATH + "redirect.php?func=" + $s.func + "&c=" + classe + "&q=medium").success(function(result){
            $s.cartoes_medium = result;                         
        });
    };       

    $s.getCartoesSmall = function(){
        $s.func = 'getCartoes';

        $http.get(SERVER_PATH + "redirect.php?func=" + $s.func + "&c=" + classe + "&q=small").success(function(result){
            $s.cartoes_small = result;                         
        });
    };   

    $s.getCartoes1 = function(){
        $s.func = 'getCartoes';

        $http.get(SERVER_PATH + "redirect.php?func=" + $s.func + "&c=" + classe).success(function(result){
            $s.cartoes1 = result;                         
        });
    };   

    $s.getCartoes2 = function(){
        $s.func = 'getCartoes';

        $http.get(SERVER_PATH + "redirect.php?func=" + $s.func + "&c=" + classe).success(function(result){
            $s.cartoes2 = result;                         
        });
    };   

    $s.getCartoes3 = function(){
        $s.func = 'getCartoes';

        $http.get(SERVER_PATH + "redirect.php?func=" + $s.func + "&c=" + classe).success(function(result){
            $s.cartoes3 = result;                         
        });
    };   

    $s.getCartoes4 = function(){
        $s.func = 'getCartoes';

        $http.get(SERVER_PATH + "redirect.php?func=" + $s.func + "&c=" + classe).success(function(result){
            $s.cartoes4 = result;                         
        });
    };   

    $s.getCartoes1();
    $s.getCartoes2();
    $s.getCartoes3();
    $s.getCartoes4();

    $s.sendCartao = function(oCartao, oEnvio){       
        if (!oEnvio.de || !oEnvio.para || !oEnvio.email) { 
            $s.showToast('É preciso preencher todos os campos!');
            return; 
        }

        oEnvio.codmodelo = oCartao.cod;
        
        var classe = 'Generic';
        $s.func = 'insertEnvio';
        $http.post(SERVER_PATH + "redirect.php?func=" + $s.func + "&c=" + classe, {
            oEnvio: oEnvio
        }).success(function(result){       
            $s.showToast("Cartão enviado!");          
        });
    }     


    $s.updatePedido = function(oPedido){

        $s.func = 'updatePedido';
        $http.post(SERVER_PATH + "redirect.php?func=" + $s.func + "&c=" + classe, {
            oPedido: oPedido
        }).success(function(result){        

        });
    }   

    $s.deletePedido = function(oPedido){

        $s.func = 'deletePedido';

        $http.post(SERVER_PATH + "redirect.php?func=" + $s.func + "&c=" + classe, {
            oPedido: oPedido
        }).success(function(result){
            $s.getPedidos();        
            $s.showToast("Excluído!");   
        });

    }         

}]);


app.controller("contatoCtrl", ['$scope', '$http', '$rootScope','DateProvider', function ($s, $http, $rs, Date) {
  
    $(document).ready(function(){
        $('.collapsible').collapsible({
          accordion : false
        });
    });

    $(document).ready(function() {
        $('select').material_select();
    });
      
    $(document).ready(function(){
        // the "href" attribute of .modal-trigger must specify the modal ID that wants to be triggered
        $('.modal').modal();
    });
          

    $s.hoje = Date.getHoje();
    console.log($s.hoje);

    var classe = 'Cartao';

    $s.showToast = function(message){        
        Materialize.toast(message, 3000);
    };

    $s.goRota = function(rota){ 
        if (rota) {
            $location.path(rota);
        }
    };

    $s.abreModal = function(cartao){
        $s.cartao = cartao;        
        $('#enviarCartao').modal('open');
    }    

    $s.fechaModal = function(){      
        $('#enviarCartao').modal('close');
    }

    $s.getContatos = function(){
        $s.func = 'getContatos';

        $http.get(SERVER_PATH + "redirect.php?func=" + $s.func + "&c=" + classe).success(function(result){
            $s.contatos = result;                         
        });
    };   

    $s.getContatos();

    $s.sendCartao = function(oCartao, oEnvio){       
        if (!oEnvio.de || !oEnvio.para || !oEnvio.email) { 
            $s.showToast('É preciso preencher todos os campos!');
            return; 
        }

        oEnvio.codmodelo = oCartao.cod;
        
        var classe = 'Generic';
        $s.func = 'insertEnvio';
        $http.post(SERVER_PATH + "redirect.php?func=" + $s.func + "&c=" + classe, {
            oEnvio: oEnvio
        }).success(function(result){       
            $s.showToast("Cartão enviado!");          
        });
    }     


    $s.updatePedido = function(oPedido){

        $s.func = 'updatePedido';
        $http.post(SERVER_PATH + "redirect.php?func=" + $s.func + "&c=" + classe, {
            oPedido: oPedido
        }).success(function(result){        

        });
    }   

    $s.deletePedido = function(oPedido){

        $s.func = 'deletePedido';

        $http.post(SERVER_PATH + "redirect.php?func=" + $s.func + "&c=" + classe, {
            oPedido: oPedido
        }).success(function(result){
            $s.getPedidos();        
            $s.showToast("Excluído!");   
        });

    }         

}]);


app.controller("feedCtrl", ['$scope', '$http', '$rootScope','DateProvider', function ($s, $http, $rs, Date) {
  
    $(document).ready(function(){
        $('.collapsible').collapsible({
          accordion : false
        });
    });

    $(document).ready(function() {
        $('select').material_select();
    });
      
    $(document).ready(function(){
        // the "href" attribute of .modal-trigger must specify the modal ID that wants to be triggered
        $('.modal').modal();
    });
      
          

    $rs.dia_semana = Date.dayOfWeek();

    $s.hoje = Date.getHoje();
    console.log($s.hoje);

    var classe = 'Cartao';

    $s.showToast = function(message){        
        Materialize.toast(message, 3000);
    };

    $s.goRota = function(rota){ 
        if (rota) {
            $location.path(rota);
        }
    };

    $s.abreModal = function(cartao){
        $s.cartao = cartao;        
        $('#enviarCartao').modal('open');
    }


    $s.sendCartao = function(oCartao, oEnvio){       
        if (!oEnvio.de || !oEnvio.para || !oEnvio.email) { 
            $s.showToast('É preciso preencher todos os campos!');
            return; 
        }

        oEnvio.codmodelo = oCartao.cod;
        
        var classe = 'Generic';
        $s.func = 'insertEnvio';
        $http.post(SERVER_PATH + "redirect.php?func=" + $s.func + "&c=" + classe, {
            oEnvio: oEnvio
        }).success(function(result){       
            $s.showToast("Cartão enviado!");          
        });
    }     


    $s.updatePedido = function(oPedido){

        $s.func = 'updatePedido';
        $http.post(SERVER_PATH + "redirect.php?func=" + $s.func + "&c=" + classe, {
            oPedido: oPedido
        }).success(function(result){        

        });
    }   

    $s.deletePedido = function(oPedido){

        $s.func = 'deletePedido';

        $http.post(SERVER_PATH + "redirect.php?func=" + $s.func + "&c=" + classe, {
            oPedido: oPedido
        }).success(function(result){
            $s.getPedidos();        
            $s.showToast("Excluído!");   
        });

    }         

    $s.getCartoesRecebidos = function(){        
        $s.func = 'getCartoesRecebidos';

        $http.get(SERVER_PATH + "redirect.php?func=" + $s.func + "&c=" + classe).success(function(result){
            $s.cartoes_recebidos = result;                         
        });
    }

    $s.getCartoesRecebidos();

}]);