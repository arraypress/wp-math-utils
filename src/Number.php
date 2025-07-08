<?php
/**
 * Number Utilities
 *
 * This class provides essential number utilities for validation, formatting,
 * and common number operations. Focuses on practical, frequently-used number
 * manipulations with consistent behavior and error handling.
 *
 * @package ArrayPress\MathUtils
 * @since   1.0.0
 * @author  ArrayPress
 * @license GPL-2.0-or-later
 */

declare( strict_types=1 );

namespace ArrayPress\MathUtils;

class Number {

	/**
	 * Convert a value to a specific numeric type with optional constraints.
	 *
	 * @param mixed      $value The value to convert.
	 * @param string     $type  The numeric type ('int' or 'float').
	 * @param float|null $min   Optional minimum value.
	 * @param float|null $max   Optional maximum value.
	 *
	 * @return int|float The converted and constrained value.
	 */
	public static function to_numeric( $value, string $type = 'float', ?float $min = null, ?float $max = null ) {
		$type = strtolower( $type );

		// Convert to appropriate numeric type
		if ( $type === 'int' ) {
			$result = is_numeric( $value ) ? (int) $value : 0;
		} else {
			$result = is_numeric( $value ) ? (float) $value : 0.0;
		}

		// Apply constraints
		if ( $min !== null ) {
			$result = max( $result, $min );
		}
		if ( $max !== null ) {
			$result = min( $result, $max );
		}

		return $type === 'int' ? (int) $result : (float) $result;
	}

	/**
	 * Check if a number is even.
	 *
	 * @param int $number Number to check.
	 *
	 * @return bool True if even, false if odd.
	 */
	public static function is_even( int $number ): bool {
		return $number % 2 === 0;
	}

	/**
	 * Check if a number is odd.
	 *
	 * @param int $number Number to check.
	 *
	 * @return bool True if odd, false if even.
	 */
	public static function is_odd( int $number ): bool {
		return $number % 2 !== 0;
	}

	/**
	 * Check if a number is positive.
	 *
	 * @param float $number Number to check.
	 *
	 * @return bool True if positive.
	 */
	public static function is_positive( float $number ): bool {
		return $number > 0;
	}

	/**
	 * Check if a number is negative.
	 *
	 * @param float $number Number to check.
	 *
	 * @return bool True if negative.
	 */
	public static function is_negative( float $number ): bool {
		return $number < 0;
	}

	/**
	 * Check if a number is within a range (inclusive).
	 *
	 * @param float $number Number to check.
	 * @param float $min    Minimum value.
	 * @param float $max    Maximum value.
	 *
	 * @return bool True if number is within range.
	 */
	public static function in_range( float $number, float $min, float $max ): bool {
		return $number >= $min && $number <= $max;
	}

	/**
	 * Format a number with grouped thousands.
	 *
	 * @param float  $number        Number to format.
	 * @param int    $decimals      Number of decimal places (default: 2).
	 * @param string $dec_point     Decimal separator (default: '.').
	 * @param string $thousands_sep Thousands separator (default: ',').
	 *
	 * @return string Formatted number.
	 */
	public static function format( float $number, int $decimals = 2, string $dec_point = '.', string $thousands_sep = ',' ): string {
		return number_format( $number, $decimals, $dec_point, $thousands_sep );
	}

	/**
	 * Get ordinal suffix for a number (1st, 2nd, 3rd, 4th, etc.).
	 *
	 * @param int $number Number to get suffix for.
	 *
	 * @return string Number with ordinal suffix.
	 */
	public static function ordinal( int $number ): string {
		$suffixes = [ 'th', 'st', 'nd', 'rd' ];
		$mod100   = $number % 100;

		if ( $mod100 >= 11 && $mod100 <= 13 ) {
			$suffix = 'th';
		} else {
			$suffix = $suffixes[ $number % 10 ] ?? 'th';
		}

		return $number . $suffix;
	}

	/**
	 * Pad a number with leading zeros.
	 *
	 * @param int $number Number to pad.
	 * @param int $length Desired length of resulting string.
	 *
	 * @return string Zero-padded number.
	 */
	public static function zero_pad( int $number, int $length ): string {
		return str_pad( (string) $number, $length, '0', STR_PAD_LEFT );
	}

	/**
	 * Round to nearest step value.
	 *
	 * @param float $number Number to round.
	 * @param float $step   Step value to round to (default: 1.0).
	 *
	 * @return float Rounded number.
	 */
	public static function round_to_step( float $number, float $step = 1.0 ): float {
		if ( $step <= 0 ) {
			return $number;
		}

		return round( $number / $step ) * $step;
	}

	/**
	 * Abbreviate large numbers (1000 → 1K, 1000000 → 1M).
	 *
	 * @param float $number    Number to abbreviate.
	 * @param int   $precision Maximum decimal places for result (default: 1).
	 *
	 * @return string Abbreviated number.
	 */
	public static function abbreviate( float $number, int $precision = 1 ): string {
		$abs  = abs( $number );
		$sign = $number < 0 ? '-' : '';

		if ( $abs >= 1000000000 ) {
			$result = $abs / 1000000000;
			$suffix = 'B';
		} elseif ( $abs >= 1000000 ) {
			$result = $abs / 1000000;
			$suffix = 'M';
		} elseif ( $abs >= 1000 ) {
			$result = $abs / 1000;
			$suffix = 'K';
		} else {
			return (string) $number;
		}

		// Smart decimal handling - only show decimals when needed
		$rounded = round( $result, $precision );
		if ( $rounded == (int) $rounded ) {
			// Whole number, no decimals needed
			return $sign . (int) $rounded . $suffix;
		} else {
			// Has fractional part, show with decimals
			return $sign . number_format( $rounded, $precision ) . $suffix;
		}
	}

	/**
	 * Ensure a number is not negative.
	 *
	 * @param float $number Number to check.
	 *
	 * @return float Non-negative number (negative values become 0).
	 */
	public static function positive( float $number ): float {
		return max( 0.0, $number );
	}

	/**
	 * Clamp a number between minimum and maximum values.
	 *
	 * @param float $number Number to clamp.
	 * @param float $min    Minimum value.
	 * @param float $max    Maximum value.
	 *
	 * @return float Clamped number.
	 */
	public static function clamp( float $number, float $min, float $max ): float {
		return max( $min, min( $max, $number ) );
	}

	/**
	 * Negate a number (always returns negative).
	 *
	 * @param float $number Number to negate.
	 *
	 * @return float Negative number.
	 */
	public static function negate( float $number ): float {
		return abs( $number ) * - 1;
	}

	/**
	 * Get absolute value of a number.
	 *
	 * @param float $number Number to get absolute value for.
	 *
	 * @return float Absolute value.
	 */
	public static function abs( float $number ): float {
		return abs( $number );
	}

}