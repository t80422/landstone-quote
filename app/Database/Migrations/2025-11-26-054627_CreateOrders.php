<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateOrders extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'o_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'o_number' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
                'unique' => true,
                'comment' => '訂單編號',
            ],
            'o_date' => [
                'type' => 'DATE',
                'comment' => '訂單日期',
            ],
            'o_c_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'comment' => '客戶ID',
            ],
            'o_q_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'null' => true,
                'comment' => '來源報價單ID',
            ],
            'o_delivery_date' => [
                'type' => 'DATE',
                'null' => true,
                'comment' => '預交期',
            ],
            'o_total_amount' => [
                'type' => 'FLOAT',
                'constraint' => 11,
                'default' => 0,
                'comment' => '訂單總金額',
            ],
            'o_payment_status' => [
                'type' => 'ENUM',
                'constraint' => ['unpaid', 'partial', 'paid'],
                'default' => 'unpaid',
                'comment' => '收款狀態',
            ],
            'o_invoice_number' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
                'null' => true,
                'comment' => '發票號碼',
            ],
            'o_status' => [
                'type' => 'ENUM',
                'constraint' => ['processing', 'completed', 'cancelled'],
                'default' => 'processing',
                'comment' => '訂單狀態',
            ],
            'o_notes' => [
                'type' => 'TEXT',
                'null' => true,
                'comment' => '備註',
            ],
            'o_created_at' => [
                'type' => 'DATETIME',
                'null' => true,
                'comment' => '建立時間',
            ],
            'o_updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
                'comment' => '更新時間',
            ],
        ]);

        $this->forge->addKey('o_id', true);
        $this->forge->addForeignKey('o_c_id', 'customers', 'c_id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('o_q_id', 'quotes', 'q_id', 'SET NULL', 'CASCADE');
        $this->forge->createTable('orders');
    }

    public function down()
    {
        $this->forge->dropTable('orders');
    }
}
