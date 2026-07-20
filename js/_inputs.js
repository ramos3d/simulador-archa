$(document).ready(() => {
    $(document).on('input', '#telefone', function () {
        const somenteNumeros = this.value.replace(/\D+/g, ''); // \D = tudo que NÃO é dígito
        this.value = somenteNumeros;
    });

    // valida e-mail toda vez que o usuário sai do campo (blur) ou digita (keyup)
    $('#email').on('blur keyup', function () {
        const $input = $(this);
        const email = $input.val().trim();

        // regex enxuto e suficiente p/ grande maioria dos casos
        const emailValido = /^[^\s@]+@[^\s@]+\.[^\s@]+$/i.test(email);

        if (email === '') {          //  ➜ campo vazio, remove estados
            $input.removeClass('is-invalid is-valid');
            $('#email-erro').addClass('d-none');
            return;
        }

        if (emailValido) {
            $input.removeClass('is-invalid').addClass('is-valid');
            $('#email-erro').addClass('d-none');
        } else {
            $input.removeClass('is-valid').addClass('is-invalid');
            $('#email-erro').removeClass('d-none');
        }
    });


    $('#nome').on('input', function () {
        let valor = $(this).val();
        // Substitui tudo que não for letra (incluindo acentuadas e ç)
        valor = valor.replace(/[^a-zA-ZÀ-ÿ\u00C0-\u017F\s]/g, '');
        $(this).val(valor);
    });

});