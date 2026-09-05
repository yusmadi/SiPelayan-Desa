<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddSkKadesToUsersTable extends Migration
{
    public function up()
    {
        $fields = [
            'sk_kades_path' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
                'after'      => 'avatar_path',
            ],
        ];

        if (! $this->db->fieldExists('sk_kades_path', 'users')) {
            $this->forge->addColumn('users', $fields);
        }
    }

    public function down()
    {
        if ($this->db->fieldExists('sk_kades_path', 'users')) {
            $this->forge->dropColumn('users', 'sk_kades_path');
        }
    }
}
