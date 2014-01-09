
/*

    Sergio Álvarez (xergio)
    mail@xergio.net
    http://xergio.net
    
    Licencia Creative Commons BY-SA
    <http://creativecommons.org/licenses/by-sa/2.0/>

*/


$(function() {

    // first load con #hash
    if (window.location.hash.substr(0, 1) == '#' && window.location.hash.length > 1) {
        window.location.href = '/reload.php?key='+ window.location.hash.substr(1);
        return;
    }
    
    var pregs = ['preg_match', 'preg_match_all', 'preg_split', 'preg_replace', 'preg_filter', 'preg_quote'],
        update_timeout = undefined,
        shhh,
        visible_inputs = ["pattern", "subject", "replacement"],
        $form = $("form"),
        last_hash = location.hash;
    

    // nada de reenviar la web
    $form.on('submit', function() { return false; });
    

    var results = function(json) {
        //console.log(json)
    	try {
            $('#dump').html(json.result.content);
	        $('#code').html(json.result.reason || json.result.use);
            $('#return').removeClass('error').removeClass('ok').addClass(json.result.status);
            $('#input_pattern').removeClass('error').removeClass('ok').addClass(json.result.status);
            last_hash = location.hash = '#'+ (json.result.permalink? json.result.permalink: '');

    	} catch (e) {
	        $('#code').html('Unexpected error');
    		$('#return').removeClass('error').removeClass('ok').addClass('error');
    		last_hash = location.hash = '#';
    	}
    }
    
    
    var send_request = function() {
        shhh = $.ajax({
            type: $form.attr('method'),
            url: $form.attr('action'),
            data: $form.serialize()
        }).done(results);
    }
    

    // no sé si es necesario hacer Stop del request, pero por si acaso...
    document.send_quietly = function() {
    	try {
    		if (shhh) shhh.abort();
    	} catch (e) {}
    	
    	send_request();
    }


    var handle_push = function(event) {
        if (update_timeout) clearTimeout(update_timeout);
        update_timeout = setTimeout('document.send_quietly()', 300);
    }
	
	
    $('.update').on('keyup', handle_push);
    $('.update').on('click', handle_push);


    var apply_visual_tab = function() {
        // pillo el tab activo y su data-
        var tab_visibles = $(".tabs .active").data("visible").split(" ");

        // los campos que diga los muestro
        $.each(tab_visibles, function(i, input) {
            $('#'+ input).show(100); 
        });

        // el resto los oculto
        $.each($(visible_inputs).not(tab_visibles).get(), function(i, input) {
            $('#'+ input).hide(100); 
        });
    }


    $(document).foundation({
        tab: {
            // con cada cambio redibujamos los inpits de ese tab y 
            //actualizamos el hidden
            callback : function (tab) {
                apply_visual_tab();

                // input hidden con la funcion a ejecutar
                $("#preg_selection").val(tab.context.hash.substr(1));

                // envia el formulario
                document.send_quietly();
            }
        }
    });
    
    // al cargar la index puede venir otra tab activa, asi que 
    //mostramos/ocultamos sus inputs
    apply_visual_tab();

    // si ha sido restaurado un id, hago un primer request 
    //para mostrar el resultado
    if (restored) send_request();

    // para cuando se modifica el hash a mano (c&p de un colega o lo que sea)
    setInterval(function() {
        if (location.hash.length > 1 && location.hash != last_hash) {
            window.location.href = '/reload.php?key='+ location.hash;
        }
    }, 1000);
    
});
