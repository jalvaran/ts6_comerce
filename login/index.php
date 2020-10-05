<!DOCTYPE html>
<html lang="spa">

    <head>

        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>TS6 PRO</title>

        <!-- CSS -->
        
        <link rel="stylesheet" href="assets/bootstrap/css/bootstrap.min.css">
        <link rel="stylesheet" href="assets/font-awesome/css/font-awesome.min.css">
        <link rel="stylesheet" href="assets/css/form-elements.css">
        <link rel='stylesheet' href='../assets/plugin/alertify/themes/alertify.core.css' />
        <link rel='stylesheet' href='../assets/plugin/alertify/themes/alertify.default.css' id='toggleCSS' />
        <link rel="stylesheet" href="assets/css/style.css">
        
        <link rel='shortcut icon' href='../images/favicontechno.png'>       

    </head>

    <body>
                
        <!-- Top content -->
        <div class="top-content">
        	<div class="container">
                	
                <div class="row">
                    <div class="col-sm-8 col-sm-offset-2 text">
                        <h1>Iniciar Sesión en Plataforma TS6 COMERCE</h1>
                        <div class="description">
                        	<p>
	                         	Plataforma de Control de Procesos Empresariales 
	                         	Creada por Techno Soluciones SAS 
	                         	
                        	</p>
                        </div>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-sm-10 col-sm-offset-1 show-forms">
                    	
                    	<span class="show-register-form active">Login</span> 
                    	
                    </div>
                </div>
                
                <div class="row register-form">
                    <div class="col-sm-4 col-sm-offset-1">
                        
	                    	<div class="form-group">
	                    		<label class="sr-only" for="l-form-username">Username</label>
	                        	<input type="text" name="user" placeholder="Usuario..." class="l-form-username form-control" id="l-form-username" autocomplete="off" onkeypress="validar(event)">
	                        </div>
	                        <div class="form-group">
	                        	<label class="sr-only" for="l-form-password">Password</label>
                                        <input type="password" name="pw" placeholder="Password..." class="l-form-password form-control" id="l-form-password" autocomplete="off" onkeypress="validar(event)">
	                        </div>
                        <button type="submit" class="btn" onclick="VerificaInicioSesion()">Entrar..!</button>
				    	
				    	
                    </div>
                    <div class="col-sm-6 forms-right-icons">
                                                <div class="row">
							<div class="col-sm-2 icon"><i class="fa fa-cog fa-spin fa-fw"></i></div>
							<div class="col-sm-10">
								<h3>Integración de todos los procesos de su Compañía</h3>
								<p>Gestión de Calidad, Gestión Comercial, Gestión de los servicos en la salud, Servicio al Cliente, Gestión del Talento Humano, Compras, Inventarios, Gestión Administrativa y Financiera.</p>
							</div>
						</div>
						<div class="row">
							<div class="col-sm-2 icon"><i class="fa fa-user"></i></div>
							<div class="col-sm-10">
								<h3>Control de Acceso</h3>
								<p>Control de sesiones por tipo de Usuarios, definido por el administrador del portal</p>
							</div>
						</div>
						<div class="row">
							<div class="col-sm-2 icon"><i class="fa fa-eye"></i></div>
							<div class="col-sm-10">
								<h3>Fácil Uso</h3>
								<p>Interfaces intuitivas para el uso adecuado y fácil de cada uno de los módulos.</p>
							</div>
						</div>
						
                    </div>
                </div>
                
                
                    
        	</div>
        </div>

        <!-- Footer -->
        <footer>
        	<div >
        		<div class="row">
        			
        			<div class="col-sm-8 col-sm-offset-2">
                                    <img src="../images/logo-header.png" alt="">
                                    <div class="footer-border">
                                        
                                    </div>
        				<p>&copy; <?php echo date("Y");?> | <a href="#">Privacy Policy</a> <br> Software  diseñado por Techno Soluciones SAS. (057) 317 774 0609</p>
        			</div>
        			
        		</div>
        	</div>
        </footer>

        <!-- Javascript -->
        <script src="assets/js/jquery-1.11.1.min.js"></script>
        <script src="assets/bootstrap/js/bootstrap.min.js"></script>
        <script src="assets/js/jquery.backstretch.min.js"></script>
        <script src="../assets/plugin/alertify/lib/alertify.min.js"></script>
        <script src="assets/js/scripts.js"></script>
        
        <script src="js/index.js"></script>
        
    
    </body>
<script>    
document.getElementById('l-form-username').focus();
</script> 

</html>