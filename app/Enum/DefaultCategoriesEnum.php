<?php

namespace App\Enum;

enum DefaultCategoriesEnum: string
{
    case TRANSPORT = 'TRANSPORT';
    case FOOD = 'FOOD';
    case LEISURE = 'LEISURE';
    case FINANCIAL = 'FINANCIAL';

    public function keywords()
    {
        return match ($this) {
            self::TRANSPORT => [
                'TRANSPORT', 'UBER', 'CARRO', 'VIAGEM', 'MOTO', '99', 'TAXI',
            ],

            self::FOOD => [
                'ALIMENTACAO', 'COMIDA', 'PIZZA', 'HAMBURGUER', 'RODIZIO', 'ALIMENTO',  'MERCADO', 'MCDONALDS', 'SUBWAY', "BOB'S", 'LANCHE', 'ALMOÇO', 'CAFE',
            ],

            self::LEISURE => [
                'LAZER', 'PASSEIO', 'CINEMA', 'RETIRO', 'ACAMPAMENTO', 'PIQUIQUE', 'SHOPPING',
            ],

            self::FINANCIAL => [
                'FINANCEIRO', 'SALARIO', 'PAGAMENTO', 'DINHEIRO', 'RECEBIDO', 'ENVIADO', 'PIX', 'DEPOSITO', 'SAQUE', 'RETIRADA', 'DEBITO', 'CHEQUE', 'BOLETO', 'PAGUEI', 'PAGO', 'RECEBI', 'ENVIEI',
            ],

            default => 'error',
        };
    }
}
