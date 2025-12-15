<?php

/** includes/checkout/_totais.php
 * Bloco: Totais (subtotal, desconto, total + observação + “Pagamento seguro”)
 * Obs.: Não inclui cupom nem dados de pagamento. Use _cupom.php e _pagamento.php.
 */
?>
<div class="ck-body">



    <div class="d-flex justify-content-between mb-1 text-azul">
        <span>Subtotal</span>
        <span id="sumSubtotal">R$ 0,00</span>
    </div>
    <div class="d-flex justify-content-between mb-2 text-azul">
        <span>Desconto aplicado:</span>
        <span id="sumDesc">R$ 0,00</span>
    </div>

    <hr class="margem-custom-bottom desktop-only">

    <div class="d-flex justify-content-between align-items-center">
        <div class="text-azul">Total</div>
        <!--<div class="price-big" id="sumTotal">R$ 514,56</div>-->
        <span id="sumTotal" class="price-big"
            data-avista="<?= isset($valor_avista) ? (float)$valor_avista : 0 ?>"
            data-parcelado-total="<?= isset($valor_parcelado_total) ? (float)$valor_parcelado_total : 0 ?>">
            R$ 0,00
        </span>

    </div>

    <div class="d-flex align-items-center mt-3 flex-nowrap">
        <!-- texto à ESQUERDA ocupando o espaço -->
        <span class="ck-muted f-exo me-auto f-12 desktop-only">
            O valor pode ser ajustado se houver <br>
            divergência nas informações enviadas <br>
            em relação a planta do ambiente físico.
        </span>

        <span class="ck-muted f-exo me-auto f-12 mobile-only">
            O valor pode ser ajustado <br>
            se houver divergência nas<br>
            informações enviadas em <br>
            relação a planta do<br>
            ambiente físico.<br>
        </span>

        <!-- ícone + texto à DIREITA, juntos -->
        <span class="d-inline-flex align-items-center gap-2 flex-shrink-0">
            <img src="images/icon-lock.png" alt="Seguro" width="18" height="18">
            <span class="ck-muted f-exo f-14">Pagamento seguro</span>
        </span>
    </div>
</div>