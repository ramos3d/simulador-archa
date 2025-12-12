<?php
declare(strict_types=1);

class PrecificacaoService
{
    private const DESCONTO_PROMOCIONAL = [
        'percentual' => 0,     // 0 = sem desconto | ex.: 0.15 = 15%
        'descricao'  => "",
    ];

    /* =================== CONSTANTES =================== */
    private const AMB_CONST   = 488.0;    // AB2
    private const AMB_DECAY   = 0.1;      // AB3
    private const AMB_MIN     = 100.0;

    private const MET_CONST   = 5.0;      // AC2
    private const MET_DECAY   = 0.0005;   // AC3

    // Pisos solicitados
    private const MINIMO_POR_AMBIENTE = 200.0; // R$200 por ambiente
    private const MINIMO_POR_M2       = 3.0;   // R$3 por metro quadrado

    private const PRAZOS  = [15 => 0.30, 21 => 0.00, 28 => -0.05];
    private const REGIOES = ['MINHA_REGIAO' => 0.10, 'BRASIL_TODO' => 0.00];
    private const TIPOS = [
        'RESIDENCIAL: Apartamento'                        => 0.00,
        'RESIDENCIAL: Casa'                               => 0.05,
        'COMERCIAL: Hotelaria'                            => 0.15,
        'COMERCIAL: Bares, restaurantes e casas noturnas' => 0.10,
        'CORPORATIVO: Escritório'                         => 0.10,
        'COMERCIAL: Lojas varejo'                         => 0.07,
        'COMERCIAL: Clínicas e espaços estéticos'         => 0.07,
        'EVENTOS: Estandes'                               => 0.00,
        'EVENTOS: Espaços para eventos e/ou masterplan/palco' => 0.00,
        'OUTROS' => 0.00,
    ];

    private const ADICIONAIS = [
        'MARCENARIA'               => 0.03,
        'MARMORE_GRANITO'          => 0.03,
        'PINTURA_PAREDES_PISOS'    => 0.02,
        'ALTERACAO_TETO'           => 0.03,
        'NOVOS_PISOS'              => 0.03,
        'ELETRODOMESTICOS'         => 0.02,
        'TOMADAS_INTERRUPTORES'    => 0.03,
        'CHUVEIROS_SANITARIO'      => 0.03,
        'PAREDES_ALVENARIA_DRYWALL'=> 0.03,
        'CONSTRUCAO_NOVA_AREA'     => 0.15,
    ];

    private const MULTIPLICADORES_CATEGORIA = [
        'Estreantes' => 1.0,
        'Verificados'=> 1.2,  // +20%
        'Preferidos' => 1.5   // +50%
    ];

    private const FATOR_SERVICO = 1.33; // Z2
    private const FATOR_FINDERS = 1.25; // AA2
    private const FATOR_CARTAO  = 1.15;

    private const S2 = 2.4445; // multiplicador Duo
    private const W2 = 3.3333; // multiplicador Trio
    private const S3 = 0.0;    // piso Duo
    private const W3 = 0.0;    // piso Trio
    private const O3 = 0.0;    // piso Solo

    /* ========== Taxas extras por origem (paridade com Laravel) ========== */
    private const TAXAS_ORIGEM = [
        'vogue'                => 1200.00,
        'lufe'                 => 2200.00,
        'living_wellness'      => 100.00,
        'living_pacific_belem' => 100.00,
    ];
    private const APLICA_TAXA_NOS_PLANOS = ['solo', 'duo', 'trio'];

    private static function normalizarOrigem(?string $o): string
    {
        $o = strtolower(trim((string)$o));
        if ($o === '') return '';
        $map = [
            'casavogue' => 'vogue',
            'casa_vogue' => 'vogue',
            'casa-vogue' => 'vogue',
            'life_by_lufe' => 'lufe',
            'lifebylufe'    => 'lufe',
            'living-wellness' => 'living_wellness',
            'living_pacific_belem' => 'living_pacific_belem',
            'living-pacific-belem' => 'living_pacific_belem',
        ];
        return $map[$o] ?? $o;
    }

