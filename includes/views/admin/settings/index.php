<div class="wrap">
    <h1><?php echo esc_html(get_admin_page_title()); ?></h1>

    <div class="nhrrob-popular-ranking text-base">
        <div class="nhrrob-popular-ranking-latest container">
            <h1 class="align-center text-3xl font-bold underline"><?php esc_html_e('WordPress Popular Plugins', 'nhrrob-popular-ranking') ?></h1>
            <div class="alert alert-primary" role="alert">
                <?php
                $updated_at = get_option($transient_name . '_fetched_at');
                $updated_at = human_time_diff($updated_at, current_time('U'));
                ?>
                <?php //printf(__('Updated %s ago.', 'nhrrob-popular-ranking'), $updated_at); ?>
            </div>
            <?php $this->print_buttons($page); ?>
            <div class="nhrrob-main-content">
                <?php
                // if (! empty($popular_plugins) ) {
                    $this->print_popular_plugins_table($popular_plugins);
                    $star_ranking_all = 0;

                    if ( isset( $_GET['paged'] ) ){
                        check_admin_referer('nhrrob-ranking-pagination-nonce');
                    }
                // }
                ?>
            </div>
        </div>
    </div>
</div>