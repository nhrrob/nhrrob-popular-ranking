<?php

namespace Nhrrob\NhrrobPopularRanking;

/**
 * Installer class
 */
class Installer {

    /**
     * Run the installer
     *
     * @return void
     */
    public function run() {
        $this->add_version();
        $this->create_tables();
    }

    /**
     * Add time and version on DB
     */
    public function add_version() {
        $installed = get_option( 'nhrrob_popular_ranking_installed' );

        if ( ! $installed ) {
            update_option( 'nhrrob_popular_ranking_installed', time() );
        }

        update_option( 'nhrrob_popular_ranking_version', NHRROB_POPULAR_RANKING_VERSION );
    }

    /**
     * Create necessary database tables
     *
     * @return void
     */
    public function create_tables() {
        //
    }
}
