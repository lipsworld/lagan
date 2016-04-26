<?php

class Slug {

	// from http://www.justin-cook.com/wp/2006/06/27/php-trim-a-string-without-cutting-any-words/
	// @param string $str string we are operating with
	// @param inyteger $n character count to cut to
	// @param string $delim delimiter. Default: ''
	private function neatTrim($str, $n, $delim='') {
		$len = strlen($str);
		if ($len > $n) {
			preg_match('/(.{' . $n . '}.*?)\b/', $str, $matches);
			return rtrim($matches[1]) . $delim;
		} else {
			return $str;
		}
	}
	
	private function uniqueSlug($bean, $string) {
		$other = R::findOne($bean->getMeta('type'), ' slug = ? ', [ $string ] );
		if ($other) {
			if ($other->id == $bean->id) {
				return true;
			} else {
				return false;
			}
		} else {
			return true;
		}
	}

	// from http://stackoverflow.com/questions/2955251/php-function-to-make-slug-url-string
	private function slugify( $text ) {
		// replace non letter or digits by -
		$text = preg_replace('~[^\pL\d]+~u', '-', $text);

		// transliterate
		$text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);

		// remove unwanted characters
		$text = preg_replace('~[^-\w]+~', '', $text);

		// trim
		$text = trim($text, '-');

		// remove duplicate -
		$text = preg_replace('~-+~', '-', $text);

		// lowercase
		$text = strtolower($text);

		if (empty($text))
		{
			return 'n-a';
		}

		return $text;
	}

	private function makeSlug($bean, $slug_string) {
		$string = $this->neatTrim( $slug_string, 100 ); // Maximaal 100 tekens met hele woorden
		$slug = $this->slugify( $string );
		if ( $this->uniqueSlug( $bean, $slug ) ) {
			return $slug;
		} else {
			return $slug . '-' . $bean->id;
		}
	}

	// If $new_value is not set, slug is based on bean title
	public function setSlug($bean, $new_value) {
		if ( $new_value && strlen($new_value) > 0 ) {
			return $this->makeSlug($bean, $new_value);
		} elseif ( $bean->title ) {
			return $this->makeSlug($bean, $bean->title);
		} else {
			return $bean->id;
		}
	}

}

?>