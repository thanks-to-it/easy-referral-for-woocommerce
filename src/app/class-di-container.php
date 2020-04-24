<?php
/**
 * Easy Referral for WooCommerce - Dependency Injection Container
 *
 * @version 1.0.0
 * @since   1.0.0
 * @author  Thanks to IT
 */

namespace ThanksToIT\ERWC;

if ( ! class_exists( 'ThanksToIT\ERWC\DI_Container' ) ) {

	class DI_Container implements \ArrayAccess {
		/**
		 * Values stored inside the container.
		 *
		 * @var array
		 */
		private $values;

		/**
		 * Constructor.
		 *
		 * @param array $values
		 */
		public function __construct( array $values = array() ) {
			$this->values = $values;
		}

		/**
		 * Configure the container using the given container configuration objects.
		 *
		 * @param array $configurations
		 */
		public function configure( array $configurations ) {
			foreach ( $configurations as $configuration ) {
				$configuration->modify( $this );
			}
		}

		/**
		 * Checks if there's a value in the container for the given key.
		 *
		 * @param mixed $key
		 *
		 * @return bool
		 */
		public function offsetExists( $key ) {
			return array_key_exists( $key, $this->values );
		}

		/**
		 * Get a value from the container.
		 *
		 * @param mixed $key
		 *
		 * @return mixed|WP_Error
		 */
		public function offsetGet( $key ) {
			if ( ! array_key_exists( $key, $this->values ) ) {
				return new \WP_Error( 'no_value_found', sprintf( 'Container doesn\'t have a value stored for the "%s" key.', $key ) );
			}

			return $this->values[ $key ] instanceof \Closure ? $this->values[$key]( $this ) : $this->values[ $key ];
		}

		/**
		 * Sets a value inside of the container.
		 *
		 * @param mixed $key
		 * @param mixed $value
		 */
		public function offsetSet( $key, $value ) {
			$this->values[ $key ] = $value;
		}

		/**
		 * Unset the value in the container for the given key.
		 *
		 * @param mixed $key
		 */
		public function offsetUnset( $key ) {
			unset( $this->values[ $key ] );
		}

		/**
		 * Creates a closure used for creating a service using the given callable.
		 *
		 * @param \Closure $closure
		 *
		 * @return \Closure
		 */
		public function service( \Closure $closure ) {
			return function ( DI_Container $container ) use ( $closure ) {
				static $object;

				if ( null === $object ) {
					$object = $closure( $container );
				}

				return $object;
			};
		}
	}
}