<?php

use App\Controller\Api\UserController;
use App\Controller\LandingController;
use App\Controller\LoginController;
use App\Controller\MarketplaceController;
use App\Controller\AdminController;
use League\Route\RouteGroup;
use League\Route\Router;


//Controlador de las rutas Esta nos permitiran redirigirnos a los diferentes clases de nuestro proyecto
return function (Router $router): void {


  //LandingController
  $router->get('/', [LandingController::class, 'index']);
  $router->get('/publicaciones-{id}', [LandingController::class, 'viewPublicaciones']);
  $router->get('/publicaciones', [LandingController::class, 'viewPublicacionesLastest']);
  $router->get('/quienes-somos', [LandingController::class, 'viewQuienesSomos']);
  $router->get('/programa-de-formacion', [LandingController::class, 'viewProgramaDeFormacion']);
  $router->get('/emprendimiento', [LandingController::class, 'viewEmprendimiento']);
  $router->get('/empleabilidad', [LandingController::class, 'viewAutoEmpleo']);
  $router->get('/404', [LandingController::class, 'error404']);
  $router->get('/salir', [LandingController::class, 'viewCerrarSesion']);

  // $router->get('/registroView',[LandingController::class,'registro']);

  //ControllerUsuario
  $router->get('/login', [LoginController::class, 'viewlogin']);
  $router->post('/login', [LoginController::class, 'login']);
  $router->get('/registro', [LoginController::class, 'registroview']);
  $router->post('/registro', [LoginController::class, 'registro']);
  $router->get('/solicitar/nueva/password', [LoginController::class, 'solicitarCambioDePassword']);
  $router->post('/solicitar/nueva/password', [LoginController::class, 'solicitarPassword']);
  $router->get('/cambiar-password-token-{token}-email-{email}', [LoginController::class, 'cambiarPassword']);
  $router->post('/cambiar-password-token-{token}-email-{email}', [LoginController::class, 'cambiarPassword']);
  $router->post('/cambiar-password', [LoginController::class, 'cambioPassword']);
  $router->get('/cambiar-password', [LoginController::class, 'cambioPassword']);


  //ControllerMarketPlace
  $router->get('/marketplace', [MarketplaceController::class, 'viewMarketplace']);
  $router->get('/marketplace/productos', [MarketplaceController::class, 'viewMarketplaceProductos']);
  $router->get('/marketplace/servicios', [MarketplaceController::class, 'viewMarketplaceServicios']);
  $router->get('/marketplace/editar/publicacion', [MarketplaceController::class, 'viewMarketplaceEditarPublicacion']);
  $router->get('/marketplace/guardado', [MarketplaceController::class, 'viewMarketplaceGuardado']);
  $router->get('/marketplace/notificacion', [MarketplaceController::class, 'viewMarketplaceNotificacion']);
  $router->get('/marketplace/publicaciones', [MarketplaceController::class, 'viewMarketplacePublicaciones']);
  $router->get('/marketplace/publicaciones/{id}', [MarketplaceController::class, 'viewMarketplacePublicacion']);
  $router->post('/marketplace/publicaciones/{id}', [MarketplaceController::class, 'sendEmailMarketplace']);
  $router->get('/marketplace/tus/publicaciones', [MarketplaceController::class, 'viewMarketplaceTusPublicaciones']);
  $router->get('/ofrecer/servicio', [MarketplaceController::class, 'viewOfrecerServicio']);
  $router->post('/ofrecer/servicio', [MarketplaceController::class, 'registrarServicio']);
  $router->get('/vender/producto', [MarketplaceController::class, 'viewVernderServicio']);
  $router->post('/vender/producto', [MarketplaceController::class, 'registrarProducto']);
  $router->get('/editar/publicaciones/marketplace/{id}', [MarketplaceController::class, 'editarProduct']);
  $router->post('/update/product/{id}', [MarketplaceController::class, 'updateProducto']);

  //Controlador para la zona de marketplace izquierda
  $router->post('/marketplace/productos', [MarketplaceController::class, 'filterMarketplace']);
  $router->post('/marketplace/servicios', [MarketplaceController::class, 'filterMarketplace']);
  $router->post('/marketplace', [MarketplaceController::class, 'filterMarketplace']);
  $router->post('/marketplace/guardado', [MarketplaceController::class, 'filterGuardado']);
  $router->post('/marketplace/tus/publicaciones', [MarketplaceController::class, 'filterTusPublicaciones']);
  $router->post('/marketplace/notificacion', [MarketplaceController::class, 'filterNotifiaciones']);

  $router->get('/delete/product/{id}', [MarketplaceController::class, 'deleteProduct']);
  $router->get('/delete-image-product-client{id}-{idproducto}', [MarketplaceController::class, 'deleteImageProductClient']);

  //ControllerAdmin
  $router->get('/entradas/pendientes', [AdminController::class, 'viewEntradasPendientes']);
  $router->get('/revision/publicacion/{id}', [AdminController::class, 'viewRevisionPublicacion']);
  $router->get('/editar/publicaciones', [AdminController::class, 'viewEditarPublicaciones']);
  $router->get('/datos/personales', [AdminController::class, 'viewDatosPersonales']);
  $router->post('/datos/personales', [AdminController::class, 'viewDatosPersonales']);
  $router->get('/editar/datos', [AdminController::class, 'viewEditarDatos']);
  $router->post('/editar/datos', [AdminController::class, 'updateDatos']);
  $router->get('/solicitar/cambiar/password', [AdminController::class, 'viewSolicitarCambioPassword']);
  $router->post('/solicitar/cambiar/password', [AdminController::class, 'viewSolicitarCambioPassword']);
  $router->get('/cambiar-passwordAdmin-token-{token}-email-{email}', [AdminController::class, 'viewCambiarPassword']);
  $router->post('/cambiar-passwordAdmin-token-{token}-email-{email}', [AdminController::class, 'viewCambiarPassword']);
  $router->get('/notificaciones', [AdminController::class, 'viewSolicitudes']);
  $router->get('/editar/notas/prensa', [AdminController::class, 'viewEditarNotasDePrensa']);
  $router->get('/editar/publicacion/{id}', [AdminController::class, 'viewEditarPublicacion']);
  $router->post('/editar/publicacion/{id}', [AdminController::class, 'editarPublicacion']);
  $router->get('/eliminar/publicacion/{id}', [AdminController::class, 'eliminarPublicacion']);
  $router->get('/editar/programa/{id}', [AdminController::class, 'viewEditarProgram']);
  $router->post('/editar/programa/{id}', [AdminController::class, 'editarPrograma']);
  $router->get('/eliminar/programa/{id}', [AdminController::class, 'eliminarPrograma']);
  $router->get('/ocultar/publicacion/{id}', [AdminController::class, 'ocultarPublicacion']);
  $router->get('/mostrar/publicacion/{id}', [AdminController::class, 'mostrarPublicacion']);
  $router->get('/nueva/publicacion', [AdminController::class, 'viewNuevaPublicacion']);

  $router->post('/nueva/publicacion', [AdminController::class, 'crearNuevaPublicacion']);

  $router->post('/nuevo/programa/de/formacion', [AdminController::class, 'crearProgrmaDeFormacion']);

  $router->post('/update/revision/publicacion/{id}', [AdminController::class, 'updateRevisionPublicacion']);
  $router->get('/delete-image-product-admin-{id}-{idproducto}', [AdminController::class, 'deleteImageProductAdmin']);


  $router->get('/contacto', [LandingController::class, 'contact']);

  $router->post('/contacto', [LandingController::class, 'contact']);

  //ver Clientes
  //$router->get('/', [UserController::class, 'getUser']);

  // Ejemplo de API
  $router->group('/api/users', function (RouteGroup $userApi) {
    // $userApi->get('/', [UserController::class, 'getUser']);
    //$userApi->get('/', [UserController::class, 'getUser']);

    // No implementados
    //  $userApi->post('/', [UserController::class, 'create']);
    $userApi->patch('/{id}', [UserController::class, 'edit']);
    $userApi->delete('/{id}', [UserController::class, 'delete']);
  });

  // TODO: Agregar rutas
  //$router->get('/iniciar-sesion', [SecurityController::class, 'login']);
  //$router->get('/registrarme', [SecurityController::class, 'signup']);
};
