<?php

namespace App\Models;

use CodeIgniter\Model;

class PedidosTimelineModel extends PedidosModel
{
    protected $table = 'pedidos';
    protected $primaryKey = 'id';

    // Adicionando os novos campos aos allowedFields
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
        'atualizado_em',
        'inicio_preparo',      // Novo campo
        'envio_entrega',       // Novo campo
        'data_entrega',        // Novo campo
        'data_cancelamento'    // Novo campo
    ];

    // Atualizando as regras de validação
    protected $validationRules = [
        'restaurante_id'       => 'required|integer',
        'cliente_nome'         => 'required|max_length[255]',
        'cliente_telefone'     => 'required|max_length[20]',
        'cliente_endereco'     => 'required|max_length[255]',
        'valor_total'          => 'required|decimal',
        'status'               => 'required|in_list[aguardando,preparando,enviado,entregue,cancelado]', // Alterado finalizado para entregue
        'data'                 => 'permit_empty|valid_date',
        'avaliacao'            => 'permit_empty|integer|greater_than_equal_to[1]|less_than_equal_to[5]',
        'avaliacao_detalhes'   => 'permit_empty|string',
        'inicio_preparo'       => 'permit_empty|valid_date',    // Nova validação
        'envio_entrega'        => 'permit_empty|valid_date',    // Nova validação
        'data_entrega'         => 'permit_empty|valid_date',    // Nova validação
        'data_cancelamento'    => 'permit_empty|valid_date'     // Nova validação
    ];

    // Adicionando mensagens de validação para os novos campos
    protected $validationMessages = [
        'valor_total' => [
            'decimal' => 'O valor total precisa ser um número decimal válido.'
        ],
        'status' => [
            'in_list' => 'Status inválido. Use: aguardando, preparando, enviado, entregue ou cancelado.'
        ],
        'avaliacao' => [
            'integer' => 'A avaliação deve ser um número inteiro.',
            'greater_than_equal_to' => 'A avaliação mínima é 1.',
            'less_than_equal_to' => 'A avaliação máxima é 5.'
        ],
        'inicio_preparo' => [
            'valid_date' => 'A data de início de preparo deve ser válida.'
        ],
        'envio_entrega' => [
            'valid_date' => 'A data de envio para entrega deve ser válida.'
        ],
        'data_entrega' => [
            'valid_date' => 'A data de entrega deve ser válida.'
        ],
        'data_cancelamento' => [
            'valid_date' => 'A data de cancelamento deve ser válida.'
        ]
    ];

    // Método para atualizar status com registro de tempo
    public function atualizarStatus(int $pedidoId, string $novoStatus)
    {
        $updateData = ['status' => $novoStatus];
        
        // Registra o timestamp específico para cada transição de status
        switch($novoStatus) {
            case 'preparando':
                $updateData['inicio_preparo'] = date('Y-m-d H:i:s');
                break;
            case 'enviado':
                $updateData['envio_entrega'] = date('Y-m-d H:i:s');
                break;
            case 'entregue':
                $updateData['data_entrega'] = date('Y-m-d H:i:s');
                break;
            case 'cancelado':
                $updateData['data_cancelamento'] = date('Y-m-d H:i:s');
                break;
        }

        return $this->update($pedidoId, $updateData);
    }

    // Método para obter os dados completos da timeline
    public function obterTimeline(int $pedidoId)
    {
        return $this->select('
            id,
            status,
            criado_em,
            inicio_preparo,
            envio_entrega,
            data_entrega,
            data_cancelamento
        ')->find($pedidoId);
    }
}