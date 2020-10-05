<?php

/**
 * Description of html_est Clase para generar codigo html dentro de php
 *
 * @author Wilson Alberto M C
 */
class html_estruct_class {
    
    /**
     * Metodo que indica el tipo de rendreizacon del html
     */
    function tipo_html(){
        
        print('<!DOCTYPE html>');
        
    } 
    
    /**
     * Metodo html para definir el cuerpo del html
     * @param type $lang ->atr. lang del elemento html lenguage con el cual estara definido al html
     * @param type $vectorhtml ->vector por si se desea meter mas atributos al elemento(se debe definir antes de ulitlizarlo y en la clase definir el uso para tal atributo)
     * @param type $Script -> Evento o funcionaliada
     */
    function html($lang,$vectorhtml,$Script,$ng_app=''){
        
        print('<html lang="'.$lang.'" '.$Script.' '.$ng_app.'>');
    }
    
    /**
     * Metodo Chmtl para cerrar el cuerpo del html
     */
    function Chtml(){
        
        print('</html>');
    }
    
    /**
     * Metodo head para el area head de la pagina
     */
    function head(){
         
        print('<head>');
     }
     
    /**
     * Metodo Chead para el cierre del area head de la pagina
     */
    function Chead(){
         
        print('</head>');
     }
     
    /**
      * Metodo meta para definir los metadatos de la pagina
      * @param type $name ->atr. lang del elemento meta
      * @param type $content ->atr. content del elemento meta
      * @param type $charset ->atr. charset del elemento meta
      * @param type $http_equiv ->atr. $http_equiv del elemento meta
      * @param type $vectorhtml ->vector por si se desea meter mas atributos al elemento(se debe definir antes de ulitlizarlo y en la clase definir el uso para tal atributo)
      */
    function meta($name,$content,$charset,$http_equiv,$vectorhtml){
         
        print('<meta name="'.$name.'" content="'.$content.'" $charset="'.$charset.'" http-equiv="'.$http_equiv.'" >');
     }
     
    /**
     * Metodo contenido para ingresar el contenido que ira dentro de un elemento html 
     */
    function conten($contenido){
         
        print($contenido);
     }
     
    /**
     * Metodo title para el definir el title dentro del head de la pagina
     */
    function title(){
         
        print('<title>');
     }
     
    /**
     * Metodo Ctitle para el cierre del title dentro del head de la pagina
     */
    function Ctitle(){
         
        print('</title>');
     }
     
    /**
      * Metodo link para el enlace a un recurso externo dentro del head
      * @param type $rel ->atr. rel del elemento link
      * @param type $href ->atr. hret del elemento link
      * @param type $type ->atr. type del elemento link
      * @param type $vectorhtml ->vector por si se desea meter mas atributos al elemento(se debe definir antes de ulitlizarlo y en la clase definir el uso para tal atributo)
      */
    function link($rel,$href,$type,$sizes,$vectorhtml,$Script){
         
        print('<link rel="'.$rel.'" href="'.$href.'" type="'.$type.'" sizes="'.$sizes.'" '.$Script.' >');
     }
     
    /**
     * Metodo style para definir estilos en el documento
     * @param type $type ->atr. type del elemento style
     * @param type $vectorhtml ->vector por si se desea meter mas atributos al elemento(se debe definir antes de ulitlizarlo y en la clase definir el uso para tal atributo)
     */ 
    function style($type,$vectorhtml,$Script){
         
        print('<style type="'.$type.'" '.$Script.' >'); 
     }
     
    /**
     * Metodo Cstyle para cerrar los estilos  en el documento
     */ 
    function Cstyle(){
         
        print('</style>');   
     }
     
    /**
     * Metodo base para URL predeterminada y un destino predeterminado para todos los enlaces en una pÃ¡gina
     * @param type $href ->atr. href del elemento base
     * @param type $target ->atr. traget del elemento base
     * @param type $vectorhtml ->vector por si se desea meter mas atributos al elemento(se debe definir antes de ulitlizarlo y en la clase definir el uso para tal atributo)
     */
    function base($href,$target,$vectorhtml,$Script){
       
         print('<base href="'.$href.'" target="'.$target.'" '.$Script.' >');
     }
    
    /**
     * Metodo script para definir un script o apunta a un archivo de script externo a travÃ©s del atributo src
     * @param type $src -> atr. src del elemneto script
     * @param type $type -> atr. type del elemneto script
     * @param type $vectorhtml ->vector por si se desea meter mas atributos al elemento(se debe definir antes de ulitlizarlo y en la clase definir el uso para tal atributo)
     */ 
    function script($src,$type,$vectorhtml,$Script){
        
         print('<script src="'.$src.'" type="'.$type.'" '.$Script.' >');
    } 
    
    /**
     * Metodo Cscript para cerrar los script en el documento
     */
    function Cscript(){
        
         print('</script>');
    }
    
    /**
     * Metodo noscript para mostrar que la paguna nosoporta javascript
     */
    function noscript(){
        
         print('<noscript>');
    }
    
