<!-- WhatsApp Widget (bottom-right fixed) -->
<div class="whatsapp-container" id="whatsappWidget" aria-live="polite">
    <div class="whatsapp-message" id="whatsappMessage" style="opacity: 0; visibility: hidden;">
        <button type="button" class="close-message" id="whatsappClose" aria-label="Fechar mensagem">
            <img src="images/close-modal.png" alt="" />
        </button>
        Precisa de ajuda?
    </div>

    <a
        href="https://api.whatsapp.com/send/?phone=5511942892984&text=Para+iniciar+seu+atendimento%2C+envie+uma+mensagem+como+esta%3A+Ol%C3%A1%21+Gostaria+de+contratar+um+projeto+de+arquitetura+com+a+Archa.&type=phone_number&app_absent=0"
        target="_blank"
        rel="noopener noreferrer"
        class="whatsapp-widget"
        title="Fale conosco"
        aria-label="Falar com a Archa no WhatsApp">
        <img src="images/icon-colored-whatsapp.svg" alt="Whatsapp" />
    </a>
</div>

<style>
    .whatsapp-container {
        position: fixed;
        right: 18px;
        bottom: 18px;
        z-index: 9999;
        display: flex;
        flex-direction: column;
        align-items: flex-end;
        gap: 10px;
        pointer-events: none;
    }

    /* Quando encostar no footer, sobe um pouco para não sobrepor */
    .whatsapp-container.is-near-footer {
        bottom: 96px;
    }

    .whatsapp-container .whatsapp-widget {
        pointer-events: auto;
        width: 56px;
        height: 56px;
        border-radius: 999px;
        background: #ffffff;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        box-shadow: 0 10px 30px rgba(0, 0, 0, .18);
        border: 1px solid rgba(0, 0, 0, .08);
        text-decoration: none;
        transform: translateZ(0);
        transition: transform .15s ease, box-shadow .15s ease;
    }

    .whatsapp-container .whatsapp-widget:hover,
    .whatsapp-container .whatsapp-widget:focus {
        transform: translateY(-2px);
        box-shadow: 0 14px 38px rgba(0, 0, 0, .22);
        outline: none;
    }

    .whatsapp-container .whatsapp-widget img {
        width: 56px;
        height: 56px;
        display: block;
    }

    .whatsapp-message {
        pointer-events: auto;
        background: #262942;
        color: #ffffff;

        /* garante espaço pro botão de fechar não encostar no texto */
        padding: 10px 14px 10px 12px;

        border-radius: 12px;
        font-size: 0.95rem;
        line-height: 1.1;
        box-shadow: 0 10px 28px rgba(0, 0, 0, .18);
        max-width: 210px;
        position: relative;
        transition: opacity .2s ease, transform .2s ease, visibility .2s ease;
        transform: translateY(6px);
    }

    .whatsapp-message::after {
        content: "";
        position: absolute;
        right: 16px;
        bottom: -8px;
        width: 0;
        height: 0;
        border-left: 8px solid transparent;
        border-right: 8px solid transparent;
        border-top: 8px solid #262942;
    }

    .close-message {
        all: unset;
        cursor: pointer;
        position: absolute;

        /* continua um pouco pra fora do balão */
        top: -5px;
        right: -5px;

        /* o botão fica do tamanho do conteúdo (o ícone) */
        width: auto;
        height: auto;

        /* remove o “círculo” grande */
        background: transparent;
        border-radius: 0;
        box-shadow: none;

        /* mantém clicável sem virar um bloco gigante */
        padding: 0;
        line-height: 0;

        opacity: .95;
        pointer-events: auto;
    }

    .close-message img {
        display: block;

        /* tamanho real do seu ícone */
        width: 16px;
        height: 16px;

        /* opcional: se quiser um micro “respiro” sem virar bordão */
        background: #fff;
        border-radius: 999px;
        padding: 0;
        /* deixe 0 para ficar colado ao ícone */
    }


    .close-message:hover {
        opacity: 1;
        transform: translateY(-1px);
    }

    @media (max-width: 576px) {
        .whatsapp-container {
            right: 14px;
            bottom: 14px;
        }

        .whatsapp-container.is-near-footer {
            bottom: 86px;
        }

        .whatsapp-container .whatsapp-widget {
            width: 54px;
            height: 54px;
        }

        .whatsapp-container .whatsapp-widget img {
            width: 54px;
            height: 54px;
        }

        .close-message {
            width: 20px;
            height: 20px;
            top: -9px;
            right: -9px;
        }

        .close-message img {
            width: 11px;
            height: 11px;
        }
    }
</style>

<script>
    (function() {
        const container = document.getElementById('whatsappWidget');
        const message = document.getElementById('whatsappMessage');
        const closeBtn = document.getElementById('whatsappClose');
        const link = container ? container.querySelector('a.whatsapp-widget') : null;
        if (!container || !message || !closeBtn || !link) return;

        const LS_KEY = 'whatsapp_help_dismissed_v1';

        function showMessage() {
            if (localStorage.getItem(LS_KEY) === '1') return;
            message.style.visibility = 'visible';
            message.style.opacity = '1';
            message.style.transform = 'translateY(0)';
        }

        function hideMessage(persist) {
            message.style.opacity = '0';
            message.style.transform = 'translateY(6px)';
            setTimeout(() => {
                message.style.visibility = 'hidden';
            }, 180);
            if (persist) localStorage.setItem(LS_KEY, '1');
        }

        setTimeout(showMessage, 1400);

        closeBtn.addEventListener('click', function() {
            hideMessage(true);
        });

        link.addEventListener('click', function() {
            localStorage.setItem(LS_KEY, '1');
        });

        const sentinel = document.getElementById('footer-sentinel');
        if ('IntersectionObserver' in window && sentinel) {
            const io = new IntersectionObserver((entries) => {
                const isVisible = entries.some(e => e.isIntersecting);
                container.classList.toggle('is-near-footer', isVisible);
            }, {
                root: null,
                threshold: 0.01
            });

            io.observe(sentinel);
        }
    })();
</script>