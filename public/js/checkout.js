Stripe.setPublishableKey('pk_test_4P1b1wzeZFBLclMkEymPERgN');
var $form = $('#chart_form');
$(document).on("click","#finish_chart",function() {
    $('#charge-error').addClass('hidden');
});
$(document).on("click","#extra_finish",function() {

    Stripe.card.createToken({
        number: $('#card-number').val(),
        cvc: $('#card-cvc').val(),
        exp_month: $('#card-expiry-month').val(),
        exp_year: $('#card-expiry-year').val()
    }, stripeResponseHandler);
    return false;
});

function stripeResponseHandler (status, response){
    if(response.error){
        $('#charge-error').removeClass('hidden');
        $('#charge-error').text(response.error.message);
    }else{
        // Get the token ID:
        var token = response.id;

        // Insert the token into the form so it gets submitted to the server:
        $form.append($('<input type="hidden" name="stripeToken" />').val(token));

        // Submit the form:
        $form.get(0).submit();
    }
}
