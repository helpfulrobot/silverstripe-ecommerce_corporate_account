<?php

/**
 * @author Nicolaas [at] sunnysideup.co.nz
 * @package: ecommerce
 * @sub-package: ecommerce_corporate_account
 */

class OrderMarker extends OrderModifier {

// ######################################## *** model defining static variables (e.g. $db, $has_one)

	public static $db = array(
		"OrderFor" => "Varchar",
	);

// ######################################## *** cms variables + functions (e.g. getCMSFields, $searchableFields)

	function getCMSFields() {
		$fields = parent::getCMSFields();
		return $fields;
	}

	public static $singular_name = "Order Marker";
		function i18n_single_name() { return _t("OrderMarker.ORDERMARKER", "Modifier Marker");}

	public static $plural_name = "Order Markers";
		function i18n_plural_name() { return _t("OrderMarker.ORDERMARKERS", "Modifier Markers");}

// ######################################## *** other (non) static variables (e.g. protected static $special_name_for_something, protected $order)

	protected static $form_header = 'Order For ...';
		static function set_form_header(string $s) {self::$form_header = $s;}

// ######################################## *** CRUD functions (e.g. canEdit)
// ######################################## *** init and update functions

	public function runUpdate() {
		$this->checkField("OrderFor");
		parent::runUpdate();
	}

	function updateOrderFor($s) {
		$this->OrderFor = $s;
	}

// ######################################## *** form functions (e. g. showform and getform)


	public function showForm() {
		return $this->Order()->Items();
	}

	function getModifierForm($controller) {
		$fields = new FieldSet();
		$fields->push(new TextField('OrderFor', "enter name or code for this order", $this->OrderFor));
		$fields->push(new LiteralField('OrderForConfirmation', "<div><div id=\"OrderForConfirmation\" class=\"middleColumn\"></span></div>"));
		$validator = new RequiredFields(array("OrderFor"));
		$actions = new FieldSet(
			new FormAction('submit', 'Update Order')
		);
		return new OrderMarker_Form($controller, 'OrderMarker', $fields, $actions, $validator);
	}

// ######################################## *** template functions (e.g. ShowInTable, TableTitle, etc...) ... USES DB VALUES

	public function ShowInTable() {
		return true;
	}
	public function CanBeRemoved() {
		return false;
	}
// ######################################## ***  inner calculations.... USES CALCULATED VALUES



// ######################################## *** calculate database fields: protected function Live[field name]  ... USES CALCULATED VALUES

	protected function LiveName() {
		return "Order For: ".$this->LiveOrderFor();
	}

	protected function LiveOrderFor() {
		return $this->OrderFor;
	}



// ######################################## *** Type Functions (IsChargeable, IsDeductable, IsNoChange, IsRemoved)

// ######################################## *** standard database related functions (e.g. onBeforeWrite, onAfterWrite, etc...)

	function onBeforeWrite() {
		parent::onBeforeWrite();
	}

// ######################################## *** AJAX related functions
// ######################################## *** debug functions

}

class OrderMarker_Form extends OrderModifierForm {

	function __construct($optionalController = null, $name,FieldSet $fields, FieldSet $actions,$validator = null) {
		parent::__construct($optionalController, $name,$fields,$actions,$validator);
		Requirements::javascript("ecommerce_corporate_account/javascript/OrderMarkerModifier.js");
	}

	public function submit($data, $form) {
		$order = ShoppingCart::current_order();
		$modifiers = $order->Modifiers();
		foreach($modifiers as $modifier) {
			if (get_class($modifier) == 'OrderMarker') {
				if(isset($data['OrderFor'])) {
					$modifier->updateOrderFor(Convert::raw2sql($data["OrderFor"]));
					$modifier->write();
					return ShoppingCart::singleton()->setMessageAndReturn(_t("OrderMarker.UPDATED", "Order saved as '".Convert::raw2xml($data["OrderFor"]))."'.", "good");
				}
			}
		}
		return ShoppingCart::singleton()->setMessageAndReturn(_t("OrderMarker.UPDATED", "Order marker could not be saved"), "bad");
	}
}