    /**
     * Metodo Cnoscript para cerrar el contenido el noscript
     */
    function Cnoscript(){
        
         print('</noscript>');
    }
    
    /**
     *Metodo body para el cuerpo de la pagina
     * @param type $vectorhtml ->vector por si se desea meter mas atributos al elemento(se debe definir antes de ulitlizarlo y en la clase definir el uso para tal atributo)  
     */
    function body($vectorhtml,$class="",$Script='',$ng_app=""){
        
         print('<body class="'.$class.'" '.$Script.' '.$ng_app.' >');
    }
    
    /**
     *Metodo Cbody para cerrar el cuerpo de la pagina  
     */
    function Cbody(){
        
         print('</body>');
    }
    
    /**
     * Metodo div para crear contenedores
     * https://www.w3schools.com/tags/tag_div.asp 
     * @param type $id ->atr global. id del elemento div
     * @param type $class ->atr global. class del elemento div
     * @param type $vectorhtml ->vector por si se desea meter mas atributos al elemento(se debe definir antes de ulitlizarlo y en la clase definir el uso para tal atributo)
     */
    function div($id,$class,$vectorhtml,$role,$ng_angular,$Script,$style){
        
         print('<div id="'.$id.'" class="'.$class.'"  role="'.$role.'" '.$ng_angular.'  '.$Script.' '.$style.' >');
        
    }
    
    /**
     * Metodo Cdiv para cerrar contenedores
     */
    function Cdiv(){
        
         print('</div>');
        
    }
    
    /**
     * Metodo h1 para etiquetas
     * https://www.w3schools.com/tags/tag_hn.asp
     * @param type $id ->atr global. id del elemento h1
     * @param type $class ->atr global. id del elemento h1
     * @param type $vectorhtml ->vector por si se desea meter mas atributos al elemento(se debe definir antes de ulitlizarlo y en la clase definir el uso para tal atributo)
     */
    function h1($id,$class,$vectorhtml,$Script){
        
         print('<h1 id="'.$id.'" class="'.$class.'" '.$Script.' >');
        
        
    }
    
    /**
     * Metodo Ch1 para cerra una etiqueta
     */
    function Ch1(){
        
         print('</h1>');
        
        
    }
    
    /**
     * Metodo h2 para etiquetas
     * https://www.w3schools.com/tags/tag_hn.asp
     * @param type $id ->atr global. id del elemento h2
     * @param type $class ->atr global. class del elemento h2
     * @param type $vectorhtml ->vector por si se desea meter mas atributos al elemento(se debe definir antes de ulitlizarlo y en la clase definir el uso para tal atributo)
     */
    function h2($id,$class,$vectorhtml,$Script){
        
         print('<h2 id="'.$id.'" class="'.$class.'" '.$Script.' >');
        
        
    }
    
    /**
     * Metodo Ch2 para cerra una etiqueta
     */
    function Ch2(){
        
         print('</h2>');
        
        
    }
    
    /**
     * Metodo h3 para etiquetas
     * https://www.w3schools.com/tags/tag_hn.asp
     * @param type $id ->atr global. id del elemento h3
     * @param type $class ->atr global. id del elemento h3
     * @param type $vectorhtml ->vector por si se desea meter mas atributos al elemento(se debe definir antes de ulitlizarlo y en la clase definir el uso para tal atributo)
     */
    function h3($id,$class,$vectorhtml,$Script){
        
         print('<h3 id="'.$id.'" class="'.$class.'" '.$Script.' >');
        
        
    }
    
    /**
     * Metodo Ch3 para cerra una etiqueta
     */
    function Ch3(){
        
         print('</h3>');
        
        
    }
    
    /**
     * Metodo h4 para etiquetas
     * https://www.w3schools.com/tags/tag_hn.asp
     * @param type $id ->atr global. id del elemento h4
     * @param type $class ->atr global. id del elemento h4
     * @param type $vectorhtml ->vector por si se desea meter mas atributos al elemento(se debe definir antes de ulitlizarlo y en la clase definir el uso para tal atributo)
     */
    function h4($id,$class,$vectorhtml,$Script){
        
         print('<h4 id="'.$id.'" class="'.$class.'" '.$Script.' >');
        
        
    }
    
    /**
     * Metodo Ch4 para cerra una etiqueta
     */
    function Ch4(){
        
         print('</h4>');
        
        
    }
    
    /**
     * Metodo h5 para etiquetas
     * https://www.w3schools.com/tags/tag_hn.asp
     * @param type $id ->atr global. id del elemento h5
     * @param type $class ->atr global. id del elemento h5
     * @param type $vectorhtml ->vector por si se desea meter mas atributos al elemento(se debe definir antes de ulitlizarlo y en la clase definir el uso para tal atributo)
     */
    function h5($id,$class,$vectorhtml,$Script){
        
         print('<h5 id="'.$id.'" class="'.$class.'" '.$Script.' >');
        
        
    }
    
    /**
     * Metodo Ch5 para cerra una etiqueta
     */
    function Ch5(){
        
         print('</h5>');
        
        
    }
    
