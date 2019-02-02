<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

// Authentication routes...
// Route::get('auth/login', 'Auth\AuthController@getLogin');
// Route::post('auth/login', ['as' =>'auth/login', 'uses' => 'Auth\AuthController@postLogin']);
// Route::get('auth/logout', ['as' => 'auth/logout', 'uses' => 'Auth\AuthController@getLogout']);
 
// Registration routes...
// Route::get('auth/register', 'Auth\AuthController@getRegister');
// Route::post('auth/register', ['as' => 'auth/register', 'uses' => 'Auth\AuthController@postRegister']);

Auth::routes();
Route::get('logout', 'Auth\LoginController@logout');

Route::get('/', function(){
    return redirect('/dashboard');
});

Route::group(['middleware' => 'guest'], function() {    
    //Password reset routes
    Route::get('password/reset', 'Auth\ForgotPasswordController@showLinkRequestForm');
    Route::post('password/email', 'Auth\ForgotPasswordController@sendResetLinkEmail');
    Route::get('password/reset/{token}', 'Auth\ResetPasswordController@showResetForm');
    Route::post('password/reset', 'Auth\ResetPasswordController@reset');
    Route::get('password','Auth\ResetPasswordController@showPasswordReset');
    //Register routes
    Route::get('registro','Auth\RegisterController@showRegistrationForm');
    Route::post('registro', 'Auth\RegisterController@register');
});

