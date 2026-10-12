<?php

// Exit if accessed directly
if (! defined('ABSPATH')) exit;


/**
 * List table class outputter for Emails
 *
 */
class WPBS_WP_List_Table_Emails extends WPBS_WP_List_Table
{

	/**
	 * The current form ID
	 *
	 * @access private
	 * @var int
	 *
	 */
	private $form_id;

	/**
	 * The number of emails that should appear in the table
	 *
	 * @access private
	 * @var int
	 *
	 */
	private $items_per_page;

	/**
	 * The number of the page being displayed by the pagination
	 *
	 * @access private
	 * @var int
	 *
	 */
	private $paged;

	/**
	 * The data of the table
	 *
	 * @access public
	 * @var array
	 *
	 */
	public $data = array();


	/**
	 * Constructor
	 *
	 */
	public function __construct($form_id)
	{

		$this->form_id = $form_id;

		parent::__construct(array(
			'plural' 	=> 'wpbs_emails',
			'singular' 	=> 'wpbs_email',
			'ajax' 		=> false
		));

		/**
		 * Filter the number of email shown in the table
		 *
		 * @param int
		 *
		 */
		$this->items_per_page = apply_filters('wpbs_list_table_email_items_per_page', 20);

		$this->paged = (! empty($_GET['paged']) ? (int)$_GET['paged'] : 1);

		$email_status = (! empty($_GET['email_status']) ? esc_attr($_GET['email_status']) : 'active');

		$this->set_pagination_args(array(
			'total_items' => wpbs_get_emails(array('form_id' => $this->form_id, 'number' => -1, 'status' => $email_status, 'search'  => (! empty($_GET['s']) ? esc_attr($_GET['s']) : '')), true),
			'per_page'    => $this->items_per_page
		));

		// Get and set table data
		$this->set_table_data();

		// Add column headers and table items
		$this->_column_headers = array($this->get_columns(), array(), $this->get_sortable_columns());
		$this->items 		   = $this->data;
	}


	/**
	 * Returns all the columns for the table
	 *
	 */
	public function get_columns()
	{

		$columns = array(
			'name'		    => __('Name', 'wp-booking-system'),
			'id' 		    => __('ID', 'wp-booking-system'),
			'trigger' 		=> __('Trigger', 'wp-booking-system'),
			'date_created'  => __('Date Created', 'wp-booking-system'),
			'date_modified' => __('Date Modified', 'wp-booking-system'),
			'status' 	    => __('Status', 'wp-booking-system'),
		);

		if (isset($_GET['email_status']) && $_GET['email_status'] == 'trash') {
			unset($columns['status']);
		}

		/**
		 * Filter the columns of the emails table
		 *
		 * @param array $columns
		 *
		 */
		return apply_filters('wpbs_list_table_emails_columns', $columns);
	}


	/**
	 * Overwrites the parent class.
	 * Define which columns are sortable
	 *
	 * @return array
	 *
	 */
	public function get_sortable_columns()
	{

		return array(
			'name' => array('name', false),
			'id'   => array('id', false),
			'date_created'  => array('date_created', false),
			'date_modified' => array('date_modified', false)
		);
	}


	/**
	 * Returns the possible views for the email list table
	 *
	 */
	protected function get_views()
	{

		$email_status = (! empty($_GET['email_status']) ? esc_attr($_GET['email_status']) : 'active');

		$views = array(
			'active' => '<a href="' . add_query_arg(array('page' => 'wpbs-forms', 'subpage' => 'edit-form', 'form_id' => $this->form_id, 'tab' => 'email-notifications', 'email_status' => 'active', 'paged' => 1, 's' => (! empty($_GET['s']) ? esc_attr($_GET['s']) : '')), admin_url('admin.php')) . '" ' . ($email_status == 'active' ? 'class="current"' : '') . '>' . __('Active', 'wp-booking-system') . ' <span class="count">(' . wpbs_get_emails(array('form_id' => $this->form_id, 'status' => 'active', 'search'  => (! empty($_GET['s']) ? esc_attr($_GET['s']) : '')), true) . ')</span></a>',
			'trash'  => '<a href="' . add_query_arg(array('page' => 'wpbs-forms', 'subpage' => 'edit-form', 'form_id' => $this->form_id, 'tab' => 'email-notifications', 'email_status' => 'trash', 'paged' => 1, 's' => (! empty($_GET['s']) ? esc_attr($_GET['s']) : '')), admin_url('admin.php')) . '" ' . ($email_status == 'trash' ? 'class="current"' : '') . '>' . __('Trash', 'wp-booking-system') . ' <span class="count">(' . wpbs_get_emails(array('form_id' => $this->form_id, 'status' => 'trash', 'search'  => (! empty($_GET['s']) ? esc_attr($_GET['s']) : '')), true) . ')</span></a>',
		);

		/**
		 * Filter the views of the emails table
		 *
		 * @param array $views
		 *
		 */
		return apply_filters('wpbs_list_table_emails_views', $views);
	}


