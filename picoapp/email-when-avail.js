import { component } from 'picoapp';

export default component((node, ctx) => {
    const open_button = node.querySelector('.open-out-of-stock-frm');
    const close_button = node.querySelector('.close-out-of-stock-frm');
    const out_of_stock_frm = node.querySelector('.out-of-stock-frm');
    const email_input_wrapper = node.querySelector('.email-input-wrapper');
    const notify_button = document.getElementById('notify_button');
    const email_input = document.getElementById('notify_email');
    const title = node.querySelector('.title');
    const success = node.querySelector('.success');
    const error_msg = node.querySelector('.error');

    open_button.addEventListener('click', e => {
        e.preventDefault();
        open_button.style.display = "none";
        fadeIn(out_of_stock_frm);
    });

    close_button.addEventListener('click', e => {
        e.preventDefault();
        fadeOut(out_of_stock_frm);
        fadeIn(open_button,'inline-block');
    });
    var notificationCallback = function(data) {
        var msg = '';
        if (data.status == 'OK') {
            msg = data.message; // just show the success message
            fadeOut(email_input_wrapper);
            fadeOut(title);
            fadeIn(success);
        } else { // it was an error
            for (var k in data.errors) {  // collect all the error messages into a string
                if (k=="email") {
                    console.log('FAIL');
                } else {
                    msg += (" " + data.errors[k].join());
                }
                error_msg.innerHTML = msg;
            }

        }
    }
    notify_button.addEventListener('click', e => {
        e.preventDefault();
        var email = email_input.value,
            productId = notify_button.getAttribute('data-product-id'),
            variantId = notify_button.getAttribute('data-variant-id');
        BIS.create(email, variantId, productId).then(notificationCallback);  
    });

    // ** FADE OUT FUNCTION **
    function fadeOut(el) {
        el.style.opacity = 1;
        (function fade() {
            if ((el.style.opacity -= .1) < 0) {
                el.style.display = "none";
            } else {
                requestAnimationFrame(fade);
            }
        })();
    };

    // ** FADE IN FUNCTION **
    function fadeIn(el, display) {
        el.style.opacity = 0;
        el.style.display = display || "block";
        (function fade() {
            var val = parseFloat(el.style.opacity);
            if (!((val += .1) > 1)) {
                el.style.opacity = val;
                requestAnimationFrame(fade);
            }
        })();
    };
});
