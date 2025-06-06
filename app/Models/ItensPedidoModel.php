<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class ItensPedido extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type'            => 'INT',
                'unsigned'        => true,
                'auto_increment'  => true,
            ],
            'pedido_id' => [
                'type'            => 'INT',
                'unsigned'        => true,
            ],
            'cardapio_id' => [
                'type'            => 'INT',
                'unsigned'        => true,
            ],
            'quantidade' => [
                'type'            => 'INT',
                'unsigned'        => true,
                'default'         => 1,
            ],
            'preco_unitario' => [
                'type'            => 'DECIMAL',
                'constraint'      => '10,2',
                'default'         => 0.00,
            ],
            'criado_em' => [
                'type'            => 'DATETIME',
                'null'            => true,
            ],
            'atualizado_em' => [
                'type'            => 'DATETIME',
                'null'            => true,
            ],
        ]);

        $this->forge->addKey('id', true);

        // Definir as chaves estrangeiras corretamente
        $this->forge->addForeignKey('pedido_id', 'pedidos', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('cardapio_id', 'itens_cardapio', 'id', 'CASCADE', 'CASCADE');

        $this->forge->createTable('itens_pedido');
    }

    public function down()
    {
        $this->forge->dropTable('itens_pedido');
    }
}
