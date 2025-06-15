<?php

namespace App\Models;

use CodeIgniter\Model;

class PedidosModel extends Model
{
    protected $table = 'pedidos';
    protected $primaryKey = 'id';

    protected $allowedFields = [
        'usuario_id',
        'restaurante_id',
        'cliente_nome',
        'cliente_telefone',
        'cliente_endereco',
        'valor_total',
        'status',
        'data',
        'avaliacao',
        'avaliacao_detalhes',
        'criado_em',
        'atualizado_em'
    ];


    protected $useTimestamps = true;
    protected $createdField  = 'criado_em';
    protected $updatedField  = 'atualizado_em';

    protected $validationRules = [
        'restaurante_id'       => 'required|integer',
        'cliente_nome'         => 'required|max_length[255]',
        'cliente_telefone'     => 'required|max_length[20]',
        'cliente_endereco'     => 'required|max_length[255]',
        'valor_total'          => 'required|decimal',
        'status'               => 'required|in_list[aguardando,preparando,enviado,entregue,cancelado]',
        'data'                 => 'permit_empty|valid_date',
        'avaliacao'            => 'permit_empty|integer|greater_than_equal_to[1]|less_than_equal_to[5]',
        'avaliacao_detalhes'   => 'permit_empty|string'
    ];

    protected $validationMessages = [
        'valor_total' => [
            'decimal' => 'O valor total precisa ser um número decimal válido.'
        ],
        'status' => [
            'in_list' => 'Status inválido.'
        ],
        'avaliacao' => [
            'integer' => 'A avaliação deve ser um número inteiro.',
            'greater_than_equal_to' => 'A avaliação mínima é 1.',
            'less_than_equal_to' => 'A avaliação máxima é 5.'
        ]
    ];

    protected $skipValidation = false;
}