    /**
     * Metodo h6 para etiquetas
     * https://www.w3schools.com/tags/tag_hn.asp
     * @param type $id ->atr global. id del elemento h6
     * @param type $class ->atr global. id del elemento h6
     * @param type $vectorhtml ->vector por si se desea meter mas atributos al elemento(se debe definir antes de ulitlizarlo y en la clase definir el uso para tal atributo)
     */
    function h6($id,$class,$vectorhtml,$Script){
        
         print('<h6 id="'.$id.'" class="'.$class.'" '.$Script.' >');
        
        
    }
    
    /**
     * Metodo Ch6 para cerra una etiqueta
     */
    function Ch6(){
        
         print('</h6>');
        
        
    }
    
    /**
     * Metodo p para parrafos
     * https://www.w3schools.com/tags/tag_p.asp
     * @param type $id ->atr global. id 
     * @param type $class ->atr global. class
     * @param type $vectorhtml ->vector por si se desea meter mas atributos al elemento(se debe definir antes de ulitlizarlo y en la clase definir el uso para tal atributo)
     */
    function p($id,$class,$vectorhtml,$Script){
        
         print('<p id="'.$id.'" class="'.$class.'" '.$Script.' >');

    
    }
    
    /**
     * Metodo Cp para cerrar parrafos
     */
    function Cp(){
        
         print('</p>');
        
        
    }
    
    /**
     * Metodo a para hipervinculos
     * https://www.w3schools.com/tags/tag_a.asp
     * @param type $id ->atr global. id del elemento a
     * @param type $class ->atr global. class del elemento a
     * @param type $href ->atr. href del elemento a
     * @param type $target ->atr. target del elemento a
     * @param type $role -> rol del elemneto
     * @param type $vectorhtml ->vector por si se desea meter mas atributos al elemento(se debe definir antes de ulitlizarlo y en la clase definir el uso para tal atributo)
     */
    function a($id,$class,$href,$target,$role,$vectorhtml,$Script){
        
         print('<a id="'.$id.'" class="'.$class.'" href="'.$href.'" target="'.$target.'" role="'.$role.'" '.$Script.' >');
    
    
    }
    
    /**
     * Metodo Ca para cerrar vinculos
     */
    function Ca(){
        
         print('</a>');
        
        
    }
    
    /**
     * Metodo span
     * https://www.w3schools.com/tags/tag_span.asp 
     * @param type $id ->atr global. id del elemento span
     * @param type $class ->atr global. id del elemento span
     * @param type $vectorhtml ->vector por si se desea meter mas atributos al elemento(se debe definir antes de ulitlizarlo y en la clase definir el uso para tal atributo)
     */
    function span($id,$class,$vectorhtml,$Script,$style=""){
        
        print('<span id="'.$id.'" class="'.$class.'" '.$Script.' '.$style.'>');
    }
    
    /**
     * Metodo Cspan para cerrar </span>
     */
    function Cspan(){
        
         print('</span>');
        
    }
    
    /**
     * Metodo strong para resaltar
     * https://www.w3schools.com/tags/tag_strong.asp 
     * @param type $id ->atr global. id del elemento strong
     * @param type $class ->atr global. id del elemento strong
     * @param type $vectorhtml ->vector por si se desea meter mas atributos al elemento(se debe definir antes de ulitlizarlo y en la clase definir el uso para tal atributo)
     */
    function strong($id,$class,$vectorhtml,$Script){
        
         print('<strong id="'.$id.'" class="'.$class.'" '.$Script.' >');
        
    }
    
    /**
     * Metodo Cstrong para cerrar strong
     */
    function Cstrong(){
        
         print('</strong>');
        
    }
    
    /**
     * Metodo i
     * https://www.w3schools.com/tags/tag_i.asp  
     * @param type $id ->atr global. id del elemento i
     * @param type $class ->atr global. class del elemento i
     * @param type $vectorhtml ->vector por si se desea meter mas atributos al elemento(se debe definir antes de ulitlizarlo y en la clase definir el uso para tal atributo)
     */
    function i($id,$class,$vectorhtml,$Script){
        
         print('<i id="'.$id.'" class="'.$class.'" '.$Script.' >');
        
    }
    
    /**
     * Metodo Ci para cerrar
     */
    function Ci(){
        
         print('</i>');
        
    }
    
    /**
     * Metodo form para formularios 
     * https://www.w3schools.com/tags/tag_form.asp
     * @param type $id ->atr global. id del elemento form
     * @param type $class ->atr global. class del elemento form
     * @param type $name ->atr. name del elemento form
     * @param type $method ->atr. method del elemento form
     * @param type $action ->atr. action del elemento form
     * @param type $target ->atr. target del elemento form
     * @param type $vectorhtml ->vector por si se desea meter mas atributos al elemento(se debe definir antes de ulitlizarlo y en la clase definir el uso para tal atributo)
     */
    function form($id,$class,$name,$method,$action,$target,$vectorhtml,$Script){
        
        print('<form id="'.$id.'" class="'.$class.'" name="'.$name.'" method="'.$method.'" action="'.$action.'" target="'.$target.'" '.$Script.' >');
    }
    