    private static function detectarOrigem(array $params): string
    {
        $raw = $params['origem'] ?? $params['utm_source'] ?? null;
        if (!$raw && isset($_COOKIE['utm_source'])) $raw = $_COOKIE['utm_source'];

        if (!$raw && !empty($_SERVER['HTTP_REFERER'])) {
            $ref  = parse_url($_SERVER['HTTP_REFERER']);
            $host = strtolower($ref['host'] ?? '');
            $path = strtolower($ref['path'] ?? '');
            if (str_contains($host, 'casavogue') || str_contains($path, '/casa-vogue')) $raw = 'vogue';
            elseif (str_contains($path, '/lifebylufe')) $raw = 'lufe';
            elseif (str_contains($path, '/living-wellness')) $raw = 'living_wellness';
            elseif (str_contains($path, '/living-pacific-belem')) $raw = 'living_pacific_belem';
        }
        return self::normalizarOrigem($raw);
    }

    private static function taxaPorOrigem(?string $origem): float
    {
        $o = self::normalizarOrigem($origem);
        return self::TAXAS_ORIGEM[$o] ?? 0.0;
    }

    /* =================== Núcleo de cálculo =================== */
    private function fatorAmbiente(int $amb): float
    {
        return max(self::AMB_CONST * exp(-self::AMB_DECAY * $amb), self::AMB_MIN);
    }

    private function fatorMetragem(float $m2): float
    {
        return self::MET_CONST * exp(-self::MET_DECAY * $m2);
    }

    private function somaPercentuais(array $p): float
    {
        $pct = 0.0;
        foreach (($p['adicionais'] ?? []) as $a) {
            $pct += self::ADICIONAIS[$a] ?? 0.0;
        }
        if (!empty($p['nova_area'])) {
            $pct += self::ADICIONAIS['CONSTRUCAO_NOVA_AREA'] ?? 0.0;
        }
        $pct += self::PRAZOS[$p['prazo']  ?? 21]      ?? 0.0;
        $pct += self::REGIOES[$p['regiao'] ?? 'BRASIL_TODO'] ?? 0.0;
        foreach (($p['tipos'] ?? []) as $t) {
            $pct += self::TIPOS[$t] ?? 0.0;
        }
        return $pct;
    }

    /* ---- Pisos unitários (sempre aplicados) ---- */
    private function precoUnitM2(float $m2): float
    {
        $calc = $this->fatorMetragem($m2);
        return max($calc, self::MINIMO_POR_M2);              // NEW
    }

    private function precoUnitAmb(int $amb): float
    {
        $calc = $this->fatorAmbiente($amb);
        return max($calc, self::MINIMO_POR_AMBIENTE);        // NEW
    }

    /* ---- Soma incremental por ambiente (monotônica) ---- */
    private function custoAmbientes(int $amb): float        // NEW
    {
        $total = 0.0;
        for ($i = 1; $i <= max(0, $amb); $i++) {
            $total += $this->precoUnitAmb($i);               // aplica piso por ambiente
        }
        return $total;
    }

    private function premioSolo(array $p, float $pct): float
    {
        // base = (m² * preço_unit_m²_com_piso) + soma_incremental_ambientes
        $base = ($p['m2'] * $this->precoUnitM2($p['m2']))    // NEW
              + $this->custoAmbientes($p['amb']);            // NEW

        $categoria = $p['categoria'] ?? 'Estreantes';
        $fCat = self::MULTIPLICADORES_CATEGORIA[$categoria] ?? 1.0;

        return max($base * (1 + $pct) * $fCat, self::O3);
    }

    private function repasseTotalDuo(float $O4): float
    {
        return max($O4 * self::S2, self::S3);
    }

    private function premioTrio(float $O4): float
    {
        return max($O4 * self::W2, self::W3);
    }

