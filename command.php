<?php

if ( ! class_exists( 'WP_CLI' ) ) {
	return;
}

/**
 * Control a DJI Tello.
 *
 * @subpackage commands/community
 * @maintainer Takayuki Miyauchi
 */
class Drone_Command extends WP_CLI_Command
{
	protected $localhost = '192.168.10.2';
	protected $localport = 9000;
	protected $socket;

	/**
	 * Takeoff the drone.
	 *
	 * ## OPTIONS
	 *
	 * [--ip=<ip-address>]
	 * : Hostname of the guest machine. Default is `vccw.test`.
	 *
	 * [--port=<port>]
	 * : IP address of the guest machine. Default is `192.168.33.10`.
	 *
	 *
	 * @when before_wp_load
	 */
	public function takeoff( $args, $assoc_args )
	{
		$this->connect( $assoc_args );
		$this->send_command('command');
		$this->send_command( 'takeoff' );
	}

	/**
	 * Land the drone.
	 *
	 * ## OPTIONS
	 *
	 * [--ip=<ip-address>]
	 * : Hostname of the guest machine. Default is `vccw.test`.
	 *
	 * [--port=<port>]
	 * : IP address of the guest machine. Default is `192.168.33.10`.
	 *
	 *
	 * @when before_wp_load
	 */
	public function land( $args, $assoc_args )
	{
		$this->connect( $assoc_args );
		$this->send_command( 'land' );
	}

	/**
	 * Rotate the drone clockwise.
	 *
	 * ## OPTIONS
	 *
	 * <degree>
	 * : Degree to rotate clockwise.
	 *
	 * [--ip=<ip-address>]
	 * : Hostname of the guest machine. Default is `vccw.test`.
	 *
	 * [--port=<port>]
	 * : IP address of the guest machine. Default is `192.168.33.10`.
	 *
	 * @when before_wp_load
	 */
	public function cw( $args, $assoc_args )
	{
		$this->connect( $assoc_args );
		$this->send_command( 'cw ' . $args[0] );
	}

	/**
	 * Rotate the drone counter-clockwise.
	 *
	 * ## OPTIONS
	 *
	 * <degree>
	 * : Degree to rotate counter-clockwise.
	 *
	 * [--ip=<ip-address>]
	 * : Hostname of the guest machine. Default is `vccw.test`.
	 *
	 * [--port=<port>]
	 * : IP address of the guest machine. Default is `192.168.33.10`.
	 *
	 * @when before_wp_load
	 */
	public function ccw( $args, $assoc_args )
	{
		$this->connect( $assoc_args );
		$this->send_command( 'ccw ' . $args[0] );
	}

	/**
	 * Flip the drone.
	 *
	 * ## OPTIONS
	 *
	 * <direction>
	 * : `left, `right`, `forward` or `back`.
	 *
	 * [--ip=<ip-address>]
	 * : Hostname of the guest machine. Default is `vccw.test`.
	 *
	 * [--port=<port>]
	 * : IP address of the guest machine. Default is `192.168.33.10`.
	 *
	 * @when before_wp_load
	 */
	public function flip( $args, $assoc_args )
	{
		$this->connect( $assoc_args );
		$this->send_command( 'flip ' . substr( $args[0], 0, 1 ) );
	}

	private function get_ip( $assoc_args )
	{
		if ( empty( $assoc_args["ip"] ) ) {
			return "192.168.10.1";
		} else {
			return $assoc_args["ip"];
		}
	}

	private function get_port( $assoc_args )
	{
		if ( empty( $assoc_args["port"] ) ) {
			return 8889;
		} else {
			return $assoc_args["port"];
		}
	}

	private function connect( $assoc_args )
    {
		$this->socket = socket_create( AF_INET, SOCK_DGRAM, SOL_UDP );
		socket_bind( $this->socket, $this->localhost, $this->localport );
		socket_connect( $this->socket, $this->get_ip( $assoc_args ), $this->get_port( $assoc_args ) );
    }

    private function send_command( $command )
    {
		WP_CLI::line( $command );
		return socket_send( $this->socket, $command, strlen( $command ), 0 );
    }
}

WP_CLI::add_command( 'drone', 'Drone_Command' );
