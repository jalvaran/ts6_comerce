<?php
header('Access-Control-Allow-Origin: *');
header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept");
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');
header('content-type: application/json; charset=utf-8');
header('Accept: application/json; charset=utf-8');

session_start();
if (!isset($_SESSION['username'])){
  exit("<a href='../../index.php' ><img src='../images/401.png'>Iniciar Sesion </a>");
  
}
$idUser=$_SESSION['idUser'];
$fecha=date("Y-m-d");
 
function callAPI($method, $url, $data){
   $curl = curl_init();

   switch ($method){
      case "POST":
         curl_setopt($curl, CURLOPT_POST, 1);
         if ($data)
            curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
         break;
      case "PUT":
         curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "PUT");
         if ($data)
            curl_setopt($curl, CURLOPT_POSTFIELDS, $data);			 					
         break;
      default:
         if ($data)
            $url = sprintf("%s?%s", $url, http_build_query($data));
   }

   // OPTIONS:
   curl_setopt($curl, CURLOPT_URL, $url);
   curl_setopt($curl, CURLOPT_HTTPHEADER, array(
      'Authorization: Bearer 8Jb24CVCVm6cKNprE4jLmOR3J0H2E5s7hQeRi4rRGsK3Elcp8wImVnN9CtDBu74vjhA0w8t3vFGjNCXi',
      'Content-Type: application/json',
      'Accept: application/json',
   ));
   curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
   curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);

   // EXECUTE:
   $result = curl_exec($curl);
   if(!$result){die("Connection Failure");}
   curl_close($curl);
   return $result;
}
include_once("../clases/facturacion_electronica.class.php");
//include_once("restclient.php");
if( !empty($_REQUEST["Accion"]) ){
    $obCon = new Factura_Electronica($idUser);
    
    switch ($_REQUEST["Accion"]) {
        
        case 1: //Crear una empresa la ruta del web service finalizando con el nit y dv devuelve el token que deberá almacenarse en la base de datos
            
            $url='http://35.238.236.240/api/ubl2.1/config/900833180/7';
             $body= ('{
                "type_document_identification_id": 6,
                "type_organization_id": 1,
                "type_regime_id": 2,
                "type_liability_id": 19,
                "business_name": "TECHNO SOLUCIONES SAS",
                "merchant_registration": "58653",
                "municipality_id": 1013,
                "address": "CALLE 5 SUR 16 62",
                "phone": 3177740609,
                "email": "jalvaran@gmail.com"
              }
            ');
           
             
              $make_call = callAPI('POST', $url, $body);
              $response = ($make_call);
              
              print_r($response);         
          
        break; //Fin caso 1
        
        case 2: // crear el software, codigo otorgado por la dian al momento de registrarse como facturador electronico 
            /*
             * {"message":"Software creado con \u00e9xito","software":{"identifier":"7166f2cf-1c23-4256-babc-6590588ec878","pin":12345,"url":"https:\/\/vpfe-hab.dian.gov.co\/WcfDianCustomerServices.svc","updated_at":"2019-09-26 14:08:23","created_at":"2019-09-26 14:08:23","id":1}}
             * 
             * 
             */
            
            $url='http://35.238.236.240/api/ubl2.1/config/software';
            $body= ('{
                "id": "7166f2cf-1c23-4256-babc-6590588ec878",
                "pin": 12345
              }
            ');
            
            $make_call = callAPI('PUT', $url, $body);
            $response = ($make_call);

            print_r($response); 
        break;
        
        case 3://Certificado digital
            $url='http://35.238.236.240/api/ubl2.1/config/certificate';
            $body= ('{
                    "certificate": "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",
                    "password": "cnec7KU922"
                  }
                ');
            
            $make_call = callAPI('PUT', $url, $body);
            $response = ($make_call);

            print_r($response); 
        break;//Fin caso 3 
    
        case 4://Resolucion
            $url='http://35.238.236.240/api/ubl2.1/config/resolution';
            $body= ('{
                    "type_document_id": 1,
                    "prefix": "SETP",
                    "from": 990000000,
                    "to": 995000000,
                    "resolution":18760000001,
                    "resolution_date":"0001-01-01",
                    "technical_key":"fc8eac422eba16e22ffd8c6f94b3f40a6e38162c",
                    "date_from":"2019-01-19",
                    "date_to":"2030-01-19"
                  }
                ');
            
            $make_call = callAPI('PUT', $url, $body);
            $response = ($make_call);

            print_r($response); 
        break;//Fin caso 4
    
        case 5: //Crear una factura simple
            $url='http://35.238.236.240/api/ubl2.1/invoice/6ce20f05-a1e4-4188-ab56-8d8e366746e6';
                        
             $body = ('{
                    "number": 990000007,
                    "type_document_id": 1,
                    "customer": {
                        "identification_number": 1094925334,
                        "name": "Frank Aguirre",
                        "phone": 3060606,
                        "address": "CALLE 47 42C 24",
                        "email": "faguirre@soenac.com",
                        "merchant_registration": "No tiene"
                    },
                    "tax_totals": [
                        {
                            "tax_id": 1,
                            "percent": "19.00",
                            "tax_amount": "57000.00",
                            "taxable_amount": "300000.00"
                        }
                    ],
                    "legal_monetary_totals": {
                        "line_extension_amount": "300000.00",
                        "tax_exclusive_amount": "300000.00",
                        "tax_inclusive_amount": "357000.00",
                        "allowance_total_amount": "0.00",
                        "charge_total_amount": "0.00",
                        "payable_amount": "357000.00"
                    },
                    "invoice_lines": [{
                            "unit_measure_id": 642,
                            "invoiced_quantity": "1.000000",
                            "line_extension_amount": "300000.00",
                            "free_of_charge_indicator": false,
                            "tax_totals": [{
                                "tax_id": 1,
                                "tax_amount": "57000.00",
                                "taxable_amount": "300000.00",
                                "percent": "19.00"
                            }],
                            "description": "Base para TV",
                            "code": "6543542313534",
                            "type_item_identification_id": 3,
                            "price_amount": "300000.00",
                            "base_quantity": "1.000000"
                        }
                    ]
                }


            ');

            
              $make_call = callAPI('POST', $url, $body);
              $response = ($make_call);
              
              print_r($response);
            break; //Fin caso 5
        
        case 6: //Factura con multiples items
            $url='http://35.238.236.240/api/ubl2.1/invoice/6ce20f05-a1e4-4188-ab56-8d8e366746e6';
                        
             $body = ('{
                    "number": 990000008,
                    "type_document_id": 1,
                    "customer": {
                        "identification_number": 1234567890,
                        "name": "Customer Test",
                        "phone": 1234567,
                        "address": "CALLE 0 0C 0",
                        "email": "test@test.com",
                        "merchant_registration": "No tiene"
                    },
                    "allowance_charges": [{
                            "charge_indicator": false,
                            "discount_id": 12,
                            "allowance_charge_reason": "Descuento cliente frecuente",
                            "amount": "10000.00",
                            "base_amount": "1720000.00"
                        },
                        {
                            "charge_indicator": false,
                            "discount_id": 8,
                            "allowance_charge_reason": "Descuento por IVA asumido",
                            "amount": "19000.00",
                            "base_amount": "1720000.00"
                        },
                        {
                            "charge_indicator": false,
                            "discount_id": 8,
                            "allowance_charge_reason": "Descuento por temporada",
                            "amount": "30000.00",
                            "base_amount": "1720000.00"
                        },
                        {
                            "charge_indicator": true,
                            "allowance_charge_reason": "Cargo financiero pago 30d",
                            "amount": "15000.00",
                            "base_amount": "1720000.00"
                        },
                        {
                            "charge_indicator": true,
                            "allowance_charge_reason": "Cargo financiero estudio de crédito",
                            "amount": "5000.00",
                            "base_amount": "1720000.00"
                        }
                    ],
                    "tax_totals": [{
                            "tax_id": 1,
                            "percent": "19.00",
                            "tax_amount": "342000.00",
                            "taxable_amount": "1800000.00"
                        },
                        {
                            "tax_id": 10,
                            "tax_amount": "60.00",
                            "taxable_amount": "0.00",
                            "unit_measure_id": 886,
                            "per_unit_amount": "30.00",
                            "base_unit_measure": "1.000000"
                        }
                    ],
                    "legal_monetary_totals": {
                        "line_extension_amount": "1720000.00",
                        "tax_exclusive_amount": "1800000.00",
                        "tax_inclusive_amount": "2062060.00",
                        "allowance_total_amount": "59000.00",
                        "charge_total_amount": "20000.00",
                        "payable_amount": "2023060.00"
                    },
                    "invoice_lines": [{
                            "unit_measure_id": 642,
                            "invoiced_quantity": "1.000000",
                            "line_extension_amount": "250000.00",
                            "free_of_charge_indicator": false,
                            "allowance_charges": [{
                                "charge_indicator": false,
                                "allowance_charge_reason": "Discount",
                                "amount": "50000.00",
                                "base_amount": "300000.00"
                            }],
                            "tax_totals": [{
                                "tax_id": 1,
                                "tax_amount": "47500.00",
                                "taxable_amount": "250000.00",
                                "percent": "19.00"
                            }],
                            "description": "Base para TV",
                            "code": "6543542313534",
                            "type_item_identification_id": 3,
                            "price_amount": "300000.00",
                            "base_quantity": "1.000000"
                        },
                        {
                            "unit_measure_id": 642,
                            "invoiced_quantity": "1.000000",
                            "line_extension_amount": "0.00",
                            "free_of_charge_indicator": true,
                            "reference_price_id": 3,
                            "tax_totals": [{
                                "tax_id": 1,
                                "tax_amount": "19000.00",
                                "taxable_amount": "100000.00",
                                "percent": "19.00"
                            }],
                            "description": "Antena (regalo)",
                            "code": "3543542234414",
                            "type_item_identification_id": 4,
                            "price_amount": "100000.00",
                            "base_quantity": "1.000000"
                        },
                        {
                            "unit_measure_id": 642,
                            "invoiced_quantity": "1.000000",
                            "line_extension_amount": "1410000.00",
                            "free_of_charge_indicator": false,
                            "allowance_charges": [{
                                "charge_indicator": true,
                                "allowance_charge_reason": "Cargo",
                                "amount": "10000.00",
                                "multiplier_factor_numeric": "10.00"
                            }],
                            "tax_totals": [{
                                "tax_id": 1,
                                "tax_amount": "267900.00",
                                "taxable_amount": "1410000.00",
                                "percent": "19.00"
                            }],
                            "description": "TV",
                            "code": "12435423151234",
                            "type_item_identification_id": 4,
                            "price_amount": "1400000.00",
                            "base_quantity": "1.000000"
                        },
                        {
                            "unit_measure_id": 642,
                            "invoiced_quantity": "1.000000",
                            "line_extension_amount": "20000.00",
                            "free_of_charge_indicator": false,
                            "description": "Servicio (excluido)",
                            "code": "6543542313534",
                            "type_item_identification_id": 3,
                            "price_amount": "20000.00",
                            "base_quantity": "1.000000"
                        },
                        {
                            "unit_measure_id": 642,
                            "invoiced_quantity": "1.000000",
                            "line_extension_amount": "40000.00",
                            "free_of_charge_indicator": false,
                            "tax_totals": [{
                                "tax_id": 1,
                                "tax_amount": "7600.00",
                                "taxable_amount": "40000.00",
                                "percent": "19.00"
                            }],
                            "description": "Acarreo",
                            "code": "6543542313534",
                            "type_item_identification_id": 3,
                            "price_amount": "40000.00",
                            "base_quantity": "1.000000"
                        },
                        {
                            "unit_measure_id": 886,
                            "invoiced_quantity": "2.000000",
                            "line_extension_amount": "0.00",
                            "free_of_charge_indicator": true,
                            "reference_price_id": 3,
                            "tax_totals": [{
                                "tax_id": 10,
                                "tax_amount": "60.00",
                                "taxable_amount": "0.00",
                                "unit_measure_id": 886,
                                "per_unit_amount": "30.00",
                                "base_unit_measure": "1.000000"
                            }],
                            "description": "Bolsas",
                            "code": "18937100-7",
                            "type_item_identification_id": 3,
                            "price_amount": "200.00",
                            "base_quantity": "1.000000"
                        }
                    ]
                }


            ');

            
              $make_call = callAPI('POST', $url, $body);
              $response = ($make_call);
              
              print_r($response);
        break;//fin caso 6
        
        case 7://Prueba json
            $url='http://35.238.236.240/api/ubl2.1/invoice/6ce20f05-a1e4-4188-ab56-8d8e366746e6';
            $idFactura="201912090718320_88617600_1575893912";
            $body=$obCon->JSONFactura($idFactura);
            $make_call = callAPI('POST', $url, $body);
              $response = ($make_call);
              
              print_r($response);
        break;//Fin caso 7  
    
        case 8://Recibir una factura y reportarla
            //$idFactura=$obCon->normalizar($_REQUEST["idFactura"]);
            $DatosServidor=$obCon->DevuelveValores("servidores", "ID", 104);
            //$url='http://35.238.236.240/api/ubl2.1/invoice/6ce20f05-a1e4-4188-ab56-8d8e366746e6';
            $url=$DatosServidor["IP"];
            
            $idFactura="201912090948130_52620200_1575902893";
            $body=$obCon->JSONFactura($idFactura);
            $make_call = callAPI('POST', $url, $body);
            $response = ($make_call);
            $obCon->FacturaElectronica_Registre_Respuesta_Server($idFactura,$response,0);
            
            print_r($response);
        break;//Fin caso 8
        case 9://Generar PDF de la Factura Electronica
            //$idFactura=$obCon->normalizar($_REQUEST["idFactura"]);
                        
            $idFactura="201912090948130_52620200_1575902893";
            $DatosFactura=$obCon->DevuelveValores("facturas", "idFacturas", $idFactura);
            $DatosLogFactura=$obCon->DevuelveValores("facturas_electronicas_log", "ID", 3); 
            $JSONFactura= json_decode($DatosLogFactura["RespuestaCompletaServidor"]);
            print("<pre>");
            print_r($JSONFactura);
            print("</pre>");
            $RespuestaReporte=$JSONFactura->responseDian->Envelope->Body->SendTestSetAsyncResponse->SendTestSetAsyncResult->ErrorMessageList->_attributes->nil;
            if($RespuestaReporte='true'){
                $obCon->CrearPDFDesdeBase64($JSONFactura->pdfBase64Bytes,$DatosFactura);
                
                exit("OK;PDF de la Factura Electronica Creado Satisfactoriamente");
            }else{
                exit("E1;Hubo un error en la recepcion del archivo por parte de la DIAN");
            }
            
        break;//Fin caso 9
        
        case 10://Generar ZIP de la Factura Electronica
            //$idFactura=$obCon->normalizar($_REQUEST["idFactura"]);
                        
            $idFactura="201912090948130_52620200_1575902893";
            $DatosFactura=$obCon->DevuelveValores("facturas", "idFacturas", $idFactura);
            $DatosLogFactura=$obCon->DevuelveValores("facturas_electronicas_log", "ID", 3); 
            $JSONFactura= json_decode($DatosLogFactura["RespuestaCompletaServidor"]);
            $RespuestaReporte=$JSONFactura->responseDian->Envelope->Body->SendTestSetAsyncResponse->SendTestSetAsyncResult->ErrorMessageList->_attributes->nil;
            if($RespuestaReporte='true'){
                $obCon->CrearZIPDesdeBase64($JSONFactura->zipBase64Bytes,$DatosFactura);
                exit("OK;ZIP de la Factura Electronica Creado Satisfactoriamente");
            }else{
                exit("E1;Hubo un error en la recepcion del archivo por parte de la DIAN");
            }
            
        break;//Fin caso 10
    }
    
    
          
}else{
    print("No se enviaron parametros");
}
?>
