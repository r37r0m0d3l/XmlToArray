<?php
/**
 * An easy way to convert XML to PHP array
 * @version 1.00
 */
class Simplices_XML_into_Array {
	/**
	 * Very simple parsing. Returns either the tag or attribute.
	 * @static
	 * @param string $xml string containing XML
	 * @param bool $recurse flag of a recursive function call
	 * @return array|null|SimpleXMLElement
	 */
	public static function xml_2_array($xml, $recurse = false) {
		$data = ((! $recurse) and is_string ( $xml )) ? simplexml_load_string ( $xml ) : $xml;
		if ($data instanceof SimpleXMLElement) {
			$data = ( array ) $data;
		}
		if (is_array ( $data )) {
			foreach ( $data as &$item ) {
				$item = self::xml_2_array ( $item, true );
			}
		}
		return (is_array ( $data ) and count ( $data ) == 0) ? null : $data;
	}
	/**
	 * Parsing XML into array.
	 * @static
	 * @param string $contents string containing XML
	 * @param bool $get_attributes
	 * @param bool $tag_priority priority of values ​​in the array - `true` if the higher priority in the tag, `false` if only the attributes needed
	 * @return array
	 */
	public static function xml_to_array($contents, $get_attributes = true, $tag_priority = true) {
		$contents = trim ( $contents );
		if (empty ( $contents )) {
			return array ();
		}
		$parser = xml_parser_create ( '' );
		xml_parser_set_option ( $parser, XML_OPTION_TARGET_ENCODING, 'utf-8' );
		xml_parser_set_option ( $parser, XML_OPTION_CASE_FOLDING, 0 );
		xml_parser_set_option ( $parser, XML_OPTION_SKIP_WHITE, 1 );
		xml_parse_into_struct ( $parser, $contents, $xml_values );
		xml_parser_free ( $parser );
		if (! $xml_values) {
			return array ();
		}
		$xml_array = array ();
		$current = &$xml_array;
		$repeated_tag_index = array ();
		foreach ( $xml_values as $xml_tag ) {
			$result = null;
			$attributes_data = null;
			if (isset ( $xml_tag ['value'] )) {
				if ($tag_priority) {
					$result = $xml_tag ['value'];
				} else {
					$result ['value'] = $xml_tag ['value'];
				}
			}
			if (isset ( $xml_tag ['attributes'] ) and $get_attributes) {
				foreach ( $xml_tag ['attributes'] as $attr => $val ) {
					if ($tag_priority) {
						$attributes_data [$attr] = $val;
					} else {
						$result ['@attributes'] [$attr] = $val;
					}
				}
			}
			if ($xml_tag ['type'] == 'open') {
				$parent [$xml_tag ['level'] - 1] = &$current;
				if (! is_array ( $current ) or (! in_array ( $xml_tag ['tag'], array_keys ( $current ) ))) {
					$current [$xml_tag ['tag']] = $result;
					if ($attributes_data) {
						$current ['@' . $xml_tag ['tag']] = $attributes_data;
					}
					$repeated_tag_index [$xml_tag ['tag'] . '_' . $xml_tag ['level']] = 1;
					$current = &$current [$xml_tag ['tag']];
				} else {
					if (isset ( $current [$xml_tag ['tag']] ['0'] )) {
						$current [$xml_tag ['tag']] [$repeated_tag_index [$xml_tag ['tag'] . '_' . $xml_tag ['level']]] = $result;
						if ($attributes_data) {
							if (isset ( $repeated_tag_index ['@' . $xml_tag ['tag'] . '_' . $xml_tag ['level']] )) {
								$current [$xml_tag ['tag']] [$repeated_tag_index ['@' . $xml_tag ['tag'] . '_' . $xml_tag ['level']]] = $attributes_data;
							}
						}
						$repeated_tag_index [$xml_tag ['tag'] . '_' . $xml_tag ['level']] ++;
					} else {
						$current [$xml_tag ['tag']] = array ($current [$xml_tag ['tag']], $result );
						$repeated_tag_index [$xml_tag ['tag'] . '_' . $xml_tag ['level']] = 2;
						if (isset ( $current ['@' . $xml_tag ['tag']] )) {
							$current [$xml_tag ['tag']] ['@0'] = $current ['@' . $xml_tag ['tag']];
							unset ( $current ['@' . $xml_tag ['tag']] );
						}
						if ($attributes_data) {
							$current [$xml_tag ['tag']] ['@1'] = $attributes_data;
						}
					}
					$last_item_index = $repeated_tag_index [$xml_tag ['tag'] . '_' . $xml_tag ['level']] - 1;
					$current = &$current [$xml_tag ['tag']] [$last_item_index];
				}
			} elseif ($xml_tag ['type'] == 'complete') {
				if (! isset ( $current [$xml_tag ['tag']] ) and empty ( $current ['@'.$xml_tag ['tag']] ) ) {
					$current [$xml_tag ['tag']] = $result;
					$repeated_tag_index [$xml_tag ['tag'] . '_' . $xml_tag ['level']] = 1;
					if ($tag_priority and $attributes_data) {
						$current ['@' . $xml_tag ['tag']] = $attributes_data;
					}
				} else {
					if (isset ( $current [$xml_tag ['tag']] ['0'] ) and is_array ( $current [$xml_tag ['tag']] )) {
						$current [$xml_tag ['tag']] [$repeated_tag_index [$xml_tag ['tag'] . '_' . $xml_tag ['level']]] = $result;
						if ($tag_priority and $get_attributes and $attributes_data) {
							$current [$xml_tag ['tag']] ['@' . $repeated_tag_index [$xml_tag ['tag'] . '_' . $xml_tag ['level']]] = $attributes_data;
						}
						$repeated_tag_index [$xml_tag ['tag'] . '_' . $xml_tag ['level']] ++;
					} else {
						$current [$xml_tag ['tag']] = array ($current [$xml_tag ['tag']], $result );
						$repeated_tag_index [$xml_tag ['tag'] . '_' . $xml_tag ['level']] = 1;
						if ($tag_priority and $get_attributes) {
							if (isset ( $current ['@' . $xml_tag ['tag']] )) {
								$current [$xml_tag ['tag']] ['@0'] = $current ['@' . $xml_tag ['tag']];
								unset ( $current ['@' . $xml_tag ['tag']] );
							}
							if ($attributes_data) {
								$current [$xml_tag ['tag']] ['@' . $repeated_tag_index [$xml_tag ['tag'] . '_' . $xml_tag ['level']]] = $attributes_data;
							}
						}
						$repeated_tag_index [$xml_tag ['tag'] . '_' . $xml_tag ['level']] ++;
					}
				}
			} elseif ($xml_tag ['type'] == 'close') {
				$current = &$parent [$xml_tag ['level'] - 1];
			}
		}
		return $xml_array;
	}
}