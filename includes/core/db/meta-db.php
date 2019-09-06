<?php
namespace ExtendedCore\DB;


// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Meta DB
 *
 * Allows for the use of metadata api usage
 *
 * @package     Includes
 * @subpackage  includes/DB
 * @author      Adrian Tobey <info@groundhogg.io>
 * @copyright   Copyright (c) 2018, Groundhogg Inc.
 * @license     https://opensource.org/licenses/GPL-3.0 GNU Public License v3
 * @since       File available since Release 0.1
 */
abstract class Meta_DB extends DB {

    /**
     * Always return Meta ID
     * 
     * @return string
     */
    public function get_primary_key()
    {
        return 'meta_id';
    }

    protected function add_additional_actions()
    {
        add_action( "{$this->get_filter_prefix()}/db/post_delete/{$this->get_object_type()}", [ $this, 'delete_associated_meta' ] );
        parent::add_additional_actions();
    }

    /**
     * Get the meta associative ID
     * 
     * @return string
     */
    public function get_object_id_col()
    {
        return $this->get_object_type() . '_id';
    }    

    /**
     * Get table columns and data types
     *
     * @access  public
     * @since   1.7.18
     */
    public function get_columns() {
        
        $object_id = $this->get_object_id_col();
        
        return [
            'meta_id'     => '%d',
            $object_id    => '%d',
            'meta_key'    => '%s',
            'meta_value'  => '%s',
        ];
    }

    /**
     * Register the table with $wpdb so the metadata api can find it
     *
     * @access  public
     * @since   2.6
     */
    public function register_table() {

        global $wpdb;

        if ( $wpdb ){
            $wpdb->__set( $this->get_object_type() . 'meta', $this->get_table_name() );
            $wpdb->tables[] = $this->get_db_suffix();
        }
    }

    /**
     * Clean up associated Meta if object gets delete
     *
     * @param $id int the ID of the object
     * @return false|int
     */
    public function delete_associated_meta( $id ){
        global $wpdb;
        $result = $wpdb->delete( $this->table_name, array( $this->get_object_id_col() => $id ), array( '%d' ) );
        return $result;
    }

    /**
     * Retrieve object meta field for a object.
     *
     * For internal use only. Use EDD_Contact->get_meta() for public usage.
     *
     * @param   int    $object_id     Object ID.
     * @param   string $meta_key      The meta key to retrieve.
     * @param   bool   $single        Whether to return a single value.
     * @return  mixed                 Will be an array if $single is false. Will be value of meta data field if $single is true.
     *
     * @access  private
     * @since   2.6
     */
    public function get_meta( $object_id = 0, $meta_key = '', $single = false ) {
        $object_id = $this->sanitize_id( $object_id );

        if ( false === $object_id ) {
            return false;
        }

        return get_metadata( $this->get_object_type(), $object_id, $meta_key, $single );
    }

    /**
     * Add meta data field to a object.
     *
     * For internal use only. Use EDD_Contact->add_meta() for public usage.
     *
     * @param   int    $object_id   Contact ID.
     * @param   string $meta_key      Metadata name.
     * @param   mixed  $meta_value    Metadata value.
     * @param   bool   $unique        Optional, default is false. Whether the same key should not be added.
     * @return  bool                  False for failure. True for success.
     *
     * @access  private
     * @since   2.6
     */
    public function add_meta( $object_id = 0, $meta_key = '', $meta_value, $unique = false ) {
        $object_id = $this->sanitize_id( $object_id );
        if ( false === $object_id ) {
            return false;
        }

        return add_metadata( $this->get_object_type(), $object_id, $meta_key, $meta_value, $unique );
    }

    /**
     * Update object meta field based on Contact ID.
     *
     * For internal use only. Use EDD_Contact->update_meta() for public usage.
     *
     * Use the $prev_value parameter to differentiate between meta fields with the
     * same key and Contact ID.
     *
     * If the meta field for the object does not exist, it will be added.
     *
     * @param   int    $object_id   Contact ID.
     * @param   string $meta_key      Metadata key.
     * @param   mixed  $meta_value    Metadata value.
     * @param   mixed  $prev_value    Optional. Previous value to check before removing.
     * @return  bool                  False on failure, true if success.
     *
     * @access  private
     * @since   2.6
     */
    public function update_meta( $object_id = 0, $meta_key = '', $meta_value, $prev_value = '' ) {
        $object_id = $this->sanitize_id( $object_id );
        if ( false === $object_id ) {
            return false;
        }

        return update_metadata( $this->get_object_type(), $object_id, $meta_key, $meta_value, $prev_value );
    }

    /**
     * Remove metadata matching criteria from a object.
     *
     * For internal use only. Use EDD_Contact->delete_meta() for public usage.
     *
     * You can match based on the key, or key and value. Removing based on key and
     * value, will keep from removing duplicate metadata with the same key. It also
     * allows removing all metadata matching key, if needed.
     *
     * @param   int    $object_id   Contact ID.
     * @param   string $meta_key      Metadata name.
     * @param   mixed  $meta_value    Optional. Metadata value.
     * @return  bool                  False for failure. True for success.
     *
     * @access  private
     * @since   2.6
     */
    public function delete_meta( $object_id = 0, $meta_key = '', $meta_value = '' ) {
        return delete_metadata( $this->get_object_type(), $object_id, $meta_key, $meta_value );
    }

    /**
     * Returns an array of all the meta keys in a table.
     *
     * @return array
     */
    public function get_keys()
    {

        global $wpdb;

        $keys = $wpdb->get_col(
            "SELECT DISTINCT meta_key FROM $this->table_name ORDER BY meta_key DESC"
        );

        $key_array = array();

        foreach ( $keys as $key ){
            $key_array[ $key ] = $key;
        }

        return $key_array;

    }

    /**
     * Create the table
     *
     * @access  public
     * @since   2.6
     */
    public function create_table() {

        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');

        $sql = "CREATE TABLE {$this->table_name} (
		meta_id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
		{$this->get_object_id_col()} bigint(20) unsigned NOT NULL,
		meta_key varchar(255) DEFAULT NULL,
		meta_value longtext,
		PRIMARY KEY  (meta_id),
		KEY {$this->get_object_id_col()} ({$this->get_object_id_col()}),
		KEY meta_key (meta_key)
		) {$this->get_charset_collate()};";

        dbDelta( $sql );

        update_option( $this->table_name . '_db_version', $this->version );
    }

    /**
     * Given a object ID, make sure it's a positive number, greater than zero before inserting or adding.
     *
     * @since  2.6
     * @param  int|string $object_id A passed object ID.
     * @return int|bool                The normalized object ID or false if it's found to not be valid.
     */
    private function sanitize_id($object_id ) {
        if ( ! is_numeric( $object_id ) ) {
            return false;
        }

        $object_id = (int) $object_id;

        // We were given a non positive number
        if ( absint( $object_id ) !== $object_id ) {
            return false;
        }

        if ( empty( $object_id ) ) {
            return false;
        }

        return absint( $object_id );

    }
}