    /**
     * Metodo Cform para cerra el formulario
     */
    function Cform(){
        
        print('</form>');
        
    }
    
    /**
     * Metodo input para definir campo de entrada
     * https://www.w3schools.com/tags/tag_input.asp
     * @param type $type ->atr. type del elemento input
     * @param type $id ->atr global. id 
     * @param type $class ->atr global. class
     * @param type $name ->atr. name del elemento input
     * @param type $title ->atr global. id
     * @param type $value ->atr. value del elemento input
     * @param type $placeholder ->atr. placeholder del elemento input
     * @param type $autocomplete ->atr. autocomplete del elemento input
     * @param type $vectorhtml ->vector por si se desea meter mas atributos al elemento(se debe definir antes de ulitlizarlo y en la clase definir el uso para tal atributo)
     */
    function input( $type,$id,$class,$name,$title,$value,$placeholder,$autocomplete,$vectorhtml,$Script,$styles='',$Pattern='',$np_app=''){
        
        print('<input type="'.$type.'" id="'.$id.'" class="'.$class.'" name="'.$name.'" title="'.$title.'" value="'.$value.'" placeholder="'.$placeholder.'" autocomplete="'.$autocomplete.'" '.$Script.' '.$styles.' '.$np_app.' '.$Pattern.' required>');
    }
    
    /**
     * Metodo texarea para definir campo de entrada texto largo
     * https://www.w3schools.com/tags/tag_textarea.asp
     * @param type $id ->atr global. id 
     * @param type $class ->atr global. class
     * @param type $name ->atr. name del elemento texarea
     * @param type $title ->atr global. id
     * @param type $placeholder ->atr. placeholder del elemento texarea
     * @param type $vectorhtml ->vector por si se desea meter mas atributos al elemento(se debe definir antes de ulitlizarlo y en la clase definir el uso para tal atributo)
     */
    function textarea($id,$class,$name,$title,$placeholder,$vectorhtml,$Script){
        
        print('<textarea id="'.$id.'" class="'.$class.'" name="'.$name.'" title="'.$title.'" placeholder="'.$placeholder.'" '.$Script.' >');
    }
    
    /**
     * Metodo Ctexarea para cerra el texarea
     */
    function Ctextarea(){
        
        print('</textarea>');
        
    }
    
    /**
     * Metodo boton para definir campo de boton
     * https://www.w3schools.com/tags/tag_button.asp
     * @param type $id ->atr global. id 
     * @param type $class ->atr global. class
     * @param type $type ->atr. type del elemento boton
     * @param type $name ->atr. name del elemento boton
     * @param type $title ->atr global. id
     * @param type $value ->atr. value del elemento boton
     * @param type $vectorhtml ->vector por si se desea meter mas atributos al elemento(se debe definir antes de ulitlizarlo y en la clase definir el uso para tal atributo)
     */
    function boton($id,$class,$type,$name,$title,$value,$vectorhtml,$Script){
        
        print('<button id="'.$id.'" class="'.$class.'" type="'.$type.'" name="'.$name.'" title="'.$title.'" value="'.$value.'" '.$Script.' >');
    }
    
    
    
    /**
     * Metodo Cboton para cerra el boton
     */
    function Cboton(){
        
        print('</button>');
        
    }
    
    /**
     * Metodo select para definir seleccion
     * https://www.w3schools.com/tags/tag_select.asp
     * @param type $id ->atr global. id 
     * @param type $class ->atr global. class
     * @param type $name ->atr. name del elemento select
     * @param type $title ->atr global. id
     * @param type $vectorhtml ->vector por si se desea meter mas atributos al elemento(se debe definir antes de ulitlizarlo y en la clase definir el uso para tal atributo)
     */
    function select($id,$class,$name,$title,$vectorhtml,$Script,$style,$Requerido=1){  
        $Required="";
        if($Requerido==1){
            $Required="required";
        }
        print('<strong>'.$title.'</strong><select id="'.$id.'" class="'.$class.'" name="'.$name.'" title="'.$title.'" '.$Script.' '.$style.' '.$Required.'>');
    }
    
    /**
     * Metodo Cselect para cerra el select
     */
    function Cselect(){
        
        print('</select>');
        
    }
    
    /**
     * Metodo datalist para definir seleccion con autocompletado
     * https://www.w3schools.com/tags/tag_datalist.asp
     * @param type $id ->atr global. id 
     * @param type $class ->atr global. class
     * @param type $title ->atr global. id
     * @param type $vectorhtml ->vector por si se desea meter mas atributos al elemento(se debe definir antes de ulitlizarlo y en la clase definir el uso para tal atributo)
     */
    function datalist($id,$class,$title,$vectorhtml,$Script){
        
        print('<datalis id="'.$id.'" class="'.$class.'" title="'.$title.'" '.$Script.' >');
    }
    
    /**
     * Metodo Cdatalist para cerra el datalist
     */
    function Cdatalist(){
        
        print('</datalist>');
        
    }
    
