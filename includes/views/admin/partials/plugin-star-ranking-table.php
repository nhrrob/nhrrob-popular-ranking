<div class="mt-10">
    <h1 class="align-center text-3xl font-bold underline"><?php esc_html_e('Popular Plugins By Stars', 'nhrrob-popular-ranking') ?></h1>

    <div class="bg-white dark:bg-gray-800 relative shadow-md sm:rounded-lg overflow-hidden mt-5">
        <div class="flex flex-col px-4 py-3 space-y-3 lg:flex-row lg:items-center lg:justify-between lg:space-y-0 lg:space-x-4">
            <div class="flex items-center flex-1 space-x-4">
                <h5>
                    <?php
                    $page = isset($_GET['paged']) ? intval($_GET['paged']) : 1;
                    $page = $page >= 1 && $page <= 20 ? $page : 1;
                    ?>
                    <?php
                    $transient_name = "{$this->transient_name}_{$page}";
                    $updated_at = get_option($transient_name . '_fetched_at');
                    $updated_at = human_time_diff($updated_at, current_time('U'));
                    ?>
                    <span class="text-gray-500"><?php echo esc_html__('Updated:', 'nhrrob-popular-ranking'); ?></span>
                    <span class="dark:text-white"><?php printf('%s %s', esc_html($updated_at), esc_html__('ago.', 'nhrrob-popular-ranking')); ?></span>
                </h5>
            </div>
            <div class="flex flex-col flex-shrink-0 space-y-3 md:flex-row md:items-center lg:justify-end md:space-y-0 md:space-x-3">
                <a type="button" href="<?php echo esc_url(admin_url("admin.php?page={$this->page_slug}&paged={$page}&username={$this->username}&cache_clear")); ?>" class="flex items-center justify-center flex-shrink-0 px-3 py-2 text-sm font-medium text-gray-900 bg-white border border-gray-200 rounded-lg focus:outline-none hover:bg-gray-100 hover:text-primary-700 focus:z-10 focus:ring-4 focus:ring-gray-200 dark:focus:ring-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:border-gray-600 dark:hover:text-white dark:hover:bg-gray-700">
                    <svg class="w-4 h-4 mr-2" xmlns="http://www.w3.org/2000/svg" aria-hidden="true" fill="none" viewbox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0l3.181 3.183a8.25 8.25 0 0013.803-3.7M4.031 9.865a8.25 8.25 0 0113.803-3.7l3.181 3.182m0-4.991v4.99" />
                    </svg>
                    <?php esc_html_e('Cache Clear', 'nhrrob-popular-ranking'); ?>
                </a>
            </div>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                    <tr>
                        <th scope="col" class="px-4 py-3">Rank</th>
                        <th scope="col" class="px-4 py-3">Active Installs</th>
                        <th scope="col" class="px-4 py-3">Name</th>
                        <th scope="col" class="px-4 py-3">Author</th>
                        <th scope="col" class="px-4 py-3">5 Stars</th>
                        <th scope="col" class="px-4 py-3">1 Stars</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $success_class = 'bg-success text-white';
                    $danger_class = 'bg-danger text-white';
                    $primary_class = 'bg-primary text-white';
                    $info_class = 'bg-info';
                    $excluded_plugins = $this->get_excluded_plugins_list();

                    $counter = 0;

                    foreach ($this->popular_plugins_stars as $index => $plugin_stars) {
                        $counter++;

                        $author_profile = str_replace('https://profiles.wordpress.org/', '', $plugin_stars['author_profile']);
                        $author_profile = '@' . rtrim($author_profile, '/');

                        if (!$all) {
                            if ($plugin_stars['author_profile'] !== "https://profiles.wordpress.org/{$this->username}/") {
                                continue;
                            }

                            if (in_array($plugin_stars['slug'], $excluded_plugins)) {
                                continue;
                            }

                            $author_profile = ! empty( $plugin_stars['plugin'] ) ? $plugin_stars['plugin']->author : '';
                        }

                        // $rank_diff          = intval( $plugin['rank_old'] ) - intval( $plugin['rank'] );
                        // $rank_diff_class    = $rank_diff > 0 ? $success_class : '';
                        // $rank_diff_class    = $rank_diff > 20 ? $primary_class : $rank_diff_class;
                        // $rank_diff_class    = $rank_diff < -20 ? $danger_class : $rank_diff_class;

                        // $active_installs_diff_raw   = intval($plugin['active_installs']) - intval($plugin['active_installs_old']);
                        // $active_installs_diff       = number_format( $active_installs_diff_raw );
                        // $active_installs_diff_class = $active_installs_diff_raw > 0 ? $success_class : '';

                        // $stars5_diff        = intval( $plugin['rating5'] ) - intval( $plugin['rating5_old'] );
                        // $stars5_diff_class  = $stars5_diff > 0 ? $success_class : '';

                        // $stars1_diff        = intval( $plugin['rating1'] ) - intval( $plugin['rating1_old'] );
                        // $stars1_diff_class  = $stars1_diff > 0 ? $danger_class : '';
                        // echo "<pre>";
                        // print_r($plugin_stars);
                    ?>
                        <tr class="border-b dark:border-gray-700">
                            <th scope="row" class="<?php //echo esc_attr( $rank_diff_class ); 
                                                    ?> px-4 py-3 font-medium text-gray-900 whitespace-nowrap dark:text-white"><?php printf('%d', intval($counter)); ?></th>
                            <td class="<?php //echo esc_attr( $active_installs_diff_class ); 
                                        ?> px-4 py-3"><?php printf('%s+', esc_html($this->shortNumber(!empty($plugin_stars['active_installs']) ? intval($plugin_stars['active_installs']) : 0))); ?></td>
                            <td class="px-4 py-3"><?php printf('<a target="_blank" href="%s">%s</a> ', esc_html( sanitize_url("https://wordpress.org/plugins/{$plugin_stars['slug']}") ), esc_html( wp_trim_words(!empty($plugin_stars['name']) ) ? esc_html( sanitize_text_field($plugin_stars['name']) ) : '', 4)); ?></td>
                            <td class="px-4 py-3"><?php echo wp_kses_post($author_profile); ?></td>
                            <td class="<?php //echo esc_attr( $stars5_diff_class ); 
                                        ?> px-4 py-3"><?php printf('%s', esc_html(intval($plugin_stars['rating5']))); ?></td>
                            <td class="<?php //echo esc_attr( $stars1_diff_class ); 
                                        ?> px-4 py-3"><?php printf('%s', esc_html(intval($plugin_stars['rating1']))); ?></td>
                        </tr>
                    <?php
                    }
                    ?>
                </tbody>
            </table>
        </div>
        <nav class="flex flex-col md:flex-row justify-between items-start md:items-center space-y-3 md:space-y-0 p-4" aria-label="Table navigation">
            <ul class="inline-flex items-stretch -space-x-px">
                <?php $prev_page = $page - 1 > 0 ? $page - 1 : 1; ?>
                <?php $next_page = $page + 1; ?>
                <li>
                    <a href="<?php echo esc_url(admin_url("admin.php?page={$this->page_slug}&star_ranking=all")); ?>" class="flex items-center justify-center text-sm py-2 px-3 leading-tight text-gray-500 bg-white border border-gray-300 hover:bg-gray-100 hover:text-gray-700 dark:bg-gray-800 dark:border-gray-700 dark:text-gray-400 dark:hover:bg-gray-700 dark:hover:text-white">All Plugins</a>
                </li>
            </ul>
        </nav>
    </div>
</div>