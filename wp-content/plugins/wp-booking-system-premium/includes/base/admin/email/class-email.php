<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;


/**
 * The main class for the Email
 *
 */
class WPBS_Email extends WPBS_Base_Object {

	/**
	 * The Id of the email item
	 *
	 * @access protected
	 * @var    int
	 *
	 */
	protected $id;

	/**
	 * The Form Id 
	 *
	 * @access protected
	 * @var    int
	 *
	 */
	protected $form_id;

	/**
	 * The email item name
	 *
	 * @access protected
	 * @var    string
	 *
	 */
	protected $name;

	/**
	 * Toggle value if an email is enabled or not
	 *
	 * @access protected
	 * @var    bool
	 *
	 */
	protected $is_enabled;

	/**
	 * The date when the email was created
	 *
	 * @access protected
	 * @var    string
	 *
	 */
	protected $date_created;

	/**
	 * The date when the form was last modified
	 *
	 * @access protected
	 * @var    string
	 *
	 */
	protected $date_modified;

	/**
	 * The status of the form
	 *
	 * @access protected
	 * @var    string
	 *
	 */
	protected $status;

}