	/**
	 * Gets the emails data and sets it
	 *
	 */
	private function set_table_data()
	{

		$email_args = array(
			'form_id' => $this->form_id,
			'number'  => $this->items_per_page,
			'offset'  => ($this->paged - 1) * $this->items_per_page,
			'status'  => (! empty($_GET['email_status']) ? sanitize_text_field($_GET['email_status']) : 'active'),
			'orderby' => (! empty($_GET['orderby']) ? sanitize_text_field($_GET['orderby']) : 'id'),
			'order'   => (! empty($_GET['order']) ? sanitize_text_field(strtoupper($_GET['order'])) : 'DESC'),
			'search'  => (! empty($_GET['s']) ? sanitize_text_field($_GET['s']) : '')
		);

		$emails = wpbs_get_emails($email_args);

		if (empty($emails))
			return;

		foreach ($emails as $email) {

			$row_data = $email->to_array();

			/**
			 * Filter the email row data
			 *
			 * @param array 		 $row_data
			 * @param WPBS_Email $email
			 *
			 */
			$row_data = apply_filters('wpbs_list_table_emails_row_data', $row_data, $email);

			$this->data[] = $row_data;
		}
	}


	/**
	 * Returns the HTML that will be displayed in each columns
	 *
	 * @param array $item 			- data for the current row
	 * @param string $column_name 	- name of the current column
	 *
	 * @return string
	 *
	 */
	public function column_default($item, $column_name)
	{

		return isset($item[$column_name]) ? $item[$column_name] : '-';
	}


	/**
	 * Returns the HTML that will be displayed in the "name" column
	 *
	 * @param array $item - data for the current row
	 *
	 * @return string
	 *
	 */
	public function column_name($item)
	{

		$output = '<span>';
		if ($item['status'] == 'active') {

			$output  .= '<strong><a class="row-title" href="' . add_query_arg(array('page' => 'wpbs-forms', 'subpage' => 'edit-form', 'form_id' => $this->form_id, 'tab' => 'email-notifications', 'email_notification_id' => $item['id']), admin_url('admin.php')) . '">' . (!empty($item['name']) ? esc_attr($item['name']) : '') . '</a></strong>';

			$actions = array(
				'edit_email' => '<a href="' . add_query_arg(array('page' => 'wpbs-forms', 'subpage' => 'edit-form', 'form_id' => $this->form_id, 'tab' => 'email-notifications', 'email_notification_id' => $item['id']), admin_url('admin.php')) . '">' . __('Edit email', 'wp-booking-system') . '</a>',

				'duplicate_email' => '<a href="' . wp_nonce_url(add_query_arg(array('page' => 'wpbs-forms', 'form_id' => $this->form_id, 'wpbs_action' => 'duplicate_email', 'email_notification_id' => $item['id']), admin_url('admin.php')), 'wpbs_duplicate_email', 'wpbs_token') . '">' . __('Duplicate email', 'wp-booking-system') . '</a>',
				'trash' 		=> '<span class="trash"><a onclick="return confirm( \'' . __("Are you sure you want to send this email to the trash?", "wp-booking-system") . ' \' )" href="' . wp_nonce_url(add_query_arg(array('page' => 'wpbs-forms', 'wpbs_action' => 'trash_email', 'form_id' => $this->form_id, 'email_notification_id' => $item['id']), admin_url('admin.php')), 'wpbs_trash_email', 'wpbs_token') . '" class="submitdelete">' . __('Trash', 'wp-booking-system') . '</a></span>'
			);
		}

		if ($item['status'] == 'trash') {

			$output  = '<strong>' . (!empty($item['name']) ? esc_attr($item['name']) : '') . '</strong>';

			$actions = array(
				'restore_email' => '<a href="' . wp_nonce_url(add_query_arg(array('page' => 'wpbs-forms', 'form_id' => $this->form_id, 'wpbs_action' => 'restore_email', 'email_notification_id' => $item['id']), admin_url('admin.php')), 'wpbs_restore_email', 'wpbs_token') . '">' . __('Restore email', 'wp-booking-system') . '</a>',
				'delete' 		   => '<span class="trash"><a onclick="return confirm( \'' . __("Are you sure you want to delete this email?", "wp-booking-system") . ' \' )" href="' . wp_nonce_url(add_query_arg(array('page' => 'wpbs-forms', 'form_id' => $this->form_id, 'wpbs_action' => 'delete_email', 'email_notification_id' => $item['id']), admin_url('admin.php')), 'wpbs_delete_email', 'wpbs_token') . '" class="submitdelete">' . __('Delete Permanently', 'wp-booking-system') . '</a></span>'
			);
		}

		/**
		 * Filter the row actions before adding them to the table
		 *
		 * @param array $actions
		 * @param array $item
		 *
		 */
		$actions = apply_filters('wpbs_list_table_emails_row_actions', $actions, $item);

		$output .= $this->row_actions($actions);

		$output .= '</span>';

		return $output;
	}

