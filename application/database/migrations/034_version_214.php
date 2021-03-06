<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_version_214 extends CI_Migration
{
    /**
     * up (create table)
     *
     * @return void
     */

    public function up()
    {
        //change news order column to comply with reserved keywords
        if ($this->db->field_exists('order', 'news'))
            $this->db->query('ALTER TABLE news CHANGE `order` list_order INT(5);');
    }

    /**
     * down (drop table)
     *
     * @return void
     */
    public function down()
    {

        //restore old column 'order'
        if ($this->db->field_exists('list_order', 'news'))
            $this->db->query('ALTER TABLE news CHANGE `list_order` `order` INT(5) NOT NULL;');
    }

}