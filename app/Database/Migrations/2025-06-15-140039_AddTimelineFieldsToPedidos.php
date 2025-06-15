<?php namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddTimelineFieldsToPedidos extends Migration
{
    public function up()
    {
        $this->forge->addColumn('pedidos', [
            'inicio_preparo' => [
                'type' => 'DATETIME',
                'null' => true,
                'after' => 'status'
            ],
            'envio_entrega' => [
                'type' => 'DATETIME',
                'null' => true,
                'after' => 'inicio_preparo'
            ],
            'data_entrega' => [
                'type' => 'DATETIME',
                'null' => true,
                'after' => 'envio_entrega'
            ],
            'data_cancelamento' => [
                'type' => 'DATETIME',
                'null' => true,
                'after' => 'data_entrega'
            ]
        ]);
    
    }

    public function down()
    {
        $this->forge->dropColumn('pedidos', ['inicio_preparo', 'envio_entrega', 'data_entrega', 'data_cancelamento']);
    }
}