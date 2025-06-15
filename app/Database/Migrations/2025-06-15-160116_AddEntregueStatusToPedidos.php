<?php namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddEntregueStatusToPedidos extends Migration
{
    public function up()
    {
        // Modifica a coluna status para incluir 'entregue'
        $this->db->query("ALTER TABLE pedidos MODIFY status ENUM('aguardando', 'preparando', 'enviado', 'entregue', 'finalizado', 'cancelado') NOT NULL DEFAULT 'aguardando'");
    }

    public function down()
    {
        // Reverte para o estado anterior sem 'entregue'
        $this->db->query("ALTER TABLE pedidos MODIFY status ENUM('aguardando', 'preparando', 'enviado', 'finalizado', 'cancelado') NOT NULL DEFAULT 'aguardando'");
    }
}