    /* =================== INTERFACE =================== */
    public function calcular(array $p): array
    {
        $origem   = $p['origem'] ?? self::detectarOrigem($p);
        $taxaExtra= self::taxaPorOrigem($origem);

        $pct = $this->somaPercentuais($p);
        $O4  = $this->premioSolo($p, $pct);
        $S4  = $this->repasseTotalDuo($O4);
        $W4  = $this->premioTrio($O4);

        $fMarca = self::FATOR_FINDERS * self::FATOR_SERVICO;

        $N4 = $O4 * $fMarca;                // solo à vista (base)
        $P4 = $S4 * $fMarca;                // duo  à vista (base)
        $V4 = $W4 * $fMarca;                // trio à vista (base)

        $parcelado = fn(float $v) => $v * self::FATOR_CARTAO;

        // Valores finais (antes da taxa do parceiro)
        $solo_avista      = round($N4);
        $solo_parcelado   = round($parcelado($N4));
        $duo_avista_base  = round($P4);
        $duo_parc_base    = round($parcelado($P4));
        $trio_avista_base = round($V4);
        $trio_parc_base   = round($parcelado($V4));

        // Aplica taxa fixa por origem (se configurada)
        $solo_avista    += in_array('solo', self::APLICA_TAXA_NOS_PLANOS) ? (int)round($taxaExtra) : 0;
        $solo_parcelado += in_array('solo', self::APLICA_TAXA_NOS_PLANOS) ? (int)round($taxaExtra) : 0;
        $duo_avista      = $duo_avista_base  + (in_array('duo',  self::APLICA_TAXA_NOS_PLANOS) ? (int)round($taxaExtra) : 0);
        $duo_parcel      = $duo_parc_base    + (in_array('duo',  self::APLICA_TAXA_NOS_PLANOS) ? (int)round($taxaExtra) : 0);
        $trio_avista     = $trio_avista_base + (in_array('trio', self::APLICA_TAXA_NOS_PLANOS) ? (int)round($taxaExtra) : 0);
        $trio_parcel     = $trio_parc_base   + (in_array('trio', self::APLICA_TAXA_NOS_PLANOS) ? (int)round($taxaExtra) : 0);

        // Preserva valores sem desconto promocional
        $solo_parcelado_sem_desc = $solo_parcelado;
        $duo_parcel_sem_desc     = $duo_parcel;
        $trio_parcel_sem_desc    = $trio_parcel;

        // Desconto opcional
        $descontoPct = self::DESCONTO_PROMOCIONAL['percentual'];
        if ($descontoPct > 0) {
            $aplicar = fn(float $v) => $v * (1 - $descontoPct);
            $solo_avista    = round($aplicar($solo_avista));
            $solo_parcelado = round($aplicar($solo_parcelado));
            $duo_avista     = round($aplicar($duo_avista));
            $duo_parcel     = round($aplicar($duo_parcel));
            $trio_avista    = round($aplicar($trio_avista));
            $trio_parcel    = round($aplicar($trio_parcel));
        }

        // Retorno
        return [
            'solo' => ['a_vista' => $solo_avista, 'parcelado' => $solo_parcelado, 'parcelado_base' => $solo_parcelado_sem_desc],
            'duo'  => ['a_vista' => $duo_avista,  'parcelado' => $duo_parcel,    'parcelado_base' => $duo_parcel_sem_desc],
            'trio' => ['a_vista' => $trio_avista, 'parcelado' => $trio_parcel,   'parcelado_base' => $trio_parcel_sem_desc],

            // Alguns intermediários úteis para conferência rápida no front (opcional)
            'intermediarios' => [
                'premioSolo'       => $O4,
                'repasseTotalDuo'  => $S4,
                'premioTrio'       => $W4,
                'unit_m2'          => $this->precoUnitM2($p['m2']), // NEW
                'unit_amb_ultimo'  => $this->precoUnitAmb($p['amb']), // NEW
            ],
            'meta' => [
                'origem_detectada'    => $origem,
                'taxa_extra_aplicada' => $taxaExtra,
            ],
        ];
    }
}

/* =================== CONTROLLER (mesmo de antes) =================== */
header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-store');

try {
    $params = $_POST + $_GET;

    $service = new PrecificacaoService();

    echo json_encode($service->calcular([
        'm2'         => (float)($params['metragem']  ?? 20),
        'amb'        => (int)($params['ambientes']   ?? 1),
        'prazo'      => (int)($params['prazo']       ?? 21),
        'regiao'     => $params['regiao']            ?? 'BRASIL_TODO',
        'adicionais' => $params['adicionais']        ?? [],
        'tipos'      => $params['tipos']             ?? ['RESIDENCIAL: Apartamento'],
        'categoria'  => $params['categoria']         ?? 'Estreantes',
        'nova_area'  => !empty($params['nova_area']),
        'origem'     => $params['origem'] ?? $params['utm_source'] ?? ($_COOKIE['utm_source'] ?? null),
    ]), JSON_UNESCAPED_UNICODE);
} catch (Throwable $e) {
    http_response_code(400);
    echo json_encode(['error' => true, 'message' => $e->getMessage()]);
}