	/**
	 * Returns the HTML that will be displayed in the "ID" column
	 *
	 * @param array $item 			- data for the current row
	 * @param string $column_name 	- name of the current column
	 *
	 * @return string
	 *
	 */
	public function column_id($item)
	{
		return '<span class="wpbs-list-table-id">' . $item['id'] . '</span>';
	}

	/**
	 * Returns the HTML that will be displayed in the "Status" column
	 *
	 * @param array $item 			- data for the current row
	 * @param string $column_name 	- name of the current column
	 *
	 * @return string
	 *
	 */
	public function column_status($item)
	{
		return '<div class="wpbs-form-field wpbs-email-status-toggle">
					<div class="wpbs-input">
						<label for="email-status-' . $item['id'] . '" class="wpbs-checkbox-switch">
							<input type="hidden" name="email-status-' . $item['id'] . '" value="0">
							<input data-email-id="' . $item['id'] . '" name="email-status-' . $item['id'] . '" type="checkbox" id="email-status-' . $item['id'] . '" class="wpbs-settings-toggle"  ' . ($item['is_enabled'] ? 'checked' : '') . '>
							<div class="wpbs-checkbox-slider"></div>
						</label>
					</div>
				</div>';
	}


	/**
	 * Returns the HTML that will be displayed in the "Trigger" column
	 *
	 * @param array $item 			- data for the current row
	 * @param string $column_name 	- name of the current column
	 *
	 * @return string
	 *
	 */
	public function column_trigger($item)
	{

		$when_to_send = wpbs_get_email_meta($item['id'], 'notification_when_to_send', true);
		$day_offset = absint(wpbs_get_email_meta($item['id'], 'notification_day_offset', true));

		$day_nice_string = $day_offset . ' ' . ($day_offset == 1 ? __('day', 'wp-booking-system') : __('days', 'wp-booking-system'));

		// When do we send it.
		switch ($when_to_send) {
			case 'booking_created':
				$string = __('When the booking is created.', 'wp-booking-system');
				break;
			case 'after_created':
				$string = sprintf(__('%s after the booking is created.', 'wp-booking-system'), $day_nice_string);
				break;
			case 'before_start':
				$string = sprintf(__('%s before the booking starts.', 'wp-booking-system'), $day_nice_string);
				break;
			case 'after_start':
				$string = sprintf(__('%s after the booking starts.', 'wp-booking-system'), $day_nice_string);
				break;
			case 'before_end':
				$string = sprintf(__('%s before the booking ends.', 'wp-booking-system'), $day_nice_string);
				break;
			case 'after_end':
				$string = sprintf(__('%s after the booking ends.', 'wp-booking-system'), $day_nice_string);
				break;
			case 'final_payment_made':
				$string = sprintf(__('When the final payment is made / booking is marked as paid.', 'wp-booking-system'), $day_nice_string);
				break;
			case 'payment_failed':
				$string = __('If a payment fails.', 'wp-booking-system');
				break;
			case 'booking_accepted':
				$string = __('When the booking is accepted.', 'wp-booking-system');
				break;
			default:
				$string = 'not set';
		}

		if (wpbs_get_email_meta($item['id'], 'notification_enable_conditional_logic', true) == 'on') {
			$string .= '<br><small>' . __('* conditional logic enabled.', 'wp-booking-system') . '</small>';
		}

		return '<span>' . $string . '</span>';
	}


	/**
	 * Returns the HTML that will be displayed in the "date_created" column
	 *
	 * @param array $item - data for the current row
	 *
	 * @return string
	 *
	 */
	public function column_date_created($item)
	{

		$output = wpbs_date_i18n(get_option('date_format') . ' ' . get_option('time_format'), strtotime($item['date_created']));

		return '<span>' . $output . '</span>';
	}


	/**
	 * Returns the HTML that will be displayed in the "date_created" column
	 *
	 * @param array $item - data for the current row
	 *
	 * @return string
	 *
	 */
	public function column_date_modified($item)
	{

		$output = wpbs_date_i18n(get_option('date_format') . ' ' . get_option('time_format'), strtotime($item['date_modified']));

		return '<span>' . $output . '</span>';
	}


	/**
	 * HTML display when there are no items in the table
	 *
	 */
	public function no_items()
	{

		echo __('No emails found.', 'wp-booking-system');
	}
}
