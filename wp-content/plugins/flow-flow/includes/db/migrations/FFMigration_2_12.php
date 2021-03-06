<?php namespace flow\db\migrations;
use flow\db\FFDB;
use flow\db\FFDBMigration;

if ( ! defined( 'WPINC' ) ) die;
/**
 * FlowFlow.
 *
 * @package   FlowFlow
 * @author    Looks Awesome <email@looks-awesome.com>
 *
 * @link      http://looks-awesome.com
 * @copyright 2014-2016 Looks Awesome
 */
class FFMigration_2_12 implements FFDBMigration{

	public function version() {
		return '2.12';
	}

	public function execute($conn, $manager) {
		if (FFDB::existColumn($manager->streams_table_name, 'status')){
			$conn->query("ALTER TABLE ?n DROP `status`",  $manager->streams_table_name);
		}
	}
}