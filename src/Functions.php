<?php
/**
 * Expression Parser Helper Functions
 *
 * @package ArrayPress\MathUtils
 * @since   1.0.0
 * @author  ArrayPress
 * @license GPL-2.0-or-later
 */

declare( strict_types=1 );

use ArrayPress\MathUtils\ExpressionParser;

if ( ! function_exists( 'wp_evaluate_expression' ) ) {
	/**
	 * Safely evaluate a mathematical expression.
	 *
	 * @param string $expression Mathematical expression to evaluate.
	 * @param int    $precision  Number of decimal places (default: 2).
	 *
	 * @return float|int|null Result or null on error.
	 */
	function wp_evaluate_expression( string $expression, int $precision = 2 ) {
		static $parser = null;
		static $current_precision = null;

		if ( $parser === null || $current_precision !== $precision ) {
			$parser            = new ExpressionParser( $precision );
			$current_precision = $precision;
		}

		try {
			return $parser->evaluate( $expression );
		} catch ( Exception $e ) {
			if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
				error_log( "Expression evaluation error: " . $e->getMessage() );
			}

			return null;
		}
	}
}