<?php
if(isset($_REQUEST["company_id"])){
    $empresa_id=$_REQUEST["company_id"];
    $ruta_logo="../logos_empresa/$empresa_id/logo.png";
    if(!is_file($ruta_logo)){
        $ruta_logo="../images/logo-header.png";
    }
    $ruta_favicon="../logos_empresa/$empresa_id/favicon.png";
    if(!is_file($ruta_favicon)){
        $ruta_favicon="../images/favicontechno.png";
    }
    $ruta_fondo="../logos_empresa/$empresa_id/fondo.jpg";
    if(!is_file($ruta_fondo)){
        $ruta_fondo="../images/fondo_web.jpg";
    }
}else{
    $ruta_logo="../images/logo-header.png";
    $ruta_favicon="../images/favicontechno.png";
    $ruta_fondo="../images/fondo_web.jpg";
}

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Aguas de Buga</title>
    <link rel="icon" type="image/x-icon" href="<?php echo $ruta_favicon; ?>">
    <!-- Custom Stylesheet -->
    <link rel="stylesheet" href="../dist/css/style.css" />
</head>
<body>

    <!-- Page Wrapper -->
    <div class="lgn-background lgn-1" style="background-image: url(<?php echo $ruta_fondo; ?>);">
        <div class="lgn-wrapper">
            <div class="lgn-logo text-center">
                <a><img src="<?php echo $ruta_logo; ?>" alt=""></a>
            </div>
            <div id="login-form" class="lgn-form ">
                
				<div class="lgn-input form-group">
					<label class="control-label col-sm-12">Usuario</label>
					<div class="col-sm-12">
						<input class="form-control" type="text" name="usuario_plataforma" id="l-form-username" placeholder="Digite su usuario" autocomplete="off">
					</div>
				</div>
				<div class="lgn-input form-group">
					<label class="control-label col-sm-12">Contraseña</label>
					<div class="col-sm-12">
						<input type="password" name="password_plataforma" id="l-form-password" class="form-control" placeholder="Digite su contraseña" autocomplete="off">
					</div>  
				</div>
				
				<div class="lgn-submit">
					<button onclick="VerificaInicioSesion();" id="btn_login" class="btn btn-primary btn-pill btn-lg" name="btn_login">Entrar</button>
				</div>
               
            </div> 
        </div>
    </div>

    <!-- Include js files -->
    <!-- jQuery Library -->
    <script type="text/javascript" src="../assets/plugin/jquery/jquery-3.3.1.min.js"></script>
    <!-- Popper Plugin -->
    
    <script type="text/javascript" src="../assets/plugin/popper/popper.min.js"></script>
    <!-- Bootstrap Framework -->
    <script type="text/javascript" src="../assets/plugin/bootstrap/bootstrap.min.js"></script>
    <script src="js/index.js"></script>
    <script>    
        document.getElementById('l-form-username').focus();
    </script>
</body>
</html>