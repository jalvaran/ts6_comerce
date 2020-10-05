
function EnviaFormSC() {

	document.FormMesa.submit();
		
}

function EnviaForm(idForm) {
	
	document.getElementById(idForm).submit();
		
}

function EnviaFormDepar() {

	document.FormDepar.submit();
		
}

function EnviaFormOrden() {

	document.FormOrden.submit();
		
}

function incrementa(id) {

	document.getElementById(id).value++;
	

}

function decrementa(id) {

if(document.getElementById(id).value > 1)
	document.getElementById(id).value--;

}
function cargar(){

$("#contenido").load("contpedidos.php");

}

function refresca(seg) {
	setTimeout("cargar()",seg);
}


function cargarMesas(){

$("#contenidoMesas").load("contMesas.php");

}

function refrescaMesas(seg) {
	setTimeout("cargarMesas()",seg);
}

function posiciona(id){ 
   
   document.getElementById(id).focus();
}

function CalculeDevuelta() {

	var total;
	var paga;
	var devuelta;
	
	total =  parseInt(document.getElementById("TxtGranTotalH").value);
	paga =  parseInt(document.getElementById("TxtPaga").value);
	
	devuelta= paga - total;
	
	document.getElementById("TxtDevuelta").value=devuelta;

}

function atajos()
{


shortcut("Ctrl+Q",function()
{
document.getElementById("TxtPaga").focus();
});
shortcut("Ctrl+E",function()
{
document.getElementById("TxtCodigoBarras").focus();
});
shortcut("Ctrl+B",function()
{
document.getElementById("TxtBuscarItem").focus();
});

shortcut("Ctrl+D",function()
{
document.getElementById("TxtBuscarCliente").focus();
});

shortcut("Ctrl+S",function()
{
document.getElementById("BtnGuardar").click();
});

}

function CreaRazonSocial() {

    campo1=document.getElementById('TxtPA').value;
    campo2=document.getElementById('TxtSA').value;
	campo3=document.getElementById('TxtPN').value;
    campo4=document.getElementById('TxtON').value;
	

    Razon=campo3+" "+campo4+" "+campo1+" "+campo2;

    document.getElementById('TxtRazonSocial').value=Razon;


}

//Funcion para enviar el contenido de una caja de texto a una pagina y dibujarlo en un div
function EnvieObjetoConsulta(Page,idElement,idTarget,BorrarId=1){
    
       
        if (window.XMLHttpRequest) {
                // code for IE7+, Firefox, Chrome, Opera, Safari
                httpEdicion = new XMLHttpRequest();
            } else {
                // code for IE6, IE5
                httpEdicion = new ActiveXObject("Microsoft.XMLHTTP");
            }
            httpEdicion.onreadystatechange = function() {
                if (this.readyState == 4 && this.status == 200) {
                    document.getElementById(idTarget).innerHTML = this.responseText;
                    
                }
            };
        
        httpEdicion.open("GET",Page,true);
        httpEdicion.send();
        
        //alert("Sale");
}

function myTimer(page) {
    
    EnvieObjetoConsulta(page,`tab-1`,`DivProcesosInternos`,`NO`);
}