    /**
     * Metodo option para definir opciones
     * https://www.w3schools.com/tags/tag_option.asp
     * @param type $id ->atr global. id 
     * @param type $class ->atr global. class
     * @param type $title ->atr global. id
     * @param type $value ->atr. value del elemento option
     * @param type $vectorhtml ->vector por si se desea meter mas atributos al elemento(se debe definir antes de ulitlizarlo y en la clase definir el uso para tal atributo)
     */
    function option($id,$class,$title,$value,$vectorhtml,$Script,$Seleccionar=0,$ng_app=""){
        $Seleccionado="";
        if($Seleccionar==1){
            $Seleccionado="selected";
        }
        print('<option id="'.$id.'" class="'.$class.'" title="'.$title.'" value="'.$value.'" '.$Script.' '.$Seleccionado.' '.$ng_app.'>');
    }
    
    /**
     * Metodo Coption para cerra el option
     */
    function Coption(){
        
        print('</option>');
        
    }
    
    /**
     * Metodo optgroup para definir opciones grupales
     * https://www.w3schools.com/tags/tag_optgroup.asp
     * @param type $id ->atr global. id 
     * @param type $class ->atr global. class
     * @param type $title ->atr global. id
     * @param type $label ->atr. value del elemento optgroup
     * @param type $vectorhtml ->vector por si se desea meter mas atributos al elemento(se debe definir antes de ulitlizarlo y en la clase definir el uso para tal atributo)
     */
    function optgroup($id,$class,$title,$label,$vectorhtml,$Script){
        
        print('<optgroup id="'.$id.'" class="'.$class.'" title="'.$title.'" label="'.$label.'" '.$Script.' >');
    }
    
    /**
     * Metodo Coptgroup para cerra el optgroup
     */
    function Coptgroup(){
        
        print('</optgroup>');
        
    }
    
    /**
     * Metodo fieldset se usa para agrupar elementos relacionados en un formulario
     * https://www.w3schools.com/tags/tag_fieldset.asp
     * @param type $id ->atr global. id 
     * @param type $class ->atr global. class
     * @param type $name ->atr. name del elemento fieldset
     * @param type $title ->atr global. id
     * @param type $vectorhtml ->vector por si se desea meter mas atributos al elemento(se debe definir antes de ulitlizarlo y en la clase definir el uso para tal atributo)
     */
    function fieldset($id,$class,$name,$title,$vectorhtml,$Script){
        
        print('<fieldset id="'.$id.'" class="'.$class.'" name="'.$name.'" title="'.$title.'" '.$Script.' >');
    }
    
    /**
     * Metodo Cfieldset para cerra el fieldset
     */
    function Cfieldset(){
        
        print('</fieldset>');
        
    }
    
    /**
     * Metodo legend define un tÃ­tulo para el elemento <fieldset> 
     * https://www.w3schools.com/tags/tag_legend.asp
     * @param type $vectorhtml ->vector por si se desea meter mas atributos al elemento(se debe definir antes de ulitlizarlo y en la clase definir el uso para tal atributo)
     */
    function legend($vectorhtml,$Script){
        
        print('<legend '.$Script.'  >');
    }
    
    /**
     * Metodo Clegend para cerra el legend
     */
    function Clegend(){
        
        print('</legend>');
        
    }
    
    /**
     * Metodo label define una etiqueta para un elemento <input>
     * https://www.w3schools.com/tags/tag_label.asp
     * @param type $id ->atr global. id 
     * @param type $class ->atr global. class
     * @param type $for ->atr. name del elemento label
     * @param type $vectorhtml ->vector por si se desea meter mas atributos al elemento(se debe definir antes de ulitlizarlo y en la clase definir el uso para tal atributo)
     */
    function label($id,$class,$for,$vectorhtml,$Script){
        
        print('<label id="'.$id.'" class="'.$class.'" for="'.$for.'"  '.$Script.' >');
    }
    
    /**
     * Metodo Clabel para cerra el label
     */
    function Clabel(){
        
        print('</label>');
        
    }
    
    /**
     * Metodo header para definir cabecera
     * https://www.w3schools.com/tags/tag_header.asp
     * @param type $id ->atr global. id 
     * @param type $class ->atr global. class
     * @param type $vectorhtml ->vector por si se desea meter mas atributos al elemento(se debe definir antes de ulitlizarlo y en la clase definir el uso para tal atributo)
     */
    function header($id,$class,$vectorhtml,$Script){
        
        print('<header id="'.$id.'" class="'.$class.'" '.$Script.'  >');
    }
    
    /**
     * Metodo Cheader para cerra la cabecera
     */
    function Cheader(){
        
        print('</header>');
    
    }
    
    /**
     * Metodo nav para definir menus o enlaces de navegacion
     * https://www.w3schools.com/tags/tag_nav.asp
     * @param type $id ->atr global. id 
     * @param type $class ->atr global. class
     * @param type $vectorhtml ->vector por si se desea meter mas atributos al elemento(se debe definir antes de ulitlizarlo y en la clase definir el uso para tal atributo)
     */
    function nav($id,$class,$vectorhtml,$Script){
        
        print('<nav id="'.$id.'" class="'.$class.'" '.$Script.' >');
    }
    
