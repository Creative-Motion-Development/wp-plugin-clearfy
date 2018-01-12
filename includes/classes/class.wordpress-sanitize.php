<?php

	/**
	 * @package name: Germanix URL
	 * @author URI - http://toscho.de/2010/wordpress-plugin-germanix/
	 * @author - Thomas Scholz
	 */
	class Wbcr_Germanizer {

		/**
		 * Fixes names of uploaded files.
		 * »häßliches bild.jpg« => haessliches-bild.jpg
		 *
		 * @param  string $filename
		 * @return string
		 */
		static function sanitize_filename_filter($filename)
		{
			// Windows LiveWriter sends escaped strings.
			$filename = html_entity_decode($filename, ENT_QUOTES, 'utf-8');
			$filename = self::translit($filename);
			$filename = self::lower_ascii($filename);
			$filename = self::remove_doubles($filename);

			return $filename;
		}

		/**
		 * Fixes URI slugs.
		 *
		 * If you don't have any latin characters in your title you may end up
		 * with an empty title. WordPress will use the post ID then.
		 *
		 * @param  string $title
		 * @param  string $raw_title
		 * @return string
		 */
		static function sanitize_title_filter($title, $raw_title = null)
		{
			!is_null($raw_title) and $title = $raw_title;

			$title = self::sanitize_filename_filter($title);
			$title = str_replace('.', '-', $title);
			// Avoid double minus. WordPress cannot resolve such URLs.
			$title = preg_replace('~--+~', '-', $title);

			// For %postname%-%post_id% permalinks.
			return rtrim($title, '-');
		}

		/**
		 * Reduces repeated meta characters (-=+.) to one.
		 *
		 * @uses   apply_filters( 'germanix_remove_doubles_regex' )
		 * @param  string $str Input string
		 * @return string
		 */
		static function remove_doubles($str)
		{
			$regex = apply_filters('germanix_remove_doubles_regex', array(
				'pattern' => '~([=+.-])\\1+~',
				'replacement' => "\\1"
			));

			return preg_replace($regex['pattern'], $regex['replacement'], $str);
		}

		/**
		 * Converts uppercase characters to lowercase and removes the rest.
		 *
		 * @uses   apply_filters( 'germanix_lower_ascii_regex' )
		 * @param  string $str Input string
		 * @return string
		 */
		static function lower_ascii($str)
		{
			$str = strtolower($str);
			$regex = apply_filters('germanix_lower_ascii_regex', array(
				'pattern' => '~([^a-z\d_.-])~',
				'replacement' => ''
			));
			// Leave underscores, otherwise the taxonomy tag cloud in the
			// backend won’t work anymore.
			return preg_replace($regex['pattern'], $regex['replacement'], $str);
		}

		/**
		 * Replaces non ASCII chars.
		 *
		 * wp-includes/formatting.php#L531 is unfortunately completely inappropriate.
		 * Modified version of Heiko Rabe’s code.
		 *
		 * @author Heiko Rabe http://code-styling.de
		 * @link   http://www.code-styling.de/?p=574
		 * @param  string $str
		 * @return string
		 */
		static function translit($str)
		{
			$utf8 = array(
				'Ä' => 'Ae',
				'ä' => 'ae',
				'Æ' => 'Ae',
				'æ' => 'ae',
				'À' => 'A',
				'à' => 'a',
				'Á' => 'A',
				'á' => 'a',
				'Â' => 'A',
				'â' => 'a',
				'Ã' => 'A',
				'ã' => 'a',
				'Å' => 'A',
				'å' => 'a',
				'ª' => 'a',
				'ₐ' => 'a',
				'ā' => 'a',
				'Ć' => 'C',
				'ć' => 'c',
				'Ç' => 'C',
				'ç' => 'c',
				'Ð' => 'D',
				'đ' => 'd',
				'È' => 'E',
				'è' => 'e',
				'É' => 'E',
				'é' => 'e',
				'Ê' => 'E',
				'ê' => 'e',
				'Ë' => 'E',
				'ë' => 'e',
				'ₑ' => 'e',
				'ƒ' => 'f',
				'ğ' => 'g',
				'Ğ' => 'G',
				'Ì' => 'I',
				'ì' => 'i',
				'Í' => 'I',
				'í' => 'i',
				'Î' => 'I',
				'î' => 'i',
				'Ï' => 'Ii',
				'ï' => 'ii',
				'ī' => 'i',
				'ı' => 'i',
				'I' => 'I' // turkish, correct?
				,
				'Ñ' => 'N',
				'ñ' => 'n',
				'ⁿ' => 'n',
				'Ò' => 'O',
				'ò' => 'o',
				'Ó' => 'O',
				'ó' => 'o',
				'Ô' => 'O',
				'ô' => 'o',
				'Õ' => 'O',
				'õ' => 'o',
				'Ø' => 'O',
				'ø' => 'o',
				'ₒ' => 'o',
				'Ö' => 'Oe',
				'ö' => 'oe',
				'Œ' => 'Oe',
				'œ' => 'oe',
				'ß' => 'ss',
				'Š' => 'S',
				'š' => 's',
				'ş' => 's',
				'Ş' => 'S',
				'™' => 'TM',
				'Ù' => 'U',
				'ù' => 'u',
				'Ú' => 'U',
				'ú' => 'u',
				'Û' => 'U',
				'û' => 'u',
				'Ü' => 'Ue',
				'ü' => 'ue',
				'Ý' => 'Y',
				'ý' => 'y',
				'ÿ' => 'y',
				'Ž' => 'Z',
				'ž' => 'z'// misc
				,
				'¢' => 'Cent',
				'€' => 'Euro',
				'‰' => 'promille',
				'№' => 'Nr',
				'$' => 'Dollar',
				'℃' => 'Grad Celsius',
				'°C' => 'Grad Celsius',
				'℉' => 'Grad Fahrenheit',
				'°F' => 'Grad Fahrenheit'// Superscripts
				,
				'⁰' => '0',
				'¹' => '1',
				'²' => '2',
				'³' => '3',
				'⁴' => '4',
				'⁵' => '5',
				'⁶' => '6',
				'⁷' => '7',
				'⁸' => '8',
				'⁹' => '9'// Subscripts
				,
				'₀' => '0',
				'₁' => '1',
				'₂' => '2',
				'₃' => '3',
				'₄' => '4',
				'₅' => '5',
				'₆' => '6',
				'₇' => '7',
				'₈' => '8',
				'₉' => '9'// Operators, punctuation
				,
				'±' => 'plusminus',
				'×' => 'x',
				'₊' => 'plus',
				'₌' => '=',
				'⁼' => '=',
				'⁻' => '-'    // sup minus
				,
				'₋' => '-'    // sub minus
				,
				'–' => '-'    // ndash
				,
				'—' => '-'    // mdash
				,
				'‑' => '-'    // non breaking hyphen
				,
				'․' => '.'    // one dot leader
				,
				'‥' => '..'  // two dot leader
				,
				'…' => '...'  // ellipsis
				,
				'‧' => '.'    // hyphenation point
				,
				' ' => '-'   // nobreak space
				,
				' ' => '-'   // normal space
				// Russian
				,
				'А' => 'A',
				'Б' => 'B',
				'В' => 'V',
				'Г' => 'G',
				'Д' => 'D',
				'Е' => 'E',
				'Ё' => 'YO',
				'Ж' => 'ZH',
				'З' => 'Z',
				'И' => 'I',
				'Й' => 'Y',
				'К' => 'K',
				'Л' => 'L',
				'М' => 'M',
				'Н' => 'N',
				'О' => 'O',
				'П' => 'P',
				'Р' => 'R',
				'С' => 'S',
				'Т' => 'T',
				'У' => 'U',
				'Ф' => 'F',
				'Х' => 'H',
				'Ц' => 'TS',
				'Ч' => 'CH',
				'Ш' => 'SH',
				'Щ' => 'SCH',
				'Ъ' => '',
				'Ы' => 'YI',
				'Ь' => '',
				'Э' => 'E',
				'Ю' => 'YU',
				'Я' => 'YA',
				'а' => 'a',
				'б' => 'b',
				'в' => 'v',
				'г' => 'g',
				'д' => 'd',
				'е' => 'e',
				'ё' => 'yo',
				'ж' => 'zh',
				'з' => 'z',
				'и' => 'i',
				'й' => 'y',
				'к' => 'k',
				'л' => 'l',
				'м' => 'm',
				'н' => 'n',
				'о' => 'o',
				'п' => 'p',
				'р' => 'r',
				'с' => 's',
				'т' => 't',
				'у' => 'u',
				'ф' => 'f',
				'х' => 'h',
				'ц' => 'ts',
				'ч' => 'ch',
				'ш' => 'sh',
				'щ' => 'sch',
				'ъ' => '',
				'ы' => 'yi',
				'ь' => '',
				'э' => 'e',
				'ю' => 'yu',
				'я' => 'ya'
			);

			$utf8 = apply_filters('germanix_translit_list', $utf8);

			$str = strtr($str, $utf8);

			return trim($str, '-');
		}
	}
