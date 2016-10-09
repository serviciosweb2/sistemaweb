<script>
    function getPais(){
        var pais = "<?php $ci=& get_instance(); $session=$ci->session->userdata('filial'); echo $region=$session['pais'];?>";
        var formato='';
        switch(pais){
            case '1':
                formato='dd/mm/yy';

            default :
                formato='dd/mm/yy';
        }    
        return formato;    
    }
    
    var inputFecha=['datepicker','fechaDesde'];    
    $(inputFecha).each(function(){
        $( "#"+this ).datepicker({
            changeMonth: true,
            changeYear: true,
            yearRange: "1930:2020",
            dateFormat: getPais()
        });    
    });
     
    $('.input-group-addon').click(function(){
        var valor = $(this).parent().find('input').trigger('focus');
        $(this).focusout();
    });
</script>