    /**
     * Metodo Cnav para cerra la menus
     */
    function Cnav(){
        
        print('</nav>');
    
    }
    
    /**
     * Metodo section define secciones en un documento, como capÃ­tulos, encabezados, pies de pÃ¡gina o cualquier otra secciÃ³n del documento
     * https://www.w3schools.com/tags/tag_section.asp
     * @param type $id ->atr global. id 
     * @param type $class ->atr global. class
     * @param type $vectorhtml ->vector por si se desea meter mas atributos al elemento(se debe definir antes de ulitlizarlo y en la clase definir el uso para tal atributo)
     */
    function section($id,$class,$vectorhtml,$Script){
        
        print('<section id="'.$id.'" class="'.$class.'" '.$Script.' >');
    }
    
    /**
     * Metodo Csection para cerra las section
     */
    function Csection(){
        
        print('</section>');
    
    }
    
    /**
     * Metodo article define especifica contenido independiente y autÃ³nomo
     * https://www.w3schools.com/tags/tag_article.asp
     * @param type $id ->atr global. id 
     * @param type $class ->atr global. class
     * @param type $vectorhtml ->vector por si se desea meter mas atributos al elemento(se debe definir antes de ulitlizarlo y en la clase definir el uso para tal atributo)
     */
    function article($id,$class,$vectorhtml,$Script){
        
        print('<article id="'.$id.'" class="'.$class.'" '.$Script.' >');
    }
    
    /**
     * Metodo Carticle para cerra los article
     */
    function Carticle(){
        
        print('</article>');
    
    }
    
    /**
     * Metodo aside define algÃºn contenido aparte del contenido en el que se coloca.
     * https://www.w3schools.com/tags/tag_aside.asp
     * @param type $id ->atr global. id 
     * @param type $class ->atr global. class
     * @param type $vectorhtml ->vector por si se desea meter mas atributos al elemento(se debe definir antes de ulitlizarlo y en la clase definir el uso para tal atributo)
     */
    function aside($id,$class,$vectorhtml,$Script){
        
        print('<aside id="'.$id.'" class="'.$class.'" '.$Script.' >');
    }
    
    /**
     * Metodo Carticle para cerra los article
     */
    function Caside(){
        
        print('</aside>');
    
    }
    
    /**
     * Metodo footer define un pie de pÃ¡gina para un documento o secciÃ³n.
     * https://www.w3schools.com/tags/tag_footer.asp
     * @param type $id ->atr global. id 
     * @param type $class ->atr global. class
     * @param type $vectorhtml ->vector por si se desea meter mas atributos al elemento(se debe definir antes de ulitlizarlo y en la clase definir el uso para tal atributo)
     */
    function footer($id,$class,$vectorhtml,$Script){
        
        print('<footer id="'.$id.'" class="'.$class.'" '.$Script.' >');
    }
    
    /**
     * Metodo Carticle para cerra los article
     */
    function Cfooter(){
        
        print('</footer>');
    
    }
    
    /**
     * Metodo br para saltos de linea
     */
    function br(){
        
         print('<br>');
    }
    
    /**
     * Metodo table define una tabla HTML
     * https://www.w3schools.com/tags/tag_table.asp
     * @param type $id ->atr global. id 
     * @param type $class ->atr global. class
     * @param type $vectorhtml ->vector por si se desea meter mas atributos al elemento(se debe definir antes de ulitlizarlo y en la clase definir el uso para tal atributo)
     */
    function table($id,$class,$vectorhtml,$Script){
        
        print('<table id="'.$id.'" class="'.$class.'" '.$Script.' >');
    }
    
    /**
     * Metodo Ctable para cerra la tabla
     */
    function Ctable(){
        
        print('</table>');
    
    }
    
    /**
     * Metodo tr define una fila en una tabla HTML
     * https://www.w3schools.com/tags/tag_tr.asp
     * @param type $id ->atr global. id 
     * @param type $class ->atr global. class
     * @param type $colspan ->atr. colspan del elemento tr
     * @param type $rowspan ->atr. rowspan del elemento tr
     * @param type $vectorhtml ->vector por si se desea meter mas atributos al elemento(se debe definir antes de ulitlizarlo y en la clase definir el uso para tal atributo)
     */
    function tr($id,$class,$colspan,$rowspan,$vectorhtml,$Script,$style=""){
        
        print('<tr id="'.$id.'" class="'.$class.'" colspan="'.$colspan.'" rowspan="'.$rowspan.'" '.$Script.' '.$style.'>');
    }
    
    /**
     * Metodo Ctr para cerra la fila
     */
    function Ctr(){
        
        print('</tr>');
    
    }
    
