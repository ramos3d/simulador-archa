<?php
declare(strict_types=1);

class Calculator
{
    private const AMB_CONST  = 488.0;
    private const AMB_DECAY  = 0.1;
    private const AMB_MIN    = 100.0;
    private const MET_CONST  = 5.0;
    private const MET_DECAY  = 0.0005;
    private const MIN_POR_AMB = 200.0;
    private const MIN_POR_M2  = 3.0;

    private const PRAZOS  = [15 => 0.30, 21 => 0.00, 28 => -0.05];
    private const REGIOES = ['MINHA_REGIAO' => 0.10, 'BRASIL_TODO' => 0.00];
    private const TIPOS   = [
        'RESIDENCIAL: Apartamento'                            => 0.00,
        'RESIDENCIAL: Casa'                                   => 0.05,
        'COMERCIAL: Hotelaria'                                => 0.15,
        'COMERCIAL: Bares, restaurantes e casas noturnas'     => 0.10,
        'CORPORATIVO: Escritório'                             => 0.10,
        'COMERCIAL: Lojas varejo'                             => 0.07,
        'COMERCIAL: Clínicas e espaços estéticos'             => 0.07,
        'EVENTOS: Estandes'                                   => 0.00,
        'EVENTOS: Espaços para eventos e/ou masterplan/palco' => 0.00,
        'OUTROS'                                              => 0.00,
    ];
    private const ADICIONAIS = [
        'MARCENARIA'                => 0.03,
        'MARMORE_GRANITO'           => 0.03,
        'PINTURA_PAREDES_PISOS'     => 0.02,
        'ALTERACAO_TETO'            => 0.03,
        'NOVOS_PISOS'               => 0.03,
        'ELETRODOMESTICOS'          => 0.02,
        'TOMADAS_INTERRUPTORES'     => 0.03,
        'CHUVEIROS_SANITARIO'       => 0.03,
        'PAREDES_ALVENARIA_DRYWALL' => 0.03,
        'CONSTRUCAO_NOVA_AREA'      => 0.15,
    ];
    private const CATEGORIAS = ['Estreantes' => 1.0, 'Verificados' => 1.2, 'Preferidos' => 1.5];

    private const FATOR_SERVICO  = 1.33;
    private const FATOR_FINDERS  = 1.25;
    private const FATOR_CARTAO   = 1.15;  // taxa de parcelamento
    private const MULT_DUO       = 2.4445;
    private const MULT_TRIO      = 3.3333;

    private const TAXAS_ORIGEM = [
        'vogue'                => 1200.00,
        'lufe'                 => 2200.00,
        'living_wellness'      => 100.00,
        'living_pacific_belem' => 100.00,
    ];

    private static function normalizeOrigem(?string $o): string
    {
        $o = strtolower(trim((string) $o));
        return match($o) {
            'casavogue', 'casa_vogue', 'casa-vogue' => 'vogue',
            'life_by_lufe', 'lifebylufe'            => 'lufe',
            'living-wellness'                        => 'living_wellness',
            'living-pacific-belem'                   => 'living_pacific_belem',
            default                                  => $o,
        };
    }

    private function unitM2(float $m2): float
    {
        return max(self::MET_CONST * exp(-self::MET_DECAY * $m2), self::MIN_POR_M2);
    }

    private function unitAmb(int $amb): float
    {
        return max(self::AMB_CONST * exp(-self::AMB_DECAY * $amb), self::AMB_MIN);
    }

    private function custoAmbientes(int $amb): float
    {
        $total = 0.0;
        for ($i = 1; $i <= max(0, $amb); $i++) {
            $total += $this->unitAmb($i);
        }
        return $total;
    }

    private function somaPct(array $p): float
    {
        $pct = 0.0;
        foreach (($p['adicionais'] ?? []) as $a) {
            $pct += self::ADICIONAIS[$a] ?? 0.0;
        }
        if (!empty($p['nova_area'])) $pct += self::ADICIONAIS['CONSTRUCAO_NOVA_AREA'];
        $pct += self::PRAZOS[$p['prazo'] ?? 21]             ?? 0.0;
        $pct += self::REGIOES[$p['regiao'] ?? 'BRASIL_TODO'] ?? 0.0;
        foreach (($p['tipos'] ?? []) as $t) {
            $pct += self::TIPOS[$t] ?? 0.0;
        }
        return $pct;
    }

    public function calcular(array $p): array
    {
        $m2  = (float) ($p['m2']  ?? 20);
        $amb = (int)   ($p['amb'] ?? 1);

        $origem    = self::normalizeOrigem($p['origem'] ?? $p['utm_source'] ?? null);
        $taxaExtra = self::TAXAS_ORIGEM[$origem] ?? 0.0;

        $pct  = $this->somaPct($p);
        $base = ($m2 * $this->unitM2($m2)) + $this->custoAmbientes($amb);
        $fCat = self::CATEGORIAS[$p['categoria'] ?? 'Estreantes'] ?? 1.0;
        $solo = $base * (1 + $pct) * $fCat;
        $duo  = max($solo * self::MULT_DUO, 0.0);
        $trio = max($solo * self::MULT_TRIO, 0.0);

        $fMarca = self::FATOR_FINDERS * self::FATOR_SERVICO;

        $calc = static function (float $v) use ($fMarca, $taxaExtra): array {
            $avista    = round($v * $fMarca + $taxaExtra);
            $parcelado = round($v * $fMarca * self::FATOR_CARTAO + $taxaExtra);
            return ['avista' => $avista, 'a_vista' => $avista, 'parcelado' => $parcelado];
        };

        return [
            'solo' => $calc($solo),
            'duo'  => $calc($duo),
            'trio' => $calc($trio),
        ];
    }
}

// Endpoint HTTP
header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-store');

try {
    $p = $_POST + $_GET;
    echo json_encode((new Calculator())->calcular([
        'm2'        => (float)  ($p['metragem']  ?? 20),
        'amb'       => (int)    ($p['ambientes'] ?? 1),
        'prazo'     => (int)    ($p['prazo']     ?? 21),
        'regiao'    => $p['regiao']               ?? 'BRASIL_TODO',
        'adicionais'=> $p['adicionais']           ?? [],
        'tipos'     => $p['tipos']                ?? ['RESIDENCIAL: Apartamento'],
        'categoria' => $p['categoria']            ?? 'Estreantes',
        'nova_area' => !empty($p['nova_area']),
        'origem'    => $p['origem'] ?? $p['utm_source'] ?? null,
    ]), JSON_UNESCAPED_UNICODE);
} catch (Throwable $e) {
    http_response_code(400);
    echo json_encode(['error' => $e->getMessage()]);
}
