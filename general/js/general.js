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