Route::group(['middleware' => 'auth'], function () {
    Route::get('/dashboard', function(){
        return View::make('dashboard.home');
    });

    Route::post('trabajador/buscar','TrabajadorController@buscar')->name('trabajador.buscar');
    Route::get('trabajador/eliminar/{id}/{listarluego}','TrabajadorController@eliminar')->name('trabajador.eliminar');
    Route::resource('trabajador', 'TrabajadorController', array('except' => array('show')));

    Route::post('sucursal/buscar','SucursalController@buscar')->name('sucursal.buscar');
    Route::get('sucursal/eliminar/{id}/{listarluego}','SucursalController@eliminar')->name('sucursal.eliminar');
    Route::resource('sucursal', 'SucursalController', array('except' => array('show')));


    Route::post('workertype/buscar', 'WorkertypeController@buscar')->name('workertype.buscar');
    Route::get('workertype/eliminar/{id}/{listarluego}', 'WorkertypeController@eliminar')->name('workertype.eliminar');
    Route::resource('workertype', 'WorkertypeController', array('except' => array('show')));

    Route::post('employee/buscar', 'EmployeeController@buscar')->name('employee.buscar');
    Route::get('employee/eliminar/{id}/{listarluego}', 'EmployeeController@eliminar')->name('employee.eliminar');
    Route::resource('employee', 'EmployeeController', array('except' => array('show')));


    /* PROVIDERS */
    Route::post('provider/search', 'ProviderController@search')->name('provider.search');
    Route::get('provider/eliminar/{id}/{listarluego}', 'ProviderController@eliminar')->name('provider.eliminar');
    Route::resource('provider', 'ProviderController', array('except' => array('show')));

    /* COMPANY */
    Route::post('company/search', 'CompanyController@search')->name('company.search');
    Route::get('company/eliminar/{id}/{listarluego}', 'CompanyController@eliminar')->name('company.eliminar');
    Route::resource('company', 'CompanyController', array('except' => array('show')));


    /*PERSON*/
    Route::post('person/search','PersonController@search')->name('person.search');
    Route::get('person/employeesautocompleting/{searching}', 'PersonController@employeesautocompleting')->name('person.employeesautocompleting');
    Route::get('person/providersautocompleting/{searching}', 'PersonController@providersautocompleting')->name('person.providersautocompleting');
    Route::get('person/customersautocompleting/{searching}', 'PersonController@customersautocompleting')->name('person.customersautocompleting');


    /*--------------------------------------------- */

    /* CAMBIAR CONTRASEÑA*/
    Route::resource('updatepassword', 'UpdatePasswordController', array('except' => array('show')));

    Route::post('categoria/buscar','CategoriaController@buscar')->name('categoria.buscar');
    Route::get('categoria/eliminar/{id}/{listarluego}','CategoriaController@eliminar')->name('categoria.eliminar');
    Route::resource('categoria', 'CategoriaController', array('except' => array('show')));

    Route::post('categoriaopcionmenu/buscar', 'CategoriaopcionmenuController@buscar')->name('categoriaopcionmenu.buscar');
    Route::get('categoriaopcionmenu/eliminar/{id}/{listarluego}', 'CategoriaopcionmenuController@eliminar')->name('categoriaopcionmenu.eliminar');
    Route::resource('categoriaopcionmenu', 'CategoriaopcionmenuController', array('except' => array('show')));

    Route::post('opcionmenu/buscar', 'OpcionmenuController@buscar')->name('opcionmenu.buscar');
    Route::get('opcionmenu/eliminar/{id}/{listarluego}', 'OpcionmenuController@eliminar')->name('opcionmenu.eliminar');
    Route::resource('opcionmenu', 'OpcionmenuController', array('except' => array('show')));

    Route::post('tipousuario/buscar', 'TipousuarioController@buscar')->name('tipousuario.buscar');
    Route::get('tipousuario/obtenerpermisos/{listar}/{id}', 'TipousuarioController@obtenerpermisos')->name('tipousuario.obtenerpermisos');
    Route::post('tipousuario/guardarpermisos/{id}', 'TipousuarioController@guardarpermisos')->name('tipousuario.guardarpermisos');
    Route::get('tipousuario/obteneroperaciones/{listar}/{id}', 'TipousuarioController@obteneroperaciones')->name('tipousuario.obteneroperaciones');
    Route::post('tipousuario/guardaroperaciones/{id}', 'TipousuarioController@guardaroperaciones')->name('tipousuario.guardaroperaciones');
    Route::get('tipousuario/eliminar/{id}/{listarluego}', 'TipousuarioController@eliminar')->name('tipousuario.eliminar');
    Route::resource('tipousuario', 'TipousuarioController', array('except' => array('show')));
    Route::get('tipousuario/pdf', 'TipousuarioController@pdf')->name('tipousuario.pdf');

    /*USUARIO*/
    Route::post('usuario/buscar', 'UsuarioController@buscar')->name('usuario.buscar');
    Route::get('usuario/eliminar/{id}/{listarluego}', 'UsuarioController@eliminar')->name('usuario.eliminar');
    Route::resource('usuario', 'UsuarioController', array('except' => array('show')));

    Route::get('usuario/cargarbinnacle', 'UsuarioController@cargarbinnacle')->name('usuario.cargarbinnacle');
    Route::get('usuario/generarreporte', 'UsuarioController@generarreporte')->name('usuario.generarreporte');
    Route::get('/binnaclePDF/{fechai?}/{fechaf?}', 'UsuarioController@binnaclePDF')->name('usuario.binnaclePDF');

    /*PERSONA*/
    Route::post('persona/buscar', 'PersonController@buscar')->name('persona.buscar');
    Route::get('persona/eliminar/{id}/{listarluego}', 'PersonController@eliminar')->name('persona.eliminar');
    Route::resource('persona', 'PersonController', array('except' => array('show')));
    Route::get('persona/cargarcontrolpersona', 'PersonController@cargarcontrolpersona')->name('persona.cargarcontrolpersona');
    Route::post('persona/buscarpersona', 'PersonController@buscarpersona')->name('persona.buscarpersona');
    Route::get('persona/cambiartardanza', 'PersonController@cambiartardanza')->name('persona.cambiartardanza');
    Route::get('persona/cambiarfalta', 'PersonController@cambiarfalta')->name('persona.cambiarfalta');
    Route::get('persona/cargarpagarmulta/{id}/{listarluego}', 'PersonController@cargarpagarmulta')->name('persona.cargarpagarmulta');
    Route::get('persona/guardarpagarmulta', 'PersonController@guardarpagarmulta')->name('persona.guardarpagarmulta');
    Route::get('/generarestadocuentaPDF/{id}', 'PersonController@generarestadocuentaPDF')->name('generarestadocuentaPDF');


    /*CONTROL PERSONA ASISTENCIA*/
    Route::post('controlpersona/buscar', 'ControlPersonaController@buscar')->name('controlpersona.buscar');
    Route::get('controlpersona/eliminar/{id}/{listarluego}', 'ControlPersonaController@eliminar')->name('controlpersona.eliminar');
    Route::resource('controlpersona', 'ControlPersonaController', array('except' => array('show')));
    Route::get('controlpersona/cambiartardanza', 'ControlPersonaController@cambiartardanza')->name('controlpersona.cambiartardanza');
    Route::get('controlpersona/cargarpagarmulta/{id}/{listarluego}', 'ControlPersonaController@cargarpagarmulta')->name('controlpersona.cargarpagarmulta');
    Route::get('controlpersona/guardarpagarmulta', 'ControlPersonaController@guardarpagarmulta')->name('controlpersona.guardarpagarmulta');
    Route::get('controlpersona/generarreporteasistenciaPDF/{fechai?}/{fechaf?}/{tipo?}', 'ControlPersonaController@generarreporteasistenciaPDF')->name('controlpersona.generarreporteasistenciaPDF');
    Route::get('controlpersona/buscarpersona', 'ControlPersonaController@buscarpersona')->name('controlpersona.buscarpersona');

    /*CONFIGURACIONES*/
    Route::post('configuraciones/buscar', 'ConfiguracionesController@buscar')->name('configuraciones.buscar');
    Route::get('configuraciones/eliminar/{id}/{listarluego}', 'ConfiguracionesController@eliminar')->name('configuraciones.eliminar');
    Route::resource('configuraciones', 'ConfiguracionesController', array('except' => array('show')));

    /*ACCIONES*/
    Route::post('acciones/buscar', 'AccionesController@buscar')->name('acciones.buscar');
    Route::get('acciones/eliminar/{id}/{listarluego}','AccionesController@eliminar')->name('acciones.eliminar');
    Route::resource('acciones', 'AccionesController', array('except' => array('show')));
    Route::get('acciones/listacciones/{persona_id}', 'AccionesController@listacciones')->name('acciones.listacciones');
    Route::get('acciones/buscaraccion', 'AccionesController@buscaraccion')->name('acciones.buscaraccion');
    Route::get('acciones/cargarventa/{id}', 'AccionesController@cargarventa')->name('acciones.cargarventa');
    Route::post('acciones/guardarventa/{id}', 'AccionesController@guardarventa')->name('acciones.guardarventa');
    Route::get('/generarvoucheraccionPDF/{id}/{cant}/{fecha}', 'AccionesController@generarvoucheraccionPDF')->name('generarvoucheraccionPDF');
    Route::get('/generarvoucheraccionventaPDF/{id}/{cant}/{fecha}', 'AccionesController@generarvoucheraccionventaPDF')->name('generarvoucheraccionventaPDF');
    Route::get('/generarnormasaccionPDF', 'AccionesController@generarnormasaccionPDF')->name('generarnormasaccionPDF');
    Route::get('acciones/listpersonas',  'AccionesController@listpersonas')->name('acciones.listpersonas');
    Route::get('/reciboaccionpdf/{accion_id?}/{cant?}/{fecha?}', 'AccionesController@reciboaccionpdf')->name('acciones.reciboaccionpdf');
    Route::get('/reciboaccionventapdf/{id_comprador?}/{id_vendedor?}/{cant?}/{fecha?}', 'AccionesController@reciboaccionventapdf')->name('acciones.reciboaccionventapdf');


     /*CREDITO*/ 
     Route::post('creditos/buscar', 'CreditoController@buscar')->name('creditos.buscar');
     Route::get('creditos/eliminar/{id?}/{listarluego?}', 'CreditoController@eliminar')->name('creditos.eliminar');
     Route::resource('creditos', 'CreditoController', array('except' => array('show')));
     Route::get('creditos/detallecredito/{idcredito?}/{listarluego?}', 'CreditoController@detallecredito')->name('creditos.detallecredito');
     Route::get('creditos/vistapagocuota/{idcredito?}/{listarluego?}/{entidad?}', 'CreditoController@vistapagocuota')->name('creditos.vistapagocuota');
     Route::get('creditos/vistaaccion/{idcredito?}/{listarluego?}', 'CreditoController@vistaaccion')->name('creditos.vistaaccion');
     
     Route::post('creditos/pagarcuota', 'CreditoController@pagarcuota')->name('creditos.pagarcuota');
     Route::get('creditos/listardetallecuotas', 'CreditoController@listardetallecuotas')->name('creditos.listardetallecuotas');
     Route::get('/generarecibopagocuotaPDF/{cuota_id?}', 'CreditoController@generarecibopagocuotaPDF')->name('creditos.generarecibopagocuotaPDF');
     Route::get('/generareportecuotasPDF/{credito_id?}', 'CreditoController@generareportecuotasPDF')->name('creditos.generareportecuotasPDF');
     Route::get('/generarecibocreditoPDF/{credito_id?}', 'CreditoController@generarecibocreditoPDF')->name('creditos.generarecibocreditoPDF');
     Route::get('creditos/listpersonas',  'CreditoController@listpersonas')->name('creditos.listpersonas');
     Route::get('creditos/cuotasalafecha', 'CreditoController@cuotasalafecha')->name('creditos.cuotasalafecha');
     Route::post('creditos/pagarcuotainteres', 'CreditoController@pagarcuotainteres')->name('creditos.pagarcuotainteres');
     Route::get('creditos/amortizarcuotas', 'CreditoController@amortizarcuotas')->name('creditos.amortizarcuotas');
     Route::get('creditos/obtenermontototal', 'CreditoController@obtenermontototal')->name('creditos.obtenermontototal');
     Route::get('creditos/pagarcreditototal', 'CreditoController@pagarcreditototal')->name('creditos.pagarcreditototal');
     
     
     
     //Route::get('creditos/abrirpdf', 'CreditoController@abrirpdf')->name('creditos.abrirpdf');
     /*RECIBOCREDITOS*/
    Route::post('recibocuotas/buscar', 'RecibocuotasController@buscar')->name('recibocuotas.buscar');
    Route::get('recibocuotas/eliminar/{id?}/{listarluego?}', 'RecibocuotasController@eliminar')->name('recibocuotas.eliminar');
    Route::resource('recibocuotas', 'RecibocuotasController', array('except' => array('show')));
    Route::post('recibocuotas/aplicarmora', 'RecibocuotasController@aplicarmora')->name('recibocuotas.aplicarmora');
    Route::get('recibocuotas/vistaaplicarmora/{id_cuota?}', 'RecibocuotasController@vistaaplicarmora')->name('recibocuotas.vistaaplicarmora');
    /*AHORROS*/
    Route::post('ahorros/buscar', 'AhorrosController@buscar')->name('ahorros.buscar');
    Route::get('ahorros/eliminar/{id}/{listarluego}', 'AhorrosController@eliminar')->name('ahorros.eliminar');
    Route::resource('ahorros', 'AhorrosController', array('except' => array('show')));
    Route::get('ahorros/verahorro/{id_ahorro}/{listarluego}', 'AhorrosController@verahorro')->name('ahorros.verahorro');
    Route::get('ahorros/vistaretiro/{persona_id}/{listarluego}', 'AhorrosController@vistaretiro')->name('ahorros.vistaretiro');
    Route::get('ahorros/retiro', 'AhorrosController@retiro')->name('ahorros.retiro');
    Route::get('ahorros/vistadetalleahorro/{persona_id}/{listarluego}', 'AhorrosController@vistadetalleahorro')->name('ahorros.vistadetalleahorro');
    Route::get('ahorros/listardetalleahorro', 'AhorrosController@listardetalleahorro')->name('ahorros.listardetalleahorro');
    Route::get('ahorros/actualizarecapitalizacion', 'AhorrosController@actualizarecapitalizacion')->name('ahorros.actualizarecapitalizacion');
    Route::get('ahorros/vistahistoricoahorro/{persona_id}/{listarluego}', 'AhorrosController@vistahistoricoahorro')->name('ahorros.vistahistoricoahorro');
    Route::get('ahorros/listarhistorico', 'AhorrosController@listarhistorico')->name('ahorros.listarhistorico');
    
    Route::get('/generareciboahorroPDF', 'AhorrosController@generareciboahorroPDF')->name('ahorros.generareciboahorroPDF');
    Route::get('/generareciboahorroPDF1/{transaccion_id?}', 'AhorrosController@generareciboahorroPDF')->name('ahorros.generareciboahorroPDF1');
    
    Route::get('/generareciboretiroPDF/{transaccion_id?}', 'AhorrosController@generareciboretiroPDF')->name('ahorros.generareciboretiroPDF');
    Route::get('/generareportehistoricoahorrosPDF/{persona_id?}/{anyo?}', 'AhorrosController@generareportehistoricoahorrosPDF')->name('ahorros.generareportehistoricoahorrosPDF');
    
    /*GASTOS*/
    Route::post('gastos/buscar', 'GastosController@buscar')->name('gastos.buscar');
    Route::get('gastos/eliminar/{id}/{listarluego}', 'GastosController@eliminar')->name('gastos.eliminar');
    Route::resource('gastos', 'GastosController', array('except' => array('show')));
    /**DISTRIBUCION */
    
    Route::post('distribucion_utilidades/buscar', 'DistUtilidadesController@buscar')->name('distribucion_utilidades.buscar');
    Route::get('distribucion_utilidades/eliminar/{id}/{listarluego}', 'DistUtilidadesController@eliminar')->name('distribucion_utilidades.eliminar');
    Route::resource('distribucion_utilidades', 'DistUtilidadesController', array('except' => array('show')));
    Route::get('distribucion_utilidades/verdistribucion/{distribucion_id?}', 'DistUtilidadesController@verdistribucion')->name('distribucion_utilidades.verdistribucion');
    Route::get('/reportedistribucionPDF/{distribucion_id?}', 'DistUtilidadesController@reportedistribucionPDF')->name('distribucion_utilidades.reportedistribucionPDF');
    
    /*CAJA*/
    Route::post('caja/buscar', 'CajaController@buscar')->name('caja.buscar');
    Route::resource('caja', 'CajaController', array('except' => array('show')));
    Route::get('caja/cargarCaja/{id}', 'CajaController@cargarCaja')->name('caja.cargarCaja');
    Route::get('/reportecajaPDF/{id}', 'CajaController@reportecajaPDF')->name('reportecajaPDF');
    Route::get('caja/detalle/{id}', 'CajaController@detalle')->name('caja.detalle');

    Route::get('/reporteingresosPDF/{id?}', 'CajaController@reporteingresosPDF')->name('caja.reporteingresosPDF');
    Route::get('/reporteegresosPDF/{id?}', 'CajaController@reporteegresosPDF')->name('caja.reporteegresosPDF');
    Route::get('/reporteresumenfinancieroPDF/{id?}', 'CajaController@reporteresumenfinancieroPDF')->name('caja.reporteresumenfinancieroPDF');

    Route::get('caja/nuevomovimiento/{id}', 'CajaController@nuevomovimiento')->name('caja.nuevomovimiento');
    Route::post('caja/registrarmovimiento/{id}', 'CajaController@registrarmovimiento')->name('caja.registrarmovimiento');
    Route::get('caja/cargarselect/{idselect}', 'CajaController@cargarselect')->name('caja.cargarselect');
    Route::get('caja/cargarselecttransaccion/{idselect}', 'CajaController@cargarselecttransaccion')->name('caja.cargarselecttransaccion');
    Route::get('caja/buscartransaccion', 'CajaController@buscartransaccion')->name('caja.buscartransaccion');
    Route::get('caja/cargarreapertura/{id}/{listarluego}', 'CajaController@cargarreapertura')->name('caja.cargarreapertura');
    Route::get('caja/guardarreapertura', 'CajaController@guardarreapertura')->name('caja.guardarreapertura');
    Route::get('caja/cargarreporte', 'CajaController@cargarreporte')->name('caja.cargarreporte');
    Route::get('caja/generarreportes', 'CajaController@generarreportes')->name('caja.generarreportes');
    Route::get('caja/listpersonas',  'CajaController@listpersonas')->name('caja.listpersonas');

    /*CONCEPTO*/
    Route::post('concepto/buscar', 'ConceptoController@buscar')->name('concepto.buscar');
    Route::get('concepto/eliminar/{id}/{listarluego}', 'ConceptoController@eliminar')->name('concepto.eliminar');
    Route::resource('concepto', 'ConceptoController', array('except' => array('show')));



    /*TRANSACCIONES*/
    Route::post('transaccion/buscar', 'TransaccionController@buscar')->name('transaccion.buscar');
    Route::get('transaccion/eliminar/{id}/{listarluego}', 'TransaccionController@eliminar')->name('transaccion.eliminar');
    Route::resource('transaccion', 'TransaccionController', array('except' => array('show')));

    /*CERTIFICADOS */
    Route::post('certificado/buscar','CertificadoController@buscar')->name('certificado.buscar');
    Route::get('certificado/eliminar/{id}/{listarluego}','CertificadoController@eliminar')->name('certificado.eliminar');
    Route::resource('certificado', 'CertificadoController', array('except' => array('show')));
    Route::get('certificado/cargarpagarcontribucion/{id}/{listarluego}', 'CertificadoController@cargarpagarcontribucion')->name('certificado.cargarpagarcontribucion');
    Route::get('certificado/guardarpagarcontribucion', 'CertificadoController@guardarpagarcontribucion')->name('certificado.guardarpagarcontribucion');
    Route::get('/reportecertificadoPDF/{id?}', 'CertificadoController@reportecertificadoPDF')->name('certificado.reportecertificadoPDF');

    /*DIRECTIVOS*/
    Route::post('directivos/buscar', 'DirectivosController@buscar')->name('directivos.buscar');
    Route::get('directivos/eliminar/{id}/{listarluego}', 'DirectivosController@eliminar')->name('directivos.eliminar');
    Route::resource('directivos', 'DirectivosController', array('except' => array('show')));
    Route::get('directivos/listdirectivos/{directivos_id}', 'DirectivosController@listdirectivos')->name('directivos.listdirectivos');
    Route::get('directivos/listpersonas',  'DirectivosController@listpersonas')->name('directivos.listpersonas');
    Route::get('/directivosPDF/{id}', 'DirectivosController@directivosPDF')->name('directivosPDF');
});

//Route::get('personas/{dni?}','AccionesController@getPersona');
Route::get('acciones/{id?}','AccionesController@getListCantAcciones');
Route::get('acciones/{id?}/{dni?}','AccionesController@getListCantAccionesPersona');
Route::get('creditos/{persona_id?}','CreditoController@getPersona');
Route::get('provincia/cboprovincia/{id?}', array('as' => 'provincia.cboprovincia', 'uses' => 'ProvinciaController@cboprovincia'));
Route::get('distrito/cbodistrito/{id?}', array('as' => 'distrito.cbodistrito', 'uses' => 'DistritoController@cbodistrito'));

/*Route::get('provincias/{id}', function($id)
{
	$departamento_id = $id;

	$provincias = Departamento::find($departamento_id)->provincias;

    return Response::json($provincias);
});
*/

Route::get('provincias/{id}','ProvinciaController@getProvincias');
Route::get('distritos/{id}','DistritoController@getDistritos');