    /**
     * Metodo td define una columna en una tabla HTML
     * https://www.w3schools.com/tags/tag_td.asp
     * @param type $id ->atr global. id 
     * @param type $class ->atr global. class
     * @param type $colspan ->atr. colspan del elemento td
     * @param type $rowspan ->atr. rowspan del elemento td
     * @param type $vectorhtml ->vector por si se desea meter mas atributos al elemento(se debe definir antes de ulitlizarlo y en la clase definir el uso para tal atributo)
     */
    function td($id,$class,$colspan,$rowspan,$vectorhtml,$Script,$style=""){
        
         print('<td id="'.$id.'" class="'.$class.'" colspan="'.$colspan.'" rowspan="'.$rowspan.'" '.$Script.' '.$style.' >');
    }
    
    /**
     * Metodo Ctd para cerra la columna
     */
    function Ctd(){
        
        print('</td>');
    
    }
    
    /**
     * Metodo th define el encabezado de celda
     * https://www.w3schools.com/tags/tag_th.asp
     * @param type $id ->atr global. id 
     * @param type $class ->atr global. class
     * @param type $colspan ->atr. colspan del elemento th
     * @param type $rowspan ->atr. rowspan del elemento th
     * @param type $vectorhtml ->vector por si se desea meter mas atributos al elemento(se debe definir antes de ulitlizarlo y en la clase definir el uso para tal atributo)
     */
    function th($id,$class,$colspan,$rowspan,$vectorhtml,$Script){
        
         print('<td id="'.$id.'" class="'.$class.'" colspan="'.$colspan.'" rowspan="'.$rowspan.'" '.$Script.' >');
    }
    
    /**
     * Metodo Ctr para cerra el en cabezado de la fila
     */
    function Cth(){
        
        print('</th>');
    
    }
    
    /**
     * Metodo caption define una leyenda de tabla, La etiqueta <caption> debe insertarse inmediatamente despuÃ©s de la etiqueta <table>
     * https://www.w3schools.com/tags/tag_caption.asp
     * @param type $id ->atr global. id 
     * @param type $class ->atr global. class
     * @param type $vectorhtml ->vector por si se desea meter mas atributos al elemento(se debe definir antes de ulitlizarlo y en la clase definir el uso para tal atributo)
     */
    function caption($id,$class,$vectorhtml,$Script){
        
        print('<caption id="'.$id.'" class="'.$class.'" '.$Script.' >');
    }
    
    /**
     * Metodo Ccaption para cerra la leyenda <caption> 
     */
    function Ccaption(){
        
        print('</caption>');
    
    }
    
    /**
     * Metodo thead  se usa para agrupar el contenido del encabezado en una tabla HTML
     * https://www.w3schools.com/tags/tag_thead.asp
     * @param type $id ->atr global. id 
     * @param type $class ->atr global. class
     * @param type $vectorhtml ->vector por si se desea meter mas atributos al elemento(se debe definir antes de ulitlizarlo y en la clase definir el uso para tal atributo)
     */
    function thead($id,$class,$vectorhtml,$Script){
        
        print('<thead id="'.$id.'" class="'.$class.'" '.$Script.' >');
    }
    
    /**
     * Metodo Cthead para cerra el contenido del encabezado de tabla </thead>
     */
    function Cthead(){
        
        print('</thead>');
    
    }
    
    /**
     * Metodo tbody se usa para agrupar el contenido del cuerpo en una tabla HTML.
     * https://www.w3schools.com/tags/tag_tbody.asp
     * @param type $id ->atr global. id 
     * @param type $class ->atr global. class
     * @param type $vectorhtml ->vector por si se desea meter mas atributos al elemento(se debe definir antes de ulitlizarlo y en la clase definir el uso para tal atributo)
     */
    function tbody($id,$class,$vectorhtml,$Script){
        
        print('<tbody id="'.$id.'" class="'.$class.'" '.$Script.' >');
    }
    
    /**
     * Metodo Ctbody para cerra el contenido del cuerpo de tabla </tbody>
     */
    function Ctbody(){
        
        print('</tbody>');
    
    }
    
    /**
     * Metodo tfoot se usa para agrupar el contenido del pie de pÃ¡gina en una tabla HTML.
     * https://www.w3schools.com/tags/tag_tfoot.asp
     * @param type $id ->atr global. id 
     * @param type $class ->atr global. class
     * @param type $vectorhtml ->vector por si se desea meter mas atributos al elemento(se debe definir antes de ulitlizarlo y en la clase definir el uso para tal atributo)
     */
    function tfoot($id,$class,$vectorhtml,$Script){
        
        print('<tfoot id="'.$id.'" class="'.$class.'" '.$Script.' >');
    }
    
    /**
     * Metodo Ctfoot para cerra el contenido del pie tabla </tfoot>
     */
    function Ctfoot(){
        
        print('</tfoot>');
    
    }
    
    /**
     * Metodo img define una imagen en una pÃ¡gina HTML.
     * https://www.w3schools.com/tags/tag_img.asp
     * @param type $id ->atr global. id 
     * @param type $class ->atr global. class
     * @param type $src ->atr. src del elemento img
     * @param type $alt ->atr. alt del elemento img  
     * @param type $vectorhtml ->vector por si se desea meter mas atributos al elemento(se debe definir antes de ulitlizarlo y en la clase definir el uso para tal atributo)
     */
    function img($id,$class,$src,$alt,$vectorhtml,$Script){
        
        print('<img id="'.$id.'" class="'.$class.'" src="'.$src.'" alt="'.$alt.'" '.$Script.' >');
    }
    
