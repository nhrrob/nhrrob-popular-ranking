<div class="wrap">
    <h1><?php echo esc_html(get_admin_page_title()); ?></h1>

    <div class="nhrrob-popular-plugins text-base">
        <!-- <h1><?php //echo esc_html(get_admin_page_title()); ?></h1> -->

        <div class="nhrrob-popular-plugins-latest container">
            <h1 class="align-center text-3xl font-bold underline"><?php _e('WordPress Popular Plugins', 'nhrrob-popular-plugins') ?></h1>
            <div class="alert alert-primary" role="alert">
                <?php
                $updated_at = get_option($transient_name . '_fetched_at');
                $updated_at = human_time_diff($updated_at, current_time('U'));
                ?>
                <?php //printf(__('Updated %s ago.', 'nhrrob-popular-plugins'), $updated_at); ?>
            </div>
            <?php $this->print_buttons($page); ?>
            <div class="nhrrob-main-content">
                <?php
                // if (! empty($popular_plugins) ) {
                    $this->print_popular_plugins_table($popular_plugins);
                    $star_ranking_all = 0;

                    if (!empty($_GET['star_ranking'])) {
                        $star_ranking_all = 'all' === sanitize_text_field($_GET['star_ranking']) ? 1 : $star_ranking_all;
                    }

                    $this->print_popular_plugins_by_stars($star_ranking_all);
                // }
                ?>
            </div>
        </div>
    </div>
</div>