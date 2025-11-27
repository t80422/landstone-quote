<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateShipmentItems extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'si_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'si_s_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'comment' => '出貨單ID',
            ],
            'si_oi_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'comment' => '訂單明細ID',
            ],
            'si_quantity' => [
                'type' => 'INT',
                'constraint' => 11,
                'default' => 0,
                'comment' => '本次出貨數量',
            ],
            'si_created_at' => [
                'type' => 'DATETIME',
                'null' => true,
                'comment' => '建立時間',
            ],
            'si_updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
                'comment' => '更新時間',
            ],
        ]);

        $this->forge->addKey('si_id', true);
        $this->forge->addForeignKey('si_s_id', 'shipments', 's_id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('si_oi_id', 'order_items', 'oi_id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('shipment_items');
    }

    public function down()
    {
        $this->forge->dropTable('shipment_items');
    }
}