    /**
     * Metodo ul define una lista desordenada (con viÃ±etas).
     * https://www.w3schools.com/tags/tag_ul.asp
     * @param type $id ->atr global. id 
     * @param type $class ->atr global. class
     * @param type $vectorhtml ->vector por si se desea meter mas atributos al elemento(se debe definir antes de ulitlizarlo y en la clase definir el uso para tal atributo)
     */
    function ul($id,$class,$vectorhtml,$Script){
        
        print('<ul id="'.$id.'" class="'.$class.'" '.$Script.' >');
    }
    
    /**
     * Metodo Cul para cerra la lista desordenada </ul>
     */
    function Cul(){
        
        print('</ul>');
    
    }
    
    /**
     * Metodo ol define una lista ordenada. Una lista ordenada puede ser numÃ©rica o alfabÃ©tica
     * https://www.w3schools.com/tags/tag_ol.asp
     * @param type $id ->atr global. id 
     * @param type $class ->atr global. class
     * @param type $vectorhtml ->vector por si se desea meter mas atributos al elemento(se debe definir antes de ulitlizarlo y en la clase definir el uso para tal atributo)
     */
    function ol($id,$class,$vectorhtml,$Script){
        
        print('<ol id="'.$id.'" class="'.$class.'" '.$Script.' >');
    }
    
    /**
     * Metodo Col para cerra la lista ordenada </ol>
     */
    function Col(){
        
        print('</ol>');
    
    }
    
    /**
     * Metodo li define un elemento de la lista
     * https://www.w3schools.com/tags/tag_li.asp
     * @param type $id ->atr global. id 
     * @param type $class ->atr global. class
     * @param type $vectorhtml ->vector por si se desea meter mas atributos al elemento(se debe definir antes de ulitlizarlo y en la clase definir el uso para tal atributo)
     */
    function li($id,$class,$vectorhtml,$Script){
        
        print('<li id="'.$id.'" class="'.$class.'" '.$Script.' >');
    }
    
    /**
     * Metodo Cli para cerra la lista </li>
     */
    function Cli(){
        
        print('</li>');
    
    }
    
    /**
     * Metodo details especifica detalles adicionales que el usuario puede ver u ocultar a pedido.
     * https://www.w3schools.com/tags/tag_details.asp
     * @param type $id ->atr global. id 
     * @param type $class ->atr global. class
     * @param type $vectorhtml ->vector por si se desea meter mas atributos al elemento(se debe definir antes de ulitlizarlo y en la clase definir el uso para tal atributo)
     */
    function details($id,$class,$vectorhtml,$Script){
        
        print('<details id="'.$id.'" class="'.$class.'" '.$Script.' >');
    }
    
    /**
     * Metodo Cdetails cerra la detalles adicionales </details>
     */
    function Cdetails(){
        
        print('</details>');
    
    }
    
    /**
     * Metodo summary define un encabezado visible para el elemento <details> . Se puede hacer clic en el encabezado para ver / ocultar los detalles.
     * https://www.w3schools.com/tags/tag_summary.asp
     * @param type $id ->atr global. id 
     * @param type $class ->atr global. class
     * @param type $vectorhtml ->vector por si se desea meter mas atributos al elemento(se debe definir antes de ulitlizarlo y en la clase definir el uso para tal atributo)
     */
    function summary($id,$class,$vectorhtml,$Script){
        
        print('<summary id="'.$id.'" class="'.$class.'" '.$Script.' >');
    }
    
    /**
     * Metodo Csummary cerra la detalles adicionales </summary>
     */
    function Csummary(){
        
        print('</summary>');
    
    }
    
    /**
     * Metodo button para definir un botÃ³n en el que se puede hacer clic.
     * https://www.w3schools.com/tags/tag_button.asp
     * @param type $type ->atr. type del elemento button
     * @param type $id ->atr global. id 
     * @param type $class ->atr global. class
     * @param type $name ->atr. name del elemento button
     * @param type $title ->atr global. id
     * @param type $value ->atr. value del elemento button
     * @param type $data_toggle ->atr. data-* del elemento button
     * @param type $data_target ->atr. data-* del elemento button
     * @param type $vectorhtml ->vector por si se desea meter mas atributos al elemento(se debe definir antes de ulitlizarlo y en la clase definir el uso para tal atributo)
     */
    function button($type,$id,$class,$name,$title,$value,$data_toggle,$data_target,$vectorhtml,$Script,$ng_angular){
        
        print('<button type="'.$type.'" id="'.$id.'" class="'.$class.'" name="'.$name.'" title="'.$title.'" value="'.$value.'" data-toggle="'.$data_toggle.'" data-target="'.$data_target.'" '.$Script.' '.$ng_angular.' >');
    }
      
    /**
     * Metodo Cbutton cerra la detalles adicionales </button>
     */
    function Cbutton(){
        
        print('</button>');
    
    }
    
    
}
