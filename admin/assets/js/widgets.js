jQuery(function($){

    function subscribeWidget(){
        var form = $('#wbcr-factory-subscribe-widget-form');
        form.submit(function(ev){
            ev.preventDefault();
            var agree = form.find('[name=agree_terms]:checked');
            if(agree.length === 0){
                return;
            }

            $.ajax({
                method: "POST",
                url: "https://clearfy.pro/subscribe-widget/",
                data: form.serialize(),
                success: function(data){
                    if(data.errors === undefined || data.errors.length === 0){
                        form.html("");
                        $("#wbcr-factory-subscribe-widget-msg-ok").show();
                    }else{
                        alert('Something went wrong :(');
                        console.error(data.errors);
                    }
                },
                error: function(){

                }
            });
        });
    }

    subscribeWidget();

});