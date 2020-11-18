/**
 * Controlador para funciones generales de la aplicacion
 * JULIAN ALVARAN 2020-05-16
 * TECHNO SOLUCIONES SAS 
 * 317 774 0609
 */
function openModal(idModal){
    var id="#"+idModal;
    $(id).modal();
}

function closeModal(idModal){
    
    $("#"+idModal).modal('hide');//ocultamos el modal
    $('body').removeClass('modal-open');//eliminamos la clase del body para poder hacer scroll
    $('.modal-backdrop').remove();//eliminamos el backdrop del modal
}

function initFormMD(){
    $('.mdl-textfield').on('blur',function(e) {
      this.element_.classList.remove(this.CssClasses_.IS_FOCUSED);
    });

    $('.mdl-textfield').on('focus',function(e) {
      this.element_.classList.add(this.CssClasses_.IS_FOCUSED);
    });

    $('.mdl-textfield').on('input',function(e) {
      this.checkDisabled(), this.checkValidity(), this.checkDirty(), this.checkFocus();
    });

    $('.mdl-textfield').on('reset',function(e) {
      this.updateClasses_();
    });
    
}

function MuestraOcultaXID(id,Mostrar){
    if(Mostrar==1){
        document.getElementById(id).style.display="block";
    }else{
        document.getElementById(id).style.display="none";
    }
}

function MarqueErrorElemento(idElemento){
    console.log(idElemento);
    if(idElemento==undefined){
       return; 
    }
    document.getElementById(idElemento).style.backgroundColor="pink";
    document.getElementById(idElemento).focus();
}


function importScript(name) {
    var s = document.createElement("script");
    s.src = name;
    document.querySelector("head").appendChild(s);
}

function tableToExcel(table, name, filename) {
           let uri = 'data:application/vnd.ms-excel;base64,', 
           template = '<html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel" xmlns="http://www.w3.org/TR/REC-html40"><title></title><head><!--[if gte mso 9]><xml><x:ExcelWorkbook><x:ExcelWorksheets><x:ExcelWorksheet><x:Name>{worksheet}</x:Name><x:WorksheetOptions><x:DisplayGridlines/></x:WorksheetOptions></x:ExcelWorksheet></x:ExcelWorksheets></x:ExcelWorkbook></xml><![endif]--><meta http-equiv="content-type" content="text/plain; charset=UTF-8"/></head><body><table>{table}</table></body></html>', 
           base64 = function(s) { return window.btoa(decodeURIComponent(encodeURIComponent(s))) },         format = function(s, c) { return s.replace(/{(\w+)}/g, function(m, p) { return c[p]; })}
           
           if (!table.nodeType) table = document.getElementById(table)
           var ctx = {worksheet: name || 'Worksheet', table: table.innerHTML}

           var link = document.createElement('a');
           link.download = filename;
           link.href = uri + base64(format(template, ctx));
           link.click();
}

function limpiar_select2(select_id){
    //$("#"+select_id).select2("val", "");
    $("#"+select_id).empty();
}
