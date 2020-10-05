/**
 * Controlador para realizar los backups automaticamente
 * JULIAN ALVARAN 2018-12-14
 * TECHNO SOLUCIONES SAS 
 * 317 774 0609
 */

/**
 * mira si hay conexion con el servidor
 * @returns {undefined}
 */
function BackupsIni(){
       
    var form_data = new FormData();
        form_data.append('idAccion', 1);
        
        $.ajax({
        url: '../../general/procesadores/backup.process.php',
        //dataType: 'json',
        cache: false,
        contentType: false,
        processData: false,
        data: form_data,
        type: 'post',
        success: function(data){
            //console.log(data);          
          if (data == "OK") {                 
                CrearTablas();                          
          }else {
                document.getElementById('hTituloBackCreacionTablas').innerHTML=data;
          }
        },
        error: function (xhr, ajaxOptions, thrownError) {
            alert(xhr.status);
            alert(thrownError);
          }
      })        
}  

/**
 * Se encarga de crear las tablas en el servidor externo
 * @returns {undefined}
 */
function CrearTablas(){
       
    var form_data = new FormData();
        form_data.append('idAccion', 2);
        
        $.ajax({
        url: '../../general/procesadores/backup.process.php',
        //dataType: 'json',
        cache: false,
        contentType: false,
        processData: false,
        data: form_data,
        type: 'post',
        success: function(data){
           // console.log(data);
          var respuestas = data.split(';'); 
          if (respuestas[0] == "OK") { 
              
                var tablasTotales=respuestas[1];
                var tablasCreadas=respuestas[2];
                if(tablasCreadas==0){
                    tablasCreadas=1;
                }
                document.getElementById('hTituloBackCreacionTablas').innerHTML=tablasCreadas+" de "+tablasTotales+" tablas creadas";
                var porcentaje=Math.round((100/tablasTotales)*tablasCreadas);
                $('#prBackCreacionTablas').css('width',porcentaje+'%');
                document.getElementById('prBackCreacionTablas').innerHTML=porcentaje+"%";
                if(porcentaje<=101 && respuestas[3]!='ST'){
                    CrearTablas();
                }     
                
                if(porcentaje>=100){
                    VerificaRegistrosXRespaldar();
                }
          }else {
                document.getElementById('hTituloBackCreacionTablas').innerHTML=data;
          }
        },
        error: function (xhr, ajaxOptions, thrownError) {
            alert(xhr.status);
            alert(thrownError);
          }
      })        
}  
/**
 * 
 * @returns {undefined}
 */
function VerificaRegistrosXRespaldar(){
     
    document.getElementById('hTituloBackCreacionTablas').innerHTML="Buscando Registros por respaldar";
    $('#prBackCreacionTablas').css('width','0%');
    document.getElementById('prBackCreacionTablas').innerHTML="0%";
    var form_data = new FormData();
        form_data.append('idAccion', 3);        
        $.ajax({
        url: '../../general/procesadores/backup.process.php',
        async: true,
        cache: false,
        contentType: false,
        processData: false,
        data: form_data,
        type: 'post',
        success: function(data){
            
          var respuestas = data.split(';'); 
          if (respuestas[0] == "OK") { 
                
                var TotalRegistros=respuestas[1];
                var Tablas=JSON.parse(respuestas[2]);        
                var TotalTablas=Object.keys(Tablas).length;
                if(TotalRegistros>0){
                    BackupTabla(Tablas,0,TotalTablas,TotalRegistros);                    
                }else{
                    document.getElementById('hTituloBackCreacionTablas').innerHTML="No hay registros por respaldar";
                    setTimeout('VerificaRegistrosXRespaldar()',60*1000);
                }          
          }else {
                document.getElementById('hTituloBackCreacionTablas').innerHTML=data;
          }
        },
        error: function (xhr, ajaxOptions, thrownError) {
            alertify.alert("Error al tratar de buscar los datos a respaldar "+xhr.status+" "+thrownError);
            
          }
      })        
}  
/**
 * inicia el respaldo de las tablas
 * @param {type} Tablas
 * @param {type} TotalRegistros
 * @returns {undefined}
 */
function BackupTabla(Tablas,IndiceTabla,TotalTablas,TotalRegistros){
    if(IndiceTabla>=TotalTablas){
      setTimeout('VerificaRegistrosXRespaldar()',60*1000);
      return;
    }
    var tabla=Tablas[IndiceTabla]["Nombre"];
    
    var TotalRegistrosTabla=Tablas[IndiceTabla]["Registros"];
    //console.log(tabla+" "+TotalRegistrosTabla);
    document.getElementById('hTituloBackCreacionTablas').innerHTML="Backup a tabla "+tabla;
          
    var form_data = new FormData();
        form_data.append('idAccion', 4); 
        form_data.append('tabla', tabla); 
        $.ajax({
        url: '../../general/procesadores/backup.process.php',
        //dataType: 'json',
        cache: false,
        contentType: false,
        processData: false,
        data: form_data,
        type: 'post',
        success: function(data){
            
          var respuestas = data.split(';'); 
          //console.log(data);
          if (respuestas[0] == "OK") { 
                var RegistrosFaltantes=respuestas[1];
                var RegistrosCreados=TotalRegistrosTabla-RegistrosFaltantes;                
                var porcentaje=Math.round((100/TotalRegistrosTabla)*RegistrosCreados);
                $('#prBackCreacionTablas').css('width',porcentaje+'%');
                document.getElementById('prBackCreacionTablas').innerHTML=porcentaje+"%";  
                if(RegistrosCreados>=TotalRegistrosTabla){
                    IndiceTabla++;
                    BackupTabla(Tablas,IndiceTabla,TotalTablas,TotalRegistros);
                    
                }else if(RegistrosFaltantes > 0){
                    BackupTabla(Tablas,IndiceTabla,TotalTablas,TotalRegistros);
                }
          }else {
                console.log(data);
                IndiceTabla++;
                setTimeout('BackupTabla()',60*1000,Tablas,IndiceTabla,TotalTablas,TotalRegistros);
                //BackupTabla(Tablas,IndiceTabla,TotalTablas,TotalRegistros);
          }
        },
        error: function (xhr, ajaxOptions, thrownError) {
            alertify.alert("Error al tratar de buscar los datos a respaldar "+xhr.status+" "+thrownError);
          }
      })   
    
}


BackupsIni();
//VerificaRegistrosXRespaldar();