<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_create_groups extends CI_Migration
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
                'auto_increment'=>TRUE
            ),
            'name' => array(
                'type' => 'VARCHAR',
                'constraint' => 100,
                'unique'=>TRUE,
            ),
            'description' => array(
                'type' => 'VARCHAR',
                'constraint' => 100,
            ),
        ));

        // Add Primary Key.
        $this->dbforge->add_key("id", TRUE);

        // Create Table groups
        $this->dbforge->create_table("groups", TRUE);

    }

    /**
     * down (drop table)
     *
     * @return void
     */
    public function down()
    {
        // Drop table groups
        $this->dbforge->drop_table("groups", TRUE);
    }

}
