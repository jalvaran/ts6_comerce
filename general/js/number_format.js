    function number_format(numero,decimals=0){
        var numero_formatear=parseFloat(numero,decimals);
        //console.log(numero+" || "+numero_formatear);
        return (numero_formatear.toLocaleString('en', {minimumFractionDigits: decimals, maximumFractionDigits: decimals}));
    }