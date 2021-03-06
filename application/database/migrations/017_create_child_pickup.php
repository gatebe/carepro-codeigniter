<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_create_child_pickup extends CI_Migration
{

    /**
     * up (create table)
     *
     * @return void
     */
    public function up()
    {

        // Add Fields.
        $this->dbforge->add_field(array(
            'id' => array(
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => TRUE,
                'auto_increment' => TRUE
            ),
            'child_id' => array(
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => TRUE
            ),
            'first_name' => array(
                'type' => 'VARCHAR',
                'constraint' => 50,
            ),
            'last_name' => array(
                'type' => 'VARCHAR',
                'constraint' => 50,
            ),
            'cell' => array(
                'type' => 'VARCHAR',
                'constraint' => 20,
            ),
            'other_phone' => array(
                'type' => 'VARCHAR',
                'constraint' => 20,
            ),
            'address' => array(
                'type' => 'TEXT',
            ),
            'pin' => array(
                'type' => 'INT',
                'constraint' => 11,
            ),
            'relation' => array(
                'type' => 'VARCHAR',
                'constraint' => 20,
            ),
            'photo' => array(
                'type' => 'VARCHAR',
                'constraint' => 100,
            ),
            'status' => array(
                'type' => 'INT',
                'constraint' => 11,
            ),
            'user_id' => array(
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => TRUE
            ),
            'created_at' => array(
                'type' => 'DATETIME',
            ),
        ));

        // Add Primary Key.
        $this->dbforge->add_key("id", TRUE);

        // Create Table child_pickup
        $this->dbforge->create_table("child_pickup", TRUE);
        $this->db->query('ALTER TABLE `child_pickup` ADD FOREIGN KEY (`child_id`) REFERENCES children(`id`) ON DELETE CASCADE ON UPDATE CASCADE');
        $this->db->query('ALTER TABLE `child_pickup` ADD FOREIGN KEY (`user_id`) REFERENCES users(`id`) ON DELETE RESTRICT ON UPDATE CASCADE');
    }

    /**
     * down (drop table)
     *
     * @return void
     */
    public function down()
    {
        // Drop table child_pickup
        $this->dbforge->drop_table("child_pickup", TRUE);
    }

}
