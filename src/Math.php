<?php
/**
 * Math Utilities
 *
 * Business and e-commerce calculations for WordPress applications.
 *
 * @package ArrayPress\MathUtils
 * @since   1.0.0
 * @author  ArrayPress
 * @license GPL-2.0-or-later
 */

declare( strict_types=1 );

namespace ArrayPress\MathUtils;

class Math {

	/**
	 * Calculate tax (inclusive or exclusive).
	 *
	 * @param float $price     Price to calculate tax for.
	 * @param float $rate      Tax rate percentage (e.g., 8.25 for 8.25% tax).
	 * @param bool  $inclusive Whether tax is included in price (default: false).
	 * @param int   $precision Decimal places to round to (default: 2).
	 *
	 * @return array Array with 'tax' amount and 'price_without_tax' or 'price_with_tax'.
	 */
	public static function tax( float $price, float $rate, bool $inclusive = false, int $precision = 2 ): array {
		if ( $inclusive ) {
			$tax_multiplier    = 1 + ( $rate / 100 );
			$price_without_tax = round( $price / $tax_multiplier, $precision );
			$tax               = round( $price - $price_without_tax, $precision );

			return [
				'tax'               => $tax,
				'price_without_tax' => $price_without_tax
			];
		} else {
			$tax            = round( $price * ( $rate / 100 ), $precision );
			$price_with_tax = round( $price + $tax, $precision );

			return [
				'tax'            => $tax,
				'price_with_tax' => $price_with_tax
			];
		}
	}

	/**
	 * Apply discount (percentage or flat amount).
	 *
	 * @param float $price         Original price.
	 * @param float $discount      Discount value (percentage or flat amount).
	 * @param bool  $is_percentage Whether discount is percentage (default: true).
	 * @param int   $precision     Decimal places to round to (default: 2).
	 *
	 * @return array Array with 'discount' amount and 'final_price'.
	 */
	public static function discount( float $price, float $discount, bool $is_percentage = true, int $precision = 2 ): array {
		if ( $is_percentage ) {
			$discount_amount = round( $price * ( $discount / 100 ), $precision );
		} else {
			$discount_amount = round( $discount, $precision );
		}

		// Ensure discount doesn't exceed price
		$discount_amount = min( $discount_amount, $price );
		$final_price     = round( $price - $discount_amount, $precision );

		return [
			'discount'    => $discount_amount,
			'final_price' => $final_price
		];
	}

	/**
	 * Calculate profit margin.
	 *
	 * @param float $revenue   Total revenue.
	 * @param float $cost      Total cost.
	 * @param int   $precision Decimal places to round to (default: 2).
	 *
	 * @return float Profit margin as percentage.
	 */
	public static function profit_margin( float $revenue, float $cost, int $precision = 2 ): float {
		if ( $revenue <= 0 ) {
			return 0.0;
		}

		$margin = ( ( $revenue - $cost ) / $revenue ) * 100;

		return round( $margin, $precision );
	}

	/**
	 * Calculate percentage change between two values.
	 *
	 * @param float $old_value Original value.
	 * @param float $new_value New value.
	 * @param int   $precision Decimal places to round to (default: 2).
	 *
	 * @return float Percentage change (positive for increase, negative for decrease).
	 */
	public static function percentage_change( float $old_value, float $new_value, int $precision = 2 ): float {
		if ( $old_value == 0 ) {
			return $new_value > 0 ? 100.0 : 0.0;
		}

		$change = ( ( $new_value - $old_value ) / $old_value ) * 100;

		return round( $change, $precision );
	}

	/**
	 * Calculate conversion rate.
	 *
	 * @param float $conversions Number of conversions.
	 * @param float $total       Total number of attempts.
	 * @param int   $precision   Decimal places to round to (default: 2).
	 *
	 * @return float Conversion rate as percentage.
	 */
	public static function conversion_rate( float $conversions, float $total, int $precision = 2 ): float {
		if ( $total <= 0 ) {
			return 0.0;
		}

		$rate = ( $conversions / $total ) * 100;

		return round( $rate, $precision );
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

		$rounded = round( $result, $precision );
		if ( $rounded == (int) $rounded ) {
			return $sign . (int) $rounded . $suffix;
		} else {
			return $sign . number_format( $rounded, $precision ) . $suffix;
		}
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

}